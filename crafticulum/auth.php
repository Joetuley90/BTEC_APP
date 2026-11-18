<?php
/*
 * Crafticulum: signing in, staff accounts and departments.
 *
 * Ways to sign in: an email and password (with optional two-step codes), Google,
 * Microsoft (Entra ID, work, school and personal accounts), any OpenID Connect
 * provider (Okta, Auth0, Keycloak, college single sign-on), and the admin
 * password in config.php until one of those is set up.
 *
 * New people ask for an account (or sign in with Google or Microsoft for the
 * first time) and wait until a head of department or super admin approves them.
 *
 *   config, signup, verify, login, login2, forgot, reset, linkinfo, google, ms_start, ms_cb, oidc_start, oidc_cb, password
 *   me, logout, dept, setpass, totp_start, totp_on, totp_off
 *   team, useradd, userset, userdel, approve, decline, resetlink     (heads of department, super admins)
 *   deptadd, deptset, deptdel                                        (super admins)
 */
declare(strict_types=1);
if (!is_file(__DIR__ . '/config.php')) { header('Content-Type: application/json; charset=utf-8'); http_response_code(500); echo json_encode(['error' => 'config.php is missing.', 'setup' => true]); exit; }
require __DIR__ . '/config.php';
require __DIR__ . '/auth-lib.php';

$action = $_GET['a'] ?? '';
if (!in_array($action, ['ms_start', 'ms_cb', 'oidc_start', 'oidc_cb'], true)) { header('Content-Type: application/json; charset=utf-8'); }
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
function out(int $code, array $data): void { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit; }
$body = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') { $raw = file_get_contents('php://input', false, null, 0, ($_GET['a'] ?? '') === 'siteset' ? 400001 : 20001); $body = json_decode($raw ?: '{}', true); if (!is_array($body)) $body = []; }
/* the daily reminders, for a scheduled job on the server: auth.php?a=pushcron&k=<the key shown in Settings, Notifications> */
if (($_GET['a'] ?? '') === 'pushcron') { if (!hash_equals(substr(hash('sha256', 'cron|' . ccSiteId()), 0, 32), (string) ($_GET['k'] ?? ''))) out(403, ['error' => 'key']); out(200, ['sent' => ccPushDaily(), 'purged' => ccDeletedPurge()]); }
if (($_GET['a'] ?? '') === 'ping') out(200, ['site' => hash('sha256', ccSiteId())]);   // lets the site check that its main address reaches this same site
ccSession();

function signIn(string $uid): void { session_regenerate_id(false); $_SESSION = ['uid' => $uid, 'teacher' => true, 'hits' => []]; }
function cleanName(string $s): string { $s = trim(preg_replace('/\s+/u', ' ', preg_replace('/[\x00-\x1F\x7F<>]+/u', '', $s) ?? '') ?? ''); return mb_substr($s, 0, 60); }
function emailOk(string $e): bool { return (bool) filter_var($e, FILTER_VALIDATE_EMAIL) && strlen($e) <= 120; }
function passOk(string $p, string $email): string { if (strlen($p) < 10) return 'short'; if (strlen($p) > 200) return 'long'; if (strtolower($p) === strtolower($email)) return 'weak'; return ''; }
function deptList(array $a, ?string $college = null): array { $o = []; $many = count(ccCollegeIds($a)) > 1;
    foreach ($a['depts'] as $k => $d) { $k = (string) $k; if ($k === CC_DEMO_DEPT) continue; $c = ccCollegeOf($a, $k); if ($college !== null && $c !== $college) continue; if (ccCollege($a, $c)['off'] && $college === null) continue;
        $o[] = ['id' => $k, 'name' => $d['name'], 'college' => $c] + ($many ? ['collegeName' => ccCollege($a, $c)['name']] : []); }
    return $o; }
const CC_ROLE_NAMES = ['superadmin' => 'Super admin', 'owner' => 'College manager', 'hod' => 'Head of department', 'teacher' => 'Teacher'];
function ccLink(string $type, string $t): string { return ccAppUrl() . '#' . $type . '=' . $t; }
function siteName(): string { return 'Crafticulum'; }
/* the welcome for someone who has just started their own space: what to do first, in three steps */
function welcomeMail(string $email, string $name, string $verify = ''): bool {
    if (!ccMailReady()) return false; $hi = trim(explode(' ', trim($name))[0] ?? '');
    return ccMail($email, 'Welcome to Crafticulum', 'Hello' . ($hi !== '' ? " $hi" : '') . ",\n\nYour Crafticulum account is ready. You're on the Free plan, and you can change that at any time in Settings, Plan and billing.\n\n"
        . ($verify !== '' ? "First, please confirm this is your email address. Lessons can be built once you have:\n$verify\n\n" : '')
        . "Here are three things to do first:\n\n"
        . "1. Add your subject: Upload the specification from your awarding body and its units and criteria are read from it. No specification to hand? Start with one topic instead.\n"
        . "2. Build your first lesson: Type one sentence about what you're teaching. You get the timed plan, teaching content, tasks, an exit quiz and slides.\n"
        . "3. Teach it and share it: Put the lesson on the board, and give your students their own page with a link or a QR code.\n\n"
        . "Pick up where you left off:\n" . ccAppUrl() . "\n\nStuck on anything? Reply to this email or use Help and feedback in the app.\n");
}
/* someone signing up on their own gets their own space: a college on the Free plan with one department, which they manage */
function newWorkspace(array &$a, string $email, string $name, string $space): string {
    $space = cleanName($space); if ($space === '') $space = trim(explode(' ', $name)[0] . "'s workspace");
    $id = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($space)), '-') ?: 'space'; $id = substr($id, 0, 20) . '-' . bin2hex(random_bytes(2)); while (isset($a['colleges'][$id])) $id .= 'x';
    $a['colleges'][$id] = ['name' => $space, 'plain' => true, 'created' => time(), 'plan' => 'free', 'self' => true];
    $did = substr($id, 0, 24); $n = 2; while (isset($a['depts'][$did]) || $did === CC_DEMO_DEPT) $did = substr($id, 0, 20) . '-' . $n++;
    $a['depts'][$did] = ['name' => 'My department', 'created' => time(), 'college' => $id];
    return $did;
}
/* not too many new spaces from one place in a day */
function startAllowed(): bool { $f = ccDataDir() . '/_start-rate.json'; $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j)) $j = []; $now = time(); $ip = substr(sha1((string) ($_SERVER['REMOTE_ADDR'] ?? '') . '|' . ccSiteId()), 0, 16);
    $j = array_filter(array_map(fn($l) => array_values(array_filter((array) $l, fn($t) => $t > $now - 86400)), $j)); if (count((array) ($j[$ip] ?? [])) >= 5 || array_sum(array_map('count', $j)) >= 300) return false;
    $j[$ip][] = $now; @file_put_contents($f, json_encode($j), LOCK_EX); return true; }
/* a Google or Microsoft account signs in: straight in if they're on the team, otherwise a request to approve */
function ssoArrive(string $email, string $name, string $via, string $picture = ''): array {
    $a = ccAccounts(); $isSuper = in_array($email, ccSupers(), true); $u = $a['users'][$email] ?? null;
    if (!$u && !$isSuper) {
        if (ccBillingOn() && startAllowed()) {   // plans are on: a new person gets their own space on the Free plan straight away
            $did = newWorkspace($a, $email, cleanName($name), '');
            $a['users'][$email] = ['name' => cleanName($name), 'status' => 'active', 'verified' => true, 'via' => $via, 'role' => 'owner', 'dept' => $did, 'canEdit' => true, 'added' => time(), 'last' => time(), 'methods' => [$via]] + ($picture !== '' ? ['picture' => $picture] : []);
            if (!ccSaveAccounts($a)) return ['pending' => true];
            welcomeMail($email, cleanName($name));
            signIn($email); return ['ok' => true, 'started' => true];
        }
        $a['users'][$email] = ['name' => cleanName($name), 'status' => 'pending', 'verified' => true, 'via' => $via, 'requested' => time(), 'role' => 'teacher', 'dept' => '', 'canEdit' => false];
        ccSaveAccounts($a); notifyApprovers($a, $email);
        return ['pending' => true];
    }
    if ($u && !ccActive($u) && !$isSuper) return ['pending' => true];
    $u = $u ?? ['role' => 'superadmin', 'dept' => (string) array_key_first($a['depts']), 'added' => time()];
    if ($isSuper) $u['status'] = 'active';
    if (empty($u['name']) && $name !== '') $u['name'] = cleanName($name);
    if ($picture !== '') $u['picture'] = $picture;
    $u['last'] = time(); $u['methods'] = array_values(array_unique(array_merge((array) ($u['methods'] ?? []), [$via]))); $u['verified'] = true;
    $a['users'][$email] = $u; ccSaveAccounts($a);
    signIn($email);
    return ['ok' => true];
}
function notifyApprovers(array $a, string $email): void {
    if (!ccMailReady()) return;
    $u = $a['users'][$email]; $d = (string) ($u['wantDept'] ?? '');
    // a head of department or subject leader is approved by the super admin; a teacher by the leaders of that department
    $to = [];
    if (($u['wantRole'] ?? 'teacher') !== 'hod' && $d !== '') foreach ($a['users'] as $e => $x) if (($x['role'] ?? '') === 'hod' && ccActive($x) && ($x['dept'] ?? '') === $d && ccPerms($x)['team']) $to[] = $e;
    if (!$to && $d !== '' && isset($a['depts'][$d])) { $c = ccCollegeOf($a, $d); foreach ($a['users'] as $e => $x) if (($x['role'] ?? '') === 'owner' && ccActive($x) && ccCollegeOf($a, (string) ($x['dept'] ?? '')) === $c) $to[] = $e; }   // then the college's manager
    if (!$to) $to = ccSupers();
    foreach (array_unique($to) as $t) ccMail($t, 'New account to approve', "Hello,\n\n" . ($u['name'] ?: $email) . " ($email) has asked for a Crafticulum account" . ($d && isset($a['depts'][$d]) ? ' in ' . $a['depts'][$d]['name'] : '') . ".\n\nApprove or decline them in Settings, Team:\n" . ccAppUrl() . "\n");
}

