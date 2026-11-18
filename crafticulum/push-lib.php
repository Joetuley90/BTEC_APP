<?php
/* Crafticulum: notifications on people's own devices (web push).
 * No outside service and no library: the site makes its own key pair the first time it's needed, encrypts each
 * message for the one browser it is going to (RFC 8291), and hands it to that browser's own push service.
 * A message only ever says what happened ("New comment on your lesson"). Learner work and names are never put in one. */
const CC_PUSH_KINDS = ['builds' => true, 'review' => true, 'people' => true, 'dates' => true, 'plan' => true];
function ccB64u(string $s): string { return rtrim(strtr(base64_encode($s), '+/', '-_'), '='); }
function ccB64ud(string $s): string { return (string) base64_decode(strtr($s, '-_', '+/') . str_repeat('=', (4 - strlen($s) % 4) % 4), true); }
function ccPushDir(): string { $d = ccDataDir() . '/_push'; if (!is_dir($d)) { ccEnsureData(); @mkdir($d, 0700, true); } return $d; }
function ccPushOk(): bool { return function_exists('openssl_pkey_derive') && function_exists('hash_hkdf') && function_exists('curl_init') && in_array('aes-128-gcm', openssl_get_cipher_methods(), true); }
/* the site's own key pair: made once, kept in the data folder */
function ccVapid(): ?array {
    static $v = null; if ($v !== null) return $v ?: null;
    $f = ccDataDir() . '/_vapid.json'; $j = is_file($f) ? json_decode((string) @file_get_contents($f), true) : null;
    if (!is_array($j) || empty($j['pem']) || empty($j['pub'])) {
        if (!ccPushOk()) { $v = false; return null; }
        $k = @openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_EC, 'curve_name' => 'prime256v1']); if (!$k) { $v = false; return null; }
        $d = openssl_pkey_get_details($k); $pem = ''; if (!openssl_pkey_export($k, $pem) || empty($d['ec']['x'])) { $v = false; return null; }
        $j = ['pem' => $pem, 'pub' => ccB64u("\x04" . str_pad($d['ec']['x'], 32, "\0", STR_PAD_LEFT) . str_pad($d['ec']['y'], 32, "\0", STR_PAD_LEFT)), 'made' => time()];
        ccEnsureData(); $tmp = $f . '.' . bin2hex(random_bytes(4)) . '.tmp'; if (@file_put_contents($tmp, json_encode($j), LOCK_EX) === false || !@rename($tmp, $f)) { @unlink($tmp); $v = false; return null; }
        @chmod($f, 0600);
    }
    return $v = $j;
}
/* only real push services are ever contacted, so a made-up address can't be used to reach anything else */
function ccPushHostOk(string $url): bool {
    $p = parse_url($url); $h = strtolower((string) ($p['host'] ?? '')); if ($h === '' || strlen($url) > 1000) return false;
    if (defined('PUSH_TEST_HOST') && ($p['scheme'] ?? '') === 'http' && $h . ':' . ($p['port'] ?? 80) === PUSH_TEST_HOST) return true;   // the test bench only
    if (($p['scheme'] ?? '') !== 'https' || isset($p['user']) || (isset($p['port']) && (int) $p['port'] !== 443)) return false;
    foreach (['fcm.googleapis.com', '.push.services.mozilla.com', '.push.apple.com', '.notify.windows.com', '.push.samsungosp.com'] as $ok) if ($h === ltrim($ok, '.') || ($ok[0] === '.' && substr($h, -strlen($ok)) === $ok)) return true;
    return false;
}
function ccPushFile(string $email): string { return ccPushDir() . '/' . sha1(strtolower($email)) . '.json'; }
function ccPushRead(string $email): array { $f = ccPushFile($email); $j = is_file($f) ? json_decode((string) @file_get_contents($f), true) : null; if (!is_array($j)) $j = []; return $j + ['email' => strtolower($email), 'subs' => [], 'prefs' => [], 'dates' => [], 'sent' => []]; }
function ccPushSave(string $email, array $j): bool { $f = ccPushFile($email); $tmp = $f . '.' . bin2hex(random_bytes(4)) . '.tmp'; if (@file_put_contents($tmp, json_encode($j, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), LOCK_EX) === false || !@rename($tmp, $f)) { @unlink($tmp); return false; } return true; }
function ccPushPrefs(array $j): array { $p = []; foreach (CC_PUSH_KINDS as $k => $def) $p[$k] = array_key_exists($k, (array) $j['prefs']) ? !empty($j['prefs'][$k]) : $def; return $p; }
/* one message, encrypted for one browser */
function ccPushEncrypt(string $payload, string $p256dh, string $auth): ?string {
    $ua = ccB64ud($p256dh); $secret = ccB64ud($auth); if (strlen($ua) !== 65 || $ua[0] !== "\x04" || strlen($secret) < 16) return null;
    $peer = @openssl_pkey_get_public("-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode(hex2bin('3059301306072a8648ce3d020106082a8648ce3d030107034200') . $ua), 64, "\n") . "-----END PUBLIC KEY-----\n");
    $mine = @openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_EC, 'curve_name' => 'prime256v1']); if (!$peer || !$mine) return null;
    $d = openssl_pkey_get_details($mine); $as = "\x04" . str_pad($d['ec']['x'], 32, "\0", STR_PAD_LEFT) . str_pad($d['ec']['y'], 32, "\0", STR_PAD_LEFT);
    $shared = @openssl_pkey_derive($peer, $mine, 32); if ($shared === false || $shared === '') return null;
    $ikm = hash_hkdf('sha256', $shared, 32, "WebPush: info\0" . $ua . $as, $secret); $salt = random_bytes(16);
    $cek = hash_hkdf('sha256', $ikm, 16, "Content-Encoding: aes128gcm\0", $salt); $nonce = hash_hkdf('sha256', $ikm, 12, "Content-Encoding: nonce\0", $salt);
    $tag = ''; $ct = openssl_encrypt($payload . "\x02", 'aes-128-gcm', $cek, OPENSSL_RAW_DATA, $nonce, $tag, '', 16); if ($ct === false) return null;
    return $salt . pack('N', 4096) . chr(65) . $as . $ct . $tag;
}
/* proof to the push service that the message is from this site */
function ccVapidHeader(string $endpoint): ?string {
    $v = ccVapid(); if (!$v) return null; $p = parse_url($endpoint); $aud = ($p['scheme'] ?? 'https') . '://' . ($p['host'] ?? '') . (isset($p['port']) ? ':' . $p['port'] : '');
    $s = ccSite(); $c = (string) ($s['contact'] ?? ''); $sub = filter_var($c, FILTER_VALIDATE_EMAIL) ? 'mailto:' . $c : rtrim(ccSiteUrl(), '/');
    $in = ccB64u('{"typ":"JWT","alg":"ES256"}') . '.' . ccB64u((string) json_encode(['aud' => $aud, 'exp' => time() + 43200, 'sub' => $sub], JSON_UNESCAPED_SLASHES));
    $der = ''; if (!@openssl_sign($in, $der, $v['pem'], OPENSSL_ALGO_SHA256)) return null;
    // the signature comes back wrapped; the push service wants the two numbers side by side
    $o = 2; if (ord($der[1]) & 0x80) $o += ord($der[1]) & 0x7f; $rl = ord($der[$o + 1]); $r = substr($der, $o + 2, $rl); $o += 2 + $rl; $sl = ord($der[$o + 1]); $sg = substr($der, $o + 2, $sl);
    $fix = fn(string $x): string => str_pad(substr(ltrim($x, "\0"), -32), 32, "\0", STR_PAD_LEFT);
    return 'vapid t=' . $in . '.' . ccB64u($fix($r) . $fix($sg)) . ', k=' . $v['pub'];
}
/* send to one browser. 0 when it couldn't be tried, otherwise the push service's answer */
function ccPushSend(array $sub, array $msg): int {
    $ep = (string) ($sub['endpoint'] ?? ''); if (!ccPushHostOk($ep)) return 0;
    $body = ccPushEncrypt((string) json_encode($msg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), (string) ($sub['p256dh'] ?? ''), (string) ($sub['auth'] ?? '')); $vh = ccVapidHeader($ep); if ($body === null || $vh === null) return 0;
    $ch = curl_init($ep);
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $body, CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 4, CURLOPT_TIMEOUT => 8, CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_PROTOCOLS => defined('PUSH_TEST_HOST') ? CURLPROTO_HTTP | CURLPROTO_HTTPS : CURLPROTO_HTTPS,
        CURLOPT_HTTPHEADER => ['Content-Type: application/octet-stream', 'Content-Encoding: aes128gcm', 'TTL: 86400', 'Urgency: normal', 'Authorization: ' . $vh, 'Content-Length: ' . strlen($body)]]);
    curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE); curl_close($ch);
    return $code;
}
/* messages wait until the page has had its answer, so nobody waits on a push service */
function ccNotify(string $email, string $kind, string $title, string $body = '', string $url = ''): void {
    $email = strtolower(trim($email)); if ($email === '' || !is_file(ccPushFile($email))) return;
    if (empty($GLOBALS['ccPushQ'])) { $GLOBALS['ccPushQ'] = []; register_shutdown_function('ccPushFlush'); }
    $GLOBALS['ccPushQ'][] = [$email, $kind, mb_substr($title, 0, 90), mb_substr(trim((string) preg_replace('/\s+/u', ' ', $body)), 0, 180), $url];
}
function ccPushNow(string $email, string $kind, string $title, string $body, string $url, bool $force = false): int {
    $j = ccPushRead($email); if (!$j['subs']) return 0; if (!$force && empty(ccPushPrefs($j)[$kind])) return 0;
    $msg = ['title' => $title, 'body' => $body, 'url' => $url !== '' ? $url : ccAppUrl(), 'tag' => $kind . '-' . substr(sha1($title . $body), 0, 8)]; $ok = 0; $keep = [];
    foreach ($j['subs'] as $s) { $c = ccPushSend($s, $msg); if ($c >= 200 && $c < 300) $ok++; if ($c === 404 || $c === 410) continue; $keep[] = $s; }   // a browser that has gone is forgotten
    if (count($keep) !== count($j['subs'])) { $j['subs'] = $keep; ccPushSave($email, $j); }
    return $ok;
}
function ccPushFlush(): void {
    $q = (array) ($GLOBALS['ccPushQ'] ?? []); $GLOBALS['ccPushQ'] = []; if (!$q || !ccPushOk()) return;
    if (session_status() === PHP_SESSION_ACTIVE) session_write_close(); if (function_exists('fastcgi_finish_request')) @fastcgi_finish_request();
    @ignore_user_abort(true); foreach (array_slice($q, 0, 40) as [$e, $k, $t, $b, $u]) { try { ccPushNow($e, $k, $t, $b, $u); } catch (\Throwable $x) {} }
}
/* some emails are also worth a nudge on the phone. The words come from the email, minus the greeting and any links */
function ccPushForMail(string $to, string $subject, string $text): void {
    static $map = ['New comment on your lesson' => 'review', 'Your change was signed off' => 'review', "Your change wasn't used" => 'review', 'A lesson change needs your sign-off' => 'review',
                   'A lesson needs your sign-off' => 'review', 'Your lesson was signed off' => 'review', 'New account to approve' => 'people'];
    if (!isset($map[$subject])) return;
    $body = ''; foreach (preg_split('/\n{2,}/', str_replace("\r", '', $text)) ?: [] as $b) { $b = trim((string) preg_replace('~https?://\S+~', '', $b)); if ($b === '' || preg_match('/^(Hello|Hi)\b.{0,70},$/u', $b)) continue; $body = (string) preg_replace('/\s*:\s*$/', '', (string) strtok($b, "\n")); break; }
    ccNotify($to, $map[$subject], $subject, $body);
}
/* once a day: assignments going out or coming in today and tomorrow. Each person's dates are sent up by their own browser, so they match what they see */
function ccPushDaily(bool $force = false): int {
    if (!ccPushOk() || !is_dir(ccDataDir() . '/_push')) return 0;
    $tz = new DateTimeZone('Europe/London'); $now = new DateTime('now', $tz); $today = $now->format('Y-m-d'); if (!$force && (int) $now->format('G') < 7) return 0;
    $stamp = ccPushDir() . '/_daily'; if (!$force && trim((string) @file_get_contents($stamp)) === $today) return 0;
    $fp = @fopen($stamp . '.lock', 'c'); if (!$fp || !flock($fp, LOCK_EX | LOCK_NB)) return 0;
    if (!$force && trim((string) @file_get_contents($stamp)) === $today) { flock($fp, LOCK_UN); fclose($fp); return 0; }
    @file_put_contents($stamp, $today); $tomorrow = (clone $now)->modify('+1 day')->format('Y-m-d'); $n = 0; $acc = ccAccounts();
    foreach ((array) glob(ccPushDir() . '/*.json') as $f) {
        $j = json_decode((string) @file_get_contents((string) $f), true); if (!is_array($j) || empty($j['subs']) || empty($j['dates']) || empty($j['email'])) continue;
        $e = (string) $j['email']; $u = $acc['users'][$e] ?? null; if ((!$u || !ccActive($u)) && !in_array($e, ccSupers(), true)) continue;
        $j += ['prefs' => [], 'sent' => []]; if (empty(ccPushPrefs($j)['dates'])) continue;
        $by = ['today' => [], 'tomorrow' => []]; foreach ((array) $j['dates'] as $d) { $day = (string) ($d['d'] ?? ''); $k = $day === $today ? 'today' : ($day === $tomorrow ? 'tomorrow' : ''); if ($k === '') continue; $key = $day . '|' . (string) ($d['k'] ?? '') . '|' . (string) ($d['t'] ?? ''); if (in_array($key . '|' . $k, (array) $j['sent'], true)) continue; $by[$k][] = $d; $j['sent'][] = $key . '|' . $k; }
        foreach ($by as $k => $list) { if (!$list) continue;
            $say = fn(array $d): string => (string) ($d['t'] ?? 'An assignment') . ' ' . (['out' => 'is handed out', 'due' => 'is due in', 'resub' => 'resubmissions are due'][(string) ($d['k'] ?? 'due')] ?? 'is due');
            $title = count($list) === 1 ? $say($list[0]) . ' ' . $k : count($list) . ' assignment dates ' . $k; $body = count($list) === 1 ? (string) ($list[0]['u'] ?? '') : implode('. ', array_map($say, array_slice($list, 0, 4)));
            if (ccPushNow($e, 'dates', mb_substr($title, 0, 90), mb_substr($body, 0, 180), '') > 0) $n++; }
        $j['sent'] = array_slice((array) $j['sent'], -200); $j2 = ccPushRead($e); $j2['sent'] = $j['sent']; ccPushSave($e, $j2);
    }
    flock($fp, LOCK_UN); fclose($fp); return $n;
}
