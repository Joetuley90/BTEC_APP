<?php
/*
 * Crafticulum: integrations, switched on by each institution for itself.
 *
 * Google Classroom and Microsoft Teams: a teacher sends the marks from a phone quiz into the class's own gradebook.
 * Nothing here works until an institution's manager turns one on in Settings, Institution, Integrations and puts in the
 * keys from the institution's own Google or Microsoft account. Each teacher then signs in to their own account once.
 * Secrets and sign-in tokens are kept encrypted in data/_integrations and never sent back to a browser.
 * Learners' names come from the institution's own class list, go to the teacher's screen to be matched to quiz initials,
 * and are not stored by Crafticulum.
 */
declare(strict_types=1);
if (!is_file(__DIR__ . '/config.php')) { http_response_code(500); exit; }
require __DIR__ . '/config.php';
require __DIR__ . '/auth-lib.php';
ccSession();
$action = (string) ($_GET['a'] ?? ''); $P = (string) ($_GET['p'] ?? '');
const IG = ['google' => 'Google Classroom', 'teams' => 'Microsoft Teams'];
function igBase(string $k): string { $d = ['GOOGLE_AUTH' => 'https://accounts.google.com/o/oauth2/v2/auth', 'GOOGLE_TOKEN' => 'https://oauth2.googleapis.com/token', 'GOOGLE_API' => 'https://classroom.googleapis.com', 'MS_LOGIN' => 'https://login.microsoftonline.com', 'MS_GRAPH' => 'https://graph.microsoft.com/v1.0']; return rtrim(defined('INTEG_' . $k) ? (string) constant('INTEG_' . $k) : $d[$k], '/'); }
function igDir(): string { $d = ccDataDir() . '/_integrations'; if (!is_dir($d)) { @mkdir($d, 0700, true); @file_put_contents($d . '/.htaccess', "Require all denied\n"); } return $d; }
function igKey(): string { $f = igDir() . '/.key'; $k = (string) @file_get_contents($f); if (strlen($k) !== 32) { $k = random_bytes(32); @file_put_contents($f, $k, LOCK_EX); @chmod($f, 0600); } return $k; }
function igSeal(string $plain): string { if ($plain === '') return ''; $n = random_bytes(12); $tag = ''; $c = openssl_encrypt($plain, 'aes-256-gcm', igKey(), OPENSSL_RAW_DATA, $n, $tag); return $c === false ? '' : 'v1:' . base64_encode($n . $tag . $c); }
function igOpen(string $sealed): string { if (strncmp($sealed, 'v1:', 3) !== 0) return ''; $b = base64_decode(substr($sealed, 3), true); if (!is_string($b) || strlen($b) < 29) return ''; $p = openssl_decrypt(substr($b, 28), 'aes-256-gcm', igKey(), OPENSSL_RAW_DATA, substr($b, 0, 12), substr($b, 12, 16)); return is_string($p) ? $p : ''; }
function igConf(array $a, string $college): array { $c = (array) ($a['colleges'][$college]['integrations'] ?? []); $o = [];
    foreach (IG as $k => $name) { $x = (array) ($c[$k] ?? []); $o[$k] = ['on' => !empty($x['on']), 'id' => (string) ($x['id'] ?? ''), 'secret' => (string) ($x['secret'] ?? ''), 'tenant' => (string) ($x['tenant'] ?? '')]; }
    $o['links'] = array_values(array_filter((array) ($c['links'] ?? []), fn($l) => is_array($l) && !empty($l['url']))); return $o; }