/* ---------- public ---------- */
if ($action === 'config') {
    $a = ccAccounts();
    $fresh = !array_filter($a['users'], fn($u) => !empty($u['pass']) || !empty($u['last']));
    out(200, ['google' => ccGoogleId() ?: null, 'googleRedirect' => ccGoogleSecret() !== '', 'microsoft' => ccMsId() !== '', 'oidc' => ccOidcOn() ? ccOidcName() : null, 'password' => ccPasswordAllowed(), 'mail' => ccMailReady(), 'depts' => array_values(array_filter(deptList($a), fn($d) => empty($a['colleges'][(string) ($a['depts'][$d['id']]['college'] ?? '')]['self']))), 'fresh' => $fresh, 'site' => ccSitePublic($a), 'url' => ccSiteUrl(), 'demo' => ccDemoOn(), 'billing' => ccBillingOn()]);
}
/* the contact page: anyone can send a message. It's kept for the super admin and emailed to the site's contact address */
if ($action === 'contact') {
    $clean = fn($s, int $n) => mb_substr(trim((string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/u', '', (string) $s)), 0, $n);
    $line = fn($s, int $n) => mb_substr(trim((string) preg_replace('/[\x00-\x1F\x7F<>]+/u', ' ', (string) $s)), 0, $n);
    // a box people can't see was filled in, or it was sent faster than anyone types: a robot. Say thanks and keep nothing
    if (trim((string) ($body['website'] ?? '')) !== '' || (int) ($body['took'] ?? 0) < 2500) out(200, ['ok' => true]);
    $kind = in_array($body['kind'] ?? '', ['support', 'problem', 'feedback', 'enquiry'], true) ? (string) $body['kind'] : 'support';
    $name = $line($body['name'] ?? '', 80); $email = strtolower(trim((string) ($body['email'] ?? ''))); $msg = $clean($body['msg'] ?? '', 4000);
    if ($name === '' || !emailOk($email)) out(400, ['error' => 'email']);
    if (mb_strlen($msg) < 10) out(400, ['error' => 'short']);
    if (!ccContactAllowed()) out(429, ['error' => 'slow']);
    $who = ccMe(); $id = bin2hex(random_bytes(6));
    $row = ['id' => $id, 'at' => time(), 'kind' => $kind, 'name' => $name, 'email' => $email, 'org' => $line($body['org'] ?? '', 100), 'msg' => $msg, 'page' => $line($body['page'] ?? '', 30), 'browser' => $line($body['browser'] ?? '', 60),
            'who' => $who && empty($who['demo']) ? (CC_ROLE_NAMES[$who['role']] ?? '') . ', ' . (string) $who['deptName'] : ($who ? 'Demo visitor' : ''), 'done' => false];
    if (!ccInboxChange(function ($j) use ($row, $id) { $j[$id] = $row; return $j; })) out(500, ['error' => 'save']);
    $a = ccAccounts(); $to = (string) (ccSite($a)['contact'] ?? ''); $tos = $to !== '' ? [$to] : array_values(array_filter(ccSupers(), fn($e) => (bool) filter_var($e, FILTER_VALIDATE_EMAIL)));
    $label = ['support' => 'Help needed', 'problem' => 'Problem reported', 'feedback' => 'Feedback', 'enquiry' => 'Enquiry'][$kind];
    foreach (ccSupers() as $t) ccNotify((string) $t, 'people', "$label from $name", 'Sent from the contact page. Open Settings, Site, Messages to read and answer it.');
    if (ccMailReady()) foreach (array_slice($tos, 0, 3) as $t) ccMail($t, "$label from $name", "$label, sent from the contact page.\n\nFrom: $name <$email>" . ($row['org'] !== '' ? "\nCollege or school: {$row['org']}" : '') . ($row['who'] !== '' ? "\nSigned in as: {$row['who']}" : '') . ($row['page'] !== '' ? "\nThey were on: {$row['page']}" : '') . ($row['browser'] !== '' ? "\nUsing: {$row['browser']}" : '') . "\n\n$msg\n\nReply to this email to answer them. Every message is also in Settings, Messages:\n" . ccSiteUrl() . (ccPretty() ? "settings" : "app.html") . "\n", $email);
    out(200, ['ok' => true]);
}
/* the demo: one shared account in a sample department. Nothing in it lasts past tonight */
if ($action === 'demo') {
    if (!ccDemoOn()) out(404, ['error' => 'off']);
    if (ccMe() && empty(ccMe()['demo'])) out(200, ['ok' => true, 'signedIn' => true]);   // already signed in as themselves: leave that alone
    if (!ccDemoEnter()) out(500, ['error' => 'save']);
    signIn(CC_DEMO_USER);
    out(200, ['ok' => true]);
}
if ($action === 'signup') {
    $email = strtolower(trim((string) ($body['email'] ?? ''))); $name = cleanName((string) ($body['name'] ?? '')); $pass = (string) ($body['password'] ?? ''); $want = (string) ($body['dept'] ?? ''); $wantRole = ($body['role'] ?? '') === 'hod' ? 'hod' : 'teacher';
    if (!emailOk($email)) out(400, ['error' => 'email']);
    if ($name === '') out(400, ['error' => 'name']);
    if ($pass === '' && !ccMailReady()) out(400, ['error' => 'pass_required']);
    if ($pass !== '' && ($e = passOk($pass, $email))) out(400, ['error' => 'pass_' . $e]);
    if (ccLocked($email)) out(429, ['error' => 'locked']);
    $a = ccAccounts();
    /* plans are on and they want their own space: they're in straight away, on the Free plan */
    if (($body['space'] ?? '') === 'new' && ccBillingOn()) {
        if ($pass === '') out(400, ['error' => 'pass_required']);
        if (isset($a['users'][$email])) { ccFail($email); out(409, ['error' => 'exists']); }
        if (in_array($email, ccSupers(), true)) out(409, ['error' => 'exists']);
        if (!startAllowed()) out(429, ['error' => 'locked']);
        $did = newWorkspace($a, $email, $name, (string) ($body['workspace'] ?? ''));
        $a['users'][$email] = ['name' => $name, 'status' => 'active', 'verified' => false, 'via' => 'password', 'role' => 'owner', 'dept' => $did, 'canEdit' => true, 'added' => time(), 'last' => time(), 'methods' => ['password'], 'pass' => password_hash($pass, PASSWORD_DEFAULT)];
        if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
        welcomeMail($email, $name, ccLink('verify', ccToken($email, 'verify', 72)));
        signIn($email);
        out(200, ['ok' => true, 'started' => true]);
    }
    if (isset($a['users'][$email])) { ccFail($email); out(200, ['ok' => true, 'verify' => ccMailReady()]); }   // same answer either way, so no one can find out who has an account
    $a['users'][$email] = ['name' => $name, 'status' => 'pending', 'verified' => false, 'via' => $pass === '' ? 'magic' : 'password', 'requested' => time(),
        'wantDept' => isset($a['depts'][$want]) ? $want : '', 'wantRole' => $wantRole, 'role' => 'teacher', 'dept' => '', 'canEdit' => false, 'methods' => [$pass === '' ? 'magic' : 'password']];
    if ($pass !== '') $a['users'][$email]['pass'] = password_hash($pass, PASSWORD_DEFAULT);
    ccSaveAccounts($a);
    if (ccMailReady()) ccMail($email, 'Confirm your email for Crafticulum', "Hello $name,\n\nPlease confirm this is your email address:\n" . ccLink('verify', ccToken($email, 'verify', 72)) . "\n\nA head of department also needs to approve your account. We'll email you when they have.\n\nIf you didn't ask for this, ignore this email.\n");
    notifyApprovers($a, $email);
    out(200, ['ok' => true, 'verify' => ccMailReady()]);
}
/* send the confirm-your-email link again, to whoever is signed in */
if ($action === 'verifyagain') {
    $me = ccMe(); if (!$me) out(401, ['error' => 'signin']); $a = ccAccounts(); $e = (string) $me['email']; $u = $a['users'][$e] ?? null; if (!$u) out(404, ['error' => 'not_found']);
    if (!empty($u['verified'])) out(200, ['ok' => true, 'verified' => true]);
    if (!ccMailReady()) out(400, ['error' => 'mail']);
    if (time() - (int) ($u['verifySent'] ?? 0) < 60) out(429, ['error' => 'wait']);
    $a['users'][$e]['verifySent'] = time(); ccSaveAccounts($a);
    $hi = trim(explode(' ', (string) ($u['name'] ?? ''))[0]);
    $ok = ccMail($e, 'Confirm your email for Crafticulum', ($hi !== '' ? "Hello $hi,\n\n" : "Hello,\n\n") . "Please confirm this is your email address:\n" . ccLink('verify', ccToken($e, 'verify', 72)) . "\n\nThe link works once, for 3 days. After that you can build lessons.\n");
    out($ok ? 200 : 502, $ok ? ['ok' => true] : ['error' => 'mail']);
}
if ($action === 'verify') {
    $v = ccUseToken((string) ($body['token'] ?? ''), ['verify']); if (!$v) out(400, ['error' => 'link']);
    $a = ccAccounts(); $e = $v['email']; if (!isset($a['users'][$e])) out(400, ['error' => 'link']);
    $a['users'][$e]['verified'] = true;
    if (in_array($e, ccSupers(), true)) { $a['users'][$e]['status'] = 'active'; $a['users'][$e]['role'] = 'superadmin'; }
    ccSaveAccounts($a);
    out(200, ['ok' => true, 'active' => ccActive($a['users'][$e])]);
}
if ($action === 'login') {
    $email = strtolower(trim((string) ($body['email'] ?? ''))); $pass = (string) ($body['password'] ?? '');
    if (!emailOk($email) || $pass === '') out(400, ['error' => 'wrong']);
    if (ccLocked($email)) out(429, ['error' => 'locked']);
    $a = ccAccounts(); $u = $a['users'][$email] ?? null;
    if (!$u || empty($u['pass']) || !password_verify($pass, (string) $u['pass'])) { ccFail($email); out(401, ['error' => 'wrong']); }
    ccPassed($email);
    if (!ccActive($u)) out(403, ['error' => 'pending', 'verified' => !empty($u['verified'])]);
    if (password_needs_rehash((string) $u['pass'], PASSWORD_DEFAULT)) { $a['users'][$email]['pass'] = password_hash($pass, PASSWORD_DEFAULT); ccSaveAccounts($a); }
    if (!empty($u['totp']['on'])) { session_regenerate_id(false); $_SESSION = ['half' => $email, 'halfAt' => time()]; out(200, ['need2fa' => true]); }
    $a['users'][$email]['last'] = time(); ccSaveAccounts($a); signIn($email);
    out(200, ['me' => ccPublic(ccMe())]);
}
if ($action === 'login2') {
    $email = (string) ($_SESSION['half'] ?? ''); if ($email === '' || time() - (int) ($_SESSION['halfAt'] ?? 0) > 300) out(401, ['error' => 'expired']);
    if (ccLocked($email)) out(429, ['error' => 'locked']);
    $a = ccAccounts(); $u = $a['users'][$email] ?? null; $code = trim((string) ($body['code'] ?? ''));
    if (!$u || empty($u['totp']['on'])) out(401, ['error' => 'expired']);
    $ok = ccTotpOk((string) $u['totp']['secret'], $code);
    if (!$ok) { $h = hash('sha256', strtolower(preg_replace('/[^A-Za-z0-9]/', '', $code))); $rc = (array) ($u['totp']['recovery'] ?? []); $i = array_search($h, $rc, true);
        if ($i !== false) { array_splice($rc, (int) $i, 1); $a['users'][$email]['totp']['recovery'] = $rc; $ok = true; } }
    if (!$ok) { ccFail($email); out(401, ['error' => 'code']); }
    ccPassed($email); $a['users'][$email]['last'] = time(); ccSaveAccounts($a); signIn($email);
    out(200, ['me' => ccPublic(ccMe())]);
}
/* magic links: a one-time sign-in link by email, no password needed */
if ($action === 'magic') {
    $email = strtolower(trim((string) ($body['email'] ?? '')));
    if (!ccMailReady()) out(400, ['error' => 'no_mail']);
    if (emailOk($email) && !ccLocked($email)) {
        $a = ccAccounts(); $u = $a['users'][$email] ?? null;
        if ($u && ccActive($u)) ccMail($email, 'Your Crafticulum sign-in link', "Hello" . (!empty($u['name']) ? ' ' . $u['name'] : '') . ",\n\nSign in to Crafticulum with this link. It works once, for 15 minutes:\n" . ccLink('magic', ccToken($email, 'magic', 0.25)) . "\n\nIf you didn't ask for it, ignore this email. No one can sign in without the link.\n");
        elseif ($u) ccMail($email, 'Your Crafticulum account', "Hello,\n\nYour Crafticulum account is still waiting for a head of department to approve it. We'll email you when they have.\n");
        ccFail($email);
    }
    out(200, ['ok' => true]);
}
if ($action === 'magic_login') {
    $v = ccUseToken((string) ($body['token'] ?? ''), ['magic']); if (!$v) out(400, ['error' => 'link']);
    $email = $v['email']; $a = ccAccounts(); $u = $a['users'][$email] ?? null;
    if (!$u && !in_array($email, ccSupers(), true)) out(400, ['error' => 'link']);
    if ($u && !ccActive($u) && !in_array($email, ccSupers(), true)) out(403, ['error' => 'pending']);
    $u = $u ?? ['role' => 'superadmin', 'dept' => (string) array_key_first($a['depts'])];
    $u['verified'] = true; $u['methods'] = array_values(array_unique(array_merge((array) ($u['methods'] ?? []), ['magic']))); $a['users'][$email] = $u; ccSaveAccounts($a);
    if (!empty($u['totp']['on'])) { session_regenerate_id(false); $_SESSION = ['half' => $email, 'halfAt' => time()]; out(200, ['need2fa' => true]); }
    $a['users'][$email]['last'] = time(); ccSaveAccounts($a); signIn($email);
    out(200, ['me' => ccPublic(ccMe())]);
}
if ($action === 'forgot') {
    $email = strtolower(trim((string) ($body['email'] ?? '')));
    if (emailOk($email) && !ccLocked($email)) { $a = ccAccounts(); $u = $a['users'][$email] ?? null;
        if ($u && ccActive($u) && ccMailReady()) ccMail($email, 'Reset your Crafticulum password', "Hello" . (!empty($u['name']) ? ' ' . $u['name'] : '') . ",\n\nChoose a new password with this link. It works once, for an hour:\n" . ccLink('reset', ccToken($email, 'reset', 1)) . "\n\nIf you didn't ask, ignore this email. Your password hasn't changed.\n");
        ccFail($email); }
    out(200, ['ok' => true, 'mail' => ccMailReady()]);
}
if ($action === 'linkinfo') {
    $v = ccUseToken((string) ($body['token'] ?? ''), ['reset', 'invite'], false); if (!$v) out(400, ['error' => 'link']);
    $a = ccAccounts(); $u = $a['users'][$v['email']] ?? []; out(200, ['email' => $v['email'], 'name' => (string) ($u['name'] ?? ''), 'type' => $v['type']]);
}
if ($action === 'reset') {
    $v = ccUseToken((string) ($body['token'] ?? ''), ['reset', 'invite'], false); if (!$v) out(400, ['error' => 'link']);
    $e = $v['email']; $pass = (string) ($body['password'] ?? '');
    if ($err = passOk($pass, $e)) out(400, ['error' => 'pass_' . $err]);
    ccUseToken((string) $body['token'], ['reset', 'invite']);
    $a = ccAccounts(); if (!isset($a['users'][$e]) && !in_array($e, ccSupers(), true)) out(400, ['error' => 'link']);
    $u = $a['users'][$e] ?? ['role' => 'superadmin', 'dept' => (string) array_key_first($a['depts'])];
    $u['pass'] = password_hash($pass, PASSWORD_DEFAULT); $u['verified'] = true; $u['methods'] = array_values(array_unique(array_merge((array) ($u['methods'] ?? []), ['password'])));
    $a['users'][$e] = $u; ccSaveAccounts($a); ccPassed($e);
    if (!ccActive($u) && !in_array($e, ccSupers(), true)) out(200, ['ok' => true, 'pending' => true]);
    if (!empty($u['totp']['on'])) out(200, ['ok' => true, 'signin' => true]);
    signIn($e); out(200, ['ok' => true, 'me' => ccPublic(ccMe())]);
}
if ($action === 'google') {
    $cid = ccGoogleId(); if ($cid === '') out(400, ['error' => 'not_set_up']);
    $tok = (string) ($body['credential'] ?? '');
    if ($tok === '' || strlen($tok) > 5000) out(400, ['error' => 'bad_token']);
    if (!function_exists('curl_init')) out(500, ['error' => 'curl']);
    $ch = curl_init((defined('GOOGLE_TOKENINFO_URL') ? GOOGLE_TOKENINFO_URL : 'https://oauth2.googleapis.com/tokeninfo') . '?id_token=' . rawurlencode($tok));
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15]);
    $res = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    $t = $code === 200 ? json_decode((string) $res, true) : null;
    if (!is_array($t) || ($t['aud'] ?? '') !== $cid || !in_array($t['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true)
        || (string) ($t['email_verified'] ?? '') !== 'true' || (int) ($t['exp'] ?? 0) < time()) out(401, ['error' => 'bad_token']);
    $pic = !empty($t['picture']) && preg_match('#^https://[a-z0-9.-]+\.googleusercontent\.com/#i', (string) $t['picture']) ? (string) $t['picture'] : '';
    $r = ssoArrive(strtolower((string) $t['email']), (string) ($t['name'] ?? ''), 'google', $pic);
    if (!empty($r['pending'])) out(403, ['error' => 'pending', 'email' => strtolower((string) $t['email'])]);
    out(200, ['me' => ccPublic(ccMe())]);
}
/* Google with a client secret: the page goes to Google and comes back here with a code, which the server swaps for the person's details */
if ($action === 'g_start') {
    $cid = ccGoogleId(); if ($cid === '' || ccGoogleSecret() === '') { header('Location: ' . ccAppUrl(true) . '#signin=g_off'); exit; }
    $state = bin2hex(random_bytes(16)); $nonce = bin2hex(random_bytes(16)); $ver = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    $_SESSION['goog'] = ['state' => $state, 'nonce' => $nonce, 'ver' => $ver, 'at' => time()];
    $q = http_build_query(['client_id' => $cid, 'response_type' => 'code', 'redirect_uri' => ccHereUrl() . 'auth.php?a=g_cb', 'scope' => 'openid email profile',
        'state' => $state, 'nonce' => $nonce, 'code_challenge' => rtrim(strtr(base64_encode(hash('sha256', $ver, true)), '+/', '-_'), '='), 'code_challenge_method' => 'S256', 'prompt' => 'select_account']);
    header('Location: ' . (defined('GOOGLE_AUTHORIZE_URL') ? GOOGLE_AUTHORIZE_URL : 'https://accounts.google.com/o/oauth2/v2/auth') . '?' . $q); exit;
}
if ($action === 'g_cb') {
    $back = fn(string $r) => (header('Location: ' . ccAppUrl(true) . '#signin=' . $r) ?: exit);
    $g = $_SESSION['goog'] ?? null; unset($_SESSION['goog']);
    if (!$g || !hash_equals((string) $g['state'], (string) ($_GET['state'] ?? '')) || time() - (int) $g['at'] > 600) $back('g_failed');
    if (empty($_GET['code'])) $back(isset($_GET['error']) && $_GET['error'] === 'access_denied' ? 'g_cancelled' : 'g_failed');
    if (!function_exists('curl_init')) $back('g_failed');
    $ch = curl_init(defined('GOOGLE_TOKEN_URL') ? GOOGLE_TOKEN_URL : 'https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15, CURLOPT_POSTFIELDS => http_build_query([
        'client_id' => ccGoogleId(), 'client_secret' => ccGoogleSecret(), 'grant_type' => 'authorization_code', 'code' => (string) $_GET['code'],
        'redirect_uri' => ccHereUrl() . 'auth.php?a=g_cb', 'code_verifier' => (string) $g['ver']])]);
    $res = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    $j = $code === 200 ? json_decode((string) $res, true) : null;
    $parts = explode('.', (string) ($j['id_token'] ?? ''));
    $c = count($parts) === 3 ? json_decode((string) base64_decode(strtr($parts[1], '-_', '+/')), true) : null;   // came straight from Google over HTTPS, so the claims can be trusted
    if (!is_array($c) || ($c['aud'] ?? '') !== ccGoogleId() || !in_array($c['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true)
        || !hash_equals((string) $g['nonce'], (string) ($c['nonce'] ?? '')) || (int) ($c['exp'] ?? 0) < time()) $back('g_failed');
    if (!in_array($c['email_verified'] ?? false, [true, 'true'], true)) $back('g_unverified');
    $email = strtolower((string) ($c['email'] ?? '')); if (!emailOk($email)) $back('g_failed');
    $pic = !empty($c['picture']) && preg_match('#^https://[a-z0-9.-]+\.googleusercontent\.com/#i', (string) $c['picture']) ? (string) $c['picture'] : '';
    $r = ssoArrive($email, (string) ($c['name'] ?? ''), 'google', $pic);
    $back(!empty($r['pending']) ? 'pending' : 'ok');
}
/* Microsoft: the page goes to Microsoft and comes back here with a code, which the server swaps for the person's details */
if ($action === 'ms_start') {
    $cid = ccMsId(); if ($cid === '') { header('Location: ' . ccAppUrl(true) . '#signin=ms_off'); exit; }
    $state = bin2hex(random_bytes(16)); $nonce = bin2hex(random_bytes(16)); $ver = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    $_SESSION['ms'] = ['state' => $state, 'nonce' => $nonce, 'ver' => $ver, 'at' => time()];
    $q = http_build_query(['client_id' => $cid, 'response_type' => 'code', 'redirect_uri' => ccHereUrl() . 'auth.php?a=ms_cb', 'response_mode' => 'query', 'scope' => 'openid email profile',
        'state' => $state, 'nonce' => $nonce, 'code_challenge' => rtrim(strtr(base64_encode(hash('sha256', $ver, true)), '+/', '-_'), '='), 'code_challenge_method' => 'S256', 'prompt' => 'select_account']);
    header('Location: ' . (defined('MS_AUTHORIZE_URL') ? MS_AUTHORIZE_URL : 'https://login.microsoftonline.com/' . ccMsTenant() . '/oauth2/v2.0/authorize') . '?' . $q); exit;
}
if ($action === 'ms_cb') {
    $back = fn(string $r) => (header('Location: ' . ccAppUrl(true) . '#signin=' . $r) ?: exit);
    $ms = $_SESSION['ms'] ?? null; unset($_SESSION['ms']);
    if (!$ms || !hash_equals((string) $ms['state'], (string) ($_GET['state'] ?? '')) || time() - (int) $ms['at'] > 600) $back('ms_failed');
    if (empty($_GET['code'])) $back(isset($_GET['error']) && $_GET['error'] === 'access_denied' ? 'ms_cancelled' : 'ms_failed');
    $ch = curl_init(defined('MS_TOKEN_URL') ? MS_TOKEN_URL : 'https://login.microsoftonline.com/' . ccMsTenant() . '/oauth2/v2.0/token');
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15, CURLOPT_POSTFIELDS => http_build_query([
        'client_id' => ccMsId(), 'client_secret' => (string) ccConf('MS_CLIENT_SECRET'), 'grant_type' => 'authorization_code', 'code' => (string) $_GET['code'],
        'redirect_uri' => ccHereUrl() . 'auth.php?a=ms_cb', 'code_verifier' => (string) $ms['ver'], 'scope' => 'openid email profile'])]);
    $res = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    $j = $code === 200 ? json_decode((string) $res, true) : null;
    $parts = explode('.', (string) ($j['id_token'] ?? ''));
    $c = count($parts) === 3 ? json_decode((string) base64_decode(strtr($parts[1], '-_', '+/')), true) : null;   // came straight from Microsoft over HTTPS, so the claims can be trusted
    if (!is_array($c) || ($c['aud'] ?? '') !== ccMsId() || !hash_equals((string) $ms['nonce'], (string) ($c['nonce'] ?? '')) || (int) ($c['exp'] ?? 0) < time()) $back('ms_failed');
    $email = strtolower((string) ($c['email'] ?? $c['preferred_username'] ?? ''));
    if (!emailOk($email)) $back('ms_noemail');
    $r = ssoArrive($email, (string) ($c['name'] ?? ''), 'microsoft');
    $back(!empty($r['pending']) ? 'pending' : 'ok');
}
/* any OpenID Connect provider: the same round trip as Microsoft, with the details read from the provider's discovery document */
if ($action === 'oidc_start') {
    $m = ccOidcMeta(); if (!$m) { header('Location: ' . ccAppUrl(true) . '#signin=sso_off'); exit; }
    $state = bin2hex(random_bytes(16)); $nonce = bin2hex(random_bytes(16)); $ver = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    $_SESSION['oidc'] = ['state' => $state, 'nonce' => $nonce, 'ver' => $ver, 'at' => time()];
    $scope = defined('OIDC_SCOPES') && OIDC_SCOPES ? (string) OIDC_SCOPES : 'openid email profile';
    $q = http_build_query(['client_id' => (string) ccConf('OIDC_CLIENT_ID'), 'response_type' => 'code', 'redirect_uri' => ccHereUrl() . 'auth.php?a=oidc_cb', 'scope' => $scope,
        'state' => $state, 'nonce' => $nonce, 'code_challenge' => rtrim(strtr(base64_encode(hash('sha256', $ver, true)), '+/', '-_'), '='), 'code_challenge_method' => 'S256']);
    $ep = (string) $m['authorization_endpoint'];
    header('Location: ' . $ep . (strpos($ep, '?') === false ? '?' : '&') . $q); exit;
}
if ($action === 'oidc_cb') {
    $back = fn(string $r) => (header('Location: ' . ccAppUrl(true) . '#signin=' . $r) ?: exit);
    $st = $_SESSION['oidc'] ?? null; unset($_SESSION['oidc']);
    if (!$st || !hash_equals((string) $st['state'], (string) ($_GET['state'] ?? '')) || time() - (int) $st['at'] > 600) $back('sso_failed');
    if (empty($_GET['code'])) $back(isset($_GET['error']) && $_GET['error'] === 'access_denied' ? 'sso_cancelled' : 'sso_failed');
    $m = ccOidcMeta(); if (!$m) $back('sso_failed');
    $post = ['grant_type' => 'authorization_code', 'code' => (string) $_GET['code'], 'redirect_uri' => ccHereUrl() . 'auth.php?a=oidc_cb', 'code_verifier' => (string) $st['ver'],
             'client_id' => (string) ccConf('OIDC_CLIENT_ID'), 'client_secret' => (string) ccConf('OIDC_CLIENT_SECRET')];
    $basic = in_array('client_secret_basic', (array) ($m['token_endpoint_auth_methods_supported'] ?? ['client_secret_basic']), true) && !in_array('client_secret_post', (array) ($m['token_endpoint_auth_methods_supported'] ?? []), true);
    $ch = curl_init((string) $m['token_endpoint']);
    $opt = [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15, CURLOPT_HTTPHEADER => ['Accept: application/json']];
    if ($basic) { $opt[CURLOPT_USERPWD] = rawurlencode((string) ccConf('OIDC_CLIENT_ID')) . ':' . rawurlencode((string) ccConf('OIDC_CLIENT_SECRET')); unset($post['client_secret']); }
    $opt[CURLOPT_POSTFIELDS] = http_build_query($post); curl_setopt_array($ch, $opt);
    $res = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    $j = $code === 200 ? json_decode((string) $res, true) : null;
    $parts = explode('.', (string) ($j['id_token'] ?? ''));
    $c = count($parts) === 3 ? json_decode((string) base64_decode(strtr($parts[1], '-_', '+/')), true) : null;   // straight from the provider's token endpoint over HTTPS
    $aud = (array) ($c['aud'] ?? []);
    if (!is_array($c) || !in_array((string) ccConf('OIDC_CLIENT_ID'), $aud, true) || rtrim((string) ($c['iss'] ?? ''), '/') !== rtrim((string) ($m['issuer'] ?? ccConf('OIDC_ISSUER')), '/')
        || !hash_equals((string) $st['nonce'], (string) ($c['nonce'] ?? '')) || (int) ($c['exp'] ?? 0) < time()) $back('sso_failed');
    $email = (string) ($c['email'] ?? ''); $name = (string) ($c['name'] ?? trim(($c['given_name'] ?? '') . ' ' . ($c['family_name'] ?? ''))); $verified = $c['email_verified'] ?? null;
    if ($email === '' && !empty($m['userinfo_endpoint']) && !empty($j['access_token'])) {
        $ch = curl_init((string) $m['userinfo_endpoint']); curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $j['access_token'], 'Accept: application/json']]);
        $ui = json_decode((string) curl_exec($ch), true); curl_close($ch);
        if (is_array($ui) && ($ui['sub'] ?? null) === ($c['sub'] ?? '')) { $email = (string) ($ui['email'] ?? ''); $verified = $ui['email_verified'] ?? $verified; if ($name === '') $name = (string) ($ui['name'] ?? ''); }
    }
    if ($verified === false || $verified === 'false') $back('sso_unverified');
    $email = strtolower($email);
    if (!emailOk($email)) $back('sso_noemail');
    $r = ssoArrive($email, $name, 'oidc');
    $back(!empty($r['pending']) ? 'pending' : 'ok');
}
if ($action === 'password') {
    if (!ccPasswordAllowed()) out(403, ['error' => 'password_off']);
    if (ccLocked('admin')) out(429, ['error' => 'locked']);
    if (!hash_equals((string) TEACHER_PASSWORD, (string) ($body['password'] ?? ''))) { ccFail('admin'); out(401, ['error' => 'wrong_password']); }
    ccPassed('admin'); signIn('admin@password');
    out(200, ['me' => ccPublic(ccMe())]);
}
if ($action === 'logout') { $_SESSION = []; if (!headers_sent()) setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax', 'secure' => ccHttps()]); session_destroy(); out(200, ['ok' => true]); }

/* ---------- signed in ---------- */
$me = ccMe();
if (!$me) out(401, ['error' => 'sign_in']);
$a = ccAccounts();
$mine = $a['users'][$me['email']] ?? [];
/* something went wrong in someone's browser: keep a line of it for the super admin. What the page was doing, never what was typed */
if ($action === 'errlog') {
    $n = (int) ($_SESSION['errs'] ?? 0); if ($n >= 30) out(200, ['ok' => true, 'kept' => false]); $_SESSION['errs'] = $n + 1;
    $msg = trim((string) ($body['msg'] ?? '')); if ($msg === '') out(200, ['ok' => true, 'kept' => false]);
    ccErr('browser', $msg, ['where' => (string) ($body['where'] ?? ''), 'page' => (string) ($body['page'] ?? ''), 'role' => $me['role'], 'dept' => !empty($me['demo']) ? 'demo' : (string) $me['deptName'], 'browser' => (string) ($body['browser'] ?? '')]);
    out(200, ['ok' => true, 'kept' => true]);
}
if ($action === 'inbox' || $action === 'inboxset' || $action === 'inboxreply') {
    if ($me['role'] !== 'superadmin') out(403, ['error' => 'noperm']);
    /* answer a message from here: it's emailed to the person, and kept under their message */
    if ($action === 'inboxreply') {
        $id = preg_replace('/[^0-9a-f]/', '', (string) ($body['id'] ?? '')); $box = ccInbox(); $m = $box[$id] ?? null;
        $text = mb_substr(trim((string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/u', '', (string) ($body['text'] ?? ''))), 0, 4000);
        if (!$m) out(404, ['error' => 'gone']);
        if (mb_strlen($text) < 2) out(400, ['error' => 'short']);
        if (!ccMailReady()) out(400, ['error' => 'nomail']);
        if (count((array) ($m['replies'] ?? [])) >= 20) out(400, ['error' => 'many']);
        $contact = (string) (ccSite($a)['contact'] ?? ''); $from = $contact !== '' ? $contact : (filter_var($me['email'], FILTER_VALIDATE_EMAIL) ? $me['email'] : '');
        $first = explode(' ', trim((string) $m['name']))[0]; $quote = '> ' . str_replace("\n", "\n> ", mb_substr((string) $m['msg'], 0, 1500));
        $mail = "Hello $first,\n\n$text\n\n" . (string) $me['name'] . "\nCrafticulum\n\n\nOn " . date('j F Y', (int) $m['at']) . " you wrote:\n$quote\n";
        if (!ccMail((string) $m['email'], 'Re: your message to Crafticulum', $mail, $from)) out(502, ['error' => 'mail', 'why' => (string) ($GLOBALS['ccMailErr'] ?? '')]);
        ccInboxChange(function ($j) use ($id, $text, $me, $body) { if (!isset($j[$id])) return $j; $j[$id]['replies'] = array_merge((array) ($j[$id]['replies'] ?? []), [['at' => time(), 'by' => (string) $me['name'], 'text' => $text]]); if (!empty($body['done'])) $j[$id]['done'] = true; return $j; });
    }
    if ($action === 'inboxset') { $id = preg_replace('/[^0-9a-f]/', '', (string) ($body['id'] ?? ''));
        ccInboxChange(function ($j) use ($id, $body) { if (!isset($j[$id])) return $j; if (!empty($body['del'])) unset($j[$id]); else $j[$id]['done'] = !empty($body['done']); return $j; }); }
    $l = array_values(ccInbox()); usort($l, fn($x, $y) => ($y['at'] ?? 0) <=> ($x['at'] ?? 0));
    out(200, ['inbox' => $l, 'mail' => ccMailReady(), 'to' => (string) (ccSite($a)['contact'] ?? '')]);
}
if ($action === 'errors' || $action === 'errclear') {
    if ($me['role'] !== 'superadmin') out(403, ['error' => 'noperm']);
    if ($action === 'errclear') @unlink(ccDataDir() . '/_errors.jsonl');
    out(200, ['errors' => ccErrRead(200)]);
}
/* the demo account can look around and save lessons in its own department. It can't touch settings, people, email or sign-in */
if (!empty($me['demo']) && !in_array($action, ['me', 'team', 'log', 'usage'], true)) out(403, ['error' => 'demo']);
if ($action === 'me') {
    register_shutdown_function(function () { try { if (session_status() === PHP_SESSION_ACTIVE) session_write_close(); if (function_exists('fastcgi_finish_request')) @fastcgi_finish_request(); ccPushDaily(); ccDeletedPurge(); } catch (\Throwable $x) {} });
    $o = ['me' => ccPublic($me) + ['methods' => (array) ($mine['methods'] ?? []), 'hasPass' => !empty($mine['pass']), 'totp' => !empty($mine['totp']['on']), 'recoveryLeft' => count((array) ($mine['totp']['recovery'] ?? [])), 'profile' => (object) (is_array($mine['profile'] ?? null) ? $mine['profile'] : []), 'added' => (int) ($mine['added'] ?? 0), 'lastSeen' => (int) ($mine['last'] ?? 0)],
          'google' => ccGoogleId() !== '', 'microsoft' => ccMsId() !== '', 'oidc' => ccOidcOn() ? ccOidcName() : null, 'mail' => ccMailReady(), 'passwordBackup' => ccPasswordAllowed(), 'prefs' => ccDeptPrefs($a, $me['dept']), 'marker' => ccMarker($a, $me['dept']) + ['can' => ccMarkerCan($me, $a), 'ns' => '.' . substr(sha1((string) $me['email']), 0, 10)], 'site' => !empty($me['demo']) || empty($me['inside'] ?? true) ? ccSitePublic($a) : ccSite($a, $me['college']), 'url' => ccSiteUrl(), 'tempHost' => (string) parse_url(ccTempUrl(), PHP_URL_HOST)];
    $pl = ccPlanOf($a, (string) $me['college']); $o['billing'] = ccBillingOn(); $pu = ccPlanUsed((string) $me['college'], ccUsageRead()); $o['plan'] = ['id' => $pl['id'], 'name' => $pl['name'], 'builds' => $pl['builds'], 'checks' => $pl['checks'], 'usedBuilds' => $pu['builds'], 'usedChecks' => $pu['checks']];
    $o['needVerify'] = !empty($o['billing']) && !empty($a['colleges'][(string) $me['college']]['self']) && ccMailReady() && empty($a['users'][(string) $me['email']]['verified']); $o['selfStart'] = !empty($a['colleges'][(string) $me['college']]['self']);
    if ($me['role'] === 'superadmin') { $o['depts'] = deptList($a); $o['inbox'] = ccInboxOpen(); $o['colleges'] = collegeList($a); }
    elseif ($me['role'] === 'owner') $o['depts'] = deptList($a, $me['college']);
    if ($me['canTeam']) $o['pending'] = count(array_filter($a['users'], fn($u) => !ccActive($u) && canManage($u, $me, $a)));
    out(200, $o);
}
if ($action === 'dept') {
    if (empty($me['admin'])) out(403, ['error' => 'noperm']);
    $id = (string) ($body['id'] ?? ''); if (!isset($a['depts'][$id])) out(404, ['error' => 'not_found']);
    if ($me['role'] === 'owner' && ($id === CC_DEMO_DEPT || ccCollegeOf($a, $id) !== ccCollegeOf($a, (string) ($mine['dept'] ?? '')))) out(403, ['error' => 'noperm']);   // a manager stays inside their own college
    $_SESSION['dept'] = $id; if ($me['role'] === 'superadmin') $_SESSION['inside'] = 1;
    if (isset($a['users'][$me['email']])) { $a['users'][$me['email']]['dept'] = $id; ccSaveAccounts($a); }
    out(200, ['me' => ccPublic(ccMe())]);
}
/* ---------- notifications on this person's own devices ---------- */
if (in_array($action, ['pushget', 'pushsub', 'pushunsub', 'pushprefs', 'pushtest', 'pushdates'], true)) {
    if (!empty($me['demo'])) out(403, ['error' => 'demo']);
    $em = (string) $me['email']; $pj = ccPushRead($em); $v = ccPushOk() ? ccVapid() : null;
    $info = function (array $pj) use ($v, $me): array { return ['ok' => (bool) $v, 'key' => $v ? $v['pub'] : '', 'prefs' => ccPushPrefs($pj), 'devices' => array_map(fn($s) => ['id' => substr(sha1((string) $s['endpoint']), 0, 12), 'ua' => (string) ($s['ua'] ?? ''), 'added' => (int) ($s['added'] ?? 0)], $pj['subs']), 'dates' => count((array) $pj['dates'])] + ($me['role'] === 'superadmin' ? ['cron' => ccSiteUrl() . 'auth.php?a=pushcron&k=' . substr(hash('sha256', 'cron|' . ccSiteId()), 0, 32)] : []); };
    if ($action === 'pushget') out(200, $info($pj));
    if (!$v) out(400, ['error' => 'unsupported']);
    if ($action === 'pushsub') {
        $s = (array) ($body['sub'] ?? []); $ep = (string) ($s['endpoint'] ?? ''); $k = (array) ($s['keys'] ?? []); $p = (string) ($k['p256dh'] ?? ''); $au = (string) ($k['auth'] ?? '');
        if (!ccPushHostOk($ep) || strlen(ccB64ud($p)) !== 65 || strlen(ccB64ud($au)) < 16 || strlen($au) > 64) out(400, ['error' => 'sub']);
        $pj['subs'] = array_values(array_filter($pj['subs'], fn($x) => ($x['endpoint'] ?? '') !== $ep));
        $pj['subs'][] = ['endpoint' => $ep, 'p256dh' => $p, 'auth' => $au, 'ua' => mb_substr(cleanName((string) ($body['ua'] ?? '')), 0, 40), 'added' => time()];
        $pj['subs'] = array_slice($pj['subs'], -8); if (!ccPushSave($em, $pj)) out(500, ['error' => 'save']);
        out(200, $info($pj) + ['this' => substr(sha1($ep), 0, 12)]);
    }
    if ($action === 'pushunsub') { $id = (string) ($body['id'] ?? ''); $ep = (string) ($body['endpoint'] ?? ''); $pj['subs'] = array_values(array_filter($pj['subs'], fn($x) => ($x['endpoint'] ?? '') !== $ep && substr(sha1((string) ($x['endpoint'] ?? '')), 0, 12) !== $id)); ccPushSave($em, $pj); out(200, $info($pj)); }
    if ($action === 'pushprefs') { foreach (CC_PUSH_KINDS as $k => $def) if (array_key_exists($k, (array) ($body['prefs'] ?? []))) $pj['prefs'][$k] = !empty($body['prefs'][$k]); ccPushSave($em, $pj); out(200, $info($pj)); }
    if ($action === 'pushdates') {   // this person's assignment dates for the weeks ahead, as their own screen shows them
        $out = []; foreach (array_slice((array) ($body['items'] ?? []), 0, 300) as $d) { $day = (string) ($d['d'] ?? ''); $k = (string) ($d['k'] ?? ''); if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $day) || !in_array($k, ['out', 'due', 'resub'], true)) continue; $out[] = ['d' => $day, 'k' => $k, 't' => mb_substr(cleanName((string) ($d['t'] ?? '')), 0, 60), 'u' => mb_substr(cleanName((string) ($d['u'] ?? '')), 0, 60)]; }
        $pj['dates'] = $out; ccPushSave($em, $pj); out(200, ['dates' => count($out)]);
    }
    if ($action === 'pushtest') { if (!$pj['subs']) out(400, ['error' => 'none']); $n = ccPushNow($em, 'builds', 'Notifications are working', 'This is what a Crafticulum notification looks like on this device.', ccAppUrl(), true); out(200, ['sent' => $n, 'devices' => count(ccPushRead($em)['subs'])]); }
}
/* the super admin steps back out of the college they were working inside */
if ($action === 'leave') {
    if ($me['role'] !== 'superadmin') out(403, ['error' => 'noperm']);
    unset($_SESSION['inside'], $_SESSION['dept']);
    out(200, ['me' => ccPublic(ccMe())]);
}
$selfOk = $me['email'] !== 'admin@password';
if ($action === 'setpass') {
    if (!$selfOk) out(400, ['error' => 'admin']);
    if (!empty($mine['pass']) && !password_verify((string) ($body['current'] ?? ''), (string) $mine['pass'])) { ccFail($me['email']); out(401, ['error' => 'current']); }
    $p = (string) ($body['password'] ?? ''); if ($e = passOk($p, $me['email'])) out(400, ['error' => 'pass_' . $e]);
    $u = $a['users'][$me['email']] ?? ['role' => 'superadmin', 'dept' => $me['dept']];
    $u['pass'] = password_hash($p, PASSWORD_DEFAULT); $u['methods'] = array_values(array_unique(array_merge((array) ($u['methods'] ?? []), ['password'])));
    $a['users'][$me['email']] = $u; ccSaveAccounts($a); out(200, ['ok' => true]);
}
/* the department's rules, set by its head of department or subject leader: sign-off for teachers' changes, and a password for deleting lessons */
if ($action === 'deptprefs') {
    if (!$me['isLead']) out(403, ['error' => 'noperm']);
    $d = $me['dept'];
    if (array_key_exists('signoff', $body)) { $a['depts'][$d]['signoff'] = !empty($body['signoff']); ccLog($d, $me, !empty($body['signoff']) ? 'turned on sign-off for teachers\' changes' : 'turned off sign-off'); }
    if (array_key_exists('pics', $body)) { $a['depts'][$d]['pics'] = !empty($body['pics']); ccLog($d, $me, !empty($body['pics']) ? 'turned on pictures in lessons' : 'turned off pictures in lessons'); }
    if (array_key_exists('base', $body)) { $a['depts'][$d]['base'] = !empty($body['base']); ccLog($d, $me, !empty($body['base']) ? 'turned on starting from the team\'s lessons' : 'turned off starting from the team\'s lessons'); }
    if (array_key_exists('delPassword', $body)) { $p = (string) $body['delPassword'];
        if ($p === '') { unset($a['depts'][$d]['delHash']); ccLog($d, $me, 'removed the delete password'); }
        else { if (strlen($p) < 6 || strlen($p) > 100) out(400, ['error' => 'pass_short']); $a['depts'][$d]['delHash'] = password_hash($p, PASSWORD_DEFAULT); ccLog($d, $me, 'set the delete password'); } }
    if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
    out(200, ['prefs' => ccDeptPrefs($a, $d)]);
}
/* Second Marker: switched on and set up by the department's leader */
if ($action === 'markerset') {
    if (!$me['isLead']) out(403, ['error' => 'noperm']);
    $d = $me['dept']; $was = ccMarker($a, $d);
    $dom = strtolower(trim((string) ($body['emailDomain'] ?? ''))); $dom = ltrim($dom, '@'); if ($dom !== '' && !preg_match('/^[a-z0-9.-]{3,80}$/', $dom)) out(400, ['error' => 'domain']);
    $guide = trim((string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', (string) ($body['guide'] ?? ''))); if (mb_strlen($guide) > 3000) out(400, ['error' => 'long']);
    $a['depts'][$d]['marker'] = ['on' => !empty($body['on']), 'who' => ($body['who'] ?? '') === 'all' ? 'all' : 'leads', 'model' => in_array($body['model'] ?? '', CC_MARKER_MODELS, true) ? (string) $body['model'] : '',
        'batch' => !empty($body['batch']), 'iv' => !empty($body['iv']), 'anon' => !empty($body['anon']), 'pdfText' => !empty($body['pdfText']), 'feedback' => !empty($body['feedback']), 'emailDomain' => $dom, 'guide' => $guide, 'ivTarget' => max(0, min(100, (int) ($body['ivTarget'] ?? ($was['ivTarget'] ?? 0))))];
    if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
    $now = ccMarker($a, $d);
    ccLog($d, $me, $now['on'] && !$was['on'] ? 'switched on Second Marker' : (!$now['on'] && $was['on'] ? 'switched off Second Marker' : 'changed the Second Marker settings'));
    out(200, ['marker' => $now + ['can' => ccMarkerCan($me, $a), 'ns' => '.' . substr(sha1((string) $me['email']), 0, 10)]]);
}
/* ---------- the site: the college's name, logo and colour, and the sign-in, email and Crafticulum AI settings (super admin) ---------- */
if ($action === 'siteget' || $action === 'siteset') {
    /* the college's own profile (name, logo, colour, contact) belongs to the college the person is working in: its manager or the super admin can change it.
       Everything else here is about the whole site, and only the super admin sees or changes it. */
    if (empty($me['admin'])) out(403, ['error' => 'noperm']);
    $super = $me['role'] === 'superadmin'; $cid = (string) $me['college'];
    $site = (array) ($a['site'] ?? []); $conf = (array) ($site['conf'] ?? []);
    if ($action === 'siteset') {
        if (!$super && array_diff(array_keys($body), ['name', 'contact', 'color', 'logo'])) out(403, ['error' => 'noperm']);
        $col = (array) ($a['colleges'][$cid] ?? []);
        if (array_key_exists('name', $body)) $col['name'] = cleanName((string) $body['name']);
        if (array_key_exists('contact', $body)) { $c = strtolower(trim((string) $body['contact'])); $col['contact'] = $c !== '' && emailOk($c) ? $c : ''; }
        if (array_key_exists('front', $body)) $site['front'] = !empty($body['front']);
        if (array_key_exists('publicBrand', $body)) $site['publicBrand'] = !empty($body['publicBrand']);
        if (array_key_exists('org', $body)) $site['org'] = cleanName((string) $body['org']);
        if (array_key_exists('siteContact', $body)) { $c = strtolower(trim((string) $body['siteContact'])); $site['contact'] = $c !== '' && emailOk($c) ? $c : ''; }
        if (array_key_exists('color', $body)) { $c = (string) $body['color']; $col['color'] = preg_match('/^#[0-9a-fA-F]{6}$/', $c) ? strtolower($c) : ''; }
        if (array_key_exists('logo', $body)) { $l = (string) $body['logo']; if ($l !== '' && (!preg_match('#^data:image/(png|jpeg|webp|svg\+xml);base64,[A-Za-z0-9+/=]+$#', $l) || strlen($l) > 300000)) out(400, ['error' => 'logo']); $col['logo'] = $l; }
        if ($cid !== '') $a['colleges'][$cid] = $col;
        if (is_array($body['conf'] ?? null)) foreach ($body['conf'] as $k => $v) { if (!in_array($k, CC_CONF_KEYS, true) || !is_scalar($v)) continue; $v = trim((string) $v);
            if ($v === '' && in_array($k, CC_CONF_SECRET, true) && empty($body['clear'][$k])) continue;   // an empty secret box means "leave it as it is"
            if (strlen($v) > 400) out(400, ['error' => 'long']);
            if ($v === '') unset($conf[$k]); else $conf[$k] = in_array($k, ['MAX_REQUESTS_PER_HOUR', 'SMTP_PORT', 'SESSION_DAYS'], true) ? max(1, min($k === 'SESSION_DAYS' ? 365 : 1000000, (int) $v)) : ($k === 'DEMO_BUILDS' ? max(0, min(500, (int) $v)) : $v);
            if ($k === 'BACKUP_URL' && isset($conf[$k]) && !preg_match('#^https://#i', (string) $conf[$k]) && !preg_match('#^http://(127\.0\.0\.1|localhost)[:/]#i', (string) $conf[$k])) out(400, ['error' => 'backupurl']); }
        $site['conf'] = $conf; $a['site'] = $site; if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
        ccLog($me['dept'], $me, $super ? 'changed the site settings' : 'changed the college\'s profile');
    }
    $inCol = fn($u) => ccCollegeOf($a, (string) ($u['dept'] ?? '')) === $cid;
    if (!$super) out(200, ['site' => ccSite($a, $cid), 'conf' => (object) [], 'mail' => ccMailReady(), 'scope' => 'college',
        'counts' => ['people' => 1 + count(array_filter($a['users'], fn($u, $e) => ccActive($u) && empty($u['demo']) && $e !== $me['email'] && $inCol($u), ARRAY_FILTER_USE_BOTH)), 'depts' => count(ccCollegeDepts($a, $cid))]]);
    $o = [];
    foreach (CC_CONF_KEYS as $k) { $sv = $conf[$k] ?? ''; $cv = defined($k) ? constant($k) : ''; $secret = in_array($k, CC_CONF_SECRET, true);
        $cset = $cv !== '' && $cv !== 'sk-ant-REPLACE-ME';
        $o[$k] = ['from' => $sv !== '' ? 'screen' : ($cset ? 'config' : ''), 'value' => $secret ? '' : (string) ($sv !== '' ? $sv : $cv), 'set' => $sv !== '' || $cset]; }
    if ($action === 'siteset') @unlink(ccDataDir() . '/_mainlive.json');
    out(200, ['site' => ccSite($a, $cid) + ['siteContact' => (string) ($site['contact'] ?? ''), 'orgSet' => (string) ($site['org'] ?? ''), 'publicBrandSet' => !empty($site['publicBrand'])], 'scope' => 'site', 'conf' => $o, 'url' => ccHereUrl(), 'mail' => ccMailReady(), 'main' => ccMainUrl(), 'temp' => ccTempUrl(), 'live' => ccMainLive($action === 'siteset' || !empty($body['check'])), 'links' => ccSiteUrl(),
               'counts' => ['people' => 1 + count(array_filter($a['users'], fn($u, $e) => ccActive($u) && empty($u['demo']) && $e !== $me['email'], ARRAY_FILTER_USE_BOTH)), 'depts' => count($a['depts'])],
               'off' => ['push' => trim((string) ccConf('BACKUP_URL')) !== '', 'link' => strlen((string) ccConf('BACKUP_TOKEN')) >= 24]]);
}
/* usage and cost: the super admin sees every department and sets the prices and limits; a leader sees their own department */
if ($action === 'usage' || $action === 'usageset') {
    if (!$me['isLead']) out(403, ['error' => 'noperm']);
    $super = $me['role'] === 'superadmin';
    if ($action === 'usageset') {
        if (!$super) out(403, ['error' => 'noperm']);
        $num = fn($k, $max) => max(0, min($max, (float) str_replace(',', '.', (string) ($body[$k] ?? 0))));
        $cur = mb_substr(trim((string) preg_replace('/[\x00-\x1F<>"\'&]+/u', '', (string) ($body['cur'] ?? '£'))), 0, 3) ?: '£';
        $site = (array) ($a['site'] ?? []);
        $site['usage'] = ['in' => $num('in', 100000), 'out' => $num('out', 100000), 'cur' => $cur, 'capCost' => $num('capCost', 10000000), 'capCalls' => (int) $num('capCalls', 100000000), 'capDept' => (int) $num('capDept', 100000000)];
        $a['site'] = $site; if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
        ccLog($me['dept'], $me, 'changed the usage limits');
    }
    $ym = (string) ($body['month'] ?? ''); if (!preg_match('/^\d{4}-\d{2}$/', $ym)) $ym = date('Y-m');
    $u = ccUsageRead($ym); $set = ccUsageSet($a); $priced = $set['in'] > 0 || $set['out'] > 0;
    $row = fn($r) => ['calls' => (int) ($r['calls'] ?? 0), 'in' => (int) ($r['in'] ?? 0), 'out' => (int) ($r['out'] ?? 0), 'cost' => $priced ? round(ccUsageCost($r, $set), 2) : null];
    $own = $me['role'] === 'owner'; $mineDepts = $own ? ccCollegeDepts($a, (string) $me['college']) : [$me['dept']]; $many = count(ccCollegeIds($a)) > 1;
    $depts = []; foreach ($u['depts'] as $id => $r) { $id = (string) $id; if (!$super && !in_array($id, $mineDepts, true)) continue; $depts[] = ['id' => $id, 'name' => (string) ($a['depts'][$id]['name'] ?? $id) . ($super && $many && isset($a['depts'][$id]) ? ' (' . ccCollege($a, ccCollegeOf($a, $id))['name'] . ')' : '')] + $row($r); }
    if ($super && !empty($u['site']['calls'])) $depts[] = ['id' => '', 'name' => 'Super admin (not part of a college)'] + $row((array) $u['site']);
    $users = []; foreach ($u['users'] as $e => $r) { if (!$super && !in_array((string) ($r['dept'] ?? ''), $mineDepts, true)) continue; $users[] = ['name' => (string) (($r['name'] ?? '') ?: $e), 'dept' => (string) ($a['depts'][$r['dept'] ?? '']['name'] ?? '')] + $row($r); }
    usort($depts, fn($x, $y) => $y['calls'] <=> $x['calls']); usort($users, fn($x, $y) => $y['calls'] <=> $x['calls']);
    $months = []; foreach (glob(ccDataDir() . '/_usage/*.json') ?: [] as $f) $months[] = basename($f, '.json'); if (!in_array(date('Y-m'), $months, true)) $months[] = date('Y-m'); rsort($months);
    $days = []; if ($super) foreach ($u['days'] as $d => $r) $days[(string) $d] = (int) ($r['calls'] ?? 0);
    $mine = $super ? $u['total'] : ['calls' => 0, 'in' => 0, 'out' => 0];
    if (!$super) foreach ($mineDepts as $d) foreach (['calls', 'in', 'out'] as $k) $mine[$k] += (int) ($u['depts'][$d][$k] ?? 0);
    out(200, ['month' => $ym, 'months' => array_slice($months, 0, 12), 'total' => $row($mine), 'depts' => $depts, 'users' => array_slice($users, 0, 60), 'days' => (object) $days, 'set' => $set, 'super' => $super, 'scope' => $super ? 'site' : ($own ? 'college' : 'dept'), 'collegeCap' => $super ? 0 : ccCollege($a, (string) $me['college'])['cap'], 'model' => (string) ($u['model'] ?? ''), 'marker' => $super && !empty($u['kinds']['marker']) ? $row($u['kinds']['marker']) : null, 'blocked' => $ym === date('Y-m') ? ccUsageBlocked($me) : '']);
}
if ($action === 'mailtest') { if ($me['role'] !== 'superadmin') out(403, ['error' => 'noperm']); $ok = ccMail($me['email'], 'Crafticulum email test', "Hello,\n\nEmail from Crafticulum is working.\n"); out(200, ['sent' => $ok, 'why' => (string) ($GLOBALS['ccMailErr'] ?? '')]); }
if ($action === 'log') { if (!$me['isLead']) out(403, ['error' => 'noperm']); out(200, ['log' => ccLogRead($me['dept'])]); }
/* your own details: only the name is shown to anyone else */
if ($action === 'profile') {
    if (!$selfOk) out(400, ['error' => 'admin']);
    $txt = fn(string $k, int $n) => mb_substr(cleanName((string) ($body[$k] ?? '')), 0, $n);
    $pick = fn(string $k, array $ok) => in_array((string) ($body[$k] ?? ''), $ok, true) ? (string) $body[$k] : '';
    $p = ['title' => $pick('title', ['Mr', 'Mrs', 'Miss', 'Ms', 'Mx', 'Dr', 'Prof']), 'first' => $txt('first', 40), 'last' => $txt('last', 40),
          'gender' => $pick('gender', ['male', 'female', 'other', 'undisclosed']), 'pronouns' => $txt('pronouns', 24), 'job' => $txt('job', 60), 'site' => $txt('site', 60),
          'phone' => substr(trim((string) preg_replace('/[^0-9+ ()\-]/', '', (string) ($body['phone'] ?? ''))), 0, 24)];
    if ($p['first'] === '') out(400, ['error' => 'first']);
    $u = $a['users'][$me['email']] ?? ['role' => 'superadmin', 'dept' => $me['dept']];
    $u['profile'] = $p; $u['name'] = trim($p['first'] . ' ' . $p['last']);
    $a['users'][$me['email']] = $u; if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
    out(200, ['ok' => true]);
}
if ($action === 'totp_start') {
    if (!$selfOk) out(400, ['error' => 'admin']);
    if (empty($mine['pass'])) out(400, ['error' => 'nopass']);
    $s = ccB32(random_bytes(20)); $_SESSION['totpNew'] = $s;
    out(200, ['secret' => $s, 'uri' => 'otpauth://totp/' . rawurlencode('Crafticulum:' . $me['email']) . '?secret=' . $s . '&issuer=' . rawurlencode('Crafticulum') . '&digits=6&period=30']);
}
if ($action === 'totp_on') {
    $s = (string) ($_SESSION['totpNew'] ?? ''); if ($s === '' || !$selfOk) out(400, ['error' => 'start']);
    if (!ccTotpOk($s, (string) ($body['code'] ?? ''))) out(400, ['error' => 'code']);
    $codes = []; $hashes = [];
    for ($i = 0; $i < 8; $i++) { $c = substr(bin2hex(random_bytes(5)), 0, 10); $codes[] = substr($c, 0, 5) . '-' . substr($c, 5); $hashes[] = hash('sha256', $c); }
    $u = $a['users'][$me['email']] ?? ['role' => 'superadmin', 'dept' => $me['dept']];
    $u['totp'] = ['on' => true, 'secret' => $s, 'recovery' => $hashes, 'since' => time()];
    $a['users'][$me['email']] = $u; ccSaveAccounts($a); unset($_SESSION['totpNew']);
    out(200, ['ok' => true, 'recovery' => $codes]);
}
if ($action === 'totp_off') {
    if (empty($mine['totp']['on'])) out(200, ['ok' => true]);
    if (!ccTotpOk((string) $mine['totp']['secret'], (string) ($body['code'] ?? '')) && !(!empty($mine['pass']) && password_verify((string) ($body['password'] ?? ''), (string) $mine['pass']))) out(401, ['error' => 'code']);
    unset($a['users'][$me['email']]['totp']); ccSaveAccounts($a); out(200, ['ok' => true]);
}

/* ---------- colleges (super admin): add one with its first department and its manager, rename it, limit it, switch it off, remove it ---------- */
function newDeptId(array $a, string $name): string { $id = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($name)), '-') ?: 'dept'; $id = substr($id, 0, 24); $base = $id; $n = 2; while (isset($a['depts'][$id]) || $id === CC_DEMO_DEPT) $id = $base . '-' . $n++; return $id; }
function collegeList(array $a): array { $o = [];
    foreach (ccCollegeIds($a) as $id) { $id = (string) $id; $c = ccCollege($a, $id); $depts = ccCollegeDepts($a, $id);
        $people = array_filter($a['users'], fn($u, $e) => ccActive($u) && empty($u['demo']) && !in_array((string) $e, ccSupers(), true) && in_array((string) ($u['dept'] ?? ''), $depts, true), ARRAY_FILTER_USE_BOTH);
        $mgr = []; foreach ($people as $e => $u) if (($u['role'] ?? '') === 'owner') $mgr[] = ['email' => (string) $e, 'name' => (string) ($u['name'] ?? '')];
        $o[] = ['id' => $id, 'name' => $c['name'], 'off' => $c['off'], 'cap' => $c['cap'], 'plain' => $c['plain'], 'plan' => $c['plan'], 'paid' => !empty($c['bill']['sub']), 'cancelled' => !empty($c['bill']['sub']) && !empty($c['bill']['cancelAt']), 'billStatus' => (string) ($c['bill']['status'] ?? ''), 'self' => !empty($a['colleges'][$id]['self']), 'contact' => $c['contact'], 'created' => $c['created'], 'hasLogo' => $c['logo'] !== '', 'color' => $c['color'],
                'depts' => array_map(fn($d) => ['id' => $d, 'name' => (string) $a['depts'][$d]['name']], $depts), 'people' => count($people), 'managers' => $mgr]; }
    return $o; }
/* ---------- the super admin: everything on the site at a glance ---------- */
if ($action === 'overview') {
    if ($me['role'] !== 'superadmin') out(403, ['error' => 'noperm']);
    $u = ccUsageRead(); $supers = ccSupers(); $rows = []; $tot = ['colleges' => 0, 'depts' => 0, 'people' => 0, 'lessons' => 0, 'calls' => 0, 'pending' => 0, 'paying' => 0]; $log = [];
    foreach (ccCollegeIds($a) as $id) { $id = (string) $id; $c = ccCollege($a, $id); $depts = ccCollegeDepts($a, $id); $people = 0; $last = 0; $pend = 0; $mgr = []; $lessons = 0; $calls = 0;
        foreach ($a['users'] as $e => $x) { if (in_array((string) $e, $supers, true) || !empty($x['demo']) || !in_array((string) ($x['dept'] ?? ($x['wantDept'] ?? '')), $depts, true)) continue;
            if (ccActive($x)) { $people++; $last = max($last, (int) ($x['last'] ?? 0)); if (($x['role'] ?? '') === 'owner') $mgr[] = (string) (($x['name'] ?? '') ?: $e); } elseif (($x['status'] ?? '') === 'pending') $pend++; }
        foreach ($depts as $d) { $dir = !empty($a['depts'][$d]['legacy']) ? ccDataDir() : ccDataDir() . '/d/' . $d; $lessons += count(glob($dir . '/lessons/*.json') ?: []); $calls += (int) ($u['depts'][$d]['calls'] ?? 0);
            foreach (ccLogRead($d, 12) as $l) $log[] = $l + ['college' => $c['name'], 'dept' => (string) $a['depts'][$d]['name']]; }
        $used = ccPlanUsed($id, $u);
        $rows[] = ['id' => $id, 'name' => $c['name'], 'off' => $c['off'], 'self' => !empty($a['colleges'][$id]['self']), 'plan' => $c['plan'], 'paid' => !empty($c['bill']['sub']), 'cancelled' => !empty($c['bill']['sub']) && !empty($c['bill']['cancelAt']), 'depts' => array_map(fn($d) => ['id' => $d, 'name' => (string) $a['depts'][$d]['name']], $depts),
                   'people' => $people, 'pending' => $pend, 'managers' => $mgr, 'lessons' => $lessons, 'calls' => $calls, 'builds' => $used['builds'], 'checks' => $used['checks'], 'last' => $last, 'created' => $c['created']];
        $tot['colleges']++; $tot['depts'] += count($depts); $tot['people'] += $people; $tot['lessons'] += $lessons; $tot['calls'] += $calls; $tot['pending'] += $pend; if (!empty($c['bill']['sub'])) $tot['paying']++; }
    usort($log, fn($x, $y) => ($y['at'] ?? 0) <=> ($x['at'] ?? 0));
    out(200, ['colleges' => $rows, 'total' => $tot + ['siteCalls' => (int) ($u['total']['calls'] ?? 0), 'own' => (int) ($u['site']['calls'] ?? 0), 'inbox' => ccInboxOpen()], 'log' => array_slice($log, 0, 25), 'billing' => ccBillingOn(), 'viewing' => ['college' => (string) $me['college'], 'dept' => (string) $me['dept']]]);
}
/* ---------- the super admin: one college, with everything in it ---------- */
if ($action === 'collegeget') {
    if ($me['role'] !== 'superadmin' && $me['role'] !== 'owner') out(403, ['error' => 'noperm']);
    $id = $me['role'] === 'owner' ? (string) $me['college'] : (string) ($body['id'] ?? ''); if (!isset($a['colleges'][$id])) out(404, ['error' => 'not_found']);   // a manager only ever gets their own college
    $c = ccCollege($a, $id); $ids = ccCollegeDepts($a, $id); $u = ccUsageRead(); $supers = ccSupers(); $depts = []; $people = []; $calls = 0; $lessons = 0; $log = [];
    foreach ($ids as $d) { $dir = !empty($a['depts'][$d]['legacy']) ? ccDataDir() : ccDataDir() . '/d/' . $d; $n = count(glob($dir . '/lessons/*.json') ?: []); $dc = (int) ($u['depts'][$d]['calls'] ?? 0); $calls += $dc; $lessons += $n;
        $depts[] = ['id' => $d, 'name' => (string) $a['depts'][$d]['name'], 'lessons' => $n, 'calls' => $dc, 'staff' => 0];
        foreach (ccLogRead($d, 8) as $l) $log[] = $l + ['dept' => (string) $a['depts'][$d]['name']]; }
    foreach ($a['users'] as $e => $x) { $e = (string) $e; $act = ccActive($x); $d = (string) ($act ? ($x['dept'] ?? '') : ($x['wantDept'] ?? ($x['dept'] ?? '')));
        if (in_array($e, $supers, true) || !empty($x['demo']) || !in_array($d, $ids, true) || (!$act && ($x['status'] ?? '') !== 'pending')) continue;
        $people[] = ['email' => $e, 'name' => (string) ($x['name'] ?? ''), 'role' => (string) ($x['role'] ?? 'teacher'), 'dept' => $d, 'last' => (int) ($x['last'] ?? 0), 'pending' => !$act];
        if ($act) foreach ($depts as &$dd) if ($dd['id'] === $d) $dd['staff']++; unset($dd); }
    usort($people, fn($x, $y) => [$x['pending'] ? 1 : 0, ['owner' => 0, 'hod' => 1][$x['role']] ?? 2, strtolower($x['name'] ?: $x['email'])] <=> [$y['pending'] ? 1 : 0, ['owner' => 0, 'hod' => 1][$y['role']] ?? 2, strtolower($y['name'] ?: $y['email'])]);
    usort($log, fn($x, $y) => ($y['at'] ?? 0) <=> ($x['at'] ?? 0));
    $pl = ccPlanOf($a, $id);
    out(200, ['planName' => $pl['name'], 'limits' => ['builds' => $pl['builds'], 'checks' => $pl['checks'], 'people' => $pl['people'], 'depts' => $pl['depts']], 'id' => $id, 'name' => $c['name'], 'contact' => $c['contact'], 'color' => $c['color'], 'logo' => $c['logo'], 'plain' => $c['plain'], 'off' => $c['off'], 'cap' => $c['cap'], 'plan' => $c['plan'], 'paid' => !empty($c['bill']['sub']), 'cancelled' => !empty($c['bill']['sub']) && !empty($c['bill']['cancelAt']), 'billStatus' => (string) ($c['bill']['status'] ?? ''),
              'self' => !empty($a['colleges'][$id]['self']), 'created' => $c['created'], 'depts' => $depts, 'people' => $people, 'lessons' => $lessons, 'calls' => $calls, 'used' => ccPlanUsed($id, $u), 'log' => array_slice($log, 0, 12), 'billing' => ccBillingOn(), 'mail' => ccMailReady()]);
}
/* a deleted college waits here for 14 days, then goes for good. Until then it can be put back */
if (in_array($action, ['collegerestore', 'collegepurge'], true)) {
    if ($me['role'] !== 'superadmin') out(403, ['error' => 'noperm']);
    $hid = (string) ($body['hold'] ?? ''); $hold = ccDataDir() . '/_deleted/' . $hid; if (!preg_match('/^[a-z0-9][a-z0-9-]*-\d{9,11}$/', $hid) || !is_file($hold . '/meta.json')) out(404, ['error' => 'not_found']);
    $meta = json_decode((string) @file_get_contents($hold . '/meta.json'), true); if (!is_array($meta)) out(404, ['error' => 'not_found']);
    if ($action === 'collegepurge') { ccDeletedRm($hold); ccLog($me['dept'], $me, 'removed a deleted college for good', (string) ($meta['name'] ?? '')); out(200, ['colleges' => collegeList($a), 'depts' => deptList($a), 'deleted' => ccDeletedList()]); }
    $cid = (string) ($meta['id'] ?? ''); if ($cid === '' || isset($a['colleges'][$cid])) out(409, ['error' => 'exists']);
    foreach ((array) ($meta['depts'] ?? []) as $d => $r) if (isset($a['depts'][(string) $d])) out(409, ['error' => 'exists']);
    foreach ($a['colleges'] as $c) if ((string) ($meta['name'] ?? '') !== '' && strcasecmp((string) ($c['name'] ?? ''), (string) $meta['name']) === 0) out(409, ['error' => 'name']);
    $a['colleges'][$cid] = (array) $meta['college']; foreach ((array) $meta['depts'] as $d => $r) $a['depts'][(string) $d] = (array) $r;
    $back = 0; $skipped = []; foreach ((array) ($meta['users'] ?? []) as $e => $u) { if (isset($a['users'][(string) $e])) { $skipped[] = (string) $e; continue; } $a['users'][(string) $e] = (array) $u; $back++; }   // anyone who has since joined somewhere else stays where they are
    $base = ccDataDir(); foreach ((array) ($meta['moved'] ?? []) as $rel) { $rel = (string) $rel; if ($rel === '' || strpos($rel, '..') !== false || $rel[0] === '/') continue; $from = $hold . '/files/' . $rel; $to = $base . '/' . $rel; if (!file_exists($from) || file_exists($to)) continue; if (!is_dir(dirname($to))) @mkdir(dirname($to), 0700, true); @rename($from, $to); }
    if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
    ccDeletedRm($hold); ccLog($me['dept'], $me, 'put a deleted college back', (string) ($meta['name'] ?? ''));
    out(200, ['colleges' => collegeList($a), 'depts' => deptList($a), 'deleted' => ccDeletedList(), 'restored' => ['id' => $cid, 'people' => $back, 'skipped' => $skipped]]);
}
if (in_array($action, ['colleges', 'collegeadd', 'collegeset', 'collegedel'], true)) {
    if ($me['role'] !== 'superadmin') out(403, ['error' => 'noperm']);
    $extra = [];
    if ($action === 'collegeadd') {
        $name = cleanName((string) ($body['name'] ?? '')); if ($name === '') out(400, ['error' => 'name']);
        foreach ($a['colleges'] as $c) if (strcasecmp((string) ($c['name'] ?? ''), $name) === 0) out(409, ['error' => 'exists']);
        $mgr = strtolower(trim((string) ($body['manager'] ?? '')));
        if ($mgr !== '') { if (!emailOk($mgr)) out(400, ['error' => 'email']); if (in_array($mgr, ccSupers(), true)) out(400, ['error' => 'is_super']); if (isset($a['users'][$mgr]) && ccActive($a['users'][$mgr])) out(409, ['error' => 'user_exists']); }
        $id = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($name)), '-') ?: 'college'; $id = substr($id, 0, 24); $base = $id; $n = 2; while (isset($a['colleges'][$id])) $id = $base . '-' . $n++;
        $a['colleges'][$id] = ['name' => $name, 'plain' => true, 'created' => time()];
        $dn = cleanName((string) ($body['dept'] ?? '')) ?: 'Main department'; $did = newDeptId($a, $name . ' ' . $dn);
        $a['depts'][$did] = ['name' => $dn, 'created' => time(), 'college' => $id];
        if ($mgr !== '') { $u = ($a['users'][$mgr] ?? []) + ['added' => time(), 'methods' => []];
            $a['users'][$mgr] = array_merge($u, ['status' => 'active', 'role' => 'owner', 'dept' => $did, 'canEdit' => true, 'addedBy' => $me['email']]); unset($a['users'][$mgr]['wantDept'], $a['users'][$mgr]['wantRole'], $a['users'][$mgr]['perms']);
            if (!empty($body['managerName'])) $a['users'][$mgr]['name'] = cleanName((string) $body['managerName']); }
        if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
        ccLog($did, $me, 'set up the college', $name);
        if ($mgr !== '') { $t = ccToken($mgr, 'invite', 24 * 7); $extra['link'] = ccLink('invite', $t);
            $extra['mailed'] = ccMail($mgr, "You've been added to Crafticulum", "Hello" . (!empty($a['users'][$mgr]['name']) ? ' ' . $a['users'][$mgr]['name'] : '') . ",\n\n" . ($me['name'] ?: $me['email']) . " has set up $name on Crafticulum and made you its manager. You can add its departments and staff, and set its name, logo and colour.\n\nChoose a password with this link (it works for 7 days):\n" . $extra['link'] . "\n\n" . (ccGoogleId() || ccMsId() ? "Or sign in with your Google or Microsoft account using this email address.\n" : "")); }
        $extra += ['newCollege' => $id, 'newDept' => $did];
    }
    if ($action === 'collegeset' || $action === 'collegedel') {
        $id = (string) ($body['id'] ?? ''); if (!isset($a['colleges'][$id])) out(404, ['error' => 'not_found']);
        if ($action === 'collegeset') {
            if (array_key_exists('name', $body)) { $name = cleanName((string) $body['name']); if ($name === '') out(400, ['error' => 'name']); $a['colleges'][$id]['name'] = $name; }
            if (array_key_exists('contact', $body)) { $c = strtolower(trim((string) $body['contact'])); if ($c !== '' && !emailOk($c)) out(400, ['error' => 'email']); $a['colleges'][$id]['contact'] = $c; }
            if (array_key_exists('color', $body)) { $c = (string) $body['color']; $a['colleges'][$id]['color'] = preg_match('/^#[0-9a-fA-F]{6}$/', $c) ? strtolower($c) : ''; }
            if (array_key_exists('logo', $body)) { $l = (string) $body['logo']; if ($l !== '' && (!preg_match('#^data:image/(png|jpeg|webp|svg\+xml);base64,[A-Za-z0-9+/=]+$#', $l) || strlen($l) > 300000)) out(400, ['error' => 'logo']); $a['colleges'][$id]['logo'] = $l; }
            if (array_key_exists('cap', $body)) $a['colleges'][$id]['cap'] = max(0, min(100000000, (int) $body['cap']));
            if (array_key_exists('plan', $body)) { $pl = (string) $body['plan']; if (!isset(CC_PLANS[$pl])) out(400, ['error' => 'plan']); $a['colleges'][$id]['plan'] = $pl; }   // set by hand: a quote agreed outside Stripe, or a free upgrade
            if (array_key_exists('off', $body)) { if (!empty($body['off']) && $id === $me['college'] && !empty($me['inside'] ?? true)) out(400, ['error' => 'own']); $a['colleges'][$id]['off'] = !empty($body['off']); }
            ccLog($me['dept'], $me, 'changed the college', (string) $a['colleges'][$id]['name']);
        } else {
            if (count($a['colleges']) < 2) out(400, ['error' => 'last']);
            if ($id === $me['college'] && !empty($me['inside'] ?? true)) out(400, ['error' => 'own']);
            $depts = ccCollegeDepts($a, $id); $cname = (string) $a['colleges'][$id]['name'];
            if (!empty($body['force'])) {
                /* gone for good: its people, departments, lessons, planning, results and student pages. Asked for twice, with its name typed */
                $typed = strtolower(trim((string) preg_replace('/\s+/', ' ', (string) ($body['confirm'] ?? '')))); $want = strtolower(trim((string) preg_replace('/\s+/', ' ', $cname)));
                if ($typed === '' || $typed !== ($want !== '' ? $want : 'delete')) out(400, ['error' => 'confirm']);
                $b = (array) ($a['colleges'][$id]['bill'] ?? []); if (!empty($b['sub']) && empty($b['cancelAt']) && in_array((string) ($b['status'] ?? ''), ['active', 'trialing', 'past_due', 'unpaid'], true)) out(400, ['error' => 'paying']);   // a plan that's been cancelled doesn't stand in the way
                $supers = ccSupers(); $other = ''; foreach ($a['depts'] as $k => $d0) if (!in_array((string) $k, $depts, true)) { $other = (string) $k; break; }
                $gone = ['people' => 0, 'depts' => count($depts), 'files' => 0]; $base = ccDataDir(); $hid = $id . '-' . time(); $hold = $base . '/_deleted/' . $hid;
                if (!@mkdir($hold . '/files', 0700, true) && !is_dir($hold . '/files')) out(500, ['error' => 'save']);
                $meta = ['id' => $id, 'name' => $cname, 'at' => time(), 'by' => (string) $me['email'], 'college' => $a['colleges'][$id], 'depts' => [], 'users' => [], 'moved' => []];
                foreach ($a['users'] as $e => $u) { if (!in_array((string) ($u['dept'] ?? ($u['wantDept'] ?? '')), $depts, true) && !in_array((string) ($u['wantDept'] ?? ''), $depts, true)) continue;
                    if (in_array((string) $e, $supers, true)) { $a['users'][$e]['dept'] = $other; continue; }
                    $meta['users'][(string) $e] = $u; unset($a['users'][$e]); $gone['people']++; }
                /* set aside, not destroyed: each thing keeps its place under files/, so it can go straight back */
                $mv = function (string $p) use (&$meta, &$gone, $base, $hold): void { if ($p === '' || strpos($p, $base . '/') !== 0 || is_link($p) || !file_exists($p)) return;
                    $rel = substr($p, strlen($base) + 1); $to = $hold . '/files/' . $rel; if (!is_dir(dirname($to))) @mkdir(dirname($to), 0700, true);
                    if (@rename($p, $to)) { $meta['moved'][] = $rel; $gone['files']++; } };
                foreach ($depts as $d) { $d = (string) $d; $meta['depts'][$d] = $a['depts'][$d]; if (!preg_match('/^[a-z0-9][a-z0-9-]*$/', $d)) continue;
                    if (!empty($a['depts'][$d]['legacy'])) { foreach (array_diff((array) @scandir($base), ['.', '..']) as $x) { if ($x[0] === '_' || $x[0] === '.' || in_array($x, ['d', 'u', 'share', 'backups', 'index.html'], true)) continue; $mv($base . '/' . $x); } }   // the first college's library sits beside the site's own files
                    else $mv($base . '/d/' . $d);
                    foreach ((array) glob($base . '/u/*/' . $d, GLOB_ONLYDIR) as $p) $mv((string) $p);
                    foreach ((array) glob($base . '/_marker/' . $d . '{.json,-*.json}', GLOB_BRACE) as $p) $mv((string) $p);
                    $mv($base . '/_log/' . $d . '.jsonl'); $mv($base . '/share/discord-' . $d . '.json'); }
                foreach ((array) glob($base . '/share/*.json') as $p) { $p = (string) $p; if (substr($p, -13) === '.results.json') continue; $j = json_decode((string) @file_get_contents($p), true);
                    if (is_array($j) && in_array((string) ($j['dept'] ?? ''), $depts, true)) { $mv(substr($p, 0, -5) . '.results.json'); $mv($p); } }   // the pages its students were given stop working straight away
                foreach (array_keys($meta['users']) as $e) $mv(ccPushFile((string) $e));
                $meta['gone'] = $gone; if (@file_put_contents($hold . '/meta.json', json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX) === false) out(500, ['error' => 'save']);
                foreach ($depts as $d) unset($a['depts'][$d]);
                unset($a['colleges'][$id]); ccLog($me['dept'], $me, 'deleted the college', $cname . ' (' . $gone['people'] . ' people, ' . $gone['depts'] . ' departments). Kept for ' . CC_DELETED_DAYS . ' days'); $extra['gone'] = $gone + ['days' => CC_DELETED_DAYS];
            } else {
            foreach ($a['users'] as $e => $u) if (in_array((string) ($u['dept'] ?? ''), $depts, true) && ccActive($u) && !in_array((string) $e, ccSupers(), true)) out(400, ['error' => 'has_staff']);
            foreach ($depts as $d) unset($a['depts'][$d]);     // the departments' files stay in data/d/ in case they're wanted back
            ccLog($me['dept'], $me, 'removed the college', $cname); unset($a['colleges'][$id]);
            }
        }
        if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
    }
    out(200, ['colleges' => collegeList($a), 'depts' => deptList($a), 'deleted' => ccDeletedList()] + $extra);
}
/* ---------- staff and departments ---------- */
if (!$me['canTeam']) out(403, ['error' => 'noperm']);
$isSuper = $me['role'] === 'superadmin'; $isAdmin = !empty($me['admin']);
/* who can manage whom. The super admin: everyone. A college manager: every head of department and teacher in their own college, and the people
   asking to join it, but not other managers. A head of department: the teachers in their own department and people asking to join it as a teacher. */
