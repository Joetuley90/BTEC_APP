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
if ($_SERVER['REQUEST_METHOD'] === 'POST') { $raw = file_get_contents('php://input', false, null, 0, ($_GET['a'] ?? '') === 'siteset' ? 400001 : (($_GET['a'] ?? '') === 'deptsplit' ? 3000001 : 20001)); $body = json_decode($raw ?: '{}', true); if (!is_array($body)) $body = []; }
/* the daily reminders, for a scheduled job on the server: auth.php?a=pushcron&k=<the key shown in Settings, Notifications> */
if (($_GET['a'] ?? '') === 'pushcron') { if (!hash_equals(substr(hash('sha256', 'cron|' . ccSiteId()), 0, 32), (string) ($_GET['k'] ?? ''))) out(403, ['error' => 'key']); $inst = 0; if (is_file(__DIR__ . '/inst-lib.php')) { require_once __DIR__ . '/inst-lib.php'; @file_put_contents(ccInstDir() . '/_cron.txt', (string) time()); try { $inst = ccInstAuto(); } catch (Throwable $e) { $inst = 0; } } $up = 0; if (is_file(__DIR__ . '/update-lib.php')) { require_once __DIR__ . '/update-lib.php'; try { $up = ccUpAuto(); } catch (Throwable $e) { $up = 0; } } out(200, ['sent' => ccPushDaily(), 'purged' => ccDeletedPurge(), 'handin' => ccHandinPurge(), 'directory' => $inst, 'updated' => $up]); }
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
const CC_ROLE_NAMES = ['superadmin' => 'Super admin', 'owner' => 'Institution manager', 'hod' => 'Head of department', 'teacher' => 'Teacher'];
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
/* someone signing up on their own gets their own space: an institution on the Free plan with one department, which they manage */
/* an institution that is already here under the same name, so nobody sets the same place up twice. "MidKent", "Midkent College" and "The Mid Kent College" are all one name */
function nameKey(string $s): string { $s = (string) preg_replace('/[^a-z0-9 ]+/', ' ', mb_strtolower($s)); $s = (string) preg_replace('/\b(the|of|college|school|academy|university|sixth form|campus)\b/', ' ', $s); return (string) preg_replace('/\s+/', '', $s); }
function collegeByName(array $a, array $names, ?array $inst): string { $keys = array_values(array_filter(array_map('nameKey', $names), fn($k) => strlen($k) >= 4)); if (!$keys) return ''; $best = '';
    foreach ($a['colleges'] as $cid => $c) { if (!empty($c['off']) || !ccCollegeDepts($a, (string) $cid)) continue; $linked = (array) ($c['inst'] ?? []);
        if ($inst && trim((string) ($inst['ref'] ?? '')) !== '' && array_filter($linked, fn($l) => trim((string) ($l['ref'] ?? '')) !== '')) continue;   // it's linked to a numbered institution, and that wasn't the one picked
        $mine = array_map('nameKey', array_merge([(string) ($c['name'] ?? '')], array_map(fn($l) => (string) ($l['name'] ?? ''), $linked)));
        if (array_intersect($keys, $mine)) { if (empty($c['self'])) return (string) $cid; if ($best === '') $best = (string) $cid; } }   // an institution set up properly comes before a workspace someone started for themselves
    return $best; }
/* the same institution by email address: someone whose address ends the way an institution manager's does belongs to that institution. Free mail (gmail and the like) says nothing, so it is never used */
const CC_PUBLIC_MAIL = ['gmail.com', 'googlemail.com', 'outlook.com', 'hotmail.com', 'hotmail.co.uk', 'live.com', 'live.co.uk', 'msn.com', 'yahoo.com', 'yahoo.co.uk', 'ymail.com', 'icloud.com', 'me.com', 'mac.com', 'aol.com', 'proton.me', 'protonmail.com', 'pm.me', 'btinternet.com', 'sky.com', 'virginmedia.com', 'talktalk.net', 'ntlworld.com', 'blueyonder.co.uk', 'gmx.com', 'gmx.co.uk', 'mail.com', 'zoho.com', 'test.com', 'example.com', 'example.org'];
function mailDomain(string $email): string { $d = strtolower((string) substr((string) strrchr($email, '@'), 1)); return $d === '' || in_array($d, CC_PUBLIC_MAIL, true) ? '' : $d; }
function collegeByDomain(array $a, string $email): string { $d = mailDomain($email); if ($d === '') return ''; $best = '';
    foreach ($a['users'] as $e => $u) { if (!ccActive($u) || ($u['role'] ?? '') !== 'owner' || !empty($u['demo']) || mailDomain((string) $e) !== $d || !isset($a['depts'][(string) ($u['dept'] ?? '')])) continue;
        $cid = ccCollegeOf($a, (string) $u['dept']); if (!isset($a['colleges'][$cid]) || !empty($a['colleges'][$cid]['off'])) continue; if (empty($a['colleges'][$cid]['self'])) return $cid; if ($best === '') $best = $cid; }
    return $best; }
/* the institution someone belongs in, if it's already on the site: the one linked to the institution they picked, then one with the same name, then one whose manager shares their email domain */
function matchCollege(array $a, ?array $inst, array $names, string $email): string {
    if ($inst) foreach ($a['colleges'] as $cid => $c) if (empty($c['off'])) foreach ((array) ($c['inst'] ?? []) as $li) if (ccInstSame($li, $inst)) return (string) $cid;
    return collegeByName($a, array_filter(array_merge([(string) ($inst['name'] ?? '')], $names)), $inst) ?: collegeByDomain($a, $email); }
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
        require_once __DIR__ . '/inst-lib.php'; $new = ['name' => cleanName($name), 'status' => 'pending', 'verified' => true, 'via' => $via, 'requested' => time(), 'role' => 'teacher', 'dept' => '', 'canEdit' => false] + ($picture !== '' ? ['picture' => $picture] : []);
        $col = collegeByDomain($a, $email);
        if ($col !== '') {   // their address belongs to an institution that's already here: they ask to join it, and a second one is never made
            $cn = (string) (ccCollege($a, $col)['name'] ?? ''); $ds = ccCollegeDepts($a, $col); $a['users'][$email] = $new + ['viaInst' => $col, 'instName' => $cn, 'wantDept' => (string) ($ds[0] ?? '')];
            ccSaveAccounts($a); notifyApprovers($a, $email); return ['pending' => true, 'joined' => $cn]; }
        if (!empty(ccInstLog()['sources'])) {   // the directory is loaded: they say where they work before anything is made
            $a['users'][$email] = $new + ['needCollege' => true]; ccSaveAccounts($a); $_SESSION['ssoChoose'] = $email; return ['pending' => true, 'choose' => true]; }
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
    if ($u && !ccActive($u) && !$isSuper) { if (!empty($u['needCollege'])) { $_SESSION['ssoChoose'] = $email; return ['pending' => true, 'choose' => true]; } return ['pending' => true]; }
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
    if (!empty($u['viaInst'])) foreach ($a['users'] as $e => $x) if (($x['role'] ?? '') === 'owner' && ccActive($x) && ccCollegeOf($a, (string) ($x['dept'] ?? '')) === $u['viaInst']) $to[] = $e;   // found through the directory: the institution's manager approves
    if (!$to && ($u['wantRole'] ?? 'teacher') !== 'hod' && $d !== '') foreach ($a['users'] as $e => $x) if (($x['role'] ?? '') === 'hod' && ccActive($x) && ($x['dept'] ?? '') === $d && ccPerms($x)['team']) $to[] = $e;
    if (!$to && $d !== '' && isset($a['depts'][$d])) { $c = ccCollegeOf($a, $d); foreach ($a['users'] as $e => $x) if (($x['role'] ?? '') === 'owner' && ccActive($x) && ccCollegeOf($a, (string) ($x['dept'] ?? '')) === $c) $to[] = $e; }   // then the institution's manager
    if (!$to) $to = ccSupers();
    foreach (array_unique($to) as $t) ccMail($t, 'New account to approve', "Hello,\n\n" . ($u['name'] ?: $email) . " ($email) has asked for a Crafticulum account" . ($d && isset($a['depts'][$d]) ? ' in ' . $a['depts'][$d]['name'] : '') . ".\n\nApprove or decline them in Settings, Team:\n" . ccAppUrl() . "\n");
}

