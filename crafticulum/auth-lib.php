<?php
/*
 * Crafticulum: who is signed in, and what they're allowed to do.
 * Used by auth.php, ai.php, store.php, share.php and backup.php.
 *
 * Roles
 *   superadmin  everything, in every department (emails listed in SUPER_ADMINS in config.php)
 *   hod         head of department: adds staff, sets their options, builds lessons with Crafticulum AI
 *   teacher     uses the department's lessons and units; can't build with Crafticulum AI;
 *               can edit lessons by hand only if their head of department allows it
 *
 * Accounts live in data/_accounts.json, which the .htaccess in data/ blocks from the web.
 */
declare(strict_types=1);

require_once __DIR__ . '/push-lib.php';
function ccDataDir(): string { return __DIR__ . '/data'; }
function ccEnsureData(): void {
    $d = ccDataDir();
    if (!is_dir($d)) @mkdir($d, 0700, true);
    if (!is_file($d . '/.htaccess')) {
        @file_put_contents($d . '/.htaccess', "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n");
        @file_put_contents($d . '/index.html', '');
    }
}
/* How long someone stays signed in on a device they use regularly: 30 days unless Settings, Site setup says otherwise. */
function ccSessionDays(): int {
    $d = 0; $f = ccDataDir() . '/_accounts.json';
    if (is_file($f)) { $j = json_decode((string) @file_get_contents($f), true); $d = (int) ($j['site']['conf']['SESSION_DAYS'] ?? 0); }
    if ($d < 1 && defined('SESSION_DAYS')) $d = (int) SESSION_DAYS;
    return $d >= 1 ? min(365, $d) : 30;
}
function ccHttps(): bool { return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https' || (string) ($_SERVER['SERVER_PORT'] ?? '') === '443'; }
function ccSession(): void {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    $life = ccSessionDays() * 86400;
    // Sessions are kept in this site's own folder with its own cookie name. On shared hosting the default folder is shared and
    // cleared after about 24 minutes, and the default cookie can clash with another PHP site on the same address: both sign people out.
    ccEnsureData(); $dir = ccDataDir() . '/_sessions';
    if (!is_dir($dir)) @mkdir($dir, 0700, true);
    if (is_dir($dir) && is_writable($dir)) { @ini_set('session.save_handler', 'files'); @session_save_path($dir); @ini_set('session.gc_probability', '1'); @ini_set('session.gc_divisor', '200'); }
    @ini_set('session.gc_maxlifetime', (string) $life);
    session_name('ccsid');
    $opts = ['cookie_lifetime' => $life, 'cookie_path' => '/', 'cookie_httponly' => true, 'cookie_samesite' => 'Lax', 'cookie_secure' => ccHttps(), 'use_only_cookies' => 1];
    @session_start($opts);
    if (session_status() !== PHP_SESSION_ACTIVE) { @session_save_path(sys_get_temp_dir()); @session_start($opts); }
    // every day it's used, the cookie is pushed on another full stretch, so regular users aren't signed out
    if (!empty($_SESSION['uid']) && (int) ($_SESSION['fresh'] ?? 0) < time() - 43200 && !headers_sent()) {
        $_SESSION['fresh'] = time();
        setcookie(session_name(), (string) session_id(), ['expires' => time() + $life, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax', 'secure' => ccHttps()]);
    }
}
function ccSupers(): array { return defined('SUPER_ADMINS') ? array_values(array_filter(array_map(fn($e) => strtolower(trim((string) $e)), (array) SUPER_ADMINS))) : []; }
function ccMsId(): string { return (ccConf('MS_CLIENT_ID') !== '') && preg_match('/^[0-9a-f-]{36}$/i', (string) ccConf('MS_CLIENT_ID')) && (ccConf('MS_CLIENT_SECRET') !== '') && ccConf('MS_CLIENT_SECRET') ? (string) ccConf('MS_CLIENT_ID') : ''; }
function ccMsTenant(): string { return (ccConf('MS_TENANT') !== '') && preg_match('/^[A-Za-z0-9.-]{3,80}$/', (string) ccConf('MS_TENANT')) ? (string) ccConf('MS_TENANT') : 'common'; }
/* any OpenID Connect provider (Okta, Auth0, Keycloak, OneLogin, college single sign-on) */
function ccOidcOn(): bool { return (ccConf('OIDC_ISSUER') !== '') && preg_match('#^https?://#', (string) ccConf('OIDC_ISSUER')) && (ccConf('OIDC_CLIENT_ID') !== '') && ccConf('OIDC_CLIENT_ID') !== '' && (ccConf('OIDC_CLIENT_SECRET') !== '') && ccConf('OIDC_CLIENT_SECRET') !== ''; }
function ccOidcName(): string { return (ccConf('OIDC_NAME') !== '') && trim((string) ccConf('OIDC_NAME')) !== '' ? mb_substr(trim((string) ccConf('OIDC_NAME')), 0, 40) : 'single sign-on'; }
function ccOidcMeta(): ?array {
    if (!ccOidcOn()) return null;
    $iss = rtrim((string) ccConf('OIDC_ISSUER'), '/'); $c = ccJson('_oidc.json');
    if (($c['issuer_cfg'] ?? '') === $iss && ($c['at'] ?? 0) > time() - 86400 && !empty($c['meta']['authorization_endpoint'])) return $c['meta'];
    $ch = curl_init($iss . '/.well-known/openid-configuration'); curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15, CURLOPT_FOLLOWLOCATION => true]);
    $res = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    $m = $code === 200 ? json_decode((string) $res, true) : null;
    if (!is_array($m) || empty($m['authorization_endpoint']) || empty($m['token_endpoint'])) return null;
    ccSaveJson('_oidc.json', ['issuer_cfg' => $iss, 'at' => time(), 'meta' => $m]); return $m;
}
function ccGoogleId(): string { return (ccConf('GOOGLE_CLIENT_ID') !== '') && strpos((string) ccConf('GOOGLE_CLIENT_ID'), '.apps.googleusercontent.com') !== false ? (string) ccConf('GOOGLE_CLIENT_ID') : ''; }
/* the password still works as a super admin way in until Google sign-in is set up, or if PASSWORD_BACKUP is true */
function ccPasswordAllowed(): bool {
    if (!defined('TEACHER_PASSWORD') || TEACHER_PASSWORD === 'change-this-password' || TEACHER_PASSWORD === '') return false;
    if (defined('PASSWORD_BACKUP') && PASSWORD_BACKUP === true) return true;
    if (ccGoogleId() !== '' || ccMsId() !== '' || ccOidcOn()) return false;
    // with only email and password accounts, it switches off once a super admin has their own password
    foreach (ccAccounts()['users'] as $e => $u) if (in_array($e, ccSupers(), true) && !empty($u['pass']) && ccActive($u)) return false;
    return true;
}
/* Settings the site admin can change on screen (Settings, Site setup). A value saved on screen is used first, then config.php. */
const CC_CONF_KEYS = ['ANTHROPIC_API_KEY', 'CLAUDE_MODEL', 'MAX_REQUESTS_PER_HOUR', 'GOOGLE_CLIENT_ID', 'GOOGLE_CLIENT_SECRET', 'MS_CLIENT_ID', 'MS_CLIENT_SECRET', 'MS_TENANT', 'OIDC_ISSUER', 'OIDC_CLIENT_ID', 'OIDC_CLIENT_SECRET', 'OIDC_NAME', 'SITE_URL', 'SITE_URL_TEMP', 'SESSION_DAYS', 'MAIL_FROM', 'SMTP_HOST', 'SMTP_PORT', 'SMTP_USER', 'SMTP_PASS', 'SMTP_SECURE', 'BACKUP_URL', 'BACKUP_USER', 'BACKUP_PASS', 'BACKUP_TOKEN', 'DEMO', 'DEMO_BUILDS', 'BILLING', 'STRIPE_SECRET_KEY', 'STRIPE_WEBHOOK_SECRET', 'STRIPE_PUBLISHABLE_KEY', 'TTS_KEY', 'TTS_VOICE', 'TTS_CAP', 'TTS_LOCK'];
const CC_CONF_SECRET = ['ANTHROPIC_API_KEY', 'GOOGLE_CLIENT_SECRET', 'MS_CLIENT_SECRET', 'OIDC_CLIENT_SECRET', 'SMTP_PASS', 'BACKUP_PASS', 'STRIPE_SECRET_KEY', 'STRIPE_WEBHOOK_SECRET', 'TTS_KEY'];
function ccConf(string $k) {
    static $s = null; if ($s === null) $s = (array) (ccAccounts()['site']['conf'] ?? []);
    $v = $s[$k] ?? ''; if ($v !== '' && $v !== null) return $v;
    return defined($k) ? constant($k) : '';
}
/* the institution's name, logo and colour, shown on the sign-in page, in the app and on student pages */
/* Crafticulum's own support address. Used on the original site until a contact email is set in Settings. A new institution's site never uses it: its messages go to its own people */
const CC_SUPPORT_EMAIL = 'app@crafticulum.co.uk';
/* ---------- colleges ----------
   One site can hold several institutions. Each has its own name, logo, colour, contact address, departments and people.
   The super admin looks after the whole site and every institution. An institution manager looks after one institution. */
function ccCollegeIds(array $a): array { return array_keys((array) ($a['colleges'] ?? [])); }
function ccCollegeOf(array $a, string $dept): string { $c = (string) ($a['depts'][$dept]['college'] ?? ''); return isset($a['colleges'][$c]) ? $c : (string) (array_key_first((array) ($a['colleges'] ?? [])) ?? ''); }
function ccCollege(array $a, string $id): array { $c = (array) ($a['colleges'][$id] ?? []);
    return ['id' => $id, 'name' => (string) ($c['name'] ?? ''), 'logo' => (string) ($c['logo'] ?? ''), 'color' => (string) ($c['color'] ?? ''), 'contact' => (string) ($c['contact'] ?? ''), 'plain' => !empty($c['plain']),
            'off' => !empty($c['off']), 'cap' => max(0, (int) ($c['cap'] ?? 0)), 'created' => (int) ($c['created'] ?? 0),
            'plan' => isset(CC_PLANS[(string) ($c['plan'] ?? '')]) ? (string) $c['plan'] : 'inst', 'bill' => (array) ($c['bill'] ?? [])]; }
/* ---------- plans ----------
   Every institution is on a plan. An institution set up by the super admin is on Institution unless they choose otherwise. Someone who signs up on their own starts on Free.
   Limits are per month and 0 means no limit. The super admin can change the prices and limits in Settings, Plans and payments. */
const CC_PLANS = ['free' => ['name' => 'Free', 'price' => 0, 'people' => 1, 'depts' => 1, 'builds' => 10, 'checks' => 2, 'for' => 'Trying it out'],
                  'pro' => ['name' => 'Teacher', 'price' => 499, 'people' => 1, 'depts' => 1, 'builds' => 100, 'checks' => 40, 'for' => 'Individual lecturers'],
                  'dept' => ['name' => 'Department', 'price' => 4900, 'people' => 10, 'depts' => 1, 'builds' => 600, 'checks' => 300, 'for' => 'Small teaching teams'],
                  'inst' => ['name' => 'Institution', 'price' => -1, 'people' => 0, 'depts' => 0, 'builds' => 0, 'checks' => 0, 'for' => 'Colleges and schools']];
function ccBillingOn(): bool { return in_array(strtolower(trim((string) ccConf('BILLING'))), ['1', 'on', 'yes', 'true'], true); }
function ccStripeReady(): bool { return strlen(trim((string) ccConf('STRIPE_SECRET_KEY'))) > 10; }
function ccPlans(?array $a = null): array { $set = (array) ((($a ?? ccAccounts())['site'] ?? [])['plans'] ?? []); $o = [];
    foreach (CC_PLANS as $k => $p) { $s = (array) ($set[$k] ?? []); foreach (['price', 'people', 'depts', 'builds', 'checks'] as $f) if (isset($s[$f]) && !($k === 'inst' && $f === 'price') && !($k === 'free' && $f === 'price')) $p[$f] = max(0, (int) $s[$f]); $o[$k] = $p; }
    return $o; }
function ccPlanOf(array $a, string $college): array { $c = ccCollege($a, $college); return ['id' => $c['plan']] + ccPlans($a)[$c['plan']]; }
/* how much of this month's allowance an institution has used */
function ccPlanUsed(string $college, ?array $u = null): array { $r = (array) ((($u ?? ccUsageRead())['colleges'] ?? [])[$college] ?? []); return ['builds' => (int) ($r['builds'] ?? 0), 'checks' => (int) ($r['checks'] ?? 0)]; }
function ccSaved(string $college = '', ?array $u = null): array { $s = (array) (($u ?? ccUsageRead())['saved'] ?? []); $r = (array) ($college === '' ? ($s['total'] ?? []) : (($s['colleges'] ?? [])[$college] ?? [])); return ['n' => (int) ($r['n'] ?? 0), 'mins' => (int) ($r['mins'] ?? 0)]; }
function ccCollegePeople(array $a, string $college): int { $d = ccCollegeDepts($a, $college); $s = ccSupers(); return count(array_filter($a['users'], fn($u, $e) => ccActive($u) && empty($u['demo']) && !in_array((string) $e, $s, true) && in_array((string) ($u['dept'] ?? ''), $d, true), ARRAY_FILTER_USE_BOTH)); }   // super admins belong to no institution, so they never take a place
function ccCollegeDepts(array $a, string $college): array { return array_values(array_filter(array_keys($a['depts']), fn($d) => $d !== CC_DEMO_DEPT && ccCollegeOf($a, (string) $d) === $college)); }
/* what a page needs to know about the site. With an institution named, that institution's name, logo and colour; without one, the site's only institution, or none if there are several */
function ccSite(?array $a = null, ?string $college = null): array { $a = $a ?? ccAccounts(); $s = (array) ($a['site'] ?? []); $ids = ccCollegeIds($a);
    $one = $college !== null && isset($a['colleges'][$college]) ? $college : (count($ids) === 1 ? (string) $ids[0] : '');
    $c = $one !== '' ? ccCollege($a, $one) : ['name' => '', 'logo' => '', 'color' => '', 'contact' => '', 'plain' => !empty($s['plain'])];
    $plain = $c['plain']; $first = $ids ? ccCollege($a, (string) $ids[0]) : $c; $home = !$first['plain'];      // the original site, with the built-in subjects
    $org = (string) ($s['org'] ?? ''); if ($org === '') $org = count($ids) > 1 ? '' : $c['name'];
    return ['name' => $c['name'], 'logo' => $c['logo'], 'color' => $c['color'], 'plain' => $plain, 'contact' => (string) ($c['contact'] !== '' ? $c['contact'] : (($s['contact'] ?? '') !== '' ? $s['contact'] : ($home ? CC_SUPPORT_EMAIL : ''))),
            'front' => array_key_exists('front', $s) ? !empty($s['front']) : $home, 'publicBrand' => !empty($s['publicBrand']) && count($ids) < 2, 'org' => $org, 'college' => $one, 'colleges' => count($ids)]; }
/* what people who aren't signed in are shown: the institution's name, logo and colour only if the site has chosen to show them before sign-in */
function ccSitePublic(?array $a = null): array { $s = ccSite($a); if (!$s['publicBrand']) { $s['name'] = ''; $s['logo'] = ''; $s['color'] = ''; } return $s; }   // 'org' stays: the terms and privacy pages must still say who runs the site
/* ---------- usage: how much each department and person asks of Crafticulum AI, month by month ---------- */
function ccUsageSet(?array $a = null): array { $u = (array) ((($a ?? ccAccounts())['site'] ?? [])['usage'] ?? []);
    return ['in' => (float) ($u['in'] ?? 0), 'out' => (float) ($u['out'] ?? 0), 'cur' => (string) ($u['cur'] ?? '£'), 'capCost' => (float) ($u['capCost'] ?? 0), 'capCalls' => (int) ($u['capCalls'] ?? 0), 'capDept' => (int) ($u['capDept'] ?? 0)]; }
function ccUsageFile(string $ym): string { return ccDataDir() . '/_usage/' . (preg_match('/^\d{4}-\d{2}$/', $ym) ? $ym : date('Y-m')) . '.json'; }
function ccUsageRead(string $ym = ''): array { $f = ccUsageFile($ym); $j = is_file($f) ? json_decode((string) @file_get_contents($f), true) : null;
    if (!is_array($j)) $j = []; return $j + ['total' => ['calls' => 0, 'in' => 0, 'out' => 0], 'depts' => [], 'users' => [], 'days' => []]; }
function ccUsageCost(array $row, array $set): float { return (($row['in'] ?? 0) * $set['in'] + ($row['out'] ?? 0) * $set['out']) / 1000000; }
function ccUsageAdd(array $me, int $in, int $out, string $model = '', string $kind = '', int $mins = 0): void {
    $dir = ccDataDir() . '/_usage'; if (!is_dir($dir)) @mkdir($dir, 0700, true);
    $fh = @fopen(ccUsageFile(''), 'c+'); if (!$fh) return;
    if (flock($fh, LOCK_EX)) {
        $j = json_decode((string) stream_get_contents($fh), true); if (!is_array($j)) $j = [];
        $note = null; $freeUse = false; try { if (ccBillingOn() && empty($me['demo']) && ($me['role'] ?? '') !== 'superadmin' && ($me['college'] ?? '') !== '') $freeUse = ccPlanOf(ccAccounts(), (string) $me['college'])['id'] === 'free'; } catch (\Throwable $e) {}
        $bump = function (&$r) use ($in, $out) { $r['calls'] = (int) ($r['calls'] ?? 0) + 1; $r['in'] = (int) ($r['in'] ?? 0) + $in; $r['out'] = (int) ($r['out'] ?? 0) + $out; };
        $j['total'] = (array) ($j['total'] ?? []); $bump($j['total']);
        $sup = ($me['role'] ?? '') === 'superadmin';   // a super admin's own use is the site's, not the institution they happen to be looking at
        $d = (string) ($me['dept'] ?? 'main'); if (!$sup) { $j['depts'][$d] = (array) ($j['depts'][$d] ?? []); $bump($j['depts'][$d]); } else { $j['site'] = (array) ($j['site'] ?? []); $bump($j['site']); }
        $e = (string) ($me['email'] ?? ''); $j['users'][$e] = (array) ($j['users'][$e] ?? []); $bump($j['users'][$e]); $j['users'][$e]['name'] = (string) ($me['name'] ?? ''); $j['users'][$e]['dept'] = $d;
        $day = date('d'); $j['days'][$day] = (array) ($j['days'][$day] ?? []); $bump($j['days'][$day]);
        if ($kind !== '') { $j['kinds'][$kind] = (array) ($j['kinds'][$kind] ?? []); $bump($j['kinds'][$kind]); }
        $cid = (string) ($me['college'] ?? ''); if ($cid !== '' && empty($me['demo']) && !$sup) { $ck = $kind === 'marker' ? 'checks' : 'builds'; $j['colleges'][$cid][$ck] = (int) ($j['colleges'][$cid][$ck] ?? 0) + 1; $note = [$cid, $ck, $j['colleges'][$cid][$ck]]; }
        if ($kind === 'tool') {   // what the tools made, and a rough count of the time they saved, for the overviews
            $sv = function (&$r) use ($mins) { $r['n'] = (int) ($r['n'] ?? 0) + ($mins > 0 ? 1 : 0); $r['mins'] = (int) ($r['mins'] ?? 0) + $mins; };
            $j['saved'] = (array) ($j['saved'] ?? []); $j['saved']['total'] = (array) ($j['saved']['total'] ?? []); $sv($j['saved']['total']);
            if (!$sup) { $j['saved']['depts'][$d] = (array) ($j['saved']['depts'][$d] ?? []); $sv($j['saved']['depts'][$d]); if ($cid !== '') { $j['saved']['colleges'][$cid] = (array) ($j['saved']['colleges'][$cid] ?? []); $sv($j['saved']['colleges'][$cid]); } }
        }
        if ($freeUse) { $j['free'] = (array) ($j['free'] ?? []); $bump($j['free']); }
        if ($model !== '' && $kind === '') $j['model'] = $model;
        ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode($j, JSON_UNESCAPED_UNICODE)); fflush($fh); flock($fh, LOCK_UN);
    }
    fclose($fh);
    if (!empty($note)) { try { ccPlanNudge($note[0], $note[1], $note[2]); } catch (\Throwable $e) {} }
}
/* the most that every Free account together may ask of Crafticulum AI in a month. 0 is no limit */
function ccFreeCap(?array $a = null): int { $s = (array) (($a ?? ccAccounts())['site'] ?? []); return array_key_exists('freeCap', $s) ? max(0, (int) $s['freeCap']) : 1000; }
/* the next plan up that would lift a limit, for the offer on screen */
function ccUpsell(array $a, string $college, string $k): array { $cur = ccPlanOf($a, $college); $n = ccCollegePeople($a, $college);
    foreach (ccPlans($a) as $id => $p) { if ($p['price'] <= 0 || $id === $cur['id']) continue; if ($p['people'] > 0 && $n > $p['people']) continue; if ($p[$k] > 0 && $cur[$k] > 0 && $p[$k] <= $cur[$k]) continue; if ($cur['price'] > 0 && $p['price'] <= $cur['price']) continue;
        return ['next' => ['id' => $id, 'name' => $p['name'], 'priceText' => '£' . ($p['price'] % 100 ? number_format($p['price'] / 100, 2) : (string) intdiv($p['price'], 100)), 'builds' => (int) $p['builds'], 'checks' => (int) $p['checks']]]; }
    return ['next' => null]; }