function canManage(array $u, array $me, ?array $a = null): bool {
    if ($me['role'] === 'superadmin') return true;
    if (empty($me['canTeam'])) return false;
    $a = $a ?? ccAccounts(); $one = count(ccCollegeIds($a)) < 2;
    if ($me['role'] === 'owner') {
        if (($u['role'] ?? '') === 'owner' && ccActive($u)) return false;
        $d = (string) (ccActive($u) ? ($u['dept'] ?? '') : ($u['wantDept'] ?? ''));
        if ($d === '') return !ccActive($u) && $one;                 // someone who didn't say which department: only when this is the site's only college
        return isset($a['depts'][$d]) && $d !== CC_DEMO_DEPT && ccCollegeOf($a, $d) === $me['college'];
    }
    if (ccActive($u)) return ($u['role'] ?? 'teacher') === 'teacher' && ($u['dept'] ?? '') === $me['dept'];
    return ($u['wantRole'] ?? 'teacher') === 'teacher' && in_array($u['wantDept'] ?? '', $one ? ['', $me['dept']] : [$me['dept']], true);
}
/* a plan only has so many places */
function seatCheck(array $a, string $dept): void { if (!ccBillingOn()) return; $c = ccCollegeOf($a, $dept); $pl = ccPlanOf($a, $c);
    if ($pl['people'] > 0 && ccCollegePeople($a, $c) >= $pl['people']) out(402, ['error' => 'seats', 'plan' => $pl['name'], 'max' => $pl['people']]); }