function igReady(array $cf, string $p): bool { return isset($cf[$p]) && $cf[$p]['on'] && $cf[$p]['id'] !== '' && $cf[$p]['secret'] !== '' && ($p !== 'teams' || $cf[$p]['tenant'] !== ''); }
function igRedirect(string $p): string { return ccSiteUrl() . 'integrate.php?a=cb&p=' . $p; }
function igUserFile(string $email): string { return igDir() . '/' . sha1(strtolower($email)) . '.json'; }
function igTokens(string $email): array { $j = json_decode((string) @file_get_contents(igUserFile($email)), true); return is_array($j) ? $j : []; }
function igTokensSave(string $email, array $t): void { $t ? @file_put_contents(igUserFile($email), json_encode($t), LOCK_EX) : @unlink(igUserFile($email)); }
function igHttp(string $method, string $url, $body = null, array $headers = []): array {
    if (!function_exists('curl_init')) return ['ok' => false, 'code' => 0, 'data' => [], 'error' => 'This server cannot make outside requests.'];
    $ch = curl_init($url); $h = $headers; $o = [CURLOPT_CUSTOMREQUEST => $method, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 25, CURLOPT_CONNECTTIMEOUT => 10];
    if (is_array($body) && in_array('form', $headers, true)) { $h = array_values(array_diff($h, ['form'])); $o[CURLOPT_POSTFIELDS] = http_build_query($body); $h[] = 'Content-Type: application/x-www-form-urlencoded'; }
    elseif ($body !== null) { $o[CURLOPT_POSTFIELDS] = json_encode($body, JSON_UNESCAPED_SLASHES); $h[] = 'Content-Type: application/json'; }
    $o[CURLOPT_HTTPHEADER] = $h; curl_setopt_array($ch, $o); $res = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); $err = curl_error($ch); curl_close($ch);
    $j = is_string($res) ? json_decode($res, true) : null; $ok = $code >= 200 && $code < 300;
    return ['ok' => $ok, 'code' => $code, 'data' => is_array($j) ? $j : [], 'error' => $ok ? '' : (string) ($j['error']['message'] ?? ($j['error_description'] ?? (is_string($j['error'] ?? null) ? $j['error'] : ($err ?: 'The service answered ' . $code . '.'))))];
}
/* an access token for this person, refreshed when it has run out */
function igAccess(string $email, string $p, array $cf): string {
    $t = igTokens($email); $x = (array) ($t[$p] ?? []); if (!$x) return ''; if ((int) ($x['exp'] ?? 0) > time() + 60 && ($acc = igOpen((string) ($x['access'] ?? ''))) !== '') return $acc;
    $refresh = igOpen((string) ($x['refresh'] ?? '')); if ($refresh === '') return '';
    $url = $p === 'google' ? igBase('GOOGLE_TOKEN') : igBase('MS_LOGIN') . '/' . rawurlencode($cf[$p]['tenant']) . '/oauth2/v2.0/token';
    $r = igHttp('POST', $url, ['grant_type' => 'refresh_token', 'refresh_token' => $refresh, 'client_id' => $cf[$p]['id'], 'client_secret' => igOpen($cf[$p]['secret'])], ['form']);
    if (!$r['ok'] || empty($r['data']['access_token'])) { if (in_array($r['code'], [400, 401], true)) { unset($t[$p]); igTokensSave($email, $t); } return ''; }
    $t[$p] = ['access' => igSeal((string) $r['data']['access_token']), 'refresh' => igSeal((string) ($r['data']['refresh_token'] ?? $refresh)), 'exp' => time() + (int) ($r['data']['expires_in'] ?? 3000), 'at' => (int) ($x['at'] ?? time())]; igTokensSave($email, $t);
    return (string) $r['data']['access_token'];
}
$page = function (string $title, string $text, bool $ok) { header('Content-Type: text/html; charset=utf-8'); header('Cache-Control: no-store'); header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; script-src 'unsafe-inline'");
    $h = fn($s) => htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    echo '<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . $h($title) . '</title><body style="font:17px/1.5 system-ui,sans-serif;margin:0;min-height:100vh;display:grid;place-items:center;background:#f5f5fb;color:#1e1b4b"><div style="max-width:440px;margin:16px;background:#fff;border-radius:16px;padding:28px;box-shadow:0 10px 40px rgba(79,70,229,.12);text-align:center"><div style="font-size:42px;line-height:1;color:' . ($ok ? '#16a34a' : '#b45309') . '">' . ($ok ? '&#10003;' : '!') . '</div><h1 style="font-size:1.3rem;margin:10px 0 6px">' . $h($title) . '</h1><p style="margin:0;color:#555">' . $h($text) . '</p></div><script>try{ if(window.opener) window.opener.postMessage({ccIntegration:' . ($ok ? 'true' : 'false') . '}, location.origin); }catch(e){}' . ($ok ? 'setTimeout(function(){ try{ window.close(); }catch(e){} }, 1200);' : '') . '</script>'; exit; };
$me = ccMe(); $a = ccAccounts(); $cid = $me ? (string) ($me['college'] ?? '') : ''; $cf = $cid !== '' ? igConf($a, $cid) : [];
$scopes = ['google' => 'https://www.googleapis.com/auth/classroom.courses.readonly https://www.googleapis.com/auth/classroom.rosters.readonly https://www.googleapis.com/auth/classroom.coursework.students', 'teams' => 'offline_access User.Read EduRoster.ReadBasic EduAssignments.ReadWrite'];

/* ---------- a teacher signs in to their own Google or Microsoft account ---------- */
if ($action === 'start') {
    if (!$me || !empty($me['demo'])) $page('Sign in to Crafticulum first', 'Then try again.', false); if (!isset(IG[$P]) || !igReady($cf, $P)) $page('Not switched on', (IG[$P] ?? 'This') . ' has not been set up for your institution. Ask your manager.', false);
    $state = bin2hex(random_bytes(16)); $_SESSION['ig_state'] = $state . '|' . $P . '|' . time(); session_write_close();
    $q = ['client_id' => $cf[$P]['id'], 'redirect_uri' => igRedirect($P), 'response_type' => 'code', 'scope' => $scopes[$P], 'state' => $state];
    if ($P === 'google') { $q += ['access_type' => 'offline', 'prompt' => 'consent', 'include_granted_scopes' => 'true']; $url = igBase('GOOGLE_AUTH'); } else { $q += ['response_mode' => 'query', 'prompt' => 'select_account']; $url = igBase('MS_LOGIN') . '/' . rawurlencode($cf[$P]['tenant']) . '/oauth2/v2.0/authorize'; }
    header('Location: ' . $url . (strpos($url, '?') === false ? '?' : '&') . http_build_query($q), true, 302); exit;
}
if ($action === 'cb') {
    $st = explode('|', (string) ($_SESSION['ig_state'] ?? '')); unset($_SESSION['ig_state']); session_write_close();
    if (!$me || !isset(IG[$P]) || count($st) !== 3 || $st[1] !== $P || !hash_equals($st[0], (string) ($_GET['state'] ?? '')) || time() - (int) $st[2] > 900) $page('That did not work', 'The sign-in took too long or was started somewhere else. Close this and try again.', false);
    if (!igReady($cf, $P)) $page('Not switched on', IG[$P] . ' has been switched off for your institution.', false);
    if (!empty($_GET['error']) || empty($_GET['code'])) $page('Not connected', 'You said no, or ' . IG[$P] . ' would not allow it. Your IT team may need to approve Crafticulum first.', false);
    $url = $P === 'google' ? igBase('GOOGLE_TOKEN') : igBase('MS_LOGIN') . '/' . rawurlencode($cf[$P]['tenant']) . '/oauth2/v2.0/token';
    $r = igHttp('POST', $url, ['grant_type' => 'authorization_code', 'code' => (string) $_GET['code'], 'redirect_uri' => igRedirect($P), 'client_id' => $cf[$P]['id'], 'client_secret' => igOpen($cf[$P]['secret'])] + ($P === 'teams' ? ['scope' => $scopes[$P]] : []), ['form']);
    if (!$r['ok'] || empty($r['data']['access_token'])) { ccErr('integration', IG[$P] . ' would not finish a sign-in. ' . $r['error']); $page('Not connected', 'The keys your institution saved were not accepted. Ask your manager to check them in Settings, Integrations.', false); }
    $t = igTokens($me['email']); $t[$P] = ['access' => igSeal((string) $r['data']['access_token']), 'refresh' => igSeal((string) ($r['data']['refresh_token'] ?? '')), 'exp' => time() + (int) ($r['data']['expires_in'] ?? 3000), 'at' => time()]; igTokensSave($me['email'], $t);
    $page('Connected to ' . IG[$P], 'You can close this tab and go back to Crafticulum.', true);
}
session_write_close();
header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store'); header('X-Content-Type-Options: nosniff');
function out(int $code, array $data): void { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit; }
if (!$me) out(401, ['error' => 'login']); if (!empty($me['demo'])) out(403, ['error' => 'demo']);
$body = json_decode((string) file_get_contents('php://input', false, null, 0, 200000) ?: '{}', true); if (!is_array($body)) $body = [];
$line = fn($s, int $n) => mb_substr(trim((string) preg_replace('/[\x00-\x1F\x7F<>]+/u', ' ', (string) $s)), 0, $n);
$boss = in_array($me['role'], ['owner', 'superadmin'], true) && $cid !== '' && isset($a['colleges'][$cid]);

/* ---------- what is switched on here, and is this person connected ---------- */
if ($action === 'info') { $t = igTokens($me['email']); $o = []; foreach (IG as $k => $n) $o[$k] = ['name' => $n, 'on' => igReady($cf, $k), 'connected' => !empty($t[$k])]; out(200, ['providers' => $o, 'links' => array_map(fn($l) => ['name' => (string) ($l['name'] ?? ''), 'url' => (string) $l['url']], $cf['links'] ?? [])]); }
if ($action === 'disconnect') { $t = igTokens($me['email']); unset($t[(string) ($body['p'] ?? '')]); igTokensSave($me['email'], $t); out(200, ['ok' => true]); }

/* ---------- the institution's manager: switch things on and off, and put the keys in ---------- */
if ($action === 'config' || $action === 'configset') {
    if (!$boss) out(403, ['error' => 'noperm']);
    if ($action === 'configset') { $in = (array) ($body['set'] ?? []); $cur = (array) ($a['colleges'][$cid]['integrations'] ?? []); $said = [];
        foreach (IG as $k => $name) { if (!isset($in[$k]) || !is_array($in[$k])) continue; $x = (array) ($cur[$k] ?? []); $n = $in[$k]; $was = !empty($x['on']);
            if (array_key_exists('id', $n)) $x['id'] = (string) preg_replace('/[^A-Za-z0-9._@:-]/', '', $line($n['id'], 200));
            if (array_key_exists('tenant', $n)) $x['tenant'] = (string) preg_replace('/[^A-Za-z0-9.-]/', '', $line($n['tenant'], 80));
            if (isset($n['secret']) && trim((string) $n['secret']) !== '') $x['secret'] = igSeal($line($n['secret'], 400));
            if (!empty($n['forget'])) $x['secret'] = '';
            if (array_key_exists('on', $n)) $x['on'] = !empty($n['on']);
            if (!empty($x['on']) && (($x['id'] ?? '') === '' || ($x['secret'] ?? '') === '' || ($k === 'teams' && ($x['tenant'] ?? '') === ''))) out(400, ['error' => 'keys', 'p' => $k]);
            $cur[$k] = $x; if ($was !== !empty($x['on'])) $said[] = (!empty($x['on']) ? 'switched on ' : 'switched off ') . $name; }
        if (array_key_exists('links', $in)) { $ls = []; foreach (array_slice((array) $in['links'], 0, 4) as $l) { $u = $line($l['url'] ?? '', 300); $nm = $line($l['name'] ?? '', 40); if ($u === '') continue; if (!preg_match('#^https://[^\s"\'<>]+$#i', $u) || !filter_var($u, FILTER_VALIDATE_URL)) out(400, ['error' => 'link']); $ls[] = ['name' => $nm !== '' ? $nm : (string) parse_url($u, PHP_URL_HOST), 'url' => $u]; } $cur['links'] = $ls; }
        $a['colleges'][$cid]['integrations'] = $cur; if (!ccSaveAccounts($a)) out(500, ['error' => 'save']); foreach ($said as $s) ccLog($me['dept'], $me, $s); if (!$said) ccLog($me['dept'], $me, 'changed the integrations'); $cf = igConf($a, $cid); }
    $o = []; foreach (IG as $k => $n) $o[$k] = ['name' => $n, 'on' => $cf[$k]['on'], 'id' => $cf[$k]['id'], 'tenant' => $cf[$k]['tenant'], 'secretSet' => $cf[$k]['secret'] !== '', 'redirect' => igRedirect($k), 'ready' => igReady($cf, $k)];
    out(200, ['providers' => $o, 'links' => $cf['links'], 'curl' => function_exists('curl_init') && function_exists('openssl_encrypt')]);
}

/* ---------- a teacher: classes, the people in one, and sending the marks ---------- */
if (in_array($action, ['classes', 'people', 'send'], true)) {
    $p = (string) ($body['p'] ?? ''); if (!isset(IG[$p]) || !igReady($cf, $p)) out(400, ['error' => 'off']);
    $tok = igAccess($me['email'], $p, $cf); if ($tok === '') out(401, ['error' => 'connect', 'p' => $p]); $H = ['Authorization: Bearer ' . $tok]; $G = igBase('GOOGLE_API') . '/v1'; $M = igBase('MS_GRAPH');
    $fail = function (array $r) use ($p) { if ($r['code'] === 401) out(401, ['error' => 'connect', 'p' => $p]); out(502, ['error' => 'service', 'why' => $r['error'], 'denied' => $r['code'] === 403]); };
    $class = (string) ($body['class'] ?? ''); if ($action !== 'classes' && !preg_match('/^[A-Za-z0-9_-]{1,80}$/', $class)) out(400, ['error' => 'class']);
    if ($action === 'classes') {
        if ($p === 'google') { $r = igHttp('GET', $G . '/courses?teacherId=me&courseStates=ACTIVE&pageSize=100', null, $H); if (!$r['ok']) $fail($r); $list = array_map(fn($c) => ['id' => (string) $c['id'], 'name' => trim((string) ($c['name'] ?? '') . (!empty($c['section']) ? ' · ' . $c['section'] : ''))], (array) ($r['data']['courses'] ?? [])); }
        else { $r = igHttp('GET', $M . '/education/me/taughtClasses?$select=id,displayName&$top=100', null, $H); if (!$r['ok']) $fail($r); $list = array_map(fn($c) => ['id' => (string) $c['id'], 'name' => (string) ($c['displayName'] ?? '')], (array) ($r['data']['value'] ?? [])); }
        usort($list, fn($x, $y) => strcasecmp($x['name'], $y['name'])); out(200, ['classes' => $list]);
    }
    $people = function () use ($p, $G, $M, $H, $class, $fail) { $out = [];
        if ($p === 'google') { $next = ''; for ($i = 0; $i < 6; $i++) { $r = igHttp('GET', $G . '/courses/' . rawurlencode($class) . '/students?pageSize=100' . ($next !== '' ? '&pageToken=' . rawurlencode($next) : ''), null, $H); if (!$r['ok']) $fail($r); foreach ((array) ($r['data']['students'] ?? []) as $s) $out[] = ['id' => (string) ($s['userId'] ?? ''), 'name' => (string) ($s['profile']['name']['fullName'] ?? '')]; $next = (string) ($r['data']['nextPageToken'] ?? ''); if ($next === '') break; } }
        else { $r = igHttp('GET', $M . '/education/classes/' . rawurlencode($class) . '/members?$select=id,displayName&$top=200', null, $H); if (!$r['ok']) $fail($r); $all = (array) ($r['data']['value'] ?? []); $t = igHttp('GET', $M . '/education/classes/' . rawurlencode($class) . '/teachers?$select=id', null, $H); $teach = array_map(fn($x) => (string) $x['id'], (array) ($t['data']['value'] ?? [])); foreach ($all as $s) if (!in_array((string) $s['id'], $teach, true)) $out[] = ['id' => (string) $s['id'], 'name' => (string) ($s['displayName'] ?? '')]; }
        $out = array_values(array_filter($out, fn($s) => $s['id'] !== '')); usort($out, fn($x, $y) => strcasecmp($x['name'], $y['name'])); return $out; };
    if ($action === 'people') out(200, ['people' => $people()]);
    /* send: make the piece of work in the class (or carry on with one made a moment ago), then put each learner's mark on it */
    $title = $line($body['title'] ?? '', 120) ?: 'Exit quiz'; $max = max(1, min(1000, (int) ($body['max'] ?? 0))); $work = (string) ($body['work'] ?? ''); if ($work !== '' && !preg_match('/^[A-Za-z0-9_-]{1,120}$/', $work)) out(400, ['error' => 'work']);
    $marks = []; foreach (array_slice((array) ($body['marks'] ?? []), 0, 300) as $m) { $id = (string) ($m['id'] ?? ''); if ($id === '' || !preg_match('/^[A-Za-z0-9_-]{1,80}$/', $id)) continue; $marks[$id] = max(0, min($max, round((float) ($m['score'] ?? 0), 2))); }
    if (!$marks) out(400, ['error' => 'marks']); $sent = 0; $missed = [];
    if ($p === 'google') { $base = $G . '/courses/' . rawurlencode($class) . '/courseWork';
        if ($work === '') { $r = igHttp('POST', $base, ['title' => $title, 'description' => 'Marks from a quiz in Crafticulum.', 'workType' => 'ASSIGNMENT', 'state' => 'PUBLISHED', 'maxPoints' => $max], $H); if (!$r['ok'] || empty($r['data']['id'])) $fail($r); $work = (string) $r['data']['id']; }
        $r = igHttp('GET', $base . '/' . rawurlencode($work) . '/studentSubmissions?pageSize=300', null, $H); if (!$r['ok']) $fail($r); $subs = []; foreach ((array) ($r['data']['studentSubmissions'] ?? []) as $s) $subs[(string) ($s['userId'] ?? '')] = (string) ($s['id'] ?? '');
        foreach ($marks as $uid => $score) { $sid = $subs[$uid] ?? ''; if ($sid === '') { $missed[] = $uid; continue; } $u = $base . '/' . rawurlencode($work) . '/studentSubmissions/' . rawurlencode($sid);
            $r = igHttp('PATCH', $u . '?updateMask=assignedGrade,draftGrade', ['assignedGrade' => $score, 'draftGrade' => $score], $H); if (!$r['ok']) { $missed[] = $uid; continue; } igHttp('POST', $u . ':return', new stdClass(), $H); $sent++; }
    } else { $base = $M . '/education/classes/' . rawurlencode($class) . '/assignments';
        if ($work === '') { $r = igHttp('POST', $base, ['displayName' => $title, 'instructions' => ['contentType' => 'text', 'content' => 'Marks from a quiz in Crafticulum.'], 'dueDateTime' => gmdate('Y-m-d\TH:i:s\Z', time() + 3600), 'status' => 'draft', 'grading' => ['@odata.type' => '#microsoft.graph.educationAssignmentPointsGradeType', 'maxPoints' => $max], 'assignTo' => ['@odata.type' => '#microsoft.graph.educationAssignmentClassRecipient']], $H); if (!$r['ok'] || empty($r['data']['id'])) $fail($r); $work = (string) $r['data']['id']; igHttp('POST', $base . '/' . rawurlencode($work) . '/publish', new stdClass(), $H); }
        $subs = []; for ($i = 0; $i < 4 && !$subs; $i++) { if ($i) sleep(3); $r = igHttp('GET', $base . '/' . rawurlencode($work) . '/submissions?$top=300', null, $H); if (!$r['ok']) $fail($r); foreach ((array) ($r['data']['value'] ?? []) as $s) $subs[(string) ($s['recipient']['userId'] ?? '')] = (string) ($s['id'] ?? ''); }
        if (!$subs) out(202, ['pending' => true, 'work' => $work]);   // Teams takes a little while to hand the work out to the class: the teacher presses Send again and it carries on with the same piece of work
        foreach ($marks as $uid => $score) { $sid = $subs[$uid] ?? ''; if ($sid === '') { $missed[] = $uid; continue; } $u = $base . '/' . rawurlencode($work) . '/submissions/' . rawurlencode($sid);
            $o = igHttp('GET', $u . '/outcomes', null, $H); $oid = ''; foreach ((array) ($o['data']['value'] ?? []) as $x) if (stripos((string) ($x['@odata.type'] ?? ''), 'educationPointsOutcome') !== false) $oid = (string) ($x['id'] ?? ''); if ($oid === '') { $missed[] = $uid; continue; }
            $r = igHttp('PATCH', $u . '/outcomes/' . rawurlencode($oid), ['@odata.type' => '#microsoft.graph.educationPointsOutcome', 'points' => ['@odata.type' => '#microsoft.graph.educationAssignmentPointsGrade', 'points' => $score]], $H); if (!$r['ok']) { $missed[] = $uid; continue; } igHttp('POST', $u . '/return', new stdClass(), $H); $sent++; }
    }
    ccLog($me['dept'], $me, 'sent ' . $sent . ' quiz mark' . ($sent === 1 ? '' : 's') . ' to ' . IG[$p]);
    out(200, ['ok' => true, 'sent' => $sent, 'missed' => $missed, 'work' => $work]);
}
out(400, ['error' => 'Unknown action.']);