/* an email to whoever manages an institution */
function ccCollegeMail(array $a, string $college, string $subject, string $text): int {
    $d = ccCollegeDepts($a, $college); $n = 0; $ready = ccMailReady();
    foreach ($a['users'] as $e => $u) if (($u['role'] ?? '') === 'owner' && ccActive($u) && empty($u['demo']) && in_array((string) ($u['dept'] ?? ''), $d, true) && filter_var((string) $e, FILTER_VALIDATE_EMAIL)) { $hi = trim(explode(' ', (string) ($u['name'] ?? ''))[0]);
        ccNotify((string) $e, 'plan', $subject, (string) strtok(trim($text), "\n"), ccAppUrl());
        if ($ready && ccMail((string) $e, $subject, ($hi !== '' ? "Hello $hi,\n\n" : "Hello,\n\n") . $text . "\n\nCrafticulum\n" . ccAppUrl())) $n++; }
    return $n;
}
/* nearly at a plan's monthly limit, or at it: tell the manager once, by email */
function ccPlanNudge(string $college, string $k, int $n): void {
    if (!ccBillingOn()) return; $a = ccAccounts(); $pl = ccPlanOf($a, $college); $max = (int) $pl[$k]; if ($max <= 0) return;
    $warn = (int) ceil($max * 0.8); $what = $k === 'checks' ? 'Second Marker checks' : 'builds with Crafticulum AI'; $where = "Settings, Plan and billing:\n" . ccAppUrl();
    if ($n === $max) ccCollegeMail($a, $college, "You've used this month's $what", "All $max $what on the {$pl['name']} plan have been used this month. They start again on the 1st.\n\nIf you need more before then, you can move to a bigger plan in $where");
    elseif ($n === $warn && $max >= 10 && $warn < $max) ccCollegeMail($a, $college, "You've used most of this month's $what", "$n of this month's $max $what on the {$pl['name']} plan have been used. They start again on the 1st.\n\nIf you think you'll need more, you can move to a bigger plan in $where");
}
/* why Crafticulum AI can't be used right now, if a monthly limit has been reached ('' when it can) */
function ccUsageBlocked(array $me, string $kind = ''): string {
    $s = ccUsageSet(); $u = ccUsageRead(); $t = $u['total'];
    $sup = ($me['role'] ?? '') === 'superadmin';
    $GLOBALS['ccBlock'] = [];   // what the screen needs to offer the right way out, beside the sentence itself
    if (ccBillingOn() && empty($me['demo']) && !$sup) { $acc0 = ccAccounts(); $c0 = (string) ($me['college'] ?? ccCollegeOf($acc0, (string) ($me['dept'] ?? ''))); $pl = ccPlanOf($acc0, $c0); $used = ccPlanUsed($c0, $u); $k = $kind === 'marker' ? 'checks' : 'builds';
        $adm = !empty($me['admin']); $self = !empty($acc0['colleges'][$c0]['self']);
        /* someone who signed up on their own confirms their email before Crafticulum AI does anything for them */
        if ($self && ccMailReady() && empty($acc0['users'][(string) ($me['email'] ?? '')]['verified'])) { $GLOBALS['ccBlock'] = ['code' => 'verify', 'email' => (string) ($me['email'] ?? '')]; return 'Confirm your email address first. We sent a link to ' . ($me['email'] ?? 'you') . ' when you signed up. Open it, then come back and try again.'; }
        /* everyone on Free shares one monthly allowance, so free accounts can never cost more than the site has agreed to */
        $fc = ccFreeCap($acc0); if ($pl['id'] === 'free' && $fc > 0 && (int) ($u['free']['calls'] ?? 0) >= $fc) { $GLOBALS['ccBlock'] = ['code' => 'freecap', 'admin' => $adm] + ccUpsell($acc0, $c0, $k);
            return 'Free accounts have used everything set aside for them this month. It starts again on the 1st. ' . ($adm ? 'To carry on now, move to a paid plan in Settings, Plan and billing.' : 'Ask whoever manages your account about a paid plan.'); }
        if ($pl[$k] > 0 && $used[$k] >= $pl[$k]) { $GLOBALS['ccBlock'] = ['code' => 'plan', 'kind' => $k, 'max' => (int) $pl[$k], 'plan' => $pl['name'], 'admin' => $adm] + ccUpsell($acc0, $c0, $k); }
        if ($pl[$k] > 0 && $used[$k] >= $pl[$k]) return ($k === 'checks' ? 'This month\'s ' . $pl[$k] . ' Second Marker checks on the ' . $pl['name'] . ' plan have been used.' : 'This month\'s ' . $pl[$k] . ' builds with Crafticulum AI on the ' . $pl['name'] . ' plan have been used.') . ' They start again on the 1st. ' . (!empty($me['admin']) ? 'Upgrade in Settings, Plan and billing.' : 'Ask whoever manages your account to upgrade the plan.'); }
    if ($s['capCalls'] && ($t['calls'] ?? 0) >= $s['capCalls']) return 'This month\'s limit for building with Crafticulum AI has been reached (' . $s['capCalls'] . ' requests). It starts again on the 1st. The site admin can raise it in Settings, Usage and cost.';
    if ($s['capCost'] && ($s['in'] || $s['out']) && ccUsageCost($t, $s) >= $s['capCost']) return 'This month\'s spending limit for Crafticulum AI has been reached. It starts again on the 1st. The site admin can raise it in Settings, Usage and cost.';
    $acc = ccAccounts(); $cid = (string) ($me['college'] ?? ccCollegeOf($acc, (string) ($me['dept'] ?? ''))); $cap = $cid !== '' ? ccCollege($acc, $cid)['cap'] : 0;
    if ($sup) return '';   // the limits below are an institution's and a department's own
    if ($cap) { $n = 0; foreach (ccCollegeDepts($acc, $cid) as $d) $n += (int) ($u['depts'][$d]['calls'] ?? 0); if ($n >= $cap) return 'Your institution has reached this month\'s limit for building with Crafticulum AI (' . $cap . ' requests). It starts again on the 1st. Ask whoever looks after the site to raise it.'; }
    if ($s['capDept'] && (($u['depts'][(string) ($me['dept'] ?? '')]['calls'] ?? 0) >= $s['capDept'])) return 'Your department has reached this month\'s limit for building with Crafticulum AI (' . $s['capDept'] . ' requests). It starts again on the 1st. The site admin can raise it in Settings, Usage and cost.';
    return '';
}
function ccAccounts(): array {
    $f = ccDataDir() . '/_accounts.json';
    $j = is_file($f) ? json_decode((string) @file_get_contents($f), true) : null;
    if (!is_array($j)) $j = [];
    if (empty($j['depts']) || !is_array($j['depts'])) $j['depts'] = ['main' => ['name' => 'Main department', 'legacy' => true, 'created' => time()]];
    // the first department used to be named after the two subjects it started with
    if (isset($j['depts']['main']['name']) && $j['depts']['main']['name'] === 'Esports & IT') $j['depts']['main']['name'] = 'Main department';
    if (empty($j['users']) || !is_array($j['users'])) $j['users'] = [];
    /* colleges. A site from before colleges existed becomes one institution holding every department, with the name, logo and colour it already had.
       Nothing is written here: it is the same answer every time until something is saved. */
    if (empty($j['colleges']) || !is_array($j['colleges'])) { $s = (array) ($j['site'] ?? []);
        $j['colleges'] = ['main' => ['name' => (string) ($s['name'] ?? ''), 'logo' => (string) ($s['logo'] ?? ''), 'color' => (string) ($s['color'] ?? ''), 'contact' => (string) ($s['contact'] ?? ''), 'plain' => !empty($s['plain']), 'created' => (int) ($j['depts'][array_key_first($j['depts'])]['created'] ?? time())]]; }
    $firstC = (string) array_key_first($j['colleges']);
    foreach ($j['depts'] as $k => $d) if (!isset($j['colleges'][(string) ($d['college'] ?? '')])) $j['depts'][$k]['college'] = $firstC;
    return $j;
}
function ccSaveAccounts(array $a): bool {
    ccEnsureData();
    $f = ccDataDir() . '/_accounts.json'; $tmp = $f . '.' . bin2hex(random_bytes(4)) . '.tmp';
    // the first save after colleges arrived: keep a copy of the accounts file exactly as it was
    $keep = ccDataDir() . '/_accounts.before-colleges.json';
    if (!is_file($keep) && is_file($f) && !empty($a['colleges'])) { $was = json_decode((string) @file_get_contents($f), true); if (is_array($was) && empty($was['colleges'])) @copy($f, $keep); }
    $ok = @file_put_contents($tmp, json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), LOCK_EX);
    if ($ok === false || !@rename($tmp, $f)) { @unlink($tmp); return false; }
    return true;
}
/* the signed-in person, read fresh every request so changes by a head of department apply straight away */
function ccMe(): ?array {
    $uid = (string) ($_SESSION['uid'] ?? '');
    if ($uid === '') return null;
    $a = ccAccounts();
    if ($uid === 'admin@password') {
        if (!ccPasswordAllowed()) return null;
        $u = ['name' => 'Admin (password)', 'role' => 'superadmin'];
    } elseif (in_array($uid, ccSupers(), true)) {
        $u = ($a['users'][$uid] ?? []); $u['role'] = 'superadmin';
    } else {
        $u = $a['users'][$uid] ?? null;
        if (!$u || ($u['role'] ?? '') === 'superadmin' || !ccActive($u)) return null;
    }
    $u['email'] = $uid;
    if ($u['role'] === 'superadmin') {
        // the super admin belongs to no institution. With more than one institution on the site they stand outside all of them until they choose to work inside one
        $u['inside'] = !empty($_SESSION['inside']) || (count(ccCollegeIds($a)) < 2 && empty($a['saOutside']));   /* a site with one institution starts with them inside it; once they step out, they stand outside like on any other site */
        $d = $u['inside'] ? (string) ($_SESSION['dept'] ?? ($u['dept'] ?? '')) : '';
        if (!isset($a['depts'][$d])) $d = (string) array_key_first($a['depts']);
        $u['dept'] = $d;
    } elseif (!isset($a['depts'][$u['dept'] ?? ''])) return null;
    elseif ($u['role'] === 'owner') {   // an institution manager can work in any department of their own institution
        $home = ccCollegeOf($a, (string) $u['dept']); $d = (string) ($_SESSION['dept'] ?? '');
        if ($d !== '' && isset($a['depts'][$d]) && $d !== CC_DEMO_DEPT && ccCollegeOf($a, $d) === $home) $u['dept'] = $d;
    }
    else {   // someone who teaches in more than one department works in whichever they've switched to. Their role there is the one they were given for it
        $d = (string) ($_SESSION['dept'] ?? ''); $al = ccAlso($a, $u); $u['home'] = (string) $u['dept'];
        if ($d !== '' && $d !== $u['dept'] && isset($al[$d])) { $u['dept'] = $d; $u['role'] = $al[$d]; }
    }
    $u['college'] = ccCollegeOf($a, (string) $u['dept']);
    if ($u['role'] !== 'superadmin' && $uid !== CC_DEMO_USER && ccCollege($a, $u['college'])['off']) return null;   // the institution has been switched off
    $p = ccPerms($u);
    $u['canGen'] = $p['gen']; $u['canTeam'] = $p['team']; $u['canSubjects'] = $p['subjects']; $u['canDates'] = $p['dates'];
    $u['canEdit'] = in_array($u['role'], ['hod', 'owner', 'superadmin'], true) || !empty($u['canEdit']);
    $u['deptName'] = (string) ($a['depts'][$u['dept']]['name'] ?? '');
    $u['collegeName'] = ccCollege($a, $u['college'])['name'];
    if ($u['role'] === 'superadmin' && !$u['inside']) { $u['collegeName'] = ''; $u['deptName'] = ''; }
    $u['isLead'] = in_array($u['role'], ['hod', 'owner', 'superadmin'], true);
    $u['admin'] = in_array($u['role'], ['owner', 'superadmin'], true);   // runs an institution: its departments, people and profile
    $u['demo'] = $uid === CC_DEMO_USER;
    if ($u['demo'] && !ccDemoOn()) return null;
    return $u;
}
/* a department's own settings: whether teachers' changes need signing off, and whether deleting a lesson needs the department's password */
function ccDeptPrefs(array $a, string $dept): array { $d = $a['depts'][$dept] ?? []; return ['signoff' => !empty($d['signoff']), 'delPass' => !empty($d['delHash']), 'base' => !array_key_exists('base', $d) || !empty($d['base']), 'tools' => !array_key_exists('tools', $d) || !empty($d['tools']), 'handin' => !empty($d['handin']), 'pics' => !array_key_exists('pics', $d) || !empty($d['pics'])]; }
/* ---------- Second Marker: checking learner work against the criteria. Each department's leader switches it on and sets how it works ---------- */
const CC_MARKER_MODELS = ['claude-opus-5-5', 'claude-sonnet-5-5', 'claude-haiku-5-5'];
function ccMarker(array $a, string $dept): array {
    $m = (array) (($a['depts'][$dept] ?? [])['marker'] ?? []);
    return ['on' => !empty($m['on']), 'who' => ($m['who'] ?? 'leads') === 'all' ? 'all' : 'leads', 'model' => in_array($m['model'] ?? '', CC_MARKER_MODELS, true) ? (string) $m['model'] : '',
            'batch' => !array_key_exists('batch', $m) || !empty($m['batch']), 'iv' => !array_key_exists('iv', $m) || !empty($m['iv']), 'anon' => !empty($m['anon']),
            'pdfText' => !array_key_exists('pdfText', $m) || !empty($m['pdfText']), 'feedback' => !array_key_exists('feedback', $m) || !empty($m['feedback']),
            'emailDomain' => (string) ($m['emailDomain'] ?? ''), 'guide' => (string) ($m['guide'] ?? ''), 'ivTarget' => max(0, min(100, (int) ($m['ivTarget'] ?? 0)))];
}
function ccMarkerCan(array $me, array $a): bool { $m = ccMarker($a, (string) $me['dept']); return $m['on'] && ccRoom($a, $me, 'marker') && (!empty($me['isLead']) || $m['who'] === 'all'); }
/* ---------- the error log: things that went wrong, for the super admin to read in Settings. No learner work and no passwords go in it ---------- */
function ccErr(string $kind, string $msg, array $extra = []): void {
    try {
        $dir = ccDataDir(); if (!is_dir($dir)) return; $f = $dir . '/_errors.jsonl';
        $clean = fn($s, int $n) => mb_substr(trim((string) preg_replace('/[\x00-\x1F\x7F]+/u', ' ', (string) $s)), 0, $n);
        $msg = preg_replace('/sk-ant-[A-Za-z0-9_-]+/', 'sk-ant-…', $clean($msg, 500));
        $row = ['at' => time(), 'kind' => $clean($kind, 20), 'msg' => $msg];
        foreach (['where', 'page', 'role', 'dept', 'browser'] as $k) if (isset($extra[$k]) && $extra[$k] !== '') $row[$k] = $clean($extra[$k], 160);
        if (is_file($f) && filesize($f) > 400000) { $l = @file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: []; @file_put_contents($f, implode("\n", array_slice($l, -500)) . "\n", LOCK_EX); }
        @file_put_contents($f, json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n", FILE_APPEND | LOCK_EX);
    } catch (Throwable $e) {}
}
function ccErrRead(int $n = 200): array { $f = ccDataDir() . '/_errors.jsonl'; if (!is_file($f)) return [];
    $o = []; foreach (array_slice(@file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [], -$n) as $l) { $j = json_decode($l, true); if (is_array($j)) $o[] = $j; } return array_reverse($o); }
/* anything PHP itself trips over on any page is written down too */
set_exception_handler(function (Throwable $e) { ccErr('server', get_class($e) . ': ' . $e->getMessage(), ['where' => basename($e->getFile()) . ':' . $e->getLine()]); if (!headers_sent()) { http_response_code(500); header('Content-Type: application/json; charset=utf-8'); } echo json_encode(['error' => 'server']); });
register_shutdown_function(function () { $e = error_get_last(); if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) ccErr('server', (string) $e['message'], ['where' => basename((string) $e['file']) . ':' . $e['line']]); });