/* the departments this person can put people in */
function myDepts(array $a, array $me): array { return $me['role'] === 'superadmin' ? array_values(array_filter(array_map('strval', array_keys($a['depts'])), fn($d) => $d !== CC_DEMO_DEPT)) : ($me['role'] === 'owner' ? ccCollegeDepts($a, (string) $me['college']) : [(string) $me['dept']]); }
function permsFrom($v): array { $o = []; foreach (CC_PERMS as $k) $o[$k] = !is_array($v) || !array_key_exists($k, $v) || !empty($v[$k]); return $o; }
function teamData(array $a, array $me): array {
    $team = []; $pend = [];
    foreach ($a['users'] as $email => $u) {
        if (in_array($email, ccSupers(), true) && ccActive($u)) continue;
        if (!canManage($u, $me, $a)) continue;
        $row = ['email' => $email, 'name' => (string) ($u['name'] ?? ''), 'role' => (string) ($u['role'] ?? 'teacher'), 'dept' => (string) ($u['dept'] ?? ''), 'canEdit' => !empty($u['canEdit']), 'perms' => ccPerms($u),
                'last' => (int) ($u['last'] ?? 0), 'methods' => (array) ($u['methods'] ?? []), 'hasPass' => !empty($u['pass']), 'totp' => !empty($u['totp']['on'])];
        if (ccActive($u)) $team[] = $row; else $pend[] = $row + ['verified' => !empty($u['verified']), 'via' => (string) ($u['via'] ?? 'password'), 'wantDept' => (string) ($u['wantDept'] ?? ''), 'wantRole' => (string) ($u['wantRole'] ?? 'teacher'), 'requested' => (int) ($u['requested'] ?? 0)];
    }
    usort($team, fn($x, $y) => [$x['dept'], $x['role'] === 'hod' ? 0 : 1, strtolower($x['name'] ?: $x['email'])] <=> [$y['dept'], $y['role'] === 'hod' ? 0 : 1, strtolower($y['name'] ?: $y['email'])]);
    $mine = myDepts($a, $me); $many = count(ccCollegeIds($a)) > 1;
    $depts = []; foreach ($a['depts'] as $k => $d) { $k = (string) $k; if ($k === CC_DEMO_DEPT || (!empty($me['admin']) && !in_array($k, $mine, true))) continue; $c = ccCollegeOf($a, $k);
        $depts[] = ['id' => $k, 'name' => $d['name'], 'college' => $c, 'staff' => count(array_filter($a['users'], fn($u, $e) => ($u['dept'] ?? '') === $k && ccActive($u) && !in_array((string) $e, ccSupers(), true), ARRAY_FILTER_USE_BOTH))] + ($many && $me['role'] === 'superadmin' ? ['collegeName' => ccCollege($a, $c)['name']] : []); }
    return ['team' => $team, 'pending' => $pend, 'mail' => ccMailReady(), 'supers' => $me['role'] === 'superadmin' ? ccSupers() : [], 'depts' => $depts]
         + ($me['role'] === 'superadmin' ? ['colleges' => collegeList($a)] : []);
}
function teamOut(array $a, array $me): void { out(200, teamData($a, $me)); }
if ($action === 'team') teamOut($a, $me);
if (in_array($action, ['deptadd', 'deptset', 'deptdel'], true)) {
    if (!$isAdmin) out(403, ['error' => 'noperm']);
    if ($action === 'deptadd') {
        $name = cleanName((string) ($body['name'] ?? '')); if ($name === '') out(400, ['error' => 'name']);
        $col = $isSuper && isset($body['college'], $a['colleges'][(string) $body['college']]) ? (string) $body['college'] : (string) $me['college'];
        if (ccBillingOn()) { $pl = ccPlanOf($a, $col); if ($pl['depts'] > 0 && count(ccCollegeDepts($a, $col)) >= $pl['depts']) out(402, ['error' => 'plan_depts', 'plan' => $pl['name'], 'max' => $pl['depts']]); }
        $id = newDeptId($a, $name);
        $a['depts'][$id] = ['name' => $name, 'created' => time(), 'college' => $col];
    }
    $id = $action === 'deptadd' ? $id : (string) ($body['id'] ?? ''); if (!isset($a['depts'][$id])) out(404, ['error' => 'not_found']);
    if (!in_array($id, myDepts($a, $me), true)) out(403, ['error' => 'noperm']);
    if ($action === 'deptset') { $name = cleanName((string) ($body['name'] ?? '')); if ($name === '') out(400, ['error' => 'name']); $a['depts'][$id]['name'] = $name; }
    if ($action === 'deptdel') {
        if (count(ccCollegeDepts($a, ccCollegeOf($a, $id))) < 2) out(400, ['error' => 'last']);
        foreach ($a['users'] as $e => $u) if (($u['dept'] ?? '') === $id && ccActive($u) && !in_array((string) $e, ccSupers(), true)) out(400, ['error' => 'has_staff']);
        unset($a['depts'][$id]);   // the department's files stay in data/d/ in case they're wanted back
    }
    ccSaveAccounts($a);
    if ($action === 'deptadd') out(200, teamData($a, $me) + ['newDept' => $id]);
    teamOut($a, $me);
}
$email = strtolower(trim((string) ($body['email'] ?? '')));
$old = $a['users'][$email] ?? null;
if ($action === 'useradd') {
    if (!emailOk($email)) out(400, ['error' => 'email']);
    if (in_array($email, ccSupers(), true)) out(400, ['error' => 'is_super']);
    if ($old && ccActive($old)) out(409, ['error' => 'exists']);
    $d = $isAdmin && isset($body['dept']) && in_array((string) $body['dept'], myDepts($a, $me), true) ? (string) $body['dept'] : $me['dept'];
    if ($old && !canManage($old, $me, $a)) out(403, ['error' => 'noperm']);
    $r = $isAdmin && ($body['role'] ?? '') === 'hod' ? 'hod' : ($isSuper && ($body['role'] ?? '') === 'owner' ? 'owner' : 'teacher');
    seatCheck($a, $d);
    $u = ($old ?? []) + ['added' => time(), 'methods' => []];
    $u = array_merge($u, ['status' => 'active', 'role' => $r, 'dept' => $d, 'canEdit' => !empty($body['canEdit']), 'addedBy' => $me['email']]);
    if ($r === 'hod') $u['perms'] = permsFrom($body['perms'] ?? null); else unset($u['perms']);
    unset($u['wantDept'], $u['wantRole']);
    if (!empty($body['name'])) $u['name'] = cleanName((string) $body['name']);
    $a['users'][$email] = $u; if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
    ccLog($d, $me, 'added ' . ($r === 'owner' ? 'a college manager' : ($r === 'hod' ? 'a head of department / subject leader' : 'a teacher')), $email);
    $extra = [];
    if (!empty($body['invite'])) { $t = ccToken($email, 'invite', 24 * 7); $extra['link'] = ccLink('invite', $t);
        $extra['mailed'] = ccMail($email, "You've been added to Crafticulum", "Hello" . (!empty($u['name']) ? ' ' . $u['name'] : '') . ",\n\n" . ($me['name'] ?: $me['email']) . " has added you to Crafticulum " . ($r === 'owner' ? 'to manage ' . (ccCollege($a, ccCollegeOf($a, $d))['name'] ?: 'your college') : 'for ' . $a['depts'][$d]['name']) . ".\n\nChoose a password with this link (it works for 7 days):\n" . $extra['link'] . "\n\n" . (ccGoogleId() || ccMsId() ? "Or sign in with your Google or Microsoft account using this email address.\n" : "")); }
    out(200, teamData($a, $me) + $extra);
}
if (!$old) out(404, ['error' => 'not_found']);
if (!canManage($old, $me, $a)) out(403, ['error' => 'noperm']);
if ($action === 'approve') {
    if (ccActive($old)) out(400, ['error' => 'active']);
    $can = myDepts($a, $me);
    $d = $isAdmin && isset($body['dept']) && in_array((string) $body['dept'], $can, true) ? (string) $body['dept'] : ($isAdmin && in_array((string) ($old['wantDept'] ?? ''), $can, true) ? (string) $old['wantDept'] : $me['dept']);
    $r = $isAdmin && ($body['role'] ?? ($old['wantRole'] ?? '')) === 'hod' ? 'hod' : 'teacher';
    seatCheck($a, $d);
    $a['users'][$email] = array_merge($old, ['status' => 'active', 'role' => $r, 'dept' => $d, 'canEdit' => !empty($body['canEdit']), 'approvedBy' => $me['email'], 'added' => time()]);
    if ($r === 'hod') $a['users'][$email]['perms'] = permsFrom($body['perms'] ?? null);
    unset($a['users'][$email]['wantDept'], $a['users'][$email]['wantRole']); ccSaveAccounts($a);
    ccLog($d, $me, 'approved ' . ($r === 'hod' ? 'a head of department / subject leader' : 'a teacher'), $email);
    ccMail($email, "You're in: Crafticulum", "Hello" . (!empty($old['name']) ? ' ' . $old['name'] : '') . ",\n\nYour Crafticulum account has been approved for " . $a['depts'][$d]['name'] . ".\n\n" . (empty($old['pass']) && in_array($old['via'] ?? '', ['magic', 'password'], true) ? "Sign in with this link (it works once, for 24 hours):\n" . ccLink('magic', ccToken($email, 'magic', 24)) . "\n\nAfter that, use Email me a sign-in link on the sign-in page.\n" : "Sign in here:\n" . ccAppUrl() . "\n"));
    teamOut($a, $me);
}
if ($action === 'decline') { if (ccActive($old)) out(400, ['error' => 'active']); unset($a['users'][$email]); ccSaveAccounts($a); ccLog($me['dept'], $me, 'declined an account request', $email); teamOut($a, $me); }
if ($action === 'resetlink') {
    if (!ccActive($old)) out(400, ['error' => 'pending']);
    $type = empty($old['pass']) ? 'invite' : 'reset'; $t = ccToken($email, $type, $type === 'invite' ? 24 * 7 : 24); $l = ccLink($type, $t);
    $mailed = ccMail($email, $type === 'invite' ? 'Choose a password for Crafticulum' : 'Reset your Crafticulum password', "Hello" . (!empty($old['name']) ? ' ' . $old['name'] : '') . ",\n\n" . ($me['name'] ?: $me['email']) . " sent you a link to " . ($type === 'invite' ? 'choose' : 'reset') . " your Crafticulum password. It works once, for " . ($type === 'invite' ? '7 days' : '24 hours') . ":\n$l\n");
    out(200, ['link' => $l, 'mailed' => $mailed, 'type' => $type]);
}
if ($action === 'userset') {
    if (in_array($email, ccSupers(), true)) out(400, ['error' => 'is_super']);
    $u = $old;
    if (array_key_exists('name', $body)) $u['name'] = cleanName((string) $body['name']);
    if (array_key_exists('role', $body)) { $r = (string) $body['role']; if (!in_array($r, ['teacher', 'hod', 'owner'], true)) out(400, ['error' => 'role']); if (!$isAdmin || ($r === 'owner' && !$isSuper)) out(403, ['error' => 'noperm']);
        $u['role'] = $r; if ($r === 'hod') { if (!isset($u['perms'])) $u['perms'] = permsFrom(null); } else unset($u['perms']); }
    if (array_key_exists('perms', $body)) { if (!$isAdmin) out(403, ['error' => 'noperm']); if (($u['role'] ?? '') !== 'hod' || !is_array($body['perms'])) out(400, ['error' => 'role']);
        $u['perms'] = array_merge(ccPerms($u), array_map(fn($v) => !empty($v), array_intersect_key($body['perms'], array_flip(CC_PERMS)))); }
    if (array_key_exists('canEdit', $body)) $u['canEdit'] = !empty($body['canEdit']);
    if (array_key_exists('dept', $body)) { $d = (string) $body['dept']; if (!isset($a['depts'][$d])) out(400, ['error' => 'dept']); if (!in_array($d, myDepts($a, $me), true)) out(403, ['error' => 'noperm']); $u['dept'] = $d; }
    if (!empty($body['totpReset'])) unset($u['totp']);
    $a['users'][$email] = $u; if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
    $ch = array_intersect(array_keys($body), ['role', 'perms', 'canEdit', 'dept', 'totpReset', 'name']);
    if ($ch) ccLog((string) ($u['dept'] ?? $me['dept']), $me, 'changed ' . implode(', ', array_map(fn($k) => ['role' => 'the role', 'perms' => 'the permissions', 'canEdit' => 'edit rights', 'dept' => 'the department', 'totpReset' => 'two-step codes', 'name' => 'the name'][$k], $ch)) . ' for', $email);
    teamOut($a, $me);
}
if ($action === 'userdel') { if ($email === $me['email']) out(400, ['error' => 'self']); unset($a['users'][$email]); ccSaveAccounts($a); ccLog((string) ($old['dept'] ?? $me['dept']), $me, 'removed', $email); teamOut($a, $me); }
out(400, ['error' => 'Unknown action.']);