/* ---------- public ---------- */
if ($action === 'config') {
    $a = ccAccounts();
    $fresh = !array_filter($a['users'], fn($u) => !empty($u['pass']) || !empty($u['last']));
    require_once __DIR__ . '/inst-lib.php';
    out(200, ['dir' => !empty(ccInstLog()['sources']), 'dirv' => ccInstVersion(), 'google' => ccGoogleId() ?: null, 'googleRedirect' => ccGoogleSecret() !== '', 'microsoft' => ccMsId() !== '', 'oidc' => ccOidcOn() ? ccOidcName() : null, 'password' => ccPasswordAllowed(), 'mail' => ccMailReady(), 'depts' => array_values(array_filter(deptList($a), fn($d) => empty($a['colleges'][(string) ($a['depts'][$d['id']]['college'] ?? '')]['self']))), 'fresh' => $fresh, 'site' => ccSitePublic($a), 'url' => ccSiteUrl(), 'demo' => ccDemoOn(), 'billing' => ccBillingOn()]);
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
/* someone new who came in with Google, Microsoft or the institution sign-in says where they work. Their institution is found if it's here; a new space is made only if it isn't */
if ($action === 'ssochoose') {
    $email = (string) ($_SESSION['ssoChoose'] ?? ''); $a = ccAccounts(); $u = $email !== '' ? ($a['users'][$email] ?? null) : null;
    if (!$u || ccActive($u) || empty($u['needCollege'])) out(400, ['error' => 'expired']);
    if (!empty($body['peek'])) out(200, ['need' => true, 'email' => $email, 'name' => (string) ($u['name'] ?? ''), 'billing' => ccBillingOn()]);
    require_once __DIR__ . '/inst-lib.php'; $inst = null; if (is_array($body['inst'] ?? null) && trim((string) ($body['inst']['name'] ?? '')) !== '') { $inst = ccProfileClean(['insts' => [$body['inst']]])['insts'][0] ?? null; }
    $space = cleanName((string) ($body['workspace'] ?? '')); $col = matchCollege($a, $inst, $space !== '' ? [$space] : [], $email);
    if ($inst) { try { ccProfileSet($email, ['country' => in_array($inst['nation'], ['eng', 'wal', 'sco', 'ni'], true) ? $inst['nation'] : '', 'insts' => [$inst]]); } catch (Throwable $e) {} }
    unset($u['needCollege']); unset($_SESSION['ssoChoose']);
    if ($col !== '') { $cn = (string) (ccCollege($a, $col)['name'] ?? ''); $ds = ccCollegeDepts($a, $col); $a['users'][$email] = array_merge($u, ['viaInst' => $col, 'instName' => (string) (($inst['name'] ?? '') ?: $cn), 'wantDept' => (string) ($ds[0] ?? '')]);
        if (!ccSaveAccounts($a)) out(500, ['error' => 'save']); notifyApprovers($a, $email); out(200, ['ok' => true, 'joined' => $cn]); }
    if (ccBillingOn() && startAllowed()) { $did = newWorkspace($a, $email, (string) ($u['name'] ?? ''), $space ?: (string) ($inst['name'] ?? ''));
        $a['users'][$email] = array_merge($u, ['status' => 'active', 'role' => 'owner', 'dept' => $did, 'canEdit' => true, 'added' => time(), 'last' => time(), 'methods' => [(string) ($u['via'] ?? 'google')]]); unset($a['users'][$email]['requested']);
        if (!ccSaveAccounts($a)) out(500, ['error' => 'save']); welcomeMail($email, (string) ($u['name'] ?? '')); signIn($email); out(200, ['ok' => true, 'started' => true]); }
    if ($inst) $u['instName'] = $inst['name']; $a['users'][$email] = $u; if (!ccSaveAccounts($a)) out(500, ['error' => 'save']); notifyApprovers($a, $email); out(200, ['ok' => true, 'pending' => true]);
}
if ($action === 'signup') {
    $email = strtolower(trim((string) ($body['email'] ?? ''))); $name = cleanName((string) ($body['name'] ?? '')); $pass = (string) ($body['password'] ?? ''); $want = (string) ($body['dept'] ?? ''); $wantRole = ($body['role'] ?? '') === 'hod' ? 'hod' : 'teacher';
    if (!emailOk($email)) out(400, ['error' => 'email']);
    if ($name === '') out(400, ['error' => 'name']);
    if ($pass === '' && !ccMailReady()) out(400, ['error' => 'pass_required']);
    if ($pass !== '' && ($e = passOk($pass, $email))) out(400, ['error' => 'pass_' . $e]);
    if (ccLocked($email)) out(429, ['error' => 'locked']);
    $a = ccAccounts();
    /* the institution they picked from the directory, if they did. It goes into their own profile. It never links a workspace to that institution */
    require_once __DIR__ . '/inst-lib.php'; $inst = null; if (is_array($body['inst'] ?? null) && trim((string) ($body['inst']['name'] ?? '')) !== '') { $inst = ccProfileClean(['insts' => [$body['inst']]])['insts'][0] ?? null; }
    $newSpace = ($body['space'] ?? '') === 'new' && ccBillingOn(); $instCol = matchCollege($a, $inst, $newSpace ? [(string) ($body['workspace'] ?? '')] : [], $email); $joined = $newSpace && $instCol !== '' ? ((string) (ccCollege($a, $instCol)['name'] ?? '') ?: 'Your institution') : '';
    if ($newSpace && $instCol !== '' && (isset($a['users'][$email]) || in_array($email, ccSupers(), true))) { ccFail($email); out(409, ['error' => 'exists']); }   // starting a space with an address that already has an account is still refused
    $keepInst = function () use ($inst, $email): void { if ($inst) { try { ccProfileSet($email, ['country' => in_array($inst['nation'], ['eng', 'wal', 'sco', 'ni'], true) ? $inst['nation'] : '', 'insts' => [$inst]]); } catch (Throwable $e) {} } };
    /* plans are on and they want their own space: they're in straight away, on the Free plan */
    if ($newSpace && $instCol === '') {   // where the institution is already here, they ask to join it below: a second one is never made
        if ($pass === '') out(400, ['error' => 'pass_required']);
        if (isset($a['users'][$email])) { ccFail($email); out(409, ['error' => 'exists']); }
        if (in_array($email, ccSupers(), true)) out(409, ['error' => 'exists']);
        if (!startAllowed()) out(429, ['error' => 'locked']);
        $did = newWorkspace($a, $email, $name, (string) ($body['workspace'] ?? ''));
        $a['users'][$email] = ['name' => $name, 'status' => 'active', 'verified' => false, 'via' => 'password', 'role' => 'owner', 'dept' => $did, 'canEdit' => true, 'added' => time(), 'last' => time(), 'methods' => ['password'], 'pass' => password_hash($pass, PASSWORD_DEFAULT)];
        if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
        $keepInst(); welcomeMail($email, $name, ccLink('verify', ccToken($email, 'verify', 72)));
        signIn($email);
        out(200, ['ok' => true, 'started' => true]);
    }
    if (isset($a['users'][$email])) { ccFail($email); out(200, ['ok' => true, 'verify' => ccMailReady()]); }   // same answer either way, so no one can find out who has an account
    $a['users'][$email] = ['name' => $name, 'status' => 'pending', 'verified' => false, 'via' => $pass === '' ? 'magic' : 'password', 'requested' => time(),
        'wantDept' => isset($a['depts'][$want]) ? $want : '', 'wantRole' => $wantRole, 'role' => 'teacher', 'dept' => '', 'canEdit' => false, 'methods' => [$pass === '' ? 'magic' : 'password']];
    if ($instCol !== '') { $cd = ccCollegeDepts($a, $instCol); $w = (string) $a['users'][$email]['wantDept']; if ($cd && ($w === '' || ccCollegeOf($a, $w) !== $instCol)) $a['users'][$email]['wantDept'] = (string) $cd[0];   // asked through their institution: it goes to that institution, and its manager decides
        $a['users'][$email]['viaInst'] = $instCol; $a['users'][$email]['instName'] = (string) (($inst['name'] ?? '') ?: (ccCollege($a, $instCol)['name'] ?? '')); }
    if ($pass !== '') $a['users'][$email]['pass'] = password_hash($pass, PASSWORD_DEFAULT);
    ccSaveAccounts($a); $keepInst();
    if (ccMailReady()) ccMail($email, 'Confirm your email for Crafticulum', "Hello $name,\n\nPlease confirm this is your email address:\n" . ccLink('verify', ccToken($email, 'verify', 72)) . "\n\n" . ($instCol !== '' ? "Your institution's manager" : 'A head of department') . " also needs to approve your account. We'll email you when they have.\n\nIf you didn't ask for this, ignore this email.\n");
    notifyApprovers($a, $email);
    out(200, ['ok' => true, 'verify' => ccMailReady()] + ($joined !== '' ? ['joined' => $joined] : []));
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
    if (!empty($r['pending'])) out(403, ['error' => 'pending', 'email' => strtolower((string) $t['email']), 'choose' => !empty($r['choose']), 'joined' => (string) ($r['joined'] ?? '')]);
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
    $back(!empty($r['choose']) ? 'choose' : (!empty($r['pending']) ? 'pending' : 'ok'));
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
    $back(!empty($r['choose']) ? 'choose' : (!empty($r['pending']) ? 'pending' : 'ok'));
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
    $back(!empty($r['choose']) ? 'choose' : (!empty($r['pending']) ? 'pending' : 'ok'));
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
    register_shutdown_function(function () { try { if (session_status() === PHP_SESSION_ACTIVE) session_write_close(); if (function_exists('fastcgi_finish_request')) @fastcgi_finish_request(); ccPushDaily(); ccDeletedPurge(); ccHandinPurge(); } catch (\Throwable $x) {} });
    $o = ['me' => ccPublic($me) + ['methods' => (array) ($mine['methods'] ?? []), 'hasPass' => !empty($mine['pass']), 'totp' => !empty($mine['totp']['on']), 'recoveryLeft' => count((array) ($mine['totp']['recovery'] ?? [])), 'profile' => (object) (is_array($mine['profile'] ?? null) ? $mine['profile'] : []), 'added' => (int) ($mine['added'] ?? 0), 'lastSeen' => (int) ($mine['last'] ?? 0)],
          'google' => ccGoogleId() !== '', 'microsoft' => ccMsId() !== '', 'oidc' => ccOidcOn() ? ccOidcName() : null, 'mail' => ccMailReady(), 'passwordBackup' => ccPasswordAllowed(), 'prefs' => ccDeptPrefs($a, $me['dept']), 'marker' => ccMarker($a, $me['dept']) + ['can' => ccMarkerCan($me, $a), 'ns' => '.' . substr(sha1((string) $me['email']), 0, 10)], 'site' => !empty($me['demo']) ? ['plain' => true] + ccSitePublic($a) : (empty($me['inside'] ?? true) ? ccSitePublic($a) : ccSite($a, $me['college'])), 'url' => ccSiteUrl(), 'tempHost' => (string) parse_url(ccTempUrl(), PHP_URL_HOST)];
    $pl = ccPlanOf($a, (string) $me['college']); $o['billing'] = ccBillingOn(); $pu = ccPlanUsed((string) $me['college'], ccUsageRead()); $o['plan'] = ['id' => $pl['id'], 'name' => $pl['name'], 'builds' => $pl['builds'], 'checks' => $pl['checks'], 'usedBuilds' => $pu['builds'], 'usedChecks' => $pu['checks']];
    $o['needVerify'] = !empty($o['billing']) && !empty($a['colleges'][(string) $me['college']]['self']) && ccMailReady() && empty($a['users'][(string) $me['email']]['verified']); $o['selfStart'] = !empty($a['colleges'][(string) $me['college']]['self']);
    if ($me['role'] === 'superadmin') { $o['depts'] = deptList($a); $o['inbox'] = ccInboxOpen(); $o['colleges'] = collegeList($a); foreach (glob(ccDataDir() . '/_split/*/manifest.json') ?: [] as $sf) { $sm = json_decode((string) @file_get_contents($sf), true); if (is_array($sm) && empty($sm['undone'])) $o['splitUndo'] = (string) ($sm['newName'] ?? ''); } }
    elseif ($me['role'] === 'owner') $o['depts'] = deptList($a, $me['college']);
    else { $al = ccAlso($a, ['dept' => (string) ($mine['dept'] ?? ''), 'also' => $mine['also'] ?? []]); if ($al) { $ids = array_merge([(string) $mine['dept']], array_keys($al)); $o['depts'] = array_values(array_filter(deptList($a, $me['college']), fn($d) => in_array($d['id'], $ids, true))); } }
    if ($me['canTeam']) $o['pending'] = count(array_filter($a['users'], fn($u) => !ccActive($u) && canManage($u, $me, $a)));
    out(200, $o);
}
if ($action === 'dept') {
    $id = (string) ($body['id'] ?? ''); if (!isset($a['depts'][$id])) out(404, ['error' => 'not_found']);
    if (empty($me['admin'])) {   // someone in more than one department switches between their own
        $home = (string) ($mine['dept'] ?? ''); $al = ccAlso($a, ['dept' => $home, 'also' => $mine['also'] ?? []]); if ($id !== $home && !isset($al[$id])) out(403, ['error' => 'noperm']);
        if ($id === $home) unset($_SESSION['dept']); else $_SESSION['dept'] = $id; out(200, ['me' => ccPublic(ccMe())]); }
    if ($me['role'] === 'owner' && ($id === CC_DEMO_DEPT || ccCollegeOf($a, $id) !== ccCollegeOf($a, (string) ($mine['dept'] ?? '')))) out(403, ['error' => 'noperm']);   // a manager stays inside their own institution
    $_SESSION['dept'] = $id; if ($me['role'] === 'superadmin') $_SESSION['inside'] = 1;
    /* a manager's own department follows them. The super admin belongs to no institution, so nothing is written to their account: stepping inside lasts for the session only */
    if (isset($a['users'][$me['email']])) { if ($me['role'] === 'superadmin') { if (isset($a['users'][$me['email']]['dept']) || isset($a['users'][$me['email']]['also'])) { unset($a['users'][$me['email']]['dept'], $a['users'][$me['email']]['also']); ccSaveAccounts($a); } } else { $a['users'][$me['email']]['dept'] = $id; ccSaveAccounts($a); } }
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
/* ---------- rooms: who can use which part of the app ---------- */
if ($action === 'roomsget' || $action === 'roomsset') {
    $sup = $me['role'] === 'superadmin'; $own = $me['role'] === 'owner'; if (!$sup && !$own) out(403, ['error' => 'noperm']);
    $cid = $sup ? (string) ($body['college'] ?? (!empty($me['inside'] ?? true) ? $me['college'] : '')) : (string) $me['college']; if ($cid !== '' && !isset($a['colleges'][$cid])) out(404, ['error' => 'not_found']);
    if ($action === 'roomsset') {
        $room = (string) ($body['room'] ?? ''); if (!isset(CC_ROOMS[$room])) out(400, ['error' => 'room']); $on = !empty($body['on']); $scope = (string) ($body['scope'] ?? '');
        $flip = function (array $off) use ($room, $on): array { $off = array_values(array_filter($off, fn($x) => $x !== $room && isset(CC_ROOMS[$x]))); if (!$on) $off[] = $room; return $off; };
        if ($scope === 'site') { if (!$sup) out(403, ['error' => 'noperm']); $a['rooms']['off'] = $flip((array) (($a['rooms'] ?? [])['off'] ?? [])); ccLog($me['dept'], $me, ($on ? 'opened ' : 'closed ') . CC_ROOMS[$room][0] . ' for the whole site'); }
        elseif ($scope === 'college') { if (!$sup || $cid === '') out(403, ['error' => 'noperm']); $a['colleges'][$cid]['roomsOff'] = $flip((array) ($a['colleges'][$cid]['roomsOff'] ?? [])); ccLog($me['dept'], $me, ($on ? 'opened ' : 'closed ') . CC_ROOMS[$room][0] . ' for', ccCollege($a, $cid)['name']); }
        elseif ($scope === 'role') { $r = (string) ($body['role'] ?? ''); if (!in_array($r, ['hod', 'teacher'], true) || $cid === '') out(400, ['error' => 'role']);
            $a['colleges'][$cid]['rooms'][$room][$r] = $on; ccLog($me['dept'], $me, ($on ? 'opened ' : 'closed ') . CC_ROOMS[$room][0] . ' for ' . ($r === 'hod' ? 'heads of department' : 'teachers')); }
        else out(400, ['error' => 'scope']);
        if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
    }
    $roles = []; if ($cid !== '') { $saved = (array) ($a['colleges'][$cid]['rooms'] ?? []); foreach (CC_ROOMS as $k => $_) $roles[$k] = ['hod' => !(isset($saved[$k]['hod']) && empty($saved[$k]['hod'])), 'teacher' => !(isset($saved[$k]['teacher']) && empty($saved[$k]['teacher']))]; }
    out(200, ['rooms' => array_map(fn($k) => ['id' => $k, 'name' => CC_ROOMS[$k][0], 'about' => CC_ROOMS[$k][1]], array_keys(CC_ROOMS)), 'super' => $sup, 'college' => $cid, 'collegeName' => $cid !== '' ? ccCollege($a, $cid)['name'] : '',
        'siteOff' => array_values((array) (($a['rooms'] ?? [])['off'] ?? [])), 'collegeOff' => $cid !== '' ? array_values((array) ($a['colleges'][$cid]['roomsOff'] ?? [])) : [], 'roles' => (object) $roles, 'me' => ccRooms($a, ccMe() ?: $me)]);
}
/* ---------- joining an institution's workspace. An institution's manager says which institution in the directory it is. Someone whose profile names that
   institution can then ask to join. Asking gives them nothing: a manager of that institution has to approve it, and chooses the department ---------- */
if (in_array($action, ['instlink', 'joinfind', 'joinask', 'joinlist', 'joindecide'], true)) {
    require_once __DIR__ . '/inst-lib.php'; $t = fn($v, int $n): string => mb_substr(trim(str_replace(["\0", '<', '>'], '', (string) $v)), 0, $n);
    $inst = fn($x): array => ['ref' => $t($x['ref'] ?? '', 30), 'name' => $t($x['name'] ?? '', 120), 'town' => $t($x['town'] ?? '', 80), 'postcode' => $t($x['postcode'] ?? '', 12), 'source' => $t($x['source'] ?? '', 40)];
    $mgr = in_array($me['role'], ['superadmin', 'owner'], true); $myCol = (string) $me['college'];
    if ($action === 'instlink') { if (!$mgr || $myCol === '' || !isset($a['colleges'][$myCol])) out(403, ['error' => 'noperm']);
        if (is_array($body['insts'] ?? null)) { $l = []; foreach (array_slice($body['insts'], 0, 5) as $x) if (is_array($x) && $t($x['name'] ?? '', 120) !== '') $l[] = $inst($x); $a['colleges'][$myCol]['inst'] = $l; if (!ccSaveAccounts($a)) out(500, ['error' => 'save']); ccLog($me['dept'], $me, 'changed which institution this institution is in the directory'); }
        out(200, ['insts' => array_values((array) ($a['colleges'][$myCol]['inst'] ?? []))]); }
    if ($action === 'joinfind') { $mine = array_map($inst, array_filter(array_slice((array) ($body['insts'] ?? []), 0, 6), 'is_array')); $o = [];
        foreach ($a['colleges'] as $cid => $c) { if ((string) $cid === $myCol && $me['role'] !== 'superadmin') continue; foreach ((array) ($c['inst'] ?? []) as $li) foreach ($mine as $mi) if (ccInstSame($li, $mi)) { $open = null; foreach ((array) ($a['joins'] ?? []) as $j) if ($j['email'] === $me['email'] && $j['college'] === (string) $cid) $open = $j;
                $o[(string) $cid] = ['college' => (string) $cid, 'name' => (string) ccCollege($a, (string) $cid)['name'] ?: $li['name'], 'inst' => $li['name'], 'asked' => $open ? (int) $open['at'] : 0, 'state' => $open ? (string) ($open['state'] ?? 'open') : '']; } }
        out(200, ['colleges' => array_values($o), 'manager' => $me['role'] === 'owner']); }
    if ($action === 'joinask') { if (!empty($me['demo']) || $me['role'] === 'superadmin') out(403, ['error' => 'noperm']); $cid = (string) ($body['college'] ?? ''); if (!isset($a['colleges'][$cid]) || $cid === $myCol) out(404, ['error' => 'not_found']);
        $p = ccProfileGet($me['email']); $hit = null; foreach ((array) ($a['colleges'][$cid]['inst'] ?? []) as $li) foreach ((array) ($p['insts'] ?? []) as $mi) if (ccInstSame($li, $mi)) $hit = $mi; if (!$hit) out(403, ['error' => 'profile']);   // only for an institution that is in your own saved profile
        $a['joins'] = array_values(array_filter((array) ($a['joins'] ?? []), fn($j) => !($j['email'] === $me['email'] && $j['college'] === $cid) && (int) $j['at'] > time() - 90 * 86400)); if (count(array_filter($a['joins'], fn($j) => $j['email'] === $me['email'] && ($j['state'] ?? 'open') === 'open')) >= 3) out(429, ['error' => 'many']);
        $a['joins'][] = ['id' => bin2hex(random_bytes(6)), 'email' => $me['email'], 'name' => (string) ($me['name'] ?? ''), 'college' => $cid, 'inst' => $hit['name'], 'note' => $t($body['note'] ?? '', 300), 'at' => time(), 'state' => 'open'];
        if (!ccSaveAccounts($a)) out(500, ['error' => 'save']); ccMemberStatus($me['email'], $hit, 'requested', $cid);
        foreach ($a['users'] as $e => $u) if (($u['role'] ?? '') === 'owner' && ccActive($u) && ccCollegeOf($a, (string) ($u['dept'] ?? '')) === $cid) ccMail((string) $e, 'Someone has asked to join your institution', "Hello,\n\n" . ((string) ($me['name'] ?? '') ?: $me['email']) . " (" . $me['email'] . ") has asked to join your institution's workspace on Crafticulum. They have been given nothing yet. Open Settings, Your institution, to approve or decline.\n\n" . ccAppUrl() . "\n");
        out(200, ['ok' => true]); }
    if (!$mgr || $myCol === '') out(403, ['error' => 'noperm']);
    if ($action === 'joindecide') { $id = (string) ($body['id'] ?? ''); $k = null; foreach ((array) ($a['joins'] ?? []) as $i => $j) if ($j['id'] === $id && $j['college'] === $myCol && ($j['state'] ?? 'open') === 'open') $k = $i; if ($k === null) out(404, ['error' => 'not_found']);
        $j = $a['joins'][$k]; $em = (string) $j['email']; $u = $a['users'][$em] ?? null; $ok = !empty($body['ok']);
        if ($ok) { if (!$u || !ccActive($u)) out(400, ['error' => 'gone']); $d = (string) ($body['dept'] ?? ''); if (!isset($a['depts'][$d]) || ccCollegeOf($a, $d) !== $myCol) out(400, ['error' => 'dept']);
            $from = ccCollegeOf($a, (string) ($u['dept'] ?? '')); if (($u['role'] ?? '') === 'owner') { $others = count(array_filter($a['users'], fn($x, $e) => $e !== $em && ccActive($x) && ccCollegeOf($a, (string) ($x['dept'] ?? '')) === $from, ARRAY_FILTER_USE_BOTH)); if ($others > 0 || !empty($a['colleges'][$from]['bill']['sub'])) out(400, ['error' => 'manager']); }   // someone running an institution with staff or a paid plan can't be moved out from under it
            seatCheck($a, $d); $a['users'][$em] = array_merge($u, ['role' => 'teacher', 'dept' => $d, 'canEdit' => false, 'movedFrom' => (string) ($u['dept'] ?? ''), 'approvedBy' => $me['email'], 'added' => time()]); unset($a['users'][$em]['perms']);
            $carried = !empty($body['lessons']) ? ccCarryLessons($a, $em, (string) ($u['dept'] ?? ''), $d) : 0; if ($carried) ccLog($d, $me, 'brought ' . $carried . ' lessons across with', $em);
            ccLog($me['dept'], $me, 'approved a request to join from', $em); ccMail($em, 'You have joined ' . (ccCollege($a, $myCol)['name'] ?: 'the institution'), "Hello,\n\nYour request to join " . (ccCollege($a, $myCol)['name'] ?: 'the institution') . " on Crafticulum was approved. Sign in and you'll be in its workspace as a teacher." . ($carried ? ' ' . ($carried === 1 ? 'The lesson you wrote has' : 'The ' . $carried . ' lessons you wrote have') . ' come with you, as ' . ($carried === 1 ? 'a draft' : 'drafts') . ' in your new department.' : ' Lessons from your old workspace stay there.') . "\n\n" . ccAppUrl() . "\n"); }
        else ccLog($me['dept'], $me, 'declined a request to join from', $em);
        $a['joins'][$k]['state'] = $ok ? 'approved' : 'declined'; $a['joins'][$k]['by'] = $me['email']; $a['joins'][$k]['decided'] = time(); if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
        foreach ((array) ($a['colleges'][$myCol]['inst'] ?? []) as $li) ccMemberStatus($em, $li, $ok ? 'approved' : 'declined', $myCol, $me['email']); }
    $depts = []; foreach ($a['depts'] as $d => $x) if (ccCollegeOf($a, (string) $d) === $myCol) $depts[] = ['id' => (string) $d, 'name' => (string) ($x['name'] ?? $d)];
    out(200, ['joins' => array_values(array_map(fn($j) => ['id' => $j['id'], 'email' => $j['email'], 'name' => $j['name'], 'inst' => $j['inst'], 'note' => (string) ($j['note'] ?? ''), 'at' => (int) $j['at'], 'state' => (string) ($j['state'] ?? 'open')], array_filter((array) ($a['joins'] ?? []), fn($j) => $j['college'] === $myCol))), 'depts' => $depts, 'insts' => array_values((array) ($a['colleges'][$myCol]['inst'] ?? [])), 'carried' => (int) ($carried ?? 0)]);
}
/* an institution's own settings: its house rules for the AI, and whether its learners are kept anonymous. For its manager, or the super admin working inside it */
if ($action === 'houseget' || $action === 'houseset') {
    $sup = $me['role'] === 'superadmin'; $own = $me['role'] === 'owner'; if (!$sup && !$own) out(403, ['error' => 'noperm']);
    $cid = (string) $me['college']; if ($cid === '' || !isset($a['colleges'][$cid])) out(404, ['error' => 'not_found']);
    if ($action === 'houseset') {
        if (is_array($body['house'] ?? null)) { $h = []; foreach (CC_HOUSE as $k => $n) $h[$k] = mb_substr(trim(str_replace(["\r", "\0"], '', (string) ($body['house'][$k] ?? ''))), 0, $n); $h['on'] = !empty($body['house']['on']);
            $a['colleges'][$cid]['house'] = $h; ccLog($me['dept'], $me, 'changed the house rules for the AI'); }
        if (array_key_exists('anon', $body)) { $on = !empty($body['anon']); if ($on !== ccAnon($a, $cid)) { $a['colleges'][$cid]['anon'] = $on; ccLog($me['dept'], $me, $on ? 'switched on anonymous learners' : 'switched off anonymous learners'); } }
        if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
    }
    out(200, ['house' => ccHouse($a, $cid), 'anon' => ccAnon($a, $cid), 'collegeName' => ccCollege($a, $cid)['name'], 'limits' => CC_HOUSE, 'me' => ccPublic(ccMe() ?: $me)]);
}
/* the super admin steps back out of the institution they were working inside */
if ($action === 'leave') {
    if ($me['role'] !== 'superadmin') out(403, ['error' => 'noperm']);
    unset($_SESSION['inside'], $_SESSION['dept']);
    if (isset($a['users'][$me['email']]['dept']) || isset($a['users'][$me['email']]['also']) || empty($a['saOutside'])) { unset($a['users'][$me['email']]['dept'], $a['users'][$me['email']]['also']); $a['saOutside'] = true; ccSaveAccounts($a); }
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
    if (array_key_exists('handin', $body)) { $a['depts'][$d]['handin'] = !empty($body['handin']); ccLog($d, $me, !empty($body['handin']) ? 'switched on hand-in links (handed-in work is kept for ' . CC_HANDIN_DAYS . ' days)' : 'switched off hand-in links'); }
    if (array_key_exists('tools', $body)) { $a['depts'][$d]['tools'] = !empty($body['tools']); ccLog($d, $me, !empty($body['tools']) ? 'opened AI Tools to all staff' : 'kept AI Tools to people who can build lessons'); }
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
/* ---------- the site: the institution's name, logo and colour, and the sign-in, email and Crafticulum AI settings (super admin) ---------- */
if ($action === 'siteget' || $action === 'siteset') {
    /* the institution's own profile (name, logo, colour, contact) belongs to the institution the person is working in: its manager or the super admin can change it.
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
        ccLog($me['dept'], $me, $super ? 'changed the site settings' : 'changed the institution\'s profile');
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
    if ($super && !empty($u['site']['calls'])) $depts[] = ['id' => '', 'name' => 'Super admin (not part of an institution)'] + $row((array) $u['site']);
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
function fileFor2(string $dir, string $p): string { return $dir . '/' . sha1($p) . '.json'; }
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
/* ---------- the super admin: one institution, with everything in it ---------- */
if ($action === 'collegeget') {
    if ($me['role'] !== 'superadmin' && $me['role'] !== 'owner') out(403, ['error' => 'noperm']);
    $id = $me['role'] === 'owner' ? (string) $me['college'] : (string) ($body['id'] ?? ''); if (!isset($a['colleges'][$id])) out(404, ['error' => 'not_found']);   // a manager only ever gets their own institution
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
    out(200, ['saved' => ccSaved($id), 'planName' => $pl['name'], 'limits' => ['builds' => $pl['builds'], 'checks' => $pl['checks'], 'people' => $pl['people'], 'depts' => $pl['depts']], 'id' => $id, 'name' => $c['name'], 'contact' => $c['contact'], 'color' => $c['color'], 'logo' => $c['logo'], 'plain' => $c['plain'], 'off' => $c['off'], 'cap' => $c['cap'], 'plan' => $c['plan'], 'paid' => !empty($c['bill']['sub']), 'cancelled' => !empty($c['bill']['sub']) && !empty($c['bill']['cancelAt']), 'billStatus' => (string) ($c['bill']['status'] ?? ''),
              'self' => !empty($a['colleges'][$id]['self']), 'created' => $c['created'], 'depts' => $depts, 'people' => $people, 'lessons' => $lessons, 'calls' => $calls, 'used' => ccPlanUsed($id, $u), 'log' => array_slice($log, 0, 12), 'billing' => ccBillingOn(), 'mail' => ccMailReady()]);
}
/* a deleted institution waits here for 14 days, then goes for good. Until then it can be put back */
if (in_array($action, ['collegerestore', 'collegepurge'], true)) {
    if ($me['role'] !== 'superadmin') out(403, ['error' => 'noperm']);
    $hid = (string) ($body['hold'] ?? ''); $hold = ccDataDir() . '/_deleted/' . $hid; if (!preg_match('/^[a-z0-9][a-z0-9-]*-\d{9,11}$/', $hid) || !is_file($hold . '/meta.json')) out(404, ['error' => 'not_found']);
    $meta = json_decode((string) @file_get_contents($hold . '/meta.json'), true); if (!is_array($meta)) out(404, ['error' => 'not_found']);
    if ($action === 'collegepurge') { ccDeletedRm($hold); ccLog($me['dept'], $me, 'removed a deleted institution for good', (string) ($meta['name'] ?? '')); out(200, ['colleges' => collegeList($a), 'depts' => deptList($a), 'deleted' => ccDeletedList()]); }
    $cid = (string) ($meta['id'] ?? ''); if ($cid === '' || isset($a['colleges'][$cid])) out(409, ['error' => 'exists']);
    foreach ((array) ($meta['depts'] ?? []) as $d => $r) if (isset($a['depts'][(string) $d])) out(409, ['error' => 'exists']);
    foreach ($a['colleges'] as $c) if ((string) ($meta['name'] ?? '') !== '' && strcasecmp((string) ($c['name'] ?? ''), (string) $meta['name']) === 0) out(409, ['error' => 'name']);
    $a['colleges'][$cid] = (array) $meta['college']; foreach ((array) $meta['depts'] as $d => $r) $a['depts'][(string) $d] = (array) $r;
    $back = 0; $skipped = []; foreach ((array) ($meta['users'] ?? []) as $e => $u) { if (isset($a['users'][(string) $e])) { $skipped[] = (string) $e; continue; } $a['users'][(string) $e] = (array) $u; $back++; }   // anyone who has since joined somewhere else stays where they are
    $base = ccDataDir(); foreach ((array) ($meta['moved'] ?? []) as $rel) { $rel = (string) $rel; if ($rel === '' || strpos($rel, '..') !== false || $rel[0] === '/') continue; $from = $hold . '/files/' . $rel; $to = $base . '/' . $rel; if (!file_exists($from) || file_exists($to)) continue; if (!is_dir(dirname($to))) @mkdir(dirname($to), 0700, true); @rename($from, $to); }
    if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
    ccDeletedRm($hold); ccLog($me['dept'], $me, 'put a deleted institution back', (string) ($meta['name'] ?? ''));
    out(200, ['colleges' => collegeList($a), 'depts' => deptList($a), 'deleted' => ccDeletedList(), 'restored' => ['id' => $cid, 'people' => $back, 'skipped' => $skipped]]);
}
if (in_array($action, ['colleges', 'collegeadd', 'collegeset', 'collegedel'], true)) {
    if ($me['role'] !== 'superadmin') out(403, ['error' => 'noperm']);
    $extra = [];
    if ($action === 'collegeadd') {
        $name = cleanName((string) ($body['name'] ?? '')); if ($name === '') out(400, ['error' => 'name']);
        $same = collegeByName($a, [$name], null); if ($same !== '' && empty($body['force'])) out(409, ['error' => 'exists', 'like' => (string) (ccCollege($a, $same)['name'] ?? '')]);   // "Midkent" when MidKent College is already here
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
        ccLog($did, $me, 'set up the institution', $name);
        if ($mgr !== '') { $t = ccToken($mgr, 'invite', 24 * 7); $extra['link'] = ccLink('invite', $t);
            $extra['mailed'] = ccMail($mgr, "You've been added to Crafticulum", "Hello" . (!empty($a['users'][$mgr]['name']) ? ' ' . $a['users'][$mgr]['name'] : '') . ",\n\n" . ($me['name'] ?: $me['email']) . " has set up $name on Crafticulum and made you its manager. You can add its departments and staff, and set its name, logo and colour.\n\nChoose a password with this link (it works for 7 days):\n" . $extra['link'] . "\n\n" . (ccGoogleId() || ccMsId() ? "Or sign in with your Google or Microsoft account using this email address.\n" : "")); }
        $extra += ['newCollege' => $id, 'newDept' => $did];
    }
    if ($action === 'collegeset' || $action === 'collegedel') {
        $id = (string) ($body['id'] ?? ''); if (!isset($a['colleges'][$id])) out(404, ['error' => 'not_found']);
        if ($action === 'collegeset') {
            if (array_key_exists('name', $body)) { $name = cleanName((string) $body['name']); if ($name === '') out(400, ['error' => 'name']); $tmp = $a; unset($tmp['colleges'][$id]); $same = nameKey($name) !== nameKey((string) ($a['colleges'][$id]['name'] ?? '')) ? collegeByName($tmp, [$name], null) : ''; if ($same !== '' && empty($body['force'])) out(409, ['error' => 'exists', 'like' => (string) (ccCollege($a, $same)['name'] ?? '')]); $a['colleges'][$id]['name'] = $name; }
            if (array_key_exists('contact', $body)) { $c = strtolower(trim((string) $body['contact'])); if ($c !== '' && !emailOk($c)) out(400, ['error' => 'email']); $a['colleges'][$id]['contact'] = $c; }
            if (array_key_exists('color', $body)) { $c = (string) $body['color']; $a['colleges'][$id]['color'] = preg_match('/^#[0-9a-fA-F]{6}$/', $c) ? strtolower($c) : ''; }
            if (array_key_exists('logo', $body)) { $l = (string) $body['logo']; if ($l !== '' && (!preg_match('#^data:image/(png|jpeg|webp|svg\+xml);base64,[A-Za-z0-9+/=]+$#', $l) || strlen($l) > 300000)) out(400, ['error' => 'logo']); $a['colleges'][$id]['logo'] = $l; }
            if (array_key_exists('cap', $body)) $a['colleges'][$id]['cap'] = max(0, min(100000000, (int) $body['cap']));
            if (array_key_exists('plan', $body)) { $pl = (string) $body['plan']; if (!isset(CC_PLANS[$pl])) out(400, ['error' => 'plan']); $a['colleges'][$id]['plan'] = $pl; }   // set by hand: a quote agreed outside Stripe, or a free upgrade
            if (array_key_exists('off', $body)) { if (!empty($body['off']) && $id === $me['college'] && !empty($me['inside'] ?? true)) out(400, ['error' => 'own']); $a['colleges'][$id]['off'] = !empty($body['off']); }
            ccLog($me['dept'], $me, 'changed the institution', (string) $a['colleges'][$id]['name']);
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
                unset($a['colleges'][$id]); ccLog($me['dept'], $me, 'deleted the institution', $cname . ' (' . $gone['people'] . ' people, ' . $gone['depts'] . ' departments). Kept for ' . CC_DELETED_DAYS . ' days'); $extra['gone'] = $gone + ['days' => CC_DELETED_DAYS];
            } else {
            foreach ($a['users'] as $e => $u) if (in_array((string) ($u['dept'] ?? ''), $depts, true) && ccActive($u) && !in_array((string) $e, ccSupers(), true)) out(400, ['error' => 'has_staff']);
            foreach ($depts as $d) unset($a['depts'][$d]);     // the departments' files stay in data/d/ in case they're wanted back
            ccLog($me['dept'], $me, 'removed the institution', $cname); unset($a['colleges'][$id]);
            }
        }
        if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
    }
    out(200, ['colleges' => collegeList($a), 'depts' => deptList($a), 'deleted' => ccDeletedList()] + $extra);
}
/* ---------- staff and departments ---------- */
if (!$me['canTeam']) out(403, ['error' => 'noperm']);
$isSuper = $me['role'] === 'superadmin'; $isAdmin = !empty($me['admin']);
/* who can manage whom. The super admin: everyone. An institution manager: every head of department and teacher in their own institution, and the people
   asking to join it, but not other managers. A head of department: the teachers in their own department and people asking to join it as a teacher. */
function canManage(array $u, array $me, ?array $a = null): bool {
    if ($me['role'] === 'superadmin') return true;
    if (empty($me['canTeam'])) return false;
    $a = $a ?? ccAccounts(); $one = count(ccCollegeIds($a)) < 2;
    if ($me['role'] === 'owner') {
        if (($u['role'] ?? '') === 'owner' && ccActive($u)) return false;
        $d = (string) (ccActive($u) ? ($u['dept'] ?? '') : ($u['wantDept'] ?? ''));
        if ($d === '') return !ccActive($u) && $one;                 // someone who didn't say which department: only when this is the site's only institution
        return isset($a['depts'][$d]) && $d !== CC_DEMO_DEPT && ccCollegeOf($a, $d) === $me['college'];
    }
    if (ccActive($u)) return ($u['role'] ?? 'teacher') === 'teacher' && ($u['dept'] ?? '') === $me['dept'];
    if (!empty($u['viaInst'])) return false;   // someone who asked through their institution is for the institution's manager to approve
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
        $row = ['email' => $email, 'name' => (string) ($u['name'] ?? ''), 'role' => (string) ($u['role'] ?? 'teacher'), 'dept' => (string) ($u['dept'] ?? ''), 'also' => (object) ccAlso($a, $u), 'canEdit' => !empty($u['canEdit']), 'rooms' => ccRooms($a, ['email' => $email, 'role' => (string) ($u['role'] ?? 'teacher'), 'college' => ccCollegeOf($a, (string) ($u['dept'] ?? ''))]), 'roomsOpen' => ccRoomsCollege($a, ccCollegeOf($a, (string) ($u['dept'] ?? ''))), 'perms' => ccPerms($u),
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
/* ---------- more than one department. A manager says which other departments someone teaches in; that person then switches between them ---------- */
if ($action === 'useralso') {
    if (!$isAdmin) out(403, ['error' => 'noperm']);
    $em = strtolower(trim((string) ($body['email'] ?? ''))); $u = $a['users'][$em] ?? null; if (!$u || in_array($em, ccSupers(), true)) out(404, ['error' => 'not_found']);
    $home = (string) ($u['dept'] ?? ''); $col = ccCollegeOf($a, $home); if (!$isSuper && $col !== (string) $me['college']) out(403, ['error' => 'noperm']);
    if (($u['role'] ?? '') === 'owner') out(400, ['error' => 'owner']);   // a manager already works in every department
    $also = []; foreach ((array) ($body['also'] ?? []) as $d => $r) { $d = (string) $d; if ($d === $home || $d === CC_DEMO_DEPT || !isset($a['depts'][$d]) || ccCollegeOf($a, $d) !== $col) continue; $also[$d] = $r === 'hod' ? 'hod' : 'teacher'; if (count($also) >= 12) break; }
    if ($also) $a['users'][$em]['also'] = $also; else unset($a['users'][$em]['also']);
    ccSaveAccounts($a); ccLog($me['dept'], $me, 'set the departments for', $em); teamOut(ccAccounts(), $me);
}
/* ---------- splitting one department into two by subject. For a department that grew up teaching several subjects (the original one here):
   a new department takes the subjects you name, with their lessons, plans and classes, and everyone stays in both. Preview first, a copy of
   everything touched is kept, and it can be undone. ---------- */
if ($action === 'deptsplit' || $action === 'deptsplitundo') {
    if (!$isSuper) out(403, ['error' => 'noperm']); @set_time_limit(300);
    $base = ccDataDir(); $bkRoot = $base . '/_split';
    if ($action === 'deptsplitundo') {
        $last = null; foreach (glob($bkRoot . '/*/manifest.json') ?: [] as $f) { $m = json_decode((string) @file_get_contents($f), true); if (is_array($m) && empty($m['undone']) && (!$last || $m['at'] > $last['at'])) $last = $m + ['file' => $f]; }
        if (!$last) out(404, ['error' => 'none']);
        foreach (array_reverse((array) $last['moves']) as [$from, $to]) { if (is_file($base . '/' . $to)) { if (!is_dir(dirname($base . '/' . $from))) @mkdir(dirname($base . '/' . $from), 0700, true); @rename($base . '/' . $to, $base . '/' . $from); } }
        foreach ((array) $last['copies'] as $to) @unlink($base . '/' . $to); foreach ((array) ($last['made'] ?? []) as $to) @unlink($base . '/' . $to);
        /* anything made in the new department since comes back too, so nothing is stranded */
        $fromDir = !empty(ccAccounts()['depts'][$last['from']]['legacy']) || is_file(dirname($last['file']) . '/accounts.json') && !empty((json_decode((string) file_get_contents(dirname($last['file']) . '/accounts.json'), true)['depts'][$last['from']] ?? [])['legacy']) ? $base : $base . '/d/' . $last['from'];
        foreach (glob($base . '/d/' . $last['new'] . '/*.json') ?: [] as $f) { $t = $fromDir . '/' . basename($f); if (!is_file($t)) @rename($f, $t); }
        foreach (glob($base . '/u/*/' . $last['new'] . '/*.json') ?: [] as $f) { $t = dirname(dirname($f)) . '/' . $last['from'] . '/' . basename($f); if (!is_dir(dirname($t))) @mkdir(dirname($t), 0700, true); if (!is_file($t)) @rename($f, $t); }
        foreach ((array) $last['saved'] as $rel) { $bk = dirname($last['file']) . '/saved/' . sha1($rel); if (is_file($bk)) @copy($bk, $base . '/' . $rel); }
        $acc = json_decode((string) @file_get_contents(dirname($last['file']) . '/accounts.json'), true); if (is_array($acc)) {   // people and departments go back as they were, keeping anyone added since
            $now = ccAccounts(); unset($now['depts'][$last['new']]); if (isset($acc['depts'][$last['from']])) $now['depts'][$last['from']] = $acc['depts'][$last['from']];
            foreach ($now['users'] as $e => $u) { if (isset($u['also'][$last['new']])) { unset($now['users'][$e]['also'][$last['new']]); if (!$now['users'][$e]['also']) unset($now['users'][$e]['also']); } if (($u['dept'] ?? '') === $last['new']) $now['users'][$e]['dept'] = $last['from']; }
            ccSaveAccounts($now); }
        $m = json_decode((string) file_get_contents($last['file']), true); $m['undone'] = time(); file_put_contents($last['file'], json_encode($m));
        ccLog($me['dept'], $me, 'undid the department split', (string) $last['newName']); out(200, ['ok' => true, 'undone' => $last['newName']]);
    }
    $from = (string) ($body['dept'] ?? ''); $di = $a['depts'][$from] ?? null; if (!$di || $from === CC_DEMO_DEPT) out(404, ['error' => 'not_found']);
    $subs = array_values(array_unique(array_filter(array_map(fn($x) => trim((string) $x), (array) ($body['subjects'] ?? [])), 'strlen'))); if (!$subs) out(400, ['error' => 'subjects']);
    $newName = cleanName((string) ($body['name'] ?? '')); $keepName = cleanName((string) ($body['keepName'] ?? '')); if ($newName === '') out(400, ['error' => 'name']);
    $units = is_array($body['units'] ?? null) ? $body['units'] : [];   // every subject the department teaches now, as the page sees them, built-in ones included
    $units = array_values(array_filter($units, fn($x) => is_array($x) && isset($x['s'], $x['l']) && is_array($x['u'] ?? null))); if (!$units) out(400, ['error' => 'units']);
    $go = fn(array $x) => in_array((string) $x['s'], $subs, true); $moveU = array_values(array_filter($units, $go)); $keepU = array_values(array_filter($units, fn($x) => !$go($x)));
    if (!$moveU) out(400, ['error' => 'subjects']); if (!$keepU) out(400, ['error' => 'all']);
    $legacy = !empty($di['legacy']); $ddir = $legacy ? $base : $base . '/d/' . $from; $rel = fn(string $abs) => ltrim(substr($abs, strlen($base)), '/');
    $isSub = fn(string $c) => in_array($c, $subs, true); $keyIs = fn(string $k) => $isSub((string) explode('|', $k)[0]);
    /* what would move, and what would be copied so the new department starts with the same settings */
    $moves = []; $copies = []; $n = ['lessons' => 0, 'plans' => 0, 'own' => 0, 'classes' => 0, 'mixed' => 0, 'people' => 0];
    foreach (glob($ddir . '/*.json') ?: [] as $f) { $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j)) continue; $p = (string) ($j['path'] ?? ''); if ($p === '') continue;
        if (strpos($p, 'lessons/') === 0) { $l = json_decode((string) ($j['data']['lesson'] ?? ''), true); $c = is_array($l) ? (string) ($l['meta']['course'] ?? ($j['data']['course'] ?? '')) : (string) ($j['data']['course'] ?? ''); if ($isSub($c)) { $moves[] = [$f, 'd']; $n['lessons']++; } continue; }
        if (preg_match('#^data/users/[^/]+/seqs/#', $p)) { $sq = json_decode((string) ($j['data']['json'] ?? $j['data']['seq'] ?? ''), true); $c = is_array($sq) && !empty($sq['course']) ? (string) $sq['course'] : (string) explode('|', (string) ($j['data']['key'] ?? (is_array($sq) ? ($sq['key'] ?? '') : '')))[0]; if ($isSub($c)) { $moves[] = [$f, 'd']; $n['plans']++; } continue; }
        if (preg_match('#^data/users/[^/]+/settings/courses$#', $p)) continue;   // written fresh for both, below
        $copies[] = [$f, 'd']; }
    $people = []; foreach ($a['users'] as $e => $u) { if (!is_array($u) || (string) ($u['dept'] ?? '') !== $from || in_array((string) $e, ccSupers(), true)) continue; $people[] = (string) $e; } $n['people'] = count($people);
    foreach (glob($base . '/u/*/' . $from . '/*.json') ?: [] as $f) { $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j)) continue; $p = (string) ($j['path'] ?? ''); $who = basename(dirname(dirname($f)));
        if (preg_match('#^data/users/[^/]+/lessons/#', $p)) { $c = (string) ($j['data']['course'] ?? ''); if ($c === '') { $l = json_decode((string) ($j['data']['lesson'] ?? ''), true); $c = is_array($l) ? (string) ($l['meta']['course'] ?? '') : ''; } if ($isSub($c)) { $moves[] = [$f, 'u/' . $who]; $n['own']++; } continue; }
        if (preg_match('#^data/users/[^/]+/learners/#', $p)) { $st = json_decode((string) ($j['data']['json'] ?? ''), true); if (!is_array($st)) continue; $keys = []; foreach (['marks', 'subs'] as $k) foreach ((array) ($st[$k] ?? []) as $uk => $v) if ($v) $keys[(string) $uk] = 1; if (!$keys) continue;
            $in = count(array_filter(array_keys($keys), $keyIs)); if ($in === count($keys)) { $moves[] = [$f, 'u/' . $who]; $n['classes']++; } elseif ($in > 0) $n['mixed']++; continue; }
        if (preg_match('#^data/users/[^/]+/seqs/#', $p)) { $sq = json_decode((string) ($j['data']['json'] ?? $j['data']['seq'] ?? ''), true); $c = is_array($sq) && !empty($sq['course']) ? (string) $sq['course'] : (string) explode('|', (string) ($j['data']['key'] ?? (is_array($sq) ? ($sq['key'] ?? '') : '')))[0]; if ($isSub($c)) { $moves[] = [$f, 'u/' . $who]; $n['plans']++; } continue; }
        $copies[] = [$f, 'u/' . $who]; }
    $plan = ['from' => (string) $di['name'], 'keepName' => $keepName !== '' ? $keepName : (string) $di['name'], 'newName' => $newName, 'moving' => array_values(array_unique(array_map(fn($x) => (string) $x['s'], $moveU))), 'staying' => array_values(array_unique(array_map(fn($x) => (string) $x['s'], $keepU)))] + $n;
    if (empty($body['go'])) out(200, ['plan' => $plan]);
    /* do it: a copy of everything first */
    $id = newDeptId($a, $newName); $bk = $bkRoot . '/' . date('Ymd-His'); if (!@mkdir($bk . '/saved', 0700, true)) out(500, ['error' => 'write']);
    @copy($base . '/_accounts.json', $bk . '/accounts.json'); $newD = $base . '/d/' . $id; $dest = fn(string $f, string $where) => ($where === 'd' ? $newD : $base . '/' . $where . '/' . $id) . '/' . basename($f);
    $man = ['at' => time(), 'by' => (string) $me['email'], 'from' => $from, 'new' => $id, 'newName' => $newName, 'moves' => [], 'copies' => [], 'saved' => []];
    $cf = fileFor2($ddir, 'data/users/teacher/settings/courses'); if (is_file($cf)) { @copy($cf, $bk . '/saved/' . sha1($rel($cf))); $man['saved'][] = $rel($cf); } else $man['made'] = [$rel($cf)];
    file_put_contents($bk . '/manifest.json', json_encode($man));
    foreach ($moves as [$f, $w]) { $t = $dest($f, $w); if (!is_dir(dirname($t))) @mkdir(dirname($t), 0700, true); if (@rename($f, $t)) $man['moves'][] = [$rel($f), $rel($t)]; }
    foreach ($copies as [$f, $w]) { $t = $dest($f, $w); if (!is_dir(dirname($t))) @mkdir(dirname($t), 0700, true); if (!is_file($t) && @copy($f, $t)) $man['copies'][] = $rel($t); }
    $doc = fn(array $list) => json_encode(['path' => 'data/users/teacher/settings/courses', 'data' => ['json' => json_encode(['builtin' => false, 'list' => $list], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'updatedAt' => (int) (microtime(true) * 1000)]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_dir($newD)) @mkdir($newD, 0700, true); $nf = fileFor2($newD, 'data/users/teacher/settings/courses'); file_put_contents($nf, $doc($moveU), LOCK_EX); $man['copies'][] = $rel($nf); file_put_contents($cf, $doc($keepU), LOCK_EX);
    $a = ccAccounts(); $nd = $a['depts'][$from]; unset($nd['legacy'], $nd['legacyOwner']); $nd['name'] = $newName; $nd['created'] = time(); $nd['splitFrom'] = $from; $a['depts'][$id] = $nd; if ($keepName !== '') $a['depts'][$from]['name'] = $keepName;
    foreach ($people as $e) { $r = (string) ($a['users'][$e]['role'] ?? 'teacher'); if ($r === 'owner') continue; $al = (array) ($a['users'][$e]['also'] ?? []); $al[$id] = $r === 'hod' ? 'hod' : 'teacher'; $a['users'][$e]['also'] = $al; }
    ccSaveAccounts($a); file_put_contents($bk . '/manifest.json', json_encode($man));
    ccLog($me['dept'], $me, 'split a department by subject:', $newName); out(200, ['ok' => true, 'plan' => $plan, 'newDept' => $id, 'depts' => deptList(ccAccounts()), 'colleges' => collegeList(ccAccounts())]);
}
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
/* colleges that look like the same place set up twice, and folding one into the other. Super admin only */
if (in_array($action, ['collegedupes', 'collegemerge'], true)) {
    if (!$isSuper) out(403, ['error' => 'noperm']);
    $people = fn(array $a, string $cid): array => array_keys(array_filter($a['users'], fn($u, $e) => empty($u['demo']) && !in_array((string) $e, ccSupers(), true) && (ccActive($u) ? in_array((string) ($u['dept'] ?? ''), ccCollegeDepts($a, $cid), true) : (($u['viaInst'] ?? '') === $cid || in_array((string) ($u['wantDept'] ?? ''), ccCollegeDepts($a, $cid), true))), ARRAY_FILTER_USE_BOTH));
    $dupes = function (array $a) use ($people): array { $by = [];
        foreach ($a['colleges'] as $cid => $c) { $cid = (string) $cid; if (!empty($c['off']) || !ccCollegeDepts($a, $cid)) continue; $k = nameKey((string) ($c['name'] ?? '')); if (strlen($k) >= 4) $by['n:' . $k][$cid] = 1;
            foreach ($a['users'] as $e => $u) if (ccActive($u) && ($u['role'] ?? '') === 'owner' && isset($a['depts'][(string) ($u['dept'] ?? '')]) && ccCollegeOf($a, (string) $u['dept']) === $cid && ($d = mailDomain((string) $e)) !== '') $by['d:' . $d][$cid] = 1; }
        $o = []; $seen = []; foreach ($by as $k => $ids) { $ids = array_keys($ids); sort($ids); if (count($ids) < 2 || isset($seen[implode('|', $ids)])) continue; $seen[implode('|', $ids)] = 1;
            $o[] = ['why' => $k[0] === 'n' ? 'name' : 'email', 'what' => $k[0] === 'n' ? '' : substr($k, 2), 'colleges' => array_map(fn($id) => ['id' => (string) $id, 'name' => (string) ($a['colleges'][$id]['name'] ?? ''), 'self' => !empty($a['colleges'][$id]['self']), 'paid' => !empty($a['colleges'][$id]['bill']['sub']), 'people' => count($people($a, (string) $id))], $ids)]; }
        return $o; };
    if ($action === 'collegedupes') out(200, ['dupes' => $dupes($a), 'colleges' => collegeList($a)]);
    $from = (string) ($body['from'] ?? ''); $into = (string) ($body['into'] ?? ''); if ($from === $into || !isset($a['colleges'][$from], $a['colleges'][$into])) out(400, ['error' => 'pick']);
    if (!empty($a['colleges'][$into]['off'])) out(400, ['error' => 'off']); if (!empty($a['colleges'][$from]['bill']['sub'])) out(400, ['error' => 'paid']);   // an institution paying by card has to cancel first, so nobody is charged for an institution that's gone
    $toDepts = ccCollegeDepts($a, $into); $d = (string) ($body['dept'] ?? ''); if (!in_array($d, $toDepts, true)) $d = (string) ($toDepts[0] ?? ''); if ($d === '') out(400, ['error' => 'dept']);
    $fromDepts = ccCollegeDepts($a, $from); $moved = 0; $lessons = 0; $base = ccDataDir(); @set_time_limit(120);
    foreach ($fromDepts as $fd) { $lessons += ccCarryLessons($a, '*', (string) $fd, $d); }
    foreach ($a['users'] as $e => $u) { $e = (string) $e; if (!empty($u['demo']) || in_array($e, ccSupers(), true)) continue;
        if (ccActive($u) && in_array((string) ($u['dept'] ?? ''), $fromDepts, true)) { $fd = (string) $u['dept'];
            $src = $base . '/u/' . sha1($e) . '/' . $fd; $dst = $base . '/u/' . sha1($e) . '/' . $d; if (is_dir($src)) { if (!is_dir($dst)) @mkdir($dst, 0700, true); foreach (glob($src . '/*.json') ?: [] as $f) if (!is_file($dst . '/' . basename($f))) @copy($f, $dst . '/' . basename($f)); }   // their own class lists, history and settings come too
            $a['users'][$e] = array_merge($u, ['dept' => $d, 'role' => 'teacher', 'movedFrom' => $fd, 'mergedBy' => $me['email']]); unset($a['users'][$e]['perms']); $moved++; }
        elseif (!ccActive($u) && (($u['viaInst'] ?? '') === $from || in_array((string) ($u['wantDept'] ?? ''), $fromDepts, true))) { $a['users'][$e]['viaInst'] = $into; $a['users'][$e]['wantDept'] = $d; $moved++; } }
    foreach ((array) ($a['joins'] ?? []) as $i => $j) if (($j['college'] ?? '') === $from) $a['joins'][$i]['college'] = $into;
    $fn = (string) ($a['colleges'][$from]['name'] ?? ''); $tn = (string) ($a['colleges'][$into]['name'] ?? ''); $a['colleges'][$from]['off'] = true; $a['colleges'][$from]['mergedInto'] = $into; $a['colleges'][$from]['mergedAt'] = time();
    if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
    ccLog($d, $me, 'merged the institution ' . $fn . ' into', $tn . ' (' . $moved . ' people, ' . $lessons . ' lessons)');
    out(200, ['dupes' => $dupes($a), 'colleges' => collegeList($a), 'merged' => ['from' => $fn, 'into' => $tn, 'people' => $moved, 'lessons' => $lessons]]);
}
/* All users: every account on the site, for the super admin. Confirm an email by hand, or put someone in any institution (approving them if they were waiting) */
if (in_array($action, ['allusers', 'userverify', 'usermove'], true)) {
    if (!$isSuper) out(403, ['error' => 'noperm']);
    $all = function (array $a, array $extra = []): array { $supers = ccSupers(); $rows = []; $dn = fn(string $d): string => (string) ($a['depts'][$d]['name'] ?? '');
        foreach ($a['users'] as $e => $x) { $e = (string) $e; if (!empty($x['demo']) || $e === 'admin@password') continue; $act = ccActive($x); $d = (string) ($x['dept'] ?? ''); $c = $act && isset($a['depts'][$d]) ? ccCollegeOf($a, $d) : ''; $wd = (string) ($x['wantDept'] ?? '');
            $rows[] = ['email' => $e, 'name' => (string) ($x['name'] ?? ''), 'pending' => !$act, 'verified' => !empty($x['verified']), 'super' => in_array($e, $supers, true), 'role' => (string) ($x['role'] ?? 'teacher'), 'dept' => $act ? $d : '', 'deptName' => $act ? $dn($d) : '', 'college' => $c, 'collegeName' => $c !== '' ? (string) (ccCollege($a, $c)['name'] ?? '') : '',
                'wantDept' => $wd, 'wantDeptName' => isset($a['depts'][$wd]) ? trim((string) (ccCollege($a, ccCollegeOf($a, $wd))['name'] ?? '') . ' · ' . $dn($wd), ' ·') : '', 'instName' => (string) ($x['instName'] ?? ''), 'last' => (int) ($x['last'] ?? 0), 'added' => (int) ($x['added'] ?? 0), 'requested' => (int) ($x['requested'] ?? 0), 'methods' => array_values((array) ($x['methods'] ?? [])), 'via' => (string) ($x['via'] ?? ''), 'hasPass' => !empty($x['pass'])]; }
        usort($rows, fn($x, $y) => [$x['pending'] ? 0 : 1, strtolower($x['name'] ?: $x['email'])] <=> [$y['pending'] ? 0 : 1, strtolower($y['name'] ?: $y['email'])]);
        $depts = []; foreach ($a['depts'] as $k => $d) { $k = (string) $k; if ($k === CC_DEMO_DEPT) continue; $depts[] = ['id' => $k, 'name' => (string) $d['name'], 'college' => ccCollegeOf($a, $k), 'collegeName' => (string) (ccCollege($a, ccCollegeOf($a, $k))['name'] ?? '')]; }
        usort($depts, fn($x, $y) => strcasecmp($x['collegeName'] . $x['name'], $y['collegeName'] . $y['name']));
        return $extra + ['users' => $rows, 'depts' => $depts, 'mail' => ccMailReady()]; };
    if ($action === 'allusers') out(200, $all($a));
    if (!$old || !empty($old['demo'])) out(404, ['error' => 'not_found']); if (in_array($email, ccSupers(), true)) out(400, ['error' => 'is_super']);
    if ($action === 'userverify') { $a['users'][$email]['verified'] = true; if (!ccSaveAccounts($a)) out(500, ['error' => 'save']); ccLog((string) (($old['dept'] ?? '') ?: $me['dept']), $me, 'confirmed the email address by hand for', $email); out(200, $all($a)); }
    $d = (string) ($body['dept'] ?? ''); if (!isset($a['depts'][$d]) || $d === CC_DEMO_DEPT) out(400, ['error' => 'dept']); $r = in_array($body['role'] ?? '', ['teacher', 'hod', 'owner'], true) ? (string) $body['role'] : 'teacher';
    $was = ccActive($old); $from = $was ? (string) ($old['dept'] ?? '') : ''; $fromCol = $from !== '' && isset($a['depts'][$from]) ? ccCollegeOf($a, $from) : ''; $toCol = ccCollegeOf($a, $d);
    if ($was && ($old['role'] ?? '') === 'owner' && $fromCol !== '' && $fromCol !== $toCol) { $others = count(array_filter($a['users'], fn($x, $e) => $e !== $email && ccActive($x) && !in_array((string) $e, ccSupers(), true) && ccCollegeOf($a, (string) ($x['dept'] ?? '')) === $fromCol, ARRAY_FILTER_USE_BOTH)); if ($others > 0 || !empty($a['colleges'][$fromCol]['bill']['sub'])) out(400, ['error' => 'manager']); }   // someone running an institution with staff or a paid plan can't be moved out from under it
    if (!$was || $fromCol !== $toCol) seatCheck($a, $d);
    $u = array_merge($old, ['status' => 'active', 'role' => $r, 'dept' => $d]); if (!$was) $u += ['methods' => []]; if (!$was) { $u['canEdit'] = false; $u['approvedBy'] = $me['email']; $u['added'] = time(); } elseif ($from !== $d) $u['movedFrom'] = $from;
    if ($r === 'owner') $u['canEdit'] = true; if ($r === 'hod') { if (!isset($u['perms'])) $u['perms'] = permsFrom(null); } else unset($u['perms']); unset($u['wantDept'], $u['wantRole']);
    $a['users'][$email] = $u; $carried = $was && $from !== '' && $from !== $d && !empty($body['lessons']) ? ccCarryLessons($a, $email, $from, $d) : 0;
    if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
    $cn = (string) (ccCollege($a, $toCol)['name'] ?? '') ?: 'the institution'; $what = $r === 'owner' ? 'an institution manager' : ($r === 'hod' ? 'a head of department / subject leader' : 'a teacher');
    ccLog($d, $me, ($was ? 'moved ' : 'approved ') . $email . ' into ' . $a['depts'][$d]['name'] . ' as', $what . ($carried ? ', with ' . $carried . ' lessons' : ''));
    if (!$was) ccMail($email, "You're in: Crafticulum", "Hello" . (!empty($old['name']) ? ' ' . $old['name'] : '') . ",\n\nYour Crafticulum account has been approved for " . $cn . ", " . $a['depts'][$d]['name'] . ".\n\n" . (empty($old['pass']) && in_array($old['via'] ?? '', ['magic', 'password'], true) ? "Sign in with this link (it works once, for 24 hours):\n" . ccLink('magic', ccToken($email, 'magic', 24)) . "\n\nAfter that, use Email me a sign-in link on the sign-in page.\n" : "Sign in here:\n" . ccAppUrl() . "\n"));
    elseif ($fromCol !== $toCol) ccMail($email, 'Your Crafticulum account has moved to ' . $cn, "Hello" . (!empty($old['name']) ? ' ' . $old['name'] : '') . ",\n\nYour account is now in " . $cn . ", " . $a['depts'][$d]['name'] . ", as " . $what . "." . ($carried ? " The lessons you wrote have come with you as drafts." : "") . "\n\n" . ccAppUrl() . "\n");
    out(200, $all($a, ['carried' => $carried]));
}
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
    ccLog($d, $me, 'added ' . ($r === 'owner' ? 'an institution manager' : ($r === 'hod' ? 'a head of department / subject leader' : 'a teacher')), $email);
    $extra = [];
    if (!empty($body['invite'])) { $t = ccToken($email, 'invite', 24 * 7); $extra['link'] = ccLink('invite', $t);
        $extra['mailed'] = ccMail($email, "You've been added to Crafticulum", "Hello" . (!empty($u['name']) ? ' ' . $u['name'] : '') . ",\n\n" . ($me['name'] ?: $me['email']) . " has added you to Crafticulum " . ($r === 'owner' ? 'to manage ' . (ccCollege($a, ccCollegeOf($a, $d))['name'] ?: 'your institution') : 'for ' . $a['depts'][$d]['name']) . ".\n\nChoose a password with this link (it works for 7 days):\n" . $extra['link'] . "\n\n" . (ccGoogleId() || ccMsId() ? "Or sign in with your Google or Microsoft account using this email address.\n" : "")); }
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
    if (array_key_exists('room', $body)) { $rk = (string) $body['room']; if (!$isAdmin) out(403, ['error' => 'noperm']); if (!isset(CC_ROOMS[$rk]) || ($u['role'] ?? '') === 'owner') out(400, ['error' => 'room']);   // one room, for this one person: only an institution's manager or the super admin
        $u['rooms'] = (array) ($u['rooms'] ?? []); $u['rooms'][$rk] = !empty($body['on']); }
    if (array_key_exists('dept', $body)) { $d = (string) $body['dept']; if (!isset($a['depts'][$d])) out(400, ['error' => 'dept']); if (!in_array($d, myDepts($a, $me), true)) out(403, ['error' => 'noperm']); $u['dept'] = $d; }
    if (!empty($body['totpReset'])) unset($u['totp']);
    $a['users'][$email] = $u; if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
    $ch = array_intersect(array_keys($body), ['role', 'perms', 'canEdit', 'dept', 'totpReset', 'name', 'room']);
    if ($ch) ccLog((string) ($u['dept'] ?? $me['dept']), $me, 'changed ' . implode(', ', array_map(fn($k) => ['role' => 'the role', 'perms' => 'the permissions', 'canEdit' => 'edit rights', 'dept' => 'the department', 'totpReset' => 'two-step codes', 'name' => 'the name', 'room' => 'what they can use (' . (CC_ROOMS[(string) ($body['room'] ?? '')] ?? '') . (!empty($body['on']) ? ' on' : ' off') . ')'][$k], $ch)) . ' for', $email);
    teamOut($a, $me);
}
if ($action === 'userdel') { if ($email === $me['email']) out(400, ['error' => 'self']); unset($a['users'][$email]); ccSaveAccounts($a); ccLog((string) ($old['dept'] ?? $me['dept']), $me, 'removed', $email); teamOut($a, $me); }
out(400, ['error' => 'Unknown action.']);