/* ---------- messages from the contact page: kept for the super admin to read in Settings, and emailed on ---------- */
function ccInboxFile(): string { return ccDataDir() . '/_inbox.json'; }
function ccInbox(): array { $j = json_decode((string) @file_get_contents(ccInboxFile()), true); return is_array($j) ? $j : []; }
function ccInboxChange(callable $fn): bool {
    ccEnsureData(); $fh = @fopen(ccInboxFile(), 'c+'); if (!$fh) return false; $ok = false;
    if (flock($fh, LOCK_EX)) { $j = json_decode((string) stream_get_contents($fh), true); if (!is_array($j)) $j = []; $j = $fn($j);
        if (count($j) > 500) { uasort($j, fn($x, $y) => ($y['at'] ?? 0) <=> ($x['at'] ?? 0)); $j = array_slice($j, 0, 500, true); }
        ftruncate($fh, 0); rewind($fh); $ok = fwrite($fh, json_encode($j, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) !== false; fflush($fh); flock($fh, LOCK_UN); }
    fclose($fh); return $ok;
}
function ccInboxOpen(): int { return count(array_filter(ccInbox(), fn($m) => empty($m['done']))); }
/* no more than a handful from one place in an hour, and a ceiling for the whole site in a day */
function ccContactAllowed(): bool {
    $f = ccDataDir() . '/_inbox-rate.json'; $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j)) $j = [];
    $now = time(); $ip = substr(sha1((string) ($_SERVER['REMOTE_ADDR'] ?? '') . '|' . ccSiteId()), 0, 16);
    $j = array_filter(array_map(fn($l) => array_values(array_filter((array) $l, fn($t) => $t > $now - 86400)), $j));
    $mine = array_filter((array) ($j[$ip] ?? []), fn($t) => $t > $now - 3600); $all = array_sum(array_map('count', $j));
    if (count($mine) >= 5 || $all >= 200) return false;
    $j[$ip][] = $now; @file_put_contents($f, json_encode($j), LOCK_EX); return true;
}

/* ---------- the demo: a sample department anyone can try from the front page, emptied every night ---------- */
const CC_DEMO_USER = 'demo@crafticulum.invalid';
const CC_DEMO_DEPT = 'demo';
function ccDemoOn(): bool { return in_array(strtolower(trim((string) ccConf('DEMO'))), ['1', 'on', 'yes', 'true'], true); }
function ccRmTree(string $d): void { if (!is_dir($d)) return; foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $f) { $f->isDir() ? @rmdir($f->getPathname()) : @unlink($f->getPathname()); } @rmdir($d); }
function ccDemoState(): array { $j = json_decode((string) @file_get_contents(ccDataDir() . '/_demo.json'), true); return is_array($j) ? $j : []; }
/* make sure the demo department and its one shared account exist, and empty them if this is the first visit today */
function ccDemoEnter(): bool {
    if (!ccDemoOn()) return false; ccEnsureData(); $a = ccAccounts(); $ch = false;
    if (!isset($a['depts'][CC_DEMO_DEPT])) { $a['depts'][CC_DEMO_DEPT] = ['name' => 'Demo department', 'demo' => true]; $ch = true; }
    $u = $a['users'][CC_DEMO_USER] ?? null;
    if (!$u || ($u['role'] ?? '') !== 'hod' || ($u['dept'] ?? '') !== CC_DEMO_DEPT || !ccActive($u)) { $a['users'][CC_DEMO_USER] = ['name' => 'Demo visitor', 'role' => 'hod', 'dept' => CC_DEMO_DEPT, 'status' => 'active', 'added' => time(), 'methods' => [], 'demo' => true, 'perms' => ['gen' => true, 'team' => false, 'subjects' => false, 'dates' => true]]; $ch = true; }
    if ($ch && !ccSaveAccounts($a)) return false;
    $st = ccDemoState(); $day = date('Y-m-d');
    if (($st['day'] ?? '') !== $day) {
        $b = ccDataDir(); ccRmTree($b . '/d/' . CC_DEMO_DEPT); ccRmTree($b . '/u/' . sha1(CC_DEMO_USER));
        foreach (glob($b . '/_marker/' . CC_DEMO_DEPT . '*.json') ?: [] as $f) @unlink($f); @unlink($b . '/_log/' . CC_DEMO_DEPT . '.jsonl');
        @file_put_contents($b . '/_demo.json', json_encode(['day' => $day, 'builds' => 0, 'visits' => 0]), LOCK_EX);
    }
    $st = ccDemoState(); $st['visits'] = (int) ($st['visits'] ?? 0) + 1; @file_put_contents(ccDataDir() . '/_demo.json', json_encode($st), LOCK_EX);
    return true;
}
/* how many lessons the demo may still build today. 0 means building is off in the demo */
function ccDemoBuildsLeft(): int { $st = ccDemoState(); $used = ($st['day'] ?? '') === date('Y-m-d') ? (int) ($st['builds'] ?? 0) : 0; return max(0, (int) ccConf('DEMO_BUILDS') - $used); }
function ccDemoBuilt(): void { $st = ccDemoState(); if (($st['day'] ?? '') !== date('Y-m-d')) $st = ['day' => date('Y-m-d'), 'builds' => 0, 'visits' => 0]; $st['builds'] = (int) ($st['builds'] ?? 0) + 1; @file_put_contents(ccDataDir() . '/_demo.json', json_encode($st), LOCK_EX); }

/* the activity log: one line for each thing that changes what the department shares */
function ccLog(string $dept, array $who, string $act, string $what = ''): void {
    $dir = ccDataDir() . '/_log'; if (!is_dir($dir)) @mkdir($dir, 0700, true);
    $f = $dir . '/' . (preg_replace('/[^a-z0-9-]/', '', $dept) ?: 'main') . '.jsonl';
    if (is_file($f) && filesize($f) > 600000) { $l = file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: []; @file_put_contents($f, implode("\n", array_slice($l, -1500)) . "\n", LOCK_EX); }
    @file_put_contents($f, json_encode(['at' => time(), 'by' => (string) (($who['name'] ?? '') ?: ($who['email'] ?? '')), 'email' => (string) ($who['email'] ?? ''), 'act' => $act, 'what' => mb_substr($what, 0, 160)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n", FILE_APPEND | LOCK_EX);
}
function ccLogRead(string $dept, int $n = 300): array {
    $f = ccDataDir() . '/_log/' . (preg_replace('/[^a-z0-9-]/', '', $dept) ?: 'main') . '.jsonl'; if (!is_file($f)) return [];
    $out = []; foreach (array_reverse(array_slice(file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [], -$n)) as $l) { $j = json_decode($l, true); if (is_array($j)) $out[] = $j; }
    return $out;
}
/* What a person may do. The super admin can do everything. A head of department or subject leader has the
   permissions the super admin gave them (all of them unless some were switched off). Teachers have none of these. */
const CC_PERMS = ['gen', 'team', 'subjects', 'dates'];
function ccPerms(array $u): array {
    $r = (string) ($u['role'] ?? 'teacher'); $o = [];
    foreach (CC_PERMS as $k) $o[$k] = $r === 'superadmin' || $r === 'owner' || ($r === 'hod' && (!isset($u['perms']) || !is_array($u['perms']) || !empty($u['perms'][$k])));
    return $o;
}
/* with a client secret, Google sign-in goes to Google and comes back (the same round trip as Microsoft). Without one it uses Google's pop-up button. */
function ccGoogleSecret(): string { return ccGoogleId() !== '' ? trim((string) ccConf('GOOGLE_CLIENT_SECRET')) : ''; }
/* a photo someone uploaded beats the one from their Google account */
/* addresses for profile pages: /<institution>/team/<person>. Made from the names, so they read well, and kept unique */
function ccSlugify(string $s): string { $s = strtolower((string) @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s)); return trim((string) preg_replace('/[^a-z0-9]+/', '-', $s), '-'); }
function ccCollegeSlugs(array $a): array { $out = []; $used = []; $ids = ccCollegeIds($a); sort($ids);
    foreach ($ids as $id) { $name = (string) (ccCollege($a, (string) $id)['name'] ?? ''); $full = ccSlugify($name); $short = trim((string) preg_replace('/(^|-)(the|college|school|academy|university|sixth-form|institute|of)(?=-|$)/', '', $full), '-'); $short = (string) preg_replace('/-+/', '-', $short);
        $sl = $short !== '' ? $short : ($full !== '' ? $full : 'team'); if (in_array($sl, ['team', 'assets', 'quiz', 'marker', 'data', 's', 'r'], true)) $sl = $full !== '' && $full !== $sl ? $full : 'inst-' . $sl; $b = $sl; $n = 2; while (isset($used[$sl])) $sl = $b . '-' . $n++; $used[$sl] = 1; $out[(string) $id] = $sl; }
    return $out; }
function ccPersonSlugs(array $a, string $college): array { $out = []; $used = []; $sup = ccSupers(); $es = array_keys($a['users']); sort($es);
    foreach ($es as $e) { $u = $a['users'][$e]; if (!is_array($u) || !ccActive($u) || !empty($u['demo']) || in_array((string) $e, $sup, true)) continue; $d = (string) ($u['dept'] ?? ''); if (!isset($a['depts'][$d]) || ccCollegeOf($a, $d) !== $college) continue;
        $sl = str_replace('-', '', ccSlugify((string) (($u['name'] ?? '') ?: explode('@', (string) $e)[0]))); if ($sl === '') $sl = 'staff'; $b = $sl; $n = 2; while (isset($used[$sl])) $sl = $b . $n++; $used[$sl] = 1; $out[(string) $e] = $sl; }
    return $out; }
function ccPhotoFile(string $email): string { return ccDataDir() . '/u/' . sha1(strtolower($email)) . '/photo.jpg'; }
function ccPhotoUrl(string $email, string $fallback = ''): string { $f = ccPhotoFile($email); return is_file($f) ? 'photo.php?u=' . sha1(strtolower($email)) . '&v=' . filemtime($f) : $fallback; }
/* the other departments someone belongs to, beside their own: id => role there. Only ones in the same institution that still exist count */
function ccAlso(array $a, array $u): array { $o = []; $home = (string) ($u['home'] ?? $u['dept'] ?? ''); $col = ccCollegeOf($a, $home); foreach ((array) ($u['also'] ?? []) as $d => $r) { $d = (string) $d; if ($d === $home || $d === CC_DEMO_DEPT || !isset($a['depts'][$d]) || ccCollegeOf($a, $d) !== $col) continue; $o[$d] = $r === 'hod' ? 'hod' : 'teacher'; } return $o; }
function ccPublic(array $u): array {
    return ['email' => $u['email'], 'name' => (string) ($u['name'] ?? ''), 'picture' => ccPhotoUrl((string) $u['email'], (string) ($u['picture'] ?? '')), 'role' => $u['role'], 'dept' => $u['dept'],
            'deptName' => $u['deptName'], 'canGen' => $u['canGen'], 'canEdit' => $u['canEdit'], 'canTeam' => $u['canTeam'], 'canSubjects' => $u['canSubjects'], 'canDates' => $u['canDates'], 'isLead' => $u['isLead'], 'demo' => !empty($u['demo']),
            'college' => (string) ($u['college'] ?? ''), 'collegeName' => (string) ($u['collegeName'] ?? ''), 'saTags' => array_map(fn($e) => sprintf('%08x', crc32((string) $e)), ccSupers()), 'admin' => !empty($u['admin']), 'rooms' => ccRooms(ccAccounts(), $u), 'anon' => ccAnon(ccAccounts(), (string) ($u['college'] ?? '')), 'house' => empty($u['demo']) && ccHouseOn(ccAccounts(), (string) ($u['college'] ?? '')), 'inside' => !array_key_exists('inside', $u) || !empty($u['inside']), 'home' => (string) ($u['home'] ?? $u['dept']), 'multi' => !in_array($u['role'], ['superadmin', 'owner'], true) && !empty($u['also']) && count(ccAlso(ccAccounts(), $u)) > 0];
}

/* ---------- rooms: the parts of the app that can be switched on and off.
   The super admin can close a room for the whole site or for one institution. An institution's manager chooses which rooms its heads of department
   and its teachers get. Every plan has every room; plans differ in how much can be built, not in what is there. ---------- */
const CC_ROOMS = ['tools' => ['AI Tools', 'The toolkit: worksheets, quizzes, reports, emails and the rest.'], 'assistant' => ['Crafti', 'The assistant you can chat to.'], 'library' => ['Resource library', 'Lessons and saved resources in one place.'],
    'learners' => ['Learners', 'Class lists and each learner against each criterion.'], 'handin' => ['Hand-in links', 'Students hand in work through a link. Each department still has to switch this on itself.'], 'marker' => ['Second Marker', 'Checking learner work against the criteria. Each department still switches this on itself.']];
function ccRoomsCollege(array $a, string $college): array {   // which rooms are open in this institution at all, before roles come into it
    $site = (array) (($a['rooms'] ?? [])['off'] ?? []); $col = (array) (($a['colleges'][$college] ?? [])['roomsOff'] ?? []); $o = [];
    foreach (CC_ROOMS as $k => $_) $o[$k] = !in_array($k, $site, true) && !in_array($k, $col, true); return $o; }
function ccRooms(array $a, array $me): array {
    $role = (string) ($me['role'] ?? 'teacher'); if ($role === 'superadmin') return array_fill_keys(array_keys(CC_ROOMS), true);   // the super admin can always walk into every room
    $cid = (string) ($me['college'] ?? ''); $open = ccRoomsCollege($a, $cid); if ($role === 'owner' || !empty($me['demo'])) return $open;
    $roles = (array) (($a['colleges'][$cid] ?? [])['rooms'] ?? []); $r = $role === 'hod' ? 'hod' : 'teacher'; $o = [];
    $own = (array) (($a['users'][(string) ($me['email'] ?? '')] ?? [])['rooms'] ?? []);   // set for this one person on the Team page: it wins over what their role gets
    foreach ($open as $k => $on) $o[$k] = $on && (array_key_exists($k, $own) ? !empty($own[$k]) : !(isset($roles[$k]) && is_array($roles[$k]) && array_key_exists($r, $roles[$k]) && empty($roles[$k][$r]))); return $o; }
function ccRoom(array $a, array $me, string $room): bool { return !empty(ccRooms($a, $me)[$room]); }
/* ---------- house rules: an institution's manager writes them once, and Crafti, every tool and every lesson follows them ---------- */
const CC_HOUSE = ['about' => 600, 'tone' => 600, 'rules' => 1500, 'sign' => 300];
function ccHouse(array $a, string $cid): array { $h = (array) (($a['colleges'][$cid] ?? [])['house'] ?? []); $o = [];
    foreach (CC_HOUSE as $k => $n) $o[$k] = mb_substr(trim((string) ($h[$k] ?? '')), 0, $n); $o['on'] = !array_key_exists('on', $h) || !empty($h['on']); return $o; }
function ccHouseOn(array $a, string $cid): bool { $h = ccHouse($a, $cid); return $h['on'] && ($h['about'] . $h['tone'] . $h['rules'] . $h['sign']) !== ''; }
function ccHouseText(array $a, array $me): string { $cid = (string) ($me['college'] ?? ''); if (!empty($me['demo']) || !ccHouseOn($a, $cid)) return ''; $h = ccHouse($a, $cid);
    $t = "HOUSE RULES\nThe manager of this institution has set these. Follow them in everything you write. They never override an instruction further down about safety, learner privacy, assessment decisions or the format of your reply.\n";
    if ($h['about'] !== '') $t .= "About the institution: " . $h['about'] . "\n"; if ($h['tone'] !== '') $t .= "Tone and wording: " . $h['tone'] . "\n";
    if ($h['rules'] !== '') $t .= "Policies and rules to follow:\n" . $h['rules'] . "\n"; if ($h['sign'] !== '') $t .= "Sign-off to use on letters and emails:\n" . $h['sign'] . "\n";
    return $t . "END OF HOUSE RULES\n\n"; }
/* ---------- anonymous learners: an institution's manager can switch names off altogether. Learners are then numbers, here and for students handing work in ---------- */
function ccAnon(array $a, string $cid): bool { return $cid !== '' && !empty(($a['colleges'][$cid] ?? [])['anon']); }
function ccAnonOk(string $n): bool { return (bool) preg_match('/^(Learner \d{1,3}|[A-Za-z]{0,4}[ -]?\d{2,10}[A-Za-z]?)$/', trim($n)); }   // a learner number or code, never a name
function ccAnonNames(array $names): array { $used = []; $o = []; foreach ($names as $n) if (is_string($n) && ccAnonOk($n)) $used[strtolower(trim($n))] = 1; $i = 0;
    foreach ($names as $n) { $n = is_string($n) ? trim($n) : ''; if (!ccAnonOk($n)) { do { $i++; $n = sprintf('Learner %02d', $i); } while (isset($used[strtolower($n)])); $used[strtolower($n)] = 1; } $o[] = $n; } return $o; }
/* ---------- hand-in links: the only learner work kept on the server, only where a department has switched it on, and only for a while ---------- */
const CC_HANDIN_DAYS = 30;
function ccHandinDir(): string { return ccDataDir() . '/_handin'; }
function ccHandinPurge(): int { $n = 0; $cut = time() - CC_HANDIN_DAYS * 86400;
    foreach ((array) glob(ccHandinDir() . '/*/*.json') as $f) { $f = (string) $f; $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j)) continue; $dir = substr($f, 0, -5); $keep = []; $ch = false;
        foreach ((array) ($j['files'] ?? []) as $x) { if ((int) ($x['at'] ?? 0) < $cut) { @unlink($dir . '/' . preg_replace('/[^a-f0-9]/', '', (string) ($x['id'] ?? '')) . '.bin'); $n++; $ch = true; } else $keep[] = $x; }
        if (!$keep && (int) ($j['created'] ?? 0) < $cut - 30 * 86400) { foreach ((array) glob($dir . '/*.bin') as $p) @unlink((string) $p); @rmdir($dir); @unlink($f); @unlink($f . '.lock'); continue; }   // an old, empty link goes too
        if ($ch) { $j['files'] = $keep; @file_put_contents($f, json_encode($j, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX); } }
    return $n; }
/* ---------- deleted institutions: kept out of sight for a while, then removed ---------- */
const CC_DELETED_DAYS = 14;
function ccDeletedRm(string $p): void { $base = ccDataDir() . '/_deleted/'; if (strpos($p, $base) !== 0 || strlen($p) <= strlen($base) || is_link($p)) return;
    if (is_dir($p)) { foreach (array_diff((array) @scandir($p), ['.', '..']) as $x) ccDeletedRm($p . '/' . $x); @rmdir($p); } elseif (is_file($p)) @unlink($p); }
function ccDeletedList(): array { $o = []; foreach ((array) glob(ccDataDir() . '/_deleted/*/meta.json') as $f) { $m = json_decode((string) @file_get_contents((string) $f), true); if (!is_array($m)) continue; $at = (int) ($m['at'] ?? 0);
        $o[] = ['hold' => basename(dirname((string) $f)), 'name' => (string) ($m['name'] ?? ''), 'at' => $at, 'by' => (string) ($m['by'] ?? ''), 'people' => count((array) ($m['users'] ?? [])), 'depts' => count((array) ($m['depts'] ?? [])), 'daysLeft' => max(0, (int) ceil(($at + CC_DELETED_DAYS * 86400 - time()) / 86400))]; }
    usort($o, fn($x, $y) => $y['at'] <=> $x['at']); return $o; }
function ccDeletedPurge(): int { $n = 0; foreach ((array) glob(ccDataDir() . '/_deleted/*', GLOB_ONLYDIR) as $d) { $d = (string) $d; $m = json_decode((string) @file_get_contents($d . '/meta.json'), true); $at = is_array($m) ? (int) ($m['at'] ?? 0) : (int) @filemtime($d);
        if ($at > 0 && time() - $at >= CC_DELETED_DAYS * 86400) { ccDeletedRm($d); $n++; } } return $n; }
/* ---------- accounts that are waiting for approval ---------- */
function ccActive(array $u): bool { return ($u['status'] ?? 'active') === 'active'; }

/* ---------- small JSON stores: one-time links and failed sign-in counts ---------- */
function ccJson(string $name): array { $f = ccDataDir() . '/' . $name; $j = is_file($f) ? json_decode((string) @file_get_contents($f), true) : null; return is_array($j) ? $j : []; }
function ccSaveJson(string $name, array $data): void { ccEnsureData(); $f = ccDataDir() . '/' . $name; $tmp = $f . '.' . bin2hex(random_bytes(4)) . '.tmp'; if (@file_put_contents($tmp, json_encode($data, JSON_UNESCAPED_SLASHES), LOCK_EX) !== false) @rename($tmp, $f); }
/* one-time links for setting or resetting a password and confirming an email; only a hash is kept */
function ccToken(string $email, string $type, float $hours): string {
    $t = bin2hex(random_bytes(24)); $all = ccJson('_tokens.json'); $now = time();
    foreach ($all as $k => $v) if (($v['exp'] ?? 0) < $now || (($v['email'] ?? '') === $email && ($v['type'] ?? '') === $type)) unset($all[$k]);
    $all[hash('sha256', $t)] = ['email' => $email, 'type' => $type, 'exp' => $now + (int) round($hours * 3600)];
    ccSaveJson('_tokens.json', $all); return $t;
}
function ccUseToken(string $t, array $types, bool $consume = true): ?array {
    if (!preg_match('/^[a-f0-9]{48}$/', $t)) return null;
    $all = ccJson('_tokens.json'); $k = hash('sha256', $t); $v = $all[$k] ?? null;
    if (!$v || ($v['exp'] ?? 0) < time() || !in_array($v['type'] ?? '', $types, true)) return null;
    if ($consume) { unset($all[$k]); ccSaveJson('_tokens.json', $all); }
    return $v;
}
/* slow down guessing: 8 wrong tries for an email or from one address locks them for 15 minutes */
function ccThrottleKey(string $email): array { return ['e:' . $email, 'i:' . ($_SERVER['REMOTE_ADDR'] ?? '')]; }
function ccLocked(string $email): bool { $all = ccJson('_throttle.json'); $now = time(); foreach (ccThrottleKey($email) as $k) { $v = $all[$k] ?? null; if ($v && $v['n'] >= 8 && $now - $v['t'] < 900) return true; } return false; }
function ccFail(string $email): void { $all = ccJson('_throttle.json'); $now = time(); foreach ($all as $k => $v) if ($now - ($v['t'] ?? 0) > 900) unset($all[$k]); foreach (ccThrottleKey($email) as $k) { $v = $all[$k] ?? ['n' => 0]; $all[$k] = ['n' => $v['n'] + 1, 't' => $now]; } ccSaveJson('_throttle.json', $all); usleep(600000); }
function ccPassed(string $email): void { $all = ccJson('_throttle.json'); unset($all['e:' . $email]); ccSaveJson('_throttle.json', $all); }

/* ---------- two-step codes (authenticator apps, RFC 6238) ---------- */
function ccB32(string $bin): string { $a = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; $bits = ''; foreach (str_split($bin) as $c) $bits .= str_pad(decbin(ord($c)), 8, '0', STR_PAD_LEFT); $out = ''; foreach (str_split($bits, 5) as $ch) $out .= $a[bindec(str_pad($ch, 5, '0'))]; return $out; }
function ccB32d(string $s): string { $a = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; $bits = ''; foreach (str_split(strtoupper(preg_replace('/[^A-Za-z2-7]/', '', $s))) as $c) $bits .= str_pad(decbin(strpos($a, $c)), 5, '0', STR_PAD_LEFT); $out = ''; foreach (str_split($bits, 8) as $b) if (strlen($b) === 8) $out .= chr(bindec($b)); return $out; }
function ccTotpAt(string $secret, int $step): string { $h = hash_hmac('sha1', pack('N*', 0, $step), ccB32d($secret), true); $o = ord($h[19]) & 0xf; $n = ((ord($h[$o]) & 0x7f) << 24 | ord($h[$o + 1]) << 16 | ord($h[$o + 2]) << 8 | ord($h[$o + 3])) % 1000000; return str_pad((string) $n, 6, '0', STR_PAD_LEFT); }
function ccTotpOk(string $secret, string $code): bool { $code = preg_replace('/\D/', '', $code); if (strlen($code) !== 6) return false; $s = intdiv(time(), 30); for ($i = -1; $i <= 1; $i++) if (hash_equals(ccTotpAt($secret, $s + $i), $code)) return true; return false; }

/* ---------- email: SMTP if it's set up in config.php, otherwise the host's mail(), otherwise nothing ---------- */
/* The site's addresses. The main address is where it lives for good. The temporary address is where it also answers for now.
   Links in emails and links given to students use the main address as soon as this same site answers there, and the address
   being used until then. Signing in always comes back to the address the person is on. */
const CC_MAIN_URL = 'https://crafticulum.co.uk/';
const CC_TEMP_URL = 'https://hopeful-lime-horse.185-199-220-70.cpanel.site/';
function ccUrlOk($u): string { $u = trim((string) $u); return preg_match('#^https?://[A-Za-z0-9.-]+(:\d+)?(/[^\s?\#]*)?$#', $u) ? rtrim($u, '/') . '/' : ''; }
function ccMainUrl(): string { return ccUrlOk(ccConf('SITE_URL')) ?: CC_MAIN_URL; }
function ccTempUrl(): string { return ccUrlOk(ccConf('SITE_URL_TEMP')) ?: CC_TEMP_URL; }
function ccHereUrl(): string {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    return ($https ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/') . '/';
}
function ccSiteId(): string { $f = ccDataDir() . '/_siteid'; $id = is_file($f) ? trim((string) @file_get_contents($f)) : ''; if ($id === '') { ccEnsureData(); $id = bin2hex(random_bytes(16)); @file_put_contents($f, $id, LOCK_EX); } return $id; }
/* is this same site answering at the main address? checked every 10 minutes, and every 5 minutes while it isn't */
function ccMainLive(bool $fresh = false): bool {
    $main = ccMainUrl(); $here = ccHereUrl();
    if (parse_url($main, PHP_URL_HOST) === parse_url($here, PHP_URL_HOST) && parse_url($main, PHP_URL_PORT) === parse_url($here, PHP_URL_PORT)) return true;
    $f = ccDataDir() . '/_mainlive.json'; $c = json_decode((string) @file_get_contents($f), true);
    if (!$fresh && is_array($c) && ($c['url'] ?? '') === $main && (int) ($c['at'] ?? 0) > time() - (!empty($c['ok']) ? 600 : 300)) return !empty($c['ok']);
    $ok = false;
    if (function_exists('curl_init')) { $ch = curl_init($main . 'auth.php?a=ping'); curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 3, CURLOPT_CONNECTTIMEOUT => 2, CURLOPT_FOLLOWLOCATION => false]);
        $r = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch); $j = $code === 200 ? json_decode((string) $r, true) : null;
        $ok = is_array($j) && hash_equals(hash('sha256', ccSiteId()), (string) ($j['site'] ?? '')); }
    ccEnsureData(); @file_put_contents($f, json_encode(['url' => $main, 'ok' => $ok, 'at' => time()]), LOCK_EX);
    return $ok;
}
function ccSiteUrl(): string { return ccMainLive() ? ccMainUrl() : ccHereUrl(); }
/* the app has its own address: /app where the server can do clean addresses, app.html everywhere else. The site's own address is the front page */
function ccPretty(): bool { return is_file(ccDataDir() . '/_pretty'); }
function ccAppUrl(bool $here = false): string { return ($here ? ccHereUrl() : ccSiteUrl()) . (ccPretty() ? 'app' : 'app.html'); }
function ccMailReady(): bool { return ((ccConf('SMTP_HOST') !== '') && ccConf('SMTP_HOST')) || ((ccConf('MAIL_FROM') !== '') && ccConf('MAIL_FROM') && function_exists('mail')); }
/* ---------- every email in the site's own look: the logo, a clear button for each link, and a footer. The plain words are sent too, for mail apps that don't show pictures ---------- */
function ccMailButton(string $url): string {
    if (strpos($url, '#verify=') !== false) return 'Confirm my email';
    if (strpos($url, '#magic=') !== false) return 'Sign in to Crafticulum';
    if (strpos($url, '#reset=') !== false) return 'Choose a new password';
    if (strpos($url, '#invite=') !== false) return 'Choose my password';
    if (preg_match('~/(privacy|terms|cookies)(\.html)?$~', $url, $m)) return 'Read the ' . $m[1] . ' page';
    if (preg_match('~/s(tudent\.html)?[/?#]~', $url)) return 'Open the page';
    return 'Open Crafticulum';
}
function ccMailHtml(string $subject, string $text): string {
    $e = fn(string $x): string => htmlspecialchars($x, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $site = ccSiteUrl(); $P = '#4f46e5'; $ink = '#0f172a'; $soft = '#475569';
    $font = "font-family:'Plus Jakarta Sans','Segoe UI',Roboto,Helvetica,Arial,sans-serif";
    $link = fn(string $x): string => preg_replace_callback('~https?://[^\s<>"]+[^\s<>".,;:!?)]~', fn($m) => '<a href="' . $m[0] . '" style="color:' . $P . ';font-weight:600">' . $m[0] . '</a>', $x) ?? $x;
    $blocks = preg_split('/\n{2,}/', trim(str_replace(["\r\n", "\r"], "\n", $text))) ?: []; $out = ''; $pre = ''; $first = true;
    foreach ($blocks as $b) {
        $lines = array_values(array_filter(array_map('rtrim', explode("\n", $b)), fn($l) => trim($l) !== '')); if (!$lines) continue;
        if ($first && preg_match('/^(Hello|Hi)\b.{0,70},$/u', $lines[0]) && count($lines) === 1) { $out .= '<p style="margin:0 0 14px;font-size:17px;font-weight:700;color:' . $ink . '">' . $e($lines[0]) . '</p>'; $first = false; continue; }
        $first = false;
        if ($pre === '' && !preg_match('~^https?://~', $lines[0])) $pre = $lines[0];
        $i = 0; $n = count($lines);
        while ($i < $n) {
            $l = $lines[$i];
            if (preg_match('~^https?://\S+$~', trim($l))) { $u = trim($l); $tok = strpos($u, '#') !== false && preg_match('/=[a-f0-9]{20,}$/', $u);
                $out .= '<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:6px 0 18px"><tr><td bgcolor="' . $P . '" style="border-radius:10px;background:' . $P . '"><a href="' . $e($u) . '" style="display:inline-block;padding:13px 26px;' . $font . ';font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:10px">' . $e(ccMailButton($u)) . '</a></td></tr></table>'
                      . ($tok ? '<p style="margin:-8px 0 18px;font-size:12px;line-height:1.5;color:#64748b">If the button doesn\'t work, copy this link into your browser:<br><span style="word-break:break-all;color:' . $soft . '">' . $e($u) . '</span></p>' : '');
                $i++; continue; }
            if (preg_match('/^(\d+)\.\s+(.+)$/u', $l)) { $out .= '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:4px 0 18px">';
                while ($i < $n && preg_match('/^(\d+)\.\s+(.+)$/u', $lines[$i], $m)) { [$h, $rest] = array_pad(explode(': ', $m[2], 2), 2, '');
                    $out .= '<tr><td width="44" valign="top" style="padding:8px 0"><div style="width:30px;height:30px;line-height:30px;border-radius:15px;background:#eef2ff;color:' . $P . ';font-weight:800;font-size:14px;text-align:center;' . $font . '">' . $m[1] . '</div></td><td valign="top" style="padding:8px 0;' . $font . ';font-size:15px;line-height:1.55;color:' . $soft . '">' . ($rest !== '' ? '<b style="color:' . $ink . '">' . $e($h) . '</b><br>' . $link($e($rest)) : $link($e($m[2]))) . '</td></tr>'; $i++; }
                $out .= '</table>'; continue; }
            if (preg_match('/^[-•]\s+/u', $l)) { $out .= '<ul style="margin:0 0 16px;padding-left:20px;color:' . $soft . '">';
                while ($i < $n && preg_match('/^[-•]\s+(.+)$/u', $lines[$i], $m)) { $out .= '<li style="margin:0 0 6px;font-size:15px;line-height:1.55">' . $link($e($m[1])) . '</li>'; $i++; }
                $out .= '</ul>'; continue; }
            $para = [];
            while ($i < $n && !preg_match('~^https?://\S+$~', trim($lines[$i])) && !preg_match('/^(\d+\.|[-•])\s+/u', $lines[$i])) { $para[] = $link($e($lines[$i])); $i++; }
            if ($para) $out .= '<p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:' . $soft . '">' . implode('<br>', $para) . '</p>';
        }
    }
    $host = (string) parse_url($site, PHP_URL_HOST); $ext = ccPretty() ? '' : '.html'; $s = ccSite(); $contact = (string) ($s['siteContact'] ?? ($s['contact'] ?? ''));
    $fl = fn(string $href, string $t): string => '<a href="' . $e($href) . '" style="color:#64748b;text-decoration:underline">' . $t . '</a>';
    return '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="color-scheme" content="light only"><title>' . $e($subject) . '</title></head>'
        . '<body style="margin:0;padding:0;background:#f5f6fa;-webkit-text-size-adjust:100%"><div style="display:none;max-height:0;overflow:hidden;opacity:0;color:#f5f6fa">' . $e(mb_substr($pre, 0, 140)) . '</div>'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f5f6fa" style="background:#f5f6fa"><tr><td align="center" style="padding:28px 12px">'
        . '<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px">'
        . '<tr><td bgcolor="' . $P . '" style="padding:22px 32px;border-radius:16px 16px 0 0;background:' . $P . ';background-image:linear-gradient(135deg,#312e81 0%,' . $P . ' 60%,#7c3aed 100%)">'
        . '<a href="' . $e($site) . '" style="text-decoration:none"><img src="' . $e($site) . 'mail-logo.png" width="30" height="30" alt="" style="vertical-align:middle;border:0;margin-right:10px"><span style="vertical-align:middle;' . $font . ';font-size:23px;font-weight:800;letter-spacing:-1px;color:#ffffff">craft<span style="color:#c7d2fe">iculum</span></span></a></td></tr>'
        . '<tr><td bgcolor="#ffffff" style="padding:32px 32px 18px;background:#ffffff;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;' . $font . '">'
        . '<h1 style="margin:0 0 18px;' . $font . ';font-size:24px;line-height:1.2;letter-spacing:-.5px;font-weight:800;color:' . $ink . '">' . $e($subject) . '</h1>' . $out . '</td></tr>'
        . '<tr><td bgcolor="#ffffff" style="padding:0 32px 26px;background:#ffffff;border:1px solid #e2e8f0;border-top:0;border-radius:0 0 16px 16px;' . $font . '"><div style="border-top:1px solid #e2e8f0;padding-top:16px;font-size:13px;line-height:1.5;color:#64748b"><b style="color:' . $ink . '">Plan it. Teach it. Share it.</b><br>From one sentence to a full lesson, built against your specification.</div></td></tr>'
        . '<tr><td align="center" style="padding:18px 24px 0;' . $font . ';font-size:12px;line-height:1.7;color:#64748b">'
        . $fl($site, 'Crafticulum') . ' &nbsp;·&nbsp; ' . $fl($site . 'privacy' . $ext, 'Privacy') . ' &nbsp;·&nbsp; ' . $fl($site . 'terms' . $ext, 'Terms') . ' &nbsp;·&nbsp; ' . $fl($site . 'contact' . $ext, 'Contact and support')
        . '<br>You\'re getting this because of an account or a message at ' . $e($host !== '' ? $host : 'Crafticulum') . ($contact !== '' && filter_var($contact, FILTER_VALIDATE_EMAIL) ? '. Questions? ' . $fl('mailto:' . $contact, $e($contact)) : '.')
        . '</td></tr></table></td></tr></table></body></html>';
}
/* the two versions of one email, plain words first, and the header that says so */
function ccMailParts(string $subject, string $text): array {
    $bd = 'cc' . bin2hex(random_bytes(12)); $plain = str_replace("\n", "\r\n", str_replace(["\r\n", "\r"], "\n", $text));
    $body = "--$bd\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n$plain\r\n\r\n--$bd\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode(ccMailHtml($subject, $text)), 76, "\r\n") . "--$bd--\r\n";
    return ["Content-Type: multipart/alternative; boundary=\"$bd\"", $body];
}
function ccMail(string $to, string $subject, string $text, string $replyTo = ''): bool {
    $rt = $replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL) && !preg_match('/[\r\n<>]/', $replyTo) ? "Reply-To: <$replyTo>\r\n" : '';
    $GLOBALS['ccMailErr'] = '';
    try { ccPushForMail($to, $subject, $text); } catch (\Throwable $x) {}
    $fail = function (string $why): bool { $GLOBALS['ccMailErr'] = $why; ccErr('email', $why); return false; };
    if (!ccMailReady()) return $fail('Email isn\'t set up yet. Add the mail server and Send from address.');
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return $fail('That isn\'t an email address.');
    $from = ccConf('MAIL_FROM') ? (string) ccConf('MAIL_FROM') : (string) ccConf('SMTP_USER');
    if (!filter_var($from, FILTER_VALIDATE_EMAIL)) return $fail('The Send from address isn\'t an email address. Use the mailbox\'s own address.');
    $subj = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    [$ctype, $body] = ccMailParts($subject, $text);
    if (!ccConf('SMTP_HOST')) return @mail($to, $subj, $body, "From: Crafticulum <$from>\r\n{$rt}MIME-Version: 1.0\r\n$ctype") ?: $fail('The web host\'s own mail() refused it. Add a mail server instead.');
    $host = trim((string) ccConf('SMTP_HOST')); $port = (int) ccConf('SMTP_PORT') ?: 587; $secure = strtolower(trim((string) ccConf('SMTP_SECURE')));
    // the port decides the security when they disagree: 465 is always SSL from the first byte, 587 and 25 start plain and upgrade
    if ($port === 465) $secure = 'ssl'; elseif ($secure === 'ssl' && in_array($port, [587, 25, 2525], true)) $secure = 'tls'; elseif (!in_array($secure, ['ssl', 'tls', 'none'], true)) $secure = 'tls';
    // a mail server on this same machine often has a certificate in the machine's name, not the domain's. That is safe to accept, because nothing leaves the machine.
    $ip = @gethostbyname($host); $local = $ip === '127.0.0.1' || ($ip !== $host && in_array($ip, array_filter([$_SERVER['SERVER_ADDR'] ?? '', @gethostbyname((string) gethostname())]), true));
    if ($ip === $host && !filter_var($host, FILTER_VALIDATE_IP)) return $fail("The mail server name \"$host\" can't be found. If the domain isn't pointed at the server yet, use the server's own name from your web host for now.");
    $open = function (bool $strict) use ($host, $port, $secure, &$errno, &$err) {
        $ctx = stream_context_create(['ssl' => ['verify_peer' => $strict, 'verify_peer_name' => $strict, 'allow_self_signed' => !$strict, 'SNI_enabled' => true, 'peer_name' => $host]]);
        return @stream_socket_client(($secure === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port, $errno, $err, 12, STREAM_CLIENT_CONNECT, $ctx);
    };
    $strict = true; $fp = $open(true);
    if (!$fp && $secure === 'ssl' && $local) { $strict = false; $fp = $open(false); }
    if (!$fp) return $fail($secure === 'ssl' && !$errno ? "Connected to $host, but its security certificate doesn't match that name. Use the server's own name from your web host as the mail server." : "Couldn't connect to $host on port $port" . ($err ? " ($err)" : '') . '. Check the server name and port, and ask your web host whether the site is allowed to send mail on that port.');
    stream_set_timeout($fp, 15);
    $last = '';
    $read = function () use ($fp, &$last) { $r = ''; while (($l = fgets($fp, 1000)) !== false) { $r .= $l; if (strlen($l) < 4 || $l[3] === ' ') break; } $last = trim($r); return $r; };
    $cmd = function (string $c, array $ok) use ($fp, $read) { if ($c !== '') fwrite($fp, $c . "\r\n"); $r = $read(); return in_array((int) substr($r, 0, 3), $ok, true); };
    $bye = function (string $why) use ($fp, $fail, &$last): bool { @fwrite($fp, "QUIT\r\n"); @fclose($fp); return $fail($why . ($last !== '' ? ' The mail server said: ' . mb_substr($last, 0, 200) : '')); };
    $me = preg_replace('/[^A-Za-z0-9.-]/', '', explode(':', $_SERVER['HTTP_HOST'] ?? 'localhost')[0]) ?: 'localhost';
    if (!$cmd('', [220])) return $bye("$host answered, but not like a mail server. Check the port and the security setting.");
    if (!$cmd("EHLO $me", [250])) return $bye('The mail server refused to start.');
    if ($secure === 'tls') {
        if (!$cmd('STARTTLS', [220])) return $bye('The mail server doesn\'t offer a secure connection on this port. Try port 465.');
        stream_context_set_option($fp, 'ssl', 'peer_name', $host);
        $up = @stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        if (!$up && $local) { stream_context_set_option($fp, 'ssl', 'verify_peer', false); stream_context_set_option($fp, 'ssl', 'verify_peer_name', false); $up = @stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT); }
        if (!$up) return $bye("The secure connection to $host failed. Its security certificate may not match that name. Try port 465, or the server's own name.");
        if (!$cmd("EHLO $me", [250])) return $bye('The mail server refused to carry on after securing the connection.');
    }
    $user = (string) ccConf('SMTP_USER');
    if ($user !== '') {
        if (!$cmd('AUTH LOGIN', [334])) return $bye('The mail server won\'t take a user name and password this way.');
        if (!$cmd(base64_encode($user), [334]) || !$cmd(base64_encode((string) ccConf('SMTP_PASS')), [235])) return $bye('The mail server didn\'t accept the user name and password. The user name is the whole email address, and the password is the mailbox\'s own.');
    }
    if (!$cmd("MAIL FROM:<$from>", [250])) return $bye("The mail server won't send from $from. Send from has to be the mailbox's own address.");
    if (!$cmd("RCPT TO:<$to>", [250, 251])) return $bye("The mail server won't send to $to.");
    if (!$cmd('DATA', [354])) return $bye('The mail server refused the message.');
    $msg = "From: Crafticulum <$from>\r\n{$rt}To: <$to>\r\nSubject: $subj\r\nDate: " . date('r') . "\r\nMessage-ID: <" . bin2hex(random_bytes(8)) . "@$me>\r\nMIME-Version: 1.0\r\n$ctype\r\n\r\n" . preg_replace('/^\./m', '..', $body);
    if (!$cmd($msg . "\r\n.", [250])) return $bye('The mail server refused the message.');
    @fwrite($fp, "QUIT\r\n"); fclose($fp);
    return true;
}
/* when someone joins an institution, the lessons they wrote can come with them: a copy of each goes into their new department's library as a draft, and the originals stay where they were */
function ccCarryLessons(array $a, string $email, string $from, string $to): int {
    if ($from === '' || $from === $to || !isset($a['depts'][$from], $a['depts'][$to])) return 0; $base = ccDataDir(); $n = 0;
    $dir = fn(string $d): string => !empty($a['depts'][$d]['legacy']) ? $base : $base . '/d/' . $d; $src = $dir($from); $dst = $dir($to); if (!is_dir($src) || (!is_dir($dst) && !@mkdir($dst, 0700, true))) return 0;
    $owner = strtolower((string) ($a['depts'][$from]['legacyOwner'] ?? '')); $em = strtolower($email);
    foreach (glob($src . '/*.json') ?: [] as $f) { if ($n >= ($email === '*' ? 5000 : 400)) break; $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j) || strpos((string) ($j['path'] ?? ''), 'lessons/') !== 0 || !is_array($j['data'] ?? null)) continue;
        $l = json_decode((string) ($j['data']['lesson'] ?? ''), true); if (!is_array($l)) continue; $by = strtolower((string) ($l['meta']['author']['email'] ?? ''));
        $everyone = $email === '*'; if (!$everyone && $by !== $em && !($by === '' && $owner === $em)) continue;   // only what they wrote themselves: lessons from before authors were kept belong to whoever owned that library
        $t = $dst . '/' . basename($f); if (is_file($t)) continue;
        if (!is_array($l['meta'] ?? null)) $l['meta'] = []; $l['meta']['status'] = 'draft'; $l['meta']['carried'] = time(); if (!$everyone) $l['meta']['author'] = ['name' => (string) (($a['users'][$email]['name'] ?? '') ?: $email), 'email' => $email]; if (is_array($l['meta']['author'] ?? null)) $l['meta']['draftBy'] = $l['meta']['author']; unset($l['meta']['approvedBy']);
        foreach (['lastResults', 'resultsBy', 'quizOnline', 'shareId', 'quizShareId', 'quizOpen', 'quizShowAnswers', 'coverId', 'liveKey'] as $k) unset($l[$k]);   // links and results belong to the old workspace
        unset($j['data']['pending'], $j['data']['pendingMine']); $j['data']['lesson'] = json_encode($l, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); $j['data']['status'] = 'draft'; $j['data']['updatedAt'] = time() * 1000;
        $tmp = $t . '.' . bin2hex(random_bytes(4)) . '.tmp'; if (@file_put_contents($tmp, json_encode($j, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX) === false || !@rename($tmp, $t)) { @unlink($tmp); continue; }
        $n++; }
    return $n; }

/* ---------- money: what each institution pays a month, and every time that changed ----------
   data/_money.json is a list of changes: when, which institution, what its monthly amount became (pence), and why.
   Everything on the Business dashboard (monthly recurring revenue, new, lost, the twelve-month line) is worked out from this list. */
const CC_INV_GRACE = 14 * 86400;    // how long after an invoice-paid term ends before the plan goes back to Free
function ccMoneyFile(): string { return ccDataDir() . '/_money.json'; }
function ccMoneyRead(): array { $j = json_decode((string) @file_get_contents(ccMoneyFile()), true); return is_array($j) ? array_values(array_filter($j, 'is_array')) : []; }
function ccMoneyLast(array $ledger, string $college, int $at = PHP_INT_MAX): int { $m = 0; foreach ($ledger as $e) if (($e['c'] ?? '') === $college && (int) ($e['t'] ?? 0) <= $at) $m = (int) ($e['mrr'] ?? 0); return $m; }
/* what an institution pays a month right now, in pence: a live card subscription, or a paid invoice spread over its term */
function ccMoneyNow(array $a, string $college): int { $c = (array) ($a['colleges'][$college] ?? []); $b = (array) ($c['bill'] ?? []);
    if (!empty($b['sub']) && in_array((string) ($b['status'] ?? ''), ['active', 'trialing', 'past_due'], true)) { if (isset($b['mrr'])) return max(0, (int) $b['mrr']); $p = ccPlans($a)[(string) ($c['plan'] ?? '')] ?? null; return $p && $p['price'] > 0 ? (int) $p['price'] : 0; }
    if (($b['method'] ?? '') === 'invoice' && ($b['status'] ?? '') === 'active' && (int) ($b['periodEnd'] ?? 0) + CC_INV_GRACE > time()) return max(0, (int) ($b['mrr'] ?? 0));
    return 0; }
/* write a line in the list whenever what an institution pays has changed */
function ccMoneySync(array $a, string $college = '', string $src = ''): void {
    $fh = @fopen(ccMoneyFile(), 'c+'); if (!$fh) return; if (!flock($fh, LOCK_EX)) { fclose($fh); return; }
    $raw = (string) stream_get_contents($fh); $first = trim($raw) === ''; $L = json_decode($raw, true); if (!is_array($L)) $L = []; $n = count($L);
    foreach ($college !== '' ? [$college] : ccCollegeIds($a) as $id) { $id = (string) $id; if (!isset($a['colleges'][$id])) continue; $now = ccMoneyNow($a, $id); $was = ccMoneyLast($L, $id); if ($now === $was) continue;
        $b = (array) ($a['colleges'][$id]['bill'] ?? []); $ever = false; foreach ($L as $e) if (($e['c'] ?? '') === $id && (int) ($e['mrr'] ?? 0) > 0) { $ever = true; break; }
        $L[] = ['t' => $first ? ((int) ($b['at'] ?? 0) ?: time()) : time(), 'c' => $id, 'k' => $was === 0 ? ($ever ? 'back' : 'new') : ($now === 0 ? 'lost' : ($now > $was ? 'up' : 'down')), 'plan' => (string) ($a['colleges'][$id]['plan'] ?? ''), 'mrr' => $now, 'src' => $src !== '' ? $src : ((($b['method'] ?? '') === 'invoice') ? 'invoice' : 'card')]; }
    if ($college === '') { $seenC = []; foreach ($L as $e) $seenC[(string) ($e['c'] ?? '')] = true; foreach (array_keys($seenC) as $id) if (!isset($a['colleges'][$id]) && ccMoneyLast($L, (string) $id) > 0) $L[] = ['t' => time(), 'c' => (string) $id, 'k' => 'lost', 'plan' => '', 'mrr' => 0, 'src' => 'removed']; }   // an institution that was removed while paying
    if (count($L) !== $n || $first) { usort($L, fn($x, $y) => ($x['t'] ?? 0) <=> ($y['t'] ?? 0)); ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode($L)); fflush($fh); }
    flock($fh, LOCK_UN); fclose($fh); }
/* ---------- invoices, for institutions that pay by bank transfer against a purchase order ---------- */
function ccInvFile(): string { return ccDataDir() . '/_invoices.json'; }
function ccInvRead(): array { $j = json_decode((string) @file_get_contents(ccInvFile()), true); return is_array($j) ? $j : []; }
function ccInvChange(callable $fn): bool { $fh = @fopen(ccInvFile(), 'c+'); if (!$fh) return false; if (!flock($fh, LOCK_EX)) { fclose($fh); return false; }
    $j = json_decode((string) stream_get_contents($fh), true); if (!is_array($j)) $j = []; $j = $fn($j); $ok = is_array($j); if ($ok) { ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode($j, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); fflush($fh); } flock($fh, LOCK_UN); fclose($fh); return $ok; }
function ccInvSet(?array $a = null): array { $s = (array) ((($a ?? ccAccounts())['site'] ?? [])['invoice'] ?? []);
    return ['from' => (string) ($s['from'] ?? "Next Level Labs Ltd\ntrading as Crafticulum\n4th Floor Office, 205 Regent Street\nLondon W1B 4HB\nCompany number 16390028"), 'bank' => (string) ($s['bank'] ?? ''), 'terms' => max(0, min(120, (int) ($s['terms'] ?? 30))), 'vat' => max(0, min(30, (float) ($s['vat'] ?? 0))), 'vatNo' => (string) ($s['vatNo'] ?? ''), 'prefix' => (string) ($s['prefix'] ?? 'CR'), 'email' => (string) ($s['email'] ?? '')]; }
function ccInvUrl(array $inv): string { return ccSiteUrl() . 'money.php?a=view&i=' . rawurlencode((string) ($inv['token'] ?? '')); }
function ccPounds(int $pence): string { return '£' . number_format($pence / 100, 2); }
/* ---------- things that happen by themselves, at most once an hour, when someone opens the app ---------- */
function ccTick(): void {
    $f = ccDataDir() . '/_tick'; if (is_file($f) && time() - (int) @filemtime($f) < 3600) return; @touch($f);
    $a = ccAccounts(); $dirty = false; $now = time(); $mail = ccMailReady();
    /* a term paid by invoice: a reminder a month before it ends, and back to Free two weeks after if nothing new has been paid */
    foreach (ccCollegeIds($a) as $id) { $id = (string) $id; $b = (array) ($a['colleges'][$id]['bill'] ?? []); if (($b['method'] ?? '') !== 'invoice' || ($b['status'] ?? '') !== 'active') continue; $end = (int) ($b['periodEnd'] ?? 0); if ($end <= 0) continue; $name = (string) ($a['colleges'][$id]['name'] ?? ''); $when = date('j F Y', $end);
        if ($end - $now < 30 * 86400 && empty($b['reminded'])) { $a['colleges'][$id]['bill']['reminded'] = $now; $dirty = true;
            ccCollegeMail($a, $id, "Your Crafticulum plan runs until $when", "The term your institution has paid for ends on $when.\n\nTo carry on without a break, ask for the next invoice in Settings, Plan and billing, or reply to this email.");
            foreach (ccSupers() as $s) ccNotify((string) $s, 'people', "$name: paid term ends $when", 'Open Admin, Money, Invoices to raise the next one.'); }
        if ($end + CC_INV_GRACE < $now) { $a['colleges'][$id]['bill']['status'] = 'ended'; $a['colleges'][$id]['bill']['at'] = $now; $a['colleges'][$id]['plan'] = 'free'; $dirty = true;
            ccCollegeMail($a, $id, 'Your plan has ended', "The term your institution paid for ended on $when, so the account is now on the Free plan. Lessons, planning and people are all still there.\n\nTo pick a plan again, go to Settings, Plan and billing."); } }
    /* a nudge for someone who signed up on their own a few days ago and has made fewer than two lessons */
    if ($mail) { $sent = 0; foreach ($a['users'] as $e => $u) { if ($sent >= 15) break; if (!empty($u['nudged']) || !empty($u['demo']) || !ccActive($u) || ($u['role'] ?? '') !== 'owner' || empty($u['verified'])) continue; $at = (int) ($u['added'] ?? 0); if ($at <= 0 || $now - $at < 3 * 86400 || $now - $at > 14 * 86400) continue;
            $d = (string) ($u['dept'] ?? ''); $cid = $d !== '' && isset($a['depts'][$d]) ? ccCollegeOf($a, $d) : ''; if ($cid === '' || empty($a['colleges'][$cid]['self']) || in_array((string) $e, ccSupers(), true)) continue;
            $a['users'][$e]['nudged'] = $now; $dirty = true; if (count(glob(ccDataDir() . '/d/' . $d . '/lessons/*.json') ?: []) >= 2) continue;
            $hi = trim(explode(' ', (string) ($u['name'] ?? ''))[0]);
            if (ccMail((string) $e, 'Your next lesson takes about a minute', 'Hello' . ($hi !== '' ? " $hi" : '') . ",\n\nYou set up Crafticulum a few days ago. If you haven't had the chance to try it properly yet, here's the quickest way in:\n\n1. Open Crafticulum: " . ccAppUrl() . "\n2. Type the subject, the level and one sentence about tomorrow's lesson.\n3. Press Build. You get the timed plan, the teaching content, tasks, an exit quiz and the slides.\n\nEverything can be edited, and nothing is shared with anyone until you choose to.\n\nIf something got in the way, reply to this email and tell us. A real person reads it.\n\nCrafticulum")) $sent++; } }
    /* Monday morning: a short summary for each institution's managers of the week just gone */
    if ($mail && (defined('CC_DIGEST_ANYDAY') || ((int) date('N') === 1 && (int) date('G') >= 7))) { $wk = date('o-W'); $seen = ccSeenRead(); $u = ccUsageRead(); $supers = ccSupers(); $done = 0;
        foreach (ccCollegeIds($a) as $id) { $id = (string) $id; if ($done >= 25) break; $c = (array) $a['colleges'][$id]; if (($c['digest'] ?? '') === $wk || !empty($c['digestOff']) || !empty($c['off'])) continue;
            $depts = ccCollegeDepts($a, $id); $people = []; $mgrs = []; foreach ($a['users'] as $e => $x) { if (in_array((string) $e, $supers, true) || !empty($x['demo']) || !ccActive($x) || !in_array((string) ($x['dept'] ?? ''), $depts, true)) continue; $people[(string) $e] = $x; if (($x['role'] ?? '') === 'owner') $mgrs[] = (string) $e; }
            if (count($people) < 2 || !$mgrs) continue;   // nothing to tell someone working on their own
            $a['colleges'][$id]['digest'] = $wk; $dirty = true;
            $built = 0; $by = []; foreach ($depts as $d) { $dd = !empty($a['depts'][$d]['legacy']) ? ccDataDir() : ccDataDir() . '/d/' . $d; foreach (glob($dd . '/*.json') ?: [] as $f) { if (@filemtime($f) < $now - 7 * 86400) continue; $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j) || strpos((string) ($j['path'] ?? ''), 'lessons/') !== 0) continue; $built++; } }
            $active = 0; $quiet = []; foreach ($people as $e => $x) { $s = max((int) ($seen[$e] ?? 0), (int) ($x['last'] ?? 0)); if ($now - $s < 7 * 86400) $active++; elseif ($now - $s > 14 * 86400) $quiet[] = (string) (($x['name'] ?? '') ?: $e); }
            $used = ccPlanUsed($id, $u); $pl = ccPlans($a)[ccCollege($a, $id)['plan']]; $name = (string) ($c['name'] ?? 'your institution');
            $text = "Here's the week at $name.\n\nLessons made or changed: $built\nPeople who opened Crafticulum: $active of " . count($people) . "\nBuilds with Crafticulum AI this month: {$used['builds']}" . ($pl['builds'] > 0 ? " of {$pl['builds']}" : '') . "\n"
                . ($quiet ? "\nNot been in for two weeks or more: " . implode(', ', array_slice($quiet, 0, 12)) . (count($quiet) > 12 ? ' and ' . (count($quiet) - 12) . ' more' : '') . ". A quick word, or ten minutes at a team meeting, usually does it.\n" : "\nEveryone has been in during the last two weeks.\n")
                . "\nOpen the manager's view: " . ccAppUrl() . "\n\nYou get this each Monday because you manage $name on Crafticulum. To stop it, switch off Weekly summary in Settings, Institution.";
            foreach ($mgrs as $m) ccMail($m, "Your week on Crafticulum: $built lesson" . ($built === 1 ? '' : 's') . ", $active of " . count($people) . ' active', $text); $done++; } }
    /* once a month: open the newest backup and read every file in it, so a backup that can't be restored is found now and not on the day it's needed */
    try { ccBackupCheck(); } catch (\Throwable $e) {}
    if ($dirty) { ccSaveAccounts($a); ccMoneySync($a); }
}
function ccBackupCheck(bool $force = false): array {
    $dir = ccDataDir() . '/backups'; $rf = $dir . '/check.json'; $last = json_decode((string) @file_get_contents($rf), true); if (!is_array($last)) $last = [];
    if (!$force && !empty($last['t']) && time() - (int) $last['t'] < 28 * 86400) return $last;
    $zips = array_values(array_filter(glob($dir . '/*.zip') ?: [], fn($f) => preg_match('/\/\d{4}-\d{2}-\d{2}\.zip$/', $f))); rsort($zips); if (!$zips || !class_exists('ZipArchive')) return $last;
    $f = $zips[0]; $z = new ZipArchive(); $res = ['t' => time(), 'file' => basename($f), 'ok' => false, 'files' => 0, 'bad' => 0, 'bytes' => (int) @filesize($f), 'why' => ''];
    if ($z->open($f) !== true) { $res['why'] = 'The backup file could not be opened.'; } else { $acc = false; $stop = microtime(true) + 20;
        for ($i = 0; $i < $z->numFiles && microtime(true) < $stop; $i++) { $n = (string) $z->getNameIndex($i); if (substr($n, -1) === '/') continue; $c = $z->getFromIndex($i); $res['files']++; if ($c === false) { $res['bad']++; continue; }
            if (substr($n, -5) === '.json' && strlen($c) < 4000000 && trim($c) !== '' && json_decode($c, true) === null && json_last_error() !== JSON_ERROR_NONE) $res['bad']++; if (basename($n) === '_accounts.json') { $j = json_decode($c, true); $acc = is_array($j) && !empty($j['users']); } }
        $z->close(); $res['ok'] = $res['bad'] === 0 && $res['files'] > 0 && $acc; if (!$res['ok']) $res['why'] = !$acc ? 'The list of accounts is missing from the backup.' : $res['bad'] . ' file' . ($res['bad'] === 1 ? '' : 's') . ' could not be read.'; }
    @file_put_contents($rf, json_encode($res), LOCK_EX);
    if (!$res['ok']) { ccErr('backup', 'The monthly check of the backup failed. ' . $res['why']); foreach (ccSupers() as $s) ccNotify((string) $s, 'people', 'The backup check failed', $res['why'] . ' Open Admin, Money, Business.'); }
    return $res;
}
/* ---------- give a month, get a month ---------- */
function ccRefCode(array &$a, string $college): string { $c = (string) ($a['colleges'][$college]['refCode'] ?? ''); if ($c !== '') return $c; do { $c = substr(strtolower(strtr(base64_encode(random_bytes(9)), '+/=', 'xyz')), 0, 8); $c = (string) preg_replace('/[^a-z0-9]/', 'k', $c); $hit = false; foreach ($a['colleges'] as $x) if (($x['refCode'] ?? '') === $c) $hit = true; } while ($hit); $a['colleges'][$college]['refCode'] = $c; return $c; }
function ccRefFrom(array $a): string { $code = strtolower((string) ($_COOKIE['cc_ref'] ?? '')); if (!preg_match('/^[a-z0-9]{8}$/', $code)) return ''; foreach ($a['colleges'] as $id => $c) if (($c['refCode'] ?? '') === $code && empty($c['off'])) return (string) $id; return ''; }
function ccFeedbackFile(): string { return ccDataDir() . '/_feedback.jsonl'; }

/* when each person last opened the app, kept apart from the accounts file so it can be written often */
function ccSeenFile(): string { return ccDataDir() . '/_seen.json'; }
function ccSeenRead(): array { $j = json_decode((string) @file_get_contents(ccSeenFile()), true); return is_array($j) ? $j : []; }
function ccSeen(string $email): void { if ($email === '') return; $fh = @fopen(ccSeenFile(), 'c+'); if (!$fh) return; if (flock($fh, LOCK_EX)) { $j = json_decode((string) stream_get_contents($fh), true); if (!is_array($j)) $j = [];
        if (time() - (int) ($j[$email] ?? 0) > 3600) { $j[$email] = time(); ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode($j)); fflush($fh); } flock($fh, LOCK_UN); } fclose($fh); }
