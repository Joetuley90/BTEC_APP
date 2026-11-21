<?php
/*
 * Keeps the Crafticulum's lessons, sequences and settings on this server.
 *
 * Each department has its own library (lessons, units, term dates, assignment
 * dates). Each person has their own groups, taught ticks, notes, start weeks,
 * student links and results, kept alongside the shared lessons and units and
 * merged in when they open the page.
 *
 * Heads of department (and teachers they allow) can change the shared lessons
 * and units. Term dates and the department's subjects (read from its
 * specifications) are for heads of department. Everyone can change their
 * own things. Only signed-in staff can use it (see auth.php).
 */
declare(strict_types=1);
if (!is_file(__DIR__ . '/config.php')) {
    http_response_code(500); header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'config.php is missing. Copy config.example.php to config.php and add your API key and password.']); exit;
}
require __DIR__ . '/config.php';
require __DIR__ . '/auth-lib.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function out(int $code, array $data): void { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(405, ['error' => 'Use POST.']);
ccSession();
$me = ccMe();
if (!$me) out(401, ['error' => 'sign_in']);
session_write_close();

$raw = file_get_contents('php://input', false, null, 0, 2000001);
if ($raw === false || strlen($raw) > 2000000) out(413, ['error' => 'Too large.']);
$body = json_decode($raw ?: '{}', true);
if (!is_array($body)) out(400, ['error' => 'Bad request.']);

ccEnsureData();
$base = ccDataDir();
if (!is_dir($base)) out(500, ['error' => 'The server could not create the data folder. Create a folder called data next to store.php and make it writable.']);
$acc = ccAccounts(); $dept = $me['dept']; $dinfo = $acc['depts'][$dept];
$legacy = !empty($dinfo['legacy']);
$ddir = $legacy ? $base : $base . '/d/' . $dept;                       // the department's shared library
$udir = $base . '/u/' . sha1($me['email']) . '/' . $dept;               // this person's own things
/* files saved before accounts existed belong to the first super admin who signs in */
if ($legacy && $me['role'] === 'superadmin' && (empty($dinfo['legacyOwner']) || ($dinfo['legacyOwner'] === 'admin@password' && $me['email'] !== 'admin@password'))) {
    // the admin password is only for setting up: the first super admin to sign in with Google takes over what it saved
    $from = $base . '/u/' . sha1('admin@password') . '/' . $dept;
    if (($dinfo['legacyOwner'] ?? '') === 'admin@password' && is_dir($from) && !is_dir($udir)) { @mkdir(dirname($udir), 0700, true); @rename($from, $udir); }
    $acc['depts'][$dept]['legacyOwner'] = $me['email']; ccSaveAccounts($acc); $dinfo = $acc['depts'][$dept];
}
$isOwner = $legacy && ($dinfo['legacyOwner'] ?? '') === $me['email'];
/* where the institution keeps learners anonymous, names never reach the disk and never leave it: class lists hold numbers */
$anon = ccAnon($acc, ccCollegeOf($acc, (string) $dept));
function anonDoc(string $p, $data) { if (!is_array($data)) return $data;
    if (preg_match('#^data/users/[^/]+/learners/#', $p)) { $s = json_decode((string) ($data['json'] ?? ''), true); if (!is_array($s) || !is_array($s['learners'] ?? null)) return $data;
        $s['learners'] = array_values(array_filter($s['learners'], 'is_array'));
        foreach ($s['learners'] as $i => $l) { unset($s['learners'][$i]['first'], $s['learners'][$i]['last'], $s['learners'][$i]['note']); $rf = trim((string) ($l['ref'] ?? '')); if ($rf !== '' && ccAnonOk($rf)) $s['learners'][$i]['name'] = $rf; else unset($s['learners'][$i]['ref']); }   // names from an imported list go too; a reference number stands in for the name
        $n = ccAnonNames(array_map(fn($l) => (string) ($l['name'] ?? ''), $s['learners'])); foreach ($s['learners'] as $i => $l) $s['learners'][$i]['name'] = $n[$i];
        $data['json'] = json_encode($s, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); return $data; }
    if (preg_match('#^data/users/[^/]+/classes/#', $p) && is_array($data['names'] ?? null)) $data['names'] = ccAnonNames(array_values($data['names']));
    return $data; }

const LESSON_OWN = ['lastResults', 'resultsBy', 'quizOnline', 'shareId', 'quizShareId', 'quizOpen', 'quizShowAnswers', 'coverId', 'liveKey'];
const LESSON_ANYONE = ['fileKey', 'filesSig'];
const SEQ_OWN = ['taught', 'notes', 'g', 'startWeek', 'shareUnit'];
const TEST_OWN = ['id', 'open', 'showAnswers'];

function kindOf(string $p): string {
    if (strpos($p, 'lessons/') === 0) return 'lesson';
    if (preg_match('#^data/users/[^/]+/seqs/#', $p)) return 'seq';
    if (preg_match('#^data/users/[^/]+/settings/calendar$#', $p)) return 'dates';
    if (preg_match('#^data/users/[^/]+/settings/courses$#', $p)) return 'subjects';
    if (preg_match('#^data/users/[^/]+/(settings/assignments$|hist/)#', $p)) return 'edit';
    if (strpos($p, 'data/users/') === 0) return 'own';
    return 'edit';
}
function fileFor(string $dir, string $p): string { return $dir . '/' . sha1($p) . '.json'; }
function readDoc(string $dir, string $p): ?array { $f = fileFor($dir, $p); if (!is_file($f)) return null; $j = json_decode((string) @file_get_contents($f), true); return is_array($j) ? $j : null; }
function writeDoc(string $dir, string $p, array $doc): void {
    if (!is_dir($dir) && !@mkdir($dir, 0700, true)) out(500, ['error' => 'Could not save. Check the data folder is writable.']);
    $f = fileFor($dir, $p); $tmp = $f . '.' . bin2hex(random_bytes(4)) . '.tmp';
    $ok = @file_put_contents($tmp, json_encode(['path' => $p] + $doc, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
    if ($ok === false || !@rename($tmp, $f)) { @unlink($tmp); out(500, ['error' => 'Could not save. Check the data folder is writable.']); }
}
function split(array $obj, array $own, bool $seq): array {
    $mine = [];
    foreach ($own as $k) if (array_key_exists($k, $obj)) { $mine[$k] = $obj[$k]; unset($obj[$k]); }
    if ($seq && isset($obj['test']) && is_array($obj['test'])) { $t = []; foreach (TEST_OWN as $k) if (array_key_exists($k, $obj['test'])) { $t[$k] = $obj['test'][$k]; unset($obj['test'][$k]); } if ($t) $mine['test'] = $t; }
    return [$obj, $mine];
}
function merge(array $shared, array $mine): array {
    foreach ($mine as $k => $v) { if ($k === 'test' && is_array($v)) { if (isset($shared['test']) && is_array($shared['test'])) $shared['test'] = $v + $shared['test']; } else $shared[$k] = $v; }
    return $shared;
}
/* the JSON inside a lesson or sequence doc */
function inner(array $data, string $kind): ?array { $j = json_decode((string) ($data[$kind === 'lesson' ? 'lesson' : 'json'] ?? ''), true); return is_array($j) ? $j : null; }
function withInner(array $data, string $kind, array $obj): array { $data[$kind === 'lesson' ? 'lesson' : 'json'] = json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); return $data; }
function stripRowIds(array $s): array { foreach (($s['rows'] ?? []) as $i => $r) if (is_array($r)) unset($s['rows'][$i]['id']); unset($s['updatedAt']); return $s; }

$action = $_GET['a'] ?? '';

if ($action === 'all') {
    $docs = []; $ownerAuthor = null; $own = (string) ($dinfo['legacyOwner'] ?? '');
    if ($legacy && ($own === '' || $own === 'admin@password') && ccSupers()) $own = (string) ccSupers()[0];
    if ($legacy && $own !== '' && $own !== 'admin@password') $ownerAuthor = ['name' => (string) (($acc['users'][$own]['name'] ?? '') ?: $own), 'email' => $own];
    foreach (glob($ddir . '/*.json') ?: [] as $f) {
        $j = json_decode((string) @file_get_contents($f), true);
        if (!is_array($j) || !isset($j['path']) || !is_string($j['path']) || !array_key_exists('data', $j)) continue;
        $p = $j['path']; $k = kindOf($p); $data = $j['data'];
        if ($k === 'own') { if ($isOwner) $docs[$p] = $anon ? anonDoc($p, $data) : $data; continue; }
        if ($k === 'lesson' && is_array($data) && !empty($data['pending'])) {
            // the person whose change is waiting keeps seeing their own version. Leaders see that one is waiting. Nobody else sees anything.
            if ((string) ($data['pending']['email'] ?? '') === $me['email']) { $pf = pendRead($ddir, $p); if ($pf) { $data = $pf['data']; $data['pendingMine'] = true; } unset($data['pending']); }
            elseif (empty($me['isLead'])) unset($data['pending']);
        }
        if (($k === 'lesson' || $k === 'seq') && is_array($data) && ($obj = inner($data, $k))) {
            [$shared, $legacyMine] = split($obj, $k === 'lesson' ? LESSON_OWN : SEQ_OWN, $k === 'seq');
            $ov = readDoc($udir, $p);
            $mine = $ov ? (array) ($ov['own'] ?? []) : ($isOwner ? $legacyMine : []);
            $merged = merge($shared, $mine);
            if ($ov && !empty($ov['updatedAt'])) { $merged['updatedAt'] = max((int) ($merged['updatedAt'] ?? 0), (int) $ov['updatedAt']); $data['updatedAt'] = max((int) ($data['updatedAt'] ?? 0), (int) $ov['updatedAt']); }
            if ($k === 'lesson') {
                // lessons built before authors were kept belong to the person who owned the library then
                if (empty($merged['meta']['author']['name']) && $ownerAuthor && is_array($merged['meta'] ?? null)) $merged['meta']['author'] = $ownerAuthor;
                if (empty($data['author']) && !empty($merged['meta']['author']['name'])) $data['author'] = (string) $merged['meta']['author']['name'];
                $data['status'] = (string) ($merged['meta']['status'] ?? ''); $data['authorEmail'] = (string) ($merged['meta']['author']['email'] ?? ''); $data['copy'] = !empty($merged['meta']['copyOf']);
            }
            $data = withInner($data, $k, $merged);
        }
        $docs[$p] = $data;
    }
    foreach (glob($udir . '/*.json') ?: [] as $f) {
        $j = json_decode((string) @file_get_contents($f), true);
        if (is_array($j) && isset($j['path']) && array_key_exists('data', $j) && kindOf((string) $j['path']) === 'own') $docs[$j['path']] = $anon ? anonDoc((string) $j['path'], $j['data']) : $j['data'];
    }
    out(200, ['docs' => (object) $docs]);
}

/* every class list and set of marks in this department, for its leaders to look at. Each one still belongs to the teacher who made it */
/* a member of staff on one page: what they teach, their classes (numbers only, never learners' names), the lessons they've written and the verification they've done and had.
   Anyone can open their own. A head of department can open people in their department, an institution manager anyone in the institution, the super admin anyone */
/* someone's profile: what they wrote on their staff profile, with their account's job title, pronouns and phone filling any gaps */
function ccAboutOf(string $base, string $em, array $u): ?array { $ab = json_decode((string) @file_get_contents($base . '/u/' . sha1($em) . '/_about.json'), true); if (!is_array($ab)) $ab = null;
    $pf = (array) ($u['profile'] ?? []); foreach (['headline' => 'job', 'pronouns' => 'pronouns', 'phone' => 'phone'] as $k => $j) if (!empty($pf[$j]) && empty($ab[$k])) { $ab = $ab ?? []; $ab[$k] = (string) $pf[$j]; }
    if (!empty($pf['site']) && empty($ab['campuses'])) { $ab = $ab ?? []; $ab['campuses'] = [(string) $pf['site']]; }
    return $ab; }
if ($action === 'staffphoto') {   // your own photo: a square JPEG the page has already cropped and shrunk
    if (!empty($me['demo'])) out(403, ['error' => 'demo']);
    $f = ccPhotoFile($me['email']);
    if (!empty($body['remove'])) { @unlink($f); out(200, ['ok' => true, 'picture' => ccPhotoUrl($me['email'], (string) (($acc['users'][$me['email']]['picture'] ?? '') ?: ''))]); }
    $img = (string) ($body['img'] ?? ''); if (!preg_match('#^data:image/jpeg;base64,([A-Za-z0-9+/=]+)$#', $img, $m)) out(400, ['error' => 'type']);
    $bin = base64_decode($m[1], true); if ($bin === false || strlen($bin) > 400000) out(400, ['error' => 'size']);
    $info = @getimagesizefromstring($bin); if (!$info || ($info['mime'] ?? '') !== 'image/jpeg' || $info[0] < 64 || $info[0] > 1024 || $info[1] < 64 || $info[1] > 1024) out(400, ['error' => 'type']);
    if (function_exists('imagecreatefromstring')) { $im = @imagecreatefromstring($bin); if (!$im) out(400, ['error' => 'type']); ob_start(); imagejpeg($im, null, 86); $bin = (string) ob_get_clean(); imagedestroy($im); }   // re-saved, so nothing but the picture is kept
    if (!is_dir(dirname($f))) @mkdir(dirname($f), 0700, true);
    if (@file_put_contents($f, $bin, LOCK_EX) === false) out(500, ['error' => 'write']); clearstatcache();
    out(200, ['ok' => true, 'picture' => ccPhotoUrl($me['email'])]);
}
if ($action === 'deptsubjects') {   // the departments someone can plan for, each with the subjects its specifications gave it
    $col = ccCollegeOf($acc, (string) $dept); $all = in_array($me['role'], ['superadmin', 'owner'], true);
    $ids = $all ? ccCollegeDepts($acc, $col) : [(string) $dept]; if (!$all) { $mu = $acc['users'][$me['email']] ?? []; $hm = (string) ($mu['dept'] ?? $dept); $ids = array_map('strval', array_merge([$hm], array_keys(ccAlso($acc, ['dept' => $hm, 'also' => $mu['also'] ?? []])))); } if (!in_array((string) $dept, $ids, true)) array_unshift($ids, (string) $dept);
    $out = []; foreach ($ids as $d) { $di = $acc['depts'][$d] ?? null; if (!$di) continue; $dd = !empty($di['legacy']) ? $base : $base . '/d/' . $d;
        $doc = readDoc($dd, 'data/users/teacher/settings/courses'); $c = $doc ? json_decode((string) ($doc['data']['json'] ?? ''), true) : null; if (!is_array($c)) $c = [];
        /* who leads it and how many people are in it, for the department tiles. Super admins are nobody's staff */
        $leads = []; $staff = 0; $sup = ccSupers(); foreach ($acc['users'] as $e => $u) { if (!is_array($u) || !ccActive($u) || !empty($u['demo']) || in_array((string) $e, $sup, true)) continue; $al = ccAlso($acc, $u); $in = (string) ($u['dept'] ?? '') === (string) $d;
            if (!$in && !isset($al[$d])) continue; $staff++; $role = $in ? (string) ($u['role'] ?? '') : (string) $al[$d]; if ($role === 'hod') $leads[] = (string) (($u['name'] ?? '') !== '' ? $u['name'] : $e); }
        $out[] = ['id' => (string) $d, 'name' => (string) ($di['name'] ?? 'Department'), 'mine' => (string) $d === (string) $dept, 'builtin' => !empty($di['legacy']) && (($c['builtin'] ?? true) !== false), 'list' => array_values(array_filter((array) ($c['list'] ?? []), 'is_array')),
                  'leads' => $leads, 'staff' => $staff, 'ctx' => is_array($c['ctx'] ?? null) ? $c['ctx'] : null]; }
    $ren = []; foreach (glob($base . '/_rename/*/manifest.json') ?: [] as $f) { $m = json_decode((string) @file_get_contents($f), true); if (is_array($m) && empty($m['undone']) && ($m['dept'] ?? '') === (string) $dept) $ren[] = ['at' => (int) $m['at'], 'from' => $m['from'], 'to' => $m['to']]; }
    usort($ren, fn($x, $y) => $x['at'] <=> $y['at']);
    out(200, ['depts' => $out, 'renames' => array_slice($ren, -20)]);
}
/* rename a subject (its name, its level or qualification, or both) everywhere it is used: the subjects list, lessons, plans, class marks, quiz results.
   A copy of every file it touches is kept first, and the last rename can be undone. Preview unless 'go' is sent. */
if ($action === 'subjectrename' || $action === 'subjectrenameundo') {
    if (!$me['canSubjects'] || !$me['isLead']) out(403, ['error' => 'noperm']); @set_time_limit(300);
    $bkRoot = $base . '/_rename'; $relOf = fn(string $abs) => ltrim(substr($abs, strlen($base)), '/');
    if ($action === 'subjectrenameundo') {
        $last = null; foreach (glob($bkRoot . '/*/manifest.json') ?: [] as $f) { $m = json_decode((string) @file_get_contents($f), true); if (is_array($m) && empty($m['undone']) && ($m['dept'] ?? '') === (string) $dept && (!$last || $m['at'] > $last['at'])) $last = $m + ['file' => $f]; }
        if (!$last) out(404, ['error' => 'none']);
        foreach ((array) $last['moved'] as [$old, $new]) @unlink($base . '/' . $new);
        foreach ((array) $last['changed'] as $rel) { $bk = dirname($last['file']) . '/files/' . sha1($rel); if (is_file($bk)) @copy($bk, $base . '/' . $rel); }
        $m = json_decode((string) file_get_contents($last['file']), true); $m['undone'] = time(); file_put_contents($last['file'], json_encode($m));
        ccLog($dept, $me, 'undid a subject rename', $last['to'][0] . ' ' . $last['to'][1]); out(200, ['ok' => true, 'back' => $last['from']]);
    }
    $line = fn($x) => mb_substr(trim((string) preg_replace('/[\x00-\x1F\x7F|<>]+/u', ' ', (string) $x)), 0, 80);
    $os = (string) ($body['from']['s'] ?? ''); $ol = (string) ($body['from']['l'] ?? ''); $ns = $line($body['to']['s'] ?? ''); $nl = $line($body['to']['l'] ?? '');
    if ($os === '' || $ns === '' || $nl === '') out(400, ['error' => 'name']); if ($os === $ns && $ol === $nl) out(400, ['error' => 'same']);
    $cdoc = readDoc($ddir, 'data/users/teacher/settings/courses'); $cc = $cdoc ? json_decode((string) ($cdoc['data']['json'] ?? ''), true) : null; $list = is_array($cc) ? (array) ($cc['list'] ?? []) : [];
    $has = fn(string $a, string $b) => (bool) array_filter($list, fn($x) => is_array($x) && (string) ($x['s'] ?? '') === $a && (string) ($x['l'] ?? '') === $b);
    if (!$has($os, $ol)) out(404, ['error' => 'not_found']); if ($has($ns, $nl)) out(409, ['error' => 'exists']);
    foreach ((array) ($body['taken'] ?? []) as $t) if (is_array($t) && (string) ($t[0] ?? '') === $ns && (string) ($t[1] ?? '') === $nl) out(409, ['error' => 'exists']);   // the built-in subjects, as the page sees them
    $pre = $os . '|' . $ol . '|'; $npre = $ns . '|' . $nl . '|'; $n = ['lessons' => 0, 'plans' => 0, 'classes' => 0, 'other' => 0];
    $fixS = function (string $v) use ($pre, $npre, $os, $ol, $ns, $nl) { if (strncmp($v, $pre, strlen($pre)) === 0) return $npre . substr($v, strlen($pre)); if ($v === $os . '|' . $ol) return $ns . '|' . $nl; return $v; };
    $walk = function ($v, &$hit) use (&$walk, $fixS, $os, $ol, $ns, $nl) {
        if (is_string($v)) { $c = $v[0] ?? ''; if (($c === '{' || $c === '[') && strlen($v) > 1) { $d = json_decode($v); if (is_object($d) || is_array($d)) { $h = false; $d = $walk($d, $h); if ($h) { $hit = true; return json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); } return $v; } }
            $f = $fixS($v); if ($f !== $v) $hit = true; return $f; }
        if (is_array($v)) { foreach ($v as $k => $x) $v[$k] = $walk($x, $hit); return $v; }
        if (is_object($v)) { $o = new stdClass();
            foreach (get_object_vars($v) as $k => $x) { $nk = $fixS((string) $k); if ($nk !== (string) $k) $hit = true; $o->{$nk} = $walk($x, $hit); }
            foreach ([['course', 'level'], ['s', 'l']] as [$a, $b]) if (isset($o->{$a}, $o->{$b}) && is_string($o->{$a}) && is_string($o->{$b}) && $o->{$a} === $os && $o->{$b} === $ol) { $o->{$a} = $ns; $o->{$b} = $nl; $hit = true; }
            return $o; }
        return $v; };
    $seqId = fn(string $k) => substr(trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($k)), '-'), 0, 100);
    $files = array_merge(array_filter(glob($ddir . '/*.json') ?: [], fn($f) => basename($f)[0] !== '_'), glob($base . '/u/*/' . $dept . '/*.json') ?: []); $todo = [];
    foreach ($files as $f) { $raw = (string) @file_get_contents($f); if ($raw === '' || (!preg_match('/[^A-Za-z0-9 ]/', $os) && strpos($raw, $os) === false)) continue; $j = json_decode($raw); if (!is_object($j) || !isset($j->path) || !is_string($j->path)) continue;
        $hit = false; $j2 = $walk($j, $hit); if (!$hit) continue; $p = (string) $j2->path; $to = $f;
        if (preg_match('#^(.*/seqs/)[^/]+$#', $p, $mm) && isset($j2->data->key) && is_string($j2->data->key)) { $np = $mm[1] . $seqId($j2->data->key); if ($np !== $p) { $cand = fileFor(dirname($f), $np); if (!is_file($cand)) { $j2->path = $np; $to = $cand; } } }
        if (strpos($p, '/seqs/') !== false) $n['plans']++; elseif (strpos($p, 'lessons/') !== false) $n['lessons']++; elseif (strpos($p, '/learners/') !== false) $n['classes']++; else $n['other']++;
        $todo[] = [$f, $to, $j2]; }
    $plan = ['from' => [$os, $ol], 'to' => [$ns, $nl]] + $n + ['files' => count($todo)];
    if (empty($body['go'])) out(200, ['plan' => $plan]);
    $stamp = date('Ymd-His') . '-' . bin2hex(random_bytes(3)); $bk = $bkRoot . '/' . $stamp; if (!@mkdir($bk . '/files', 0700, true)) out(500, ['error' => 'Could not make the safety copy, so nothing was changed. Check the data folder is writable.']);
    @file_put_contents($bkRoot . '/.htaccess', "Require all denied\n"); $changed = []; $moved = [];
    foreach ($todo as [$f, $to, $j2]) { if (!@copy($f, $bk . '/files/' . sha1($relOf($f)))) out(500, ['error' => 'Could not make the safety copy, so nothing was changed.']); }
    foreach ($todo as [$f, $to, $j2]) { $tmp = $to . '.' . bin2hex(random_bytes(4)) . '.tmp'; if (@file_put_contents($tmp, json_encode($j2, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX) === false || !@rename($tmp, $to)) { @unlink($tmp); continue; }
        $changed[] = $relOf($f); if ($to !== $f) { @unlink($f); $moved[] = [$relOf($f), $relOf($to)]; } }
    file_put_contents($bk . '/manifest.json', json_encode(['at' => time(), 'by' => $me['email'], 'dept' => (string) $dept, 'from' => [$os, $ol], 'to' => [$ns, $nl], 'changed' => $changed, 'moved' => $moved]));
    ccLog($dept, $me, 'renamed a subject', "$os $ol to $ns $nl"); out(200, ['ok' => true, 'plan' => $plan]);
}
if ($action === 'mgrset') {   // the CPD hours each person is expected to log in a year
    if (!in_array($me['role'], ['superadmin', 'owner'], true)) out(403, ['error' => 'noperm']);
    $col = ccCollegeOf($acc, (string) $dept); $f = $base . '/_mgr.json'; $all = json_decode((string) @file_get_contents($f), true); if (!is_array($all)) $all = [];
    $all[$col] = ['cpdTarget' => max(0, min(500, (int) ($body['cpdTarget'] ?? 0)))] + (array) ($all[$col] ?? []); @file_put_contents($f, json_encode($all), LOCK_EX); out(200, ['ok' => true, 'cpdTarget' => $all[$col]['cpdTarget']]);
}
if ($action === 'mgrdash') {   // a manager's overview: each department's lessons, people, CPD and overdue work, as numbers. Learners are never named
    $boss = in_array($me['role'], ['superadmin', 'owner'], true); if (!$boss && empty($me['isLead'])) out(403, ['error' => 'noperm']);
    $col = ccCollegeOf($acc, (string) $dept); $ids = $boss ? ccCollegeDepts($acc, $col) : [(string) $dept]; $today = date('Y-m-d'); $now = time(); $ys = (date('n') >= 9 ? (int) date('Y') : (int) date('Y') - 1) . '-09-01';
    $mg = json_decode((string) @file_get_contents($base . '/_mgr.json'), true); $target = (int) ($mg[$col]['cpdTarget'] ?? 30);
    $months = []; for ($i = 5; $i >= 0; $i--) $months[date('Y-m', strtotime(date('Y-m-01') . " -$i month"))] = 0;
    $who = $acc['users']; foreach (ccSupers() as $e) unset($who[$e]); $depts = []; $people = [];
    foreach ($ids as $d) { $di = $acc['depts'][$d] ?? null; if (!$di) continue; $dd = !empty($di['legacy']) ? $base : $base . '/d/' . $d;
        $row = ['id' => (string) $d, 'name' => (string) ($di['name'] ?? 'Department'), 'staff' => 0, 'quiet' => 0, 'lessons' => 0, 'month' => 0, 'waiting' => 0, 'months' => $months, 'learners' => 0, 'open' => 0, 'late' => 0, 'ivOpen' => 0, 'cpd' => 0, 'under' => 0]; $by = [];
        foreach (glob($dd . '/*.json') ?: [] as $f) { $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j) || strpos((string) ($j['path'] ?? ''), 'lessons/') !== 0) continue; $l = json_decode((string) ($j['data']['lesson'] ?? ''), true); if (!is_array($l)) continue;
            $row['lessons']++; if ((string) ($l['meta']['status'] ?? '') === 'draft') $row['waiting']++; $at = (int) floor(((int) ($j['data']['updatedAt'] ?? 0)) / 1000); $m = $at ? date('Y-m', $at) : ''; if (isset($row['months'][$m])) $row['months'][$m]++; if ($m === date('Y-m')) $row['month']++;
            $ae = strtolower((string) ($l['meta']['author']['email'] ?? '')); if ($ae !== '' && $at >= strtotime($ys)) $by[$ae] = ($by[$ae] ?? 0) + 1; }
        $iv = json_decode((string) @file_get_contents($dd . '/_iv.json'), true); foreach ((array) $iv as $r) if (is_array($r) && ($r['decision'] ?? '') !== 'agreed' && empty($r['done'])) $row['ivOpen']++;
        foreach ($who as $e => $u) { if (!is_array($u) || !ccActive($u) || !empty($u['demo']) || (string) ($u['dept'] ?? '') !== (string) $d) continue; $e = (string) $e; $row['staff']++; $last = (int) ($u['last'] ?? 0); $quiet = !$last || $now - $last > 30 * 86400; if ($quiet) $row['quiet']++;
            $ab = ccAboutOf($base, $e, $u) ?? []; $h = 0; foreach ((array) ($ab['cpd'] ?? []) as $c) if (is_array($c) && (string) ($c['d'] ?? '') >= $ys) $h += (float) ($c['h'] ?? 0); $row['cpd'] += $h; if ($target > 0 && $h < $target) $row['under']++;
            $open = 0; $late = 0; $ln = 0; foreach (glob($base . '/u/' . sha1($e) . '/' . $d . '/*.json') ?: [] as $f) { $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j) || !preg_match('#^data/users/[^/]+/learners/#', (string) ($j['path'] ?? ''))) continue; $st = json_decode((string) ($j['data']['json'] ?? ''), true); if (!is_array($st) || !is_array($st['learners'] ?? null)) continue;
                $lids = array_map(fn($l) => (string) ($l['id'] ?? ''), array_filter($st['learners'], 'is_array')); $ln += count($lids);
                foreach ((array) ($st['subs'] ?? []) as $k => $m) foreach ((array) $m as $lid => $x) { if (!is_array($x) || empty($x['ra']) || !empty($x['ro']) || !in_array((string) $lid, $lids, true)) continue; $open++; if (empty($x['ri']) && !empty($x['rd']) && (string) $x['rd'] < $today) $late++; } }
            $row['learners'] += $ln; $row['open'] += $open; $row['late'] += $late;
            $people[] = ['email' => $e, 'name' => (string) (($u['name'] ?? '') ?: $e), 'role' => (string) ($u['role'] ?? 'teacher'), 'dept' => (string) $d, 'deptName' => $row['name'], 'last' => $last, 'quiet' => $quiet, 'lessons' => (int) ($by[$e] ?? 0), 'cpd' => round($h, 1), 'late' => $late, 'learners' => $ln]; }
        $row['cpd'] = round($row['cpd'], 1); $depts[] = $row; }
    usort($people, fn($x, $y) => strcasecmp($x['name'], $y['name']));
    out(200, ['depts' => $depts, 'people' => $people, 'cpdTarget' => $target, 'canSet' => $boss, 'since' => $ys]);
}
/* ---------- the lesson library: every finished lesson in the institution, to search, open and copy ---------- */
if ($action === 'library' || $action === 'libget') {
    $col = ccCollegeOf($acc, (string) $dept); $shut = !empty($acc['colleges'][$col]['libraryOff']); $ids = $shut ? [(string) $dept] : array_map('strval', ccCollegeDepts($acc, $col)); if (!in_array((string) $dept, $ids, true)) $ids[] = (string) $dept;
    $uf = $base . '/_uses.json';
    if ($action === 'libget') { $d = (string) ($body['dept'] ?? ''); $p = (string) ($body['path'] ?? ''); if (!in_array($d, $ids, true) || !preg_match('#^lessons/[A-Za-z0-9_-]{1,80}$#', $p)) out(404, ['error' => 'gone']);
        $di = $acc['depts'][$d] ?? null; if (!$di) out(404, ['error' => 'gone']); $dd = !empty($di['legacy']) ? $base : $base . '/d/' . $d; $hit = null;
        foreach (glob($dd . '/*.json') ?: [] as $f) { $j = json_decode((string) @file_get_contents($f), true); if (is_array($j) && ($j['path'] ?? '') === $p) { $hit = $j; break; } }
        $l = $hit ? json_decode((string) ($hit['data']['lesson'] ?? ''), true) : null; if (!is_array($l) || ($d !== (string) $dept && (string) ($l['meta']['status'] ?? '') === 'draft')) out(404, ['error' => 'gone']);
        if ($d !== (string) $dept || !empty($body['count'])) { $fh = @fopen($uf, 'c+'); if ($fh) { flock($fh, LOCK_EX); $u = json_decode((string) stream_get_contents($fh), true); if (!is_array($u)) $u = []; $k = $d . '|' . $p; $u[$k] = (int) ($u[$k] ?? 0) + 1; ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode($u)); flock($fh, LOCK_UN); fclose($fh); } }
        out(200, ['lesson' => $l, 'dept' => $d, 'deptName' => (string) ($di['name'] ?? ''), 'mine' => $d === (string) $dept]); }
    $uses = json_decode((string) @file_get_contents($uf), true); if (!is_array($uses)) $uses = []; $list = [];
    foreach ($ids as $d) { $di = $acc['depts'][$d] ?? null; if (!$di) continue; $dd = !empty($di['legacy']) ? $base : $base . '/d/' . $d;
        foreach (glob($dd . '/*.json') ?: [] as $f) { if (count($list) >= 1500) break 2; $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j) || strpos((string) ($j['path'] ?? ''), 'lessons/') !== 0) continue; $l = json_decode((string) ($j['data']['lesson'] ?? ''), true); if (!is_array($l)) continue;
            $draft = (string) ($l['meta']['status'] ?? '') === 'draft'; if ($draft && $d !== (string) $dept) continue; $m = (array) ($l['meta'] ?? []); $crit = [];
            foreach ((array) ($l['plan'] ?? []) as $pl) foreach (preg_split('/[,;\s]+/', (string) ($pl['criteria'] ?? '')) ?: [] as $c) if ($c !== '' && strlen($c) < 12 && preg_match('/\d/', $c)) $crit[$c] = true;
            $list[] = ['dept' => (string) $d, 'deptName' => (string) ($di['name'] ?? ''), 'path' => (string) $j['path'], 'title' => (string) ($l['title'] ?? 'Lesson'), 'course' => (string) ($m['course'] ?? ''), 'level' => (string) ($m['level'] ?? ''), 'unit' => (string) ($m['unit'] ?? ''), 'length' => (string) ($m['length'] ?? ''), 'by' => (string) ($m['by'] ?? ($m['author'] ?? '')), 'criteria' => array_slice(array_keys($crit), 0, 12),
                       'at' => (int) floor(((int) ($j['data']['updatedAt'] ?? 0)) / 1000), 'uses' => (int) ($uses[$d . '|' . $j['path']] ?? 0), 'draft' => $draft, 'mine' => $d === (string) $dept]; } }
    usort($list, fn($x, $y) => $y['uses'] <=> $x['uses'] ?: $y['at'] <=> $x['at']);
    out(200, ['lessons' => $list, 'shut' => $shut, 'depts' => count($ids)]);
}
if ($action === 'directory') {   // everyone in the institution, as colleagues see them: no classes, lessons or verification here
    $col = ccCollegeOf($acc, (string) $dept); $boss = in_array($me['role'], ['superadmin', 'owner'], true); $ys = (date('n') >= 9 ? (int) date('Y') : (int) date('Y') - 1) . '-09-01';
    $slugs = ccPersonSlugs($acc, $col); $cslug = ccCollegeSlugs($acc)[$col] ?? '';
    $people = []; $who = $acc['users']; foreach (ccSupers() as $e) unset($who[$e]);
    foreach ($who as $e => $u) { if (!is_array($u) || !ccActive($u) || !empty($u['demo'])) continue; $ud = (string) ($u['dept'] ?? ''); if (!isset($acc['depts'][$ud]) || ccCollegeOf($acc, $ud) !== $col) continue;
        $ab = ccAboutOf($base, (string) $e, $u) ?? [];
        $row = ['email' => (string) $e, 'name' => (string) (($u['name'] ?? '') ?: $e), 'role' => (string) ($u['role'] ?? 'teacher'), 'dept' => $ud, 'deptName' => (string) ($acc['depts'][$ud]['name'] ?? ''), 'picture' => ccPhotoUrl((string) $e, (string) ($u['picture'] ?? '')), 'last' => (int) ($u['last'] ?? 0)];
        foreach (['headline', 'pronouns', 'status', 'room', 'phone', 'hours'] as $k) $row[$k] = (string) ($ab[$k] ?? '');
        foreach (['skills', 'held', 'campuses'] as $k) $row[$k] = array_values((array) ($ab[$k] ?? []));
        if ($boss || (!empty($me['isLead']) && $ud === (string) $dept)) { $h = 0; foreach ((array) ($ab['cpd'] ?? []) as $c) if (is_array($c) && (string) ($c['d'] ?? '') >= $ys) $h += (float) ($c['h'] ?? 0); $row['cpdHours'] = round($h, 1); }
        $row['slug'] = (string) ($slugs[(string) $e] ?? ''); $row['also'] = array_map('strval', array_keys(ccAlso($acc, $u))); $row['public'] = is_array($ab) && array_key_exists('public', $ab) ? !empty($ab['public']) : !empty($acc['colleges'][$col]['profilesPublic']);
        $people[] = $row; }
    usort($people, fn($x, $y) => strcasecmp($x['name'], $y['name']));
    $depts = []; foreach (ccCollegeDepts($acc, $col) as $d) $depts[] = ['id' => (string) $d, 'name' => (string) ($acc['depts'][$d]['name'] ?? '')];
    out(200, ['people' => $people, 'depts' => $depts, 'cpd' => $boss || !empty($me['isLead']), 'cslug' => $cslug, 'mine' => (string) $dept, 'allPublic' => !empty($acc['colleges'][$col]['profilesPublic'])]);
}
if ($action === 'staffme') {   // your own profile: the bits only you can say about yourself
    if (!empty($me['demo'])) out(403, ['error' => 'demo']);
    $t = fn($k, $n) => mb_substr(trim(preg_replace('/\s+/u', ' ', (string) ($body[$k] ?? ''))), 0, $n);
    $list = fn($k, $n, $len) => array_values(array_slice(array_unique(array_filter(array_map(fn($x) => mb_substr(trim(preg_replace('/\s+/u', ' ', (string) $x)), 0, $len), is_array($body[$k] ?? null) ? $body[$k] : []), 'strlen')), 0, $n));
    $link = $t('link', 200); if ($link !== '' && !preg_match('#^https://[^\s<>"]+$#i', $link)) out(400, ['error' => 'link']);
    $cpd = []; foreach (array_slice(is_array($body['cpd'] ?? null) ? $body['cpd'] : [], 0, 60) as $c) { if (!is_array($c)) continue; $ti = mb_substr(trim((string) ($c['t'] ?? '')), 0, 120); if ($ti === '') continue; $d = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($c['d'] ?? '')) ? (string) $c['d'] : ''; $cpd[] = ['t' => $ti, 'd' => $d, 'h' => max(0, min(200, round((float) ($c['h'] ?? 0), 1))), 'k' => mb_substr(trim((string) ($c['k'] ?? '')), 0, 40)]; }
    usort($cpd, fn($x, $y) => strcmp($y['d'], $x['d']));
    $bio = mb_substr(trim(str_replace("\r", '', (string) ($body['bio'] ?? ''))), 0, 800);
    $ab = ['headline' => $t('headline', 80), 'pronouns' => $t('pronouns', 30), 'bio' => $bio, 'phone' => $t('phone', 40), 'room' => $t('room', 60), 'hours' => $t('hours', 140), 'campuses' => $list('campuses', 6, 40), 'skills' => $list('skills', 12, 40), 'held' => $list('held', 12, 60), 'link' => $link, 'status' => in_array((string) ($body['status'] ?? ''), ['in', 'class', 'away', 'wfh'], true) ? (string) $body['status'] : '', 'cpd' => $cpd, 'at' => time()];
    $d = $base . '/u/' . sha1($me['email']); if (!is_dir($d)) @mkdir($d, 0700, true);
    $was = json_decode((string) @file_get_contents($d . '/_about.json'), true); if (is_array($was) && array_key_exists('public', $was)) $ab['public'] = !empty($was['public']);
    if (@file_put_contents($d . '/_about.json', json_encode($ab, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX) === false) out(500, ['error' => 'write']);
    out(200, ['ok' => true, 'about' => $ab]);
}
if ($action === 'staffpublic') {   // your choice: can someone who isn't signed in see your profile page
    if (!empty($me['demo']) || $me['role'] === 'superadmin') out(403, ['error' => 'noperm']);
    $d = $base . '/u/' . sha1($me['email']); if (!is_dir($d)) @mkdir($d, 0700, true); $ab = json_decode((string) @file_get_contents($d . '/_about.json'), true); if (!is_array($ab)) $ab = [];
    $ab['public'] = !empty($body['public']);   /* kept either way, so a person's No stands when the institution makes profiles public */
    if (@file_put_contents($d . '/_about.json', json_encode($ab, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX) === false) out(500, ['error' => 'write']);
    out(200, ['ok' => true, 'public' => !empty($ab['public'])]);
}
if ($action === 'staff') {
    $em = strtolower(trim((string) ($body['email'] ?? ''))) ?: $me['email']; $u = $acc['users'][$em] ?? null; $self = $em === $me['email'];
    if (!$self) { if (!$u || !ccActive($u) || !empty($u['demo'])) out(404, ['error' => 'not_found']); $td = (string) ($u['dept'] ?? ''); if (!isset($acc['depts'][$td])) out(404, ['error' => 'not_found']);
        $ok = $me['role'] === 'superadmin' || ($me['role'] === 'owner' && ccCollegeOf($acc, $td) === ccCollegeOf($acc, (string) $dept)) || (!empty($me['isLead']) && $td === (string) $dept);
        if (!$ok) { if (ccCollegeOf($acc, $td) !== ccCollegeOf($acc, (string) $dept)) out(403, ['error' => 'noperm']);   // a colleague in the same institution sees who they are, not their classes or records
            $ab = ccAboutOf($base, $em, (array) $u); if (is_array($ab)) unset($ab['cpd']); require_once __DIR__ . '/inst-lib.php'; $pr = null; try { $pr = ccProfileGet($em); } catch (Throwable $e) {}
            out(200, ['limited' => true, 'person' => ['email' => $em, 'name' => (string) ($u['name'] ?? ''), 'role' => (string) ($u['role'] ?? 'teacher'), 'dept' => (string) ($acc['depts'][$td]['name'] ?? ''), 'college' => (string) (ccCollege($acc, ccCollegeOf($acc, $td))['name'] ?? ''), 'last' => (int) ($u['last'] ?? 0), 'added' => (int) ($u['added'] ?? 0), 'picture' => ccPhotoUrl($em, (string) ($u['picture'] ?? '')), 'self' => false],
                'about' => is_array($ab) ? $ab : null, 'teaches' => $pr ? ['role' => (string) $pr['role'], 'subjects' => $pr['subjects'], 'stages' => $pr['stages'], 'levels' => $pr['levels'], 'orgs' => $pr['orgs'], 'quals' => $pr['quals'], 'insts' => []] : null,
                'lessons' => ['total' => 0, 'approved' => 0, 'draft' => 0, 'list' => []], 'classes' => [], 'iv' => ['did' => [], 'didTotal' => 0, 'had' => [], 'hadTotal' => 0, 'actions' => 0], 'months' => [], 'units' => [], 'unitsTotal' => 0]); } }
    $td = $self ? (string) $dept : (string) $u['dept']; $u = $u ?? []; $tdir = !empty($acc['depts'][$td]['legacy']) ? $base : $base . '/d/' . $td; $tcol = ccCollegeOf($acc, $td);
    require_once __DIR__ . '/inst-lib.php'; $pr = null; try { $pr = ccProfileGet($em); } catch (Throwable $e) {}
    $lessons = []; foreach (glob($tdir . '/*.json') ?: [] as $f) { $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j) || strpos((string) ($j['path'] ?? ''), 'lessons/') !== 0) continue; $l = json_decode((string) ($j['data']['lesson'] ?? ''), true); if (!is_array($l) || strtolower((string) ($l['meta']['author']['email'] ?? '')) !== $em) continue;
        $lessons[] = ['id' => substr((string) $j['path'], 8), 'title' => mb_substr((string) (($l['title'] ?? '') ?: ($j['data']['title'] ?? 'Lesson')), 0, 120), 'status' => (string) (($l['meta']['status'] ?? '') ?: 'approved'), 'at' => (int) floor(((int) ($j['data']['updatedAt'] ?? 0)) / 1000), 'unit' => mb_substr((string) ($l['meta']['unit'] ?? $l['unit'] ?? ''), 0, 120)]; }
    usort($lessons, fn($x, $y) => $y['at'] <=> $x['at']);
    $months = []; for ($i = 5; $i >= 0; $i--) $months[date('Y-m', strtotime(date('Y-m-01') . " -$i month"))] = 0; $unitsN = [];
    foreach ($lessons as $l) { $m = $l['at'] ? date('Y-m', $l['at']) : ''; if (isset($months[$m])) $months[$m]++; if ($l['unit'] !== '') $unitsN[$l['unit']] = ($unitsN[$l['unit']] ?? 0) + 1; }
    arsort($unitsN); $about = ccAboutOf($base, $em, (array) $u);
    $classes = []; $today = date('Y-m-d'); foreach (glob($base . '/u/' . sha1($em) . '/' . $td . '/*.json') ?: [] as $f) { $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j) || !preg_match('#^data/users/[^/]+/learners/#', (string) ($j['path'] ?? ''))) continue; $st = json_decode((string) ($j['data']['json'] ?? ''), true); if (!is_array($st) || !is_array($st['learners'] ?? null)) continue;
        $ids = array_map(fn($l) => (string) ($l['id'] ?? ''), array_filter($st['learners'], 'is_array')); $tot = 0; $ach = 0; $units = 0; $open = 0; $late = 0;
        foreach ((array) ($st['marks'] ?? []) as $k => $m) { $any = false; foreach ((array) $m as $lid => $cs) { if (!in_array((string) $lid, $ids, true)) continue; foreach ((array) $cs as $v) { if ($v === '' || $v === null) continue; $any = true; $tot++; if ($v === 'a') $ach++; } } if ($any) $units++; }
        foreach ((array) ($st['subs'] ?? []) as $k => $m) foreach ((array) $m as $lid => $x) { if (!is_array($x) || empty($x['ra']) || !empty($x['ro']) || !in_array((string) $lid, $ids, true)) continue; $open++; if (empty($x['ri']) && !empty($x['rd']) && (string) $x['rd'] < $today) $late++; }
        $classes[] = ['name' => mb_substr((string) ($st['name'] ?? 'Group'), 0, 60), 'campus' => mb_substr((string) ($st['campus'] ?? ''), 0, 60), 'learners' => count($ids), 'units' => $units, 'marked' => $tot, 'achieved' => $ach, 'open' => $open, 'late' => $late]; }
    usort($classes, fn($x, $y) => strcasecmp($x['name'], $y['name']));
    $iv = json_decode((string) @file_get_contents($tdir . '/_iv.json'), true); if (!is_array($iv)) $iv = []; $row = fn($r) => ['at' => (int) ($r['at'] ?? 0), 'unit' => (string) (($r['unitName'] ?? '') ?: ($r['unit'] ?? '')), 'group' => (string) ($r['group'] ?? ''), 'decision' => (string) ($r['decision'] ?? ''), 'done' => !empty($r['done']), 'who' => ''];
    $did = []; $had = []; foreach ($iv as $r) { if (!is_array($r)) continue; if (($r['by'] ?? '') === $em) $did[] = ['who' => (string) ($r['ownerName'] ?? '')] + $row($r); if (($r['owner'] ?? '') === $em) $had[] = ['who' => (string) ($r['byName'] ?? '')] + $row($r); }
    usort($did, fn($x, $y) => $y['at'] <=> $x['at']); usort($had, fn($x, $y) => $y['at'] <=> $x['at']);
    out(200, ['person' => ['email' => $em, 'name' => (string) (($u['name'] ?? '') ?: ($self ? ($me['name'] ?? '') : '')), 'role' => $self ? (string) $me['role'] : (string) ($u['role'] ?? 'teacher'), 'dept' => $self && $me['role'] === 'superadmin' ? '' : (string) ($acc['depts'][$td]['name'] ?? ''), 'college' => $self && $me['role'] === 'superadmin' ? 'Whole site' : (string) (ccCollege($acc, $tcol)['name'] ?? ''), 'last' => (int) ($u['last'] ?? 0), 'added' => (int) ($u['added'] ?? 0), 'picture' => ccPhotoUrl($em, (string) ($u['picture'] ?? '')), 'self' => $self],
        'teaches' => $pr ? ['role' => (string) $pr['role'], 'subjects' => $pr['subjects'], 'stages' => $pr['stages'], 'levels' => $pr['levels'], 'orgs' => $pr['orgs'], 'quals' => $pr['quals'], 'insts' => array_map(fn($i) => (string) $i['name'], $pr['insts'])] : null,
        'lessons' => ['total' => count($lessons), 'approved' => count(array_filter($lessons, fn($l) => $l['status'] === 'approved')), 'draft' => count(array_filter($lessons, fn($l) => $l['status'] === 'draft')), 'list' => array_slice($lessons, 0, 30)],
        'about' => $about, 'months' => $months, 'units' => array_slice(array_map(fn($k, $v) => ['unit' => (string) $k, 'n' => $v], array_keys($unitsN), array_values($unitsN)), 0, 8), 'unitsTotal' => count($unitsN), 'classes' => $classes, 'iv' => ['did' => array_slice($did, 0, 20), 'didTotal' => count($did), 'had' => array_slice($had, 0, 20), 'hadTotal' => count($had), 'actions' => count(array_filter($had, fn($r) => !$r['done']))]]);
}
if ($action === 'deptlearners') {
    if (empty($me['isLead'])) out(403, ['error' => 'noperm']);
    $sets = []; $who = $acc['users']; foreach (ccSupers() as $e) if (!isset($who[$e])) $who[$e] = ['name' => ''];
    foreach ($who as $e => $u) { foreach (glob($base . '/u/' . sha1((string) $e) . '/' . $dept . '/*.json') ?: [] as $f) {
        $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j) || !preg_match('#^data/users/[^/]+/learners/#', (string) ($j['path'] ?? ''))) continue;
        $s = json_decode((string) ($j['data']['json'] ?? ''), true); if (!is_array($s) || empty($s['learners']) || !is_array($s['learners'])) continue;
        $ls = array_slice(array_values(array_filter($s['learners'], 'is_array')), 0, 80); foreach ($ls as $i => $l) unset($ls[$i]['note']);   // a teacher's own notes on a learner stay with the teacher
        if ($anon) { foreach ($ls as $i => $l) { unset($ls[$i]['first'], $ls[$i]['last']); $rf = trim((string) ($l['ref'] ?? '')); if ($rf !== '' && ccAnonOk($rf)) $ls[$i]['name'] = $rf; else unset($ls[$i]['ref']); } $n = ccAnonNames(array_map(fn($l) => (string) ($l['name'] ?? ''), $ls)); foreach ($ls as $i => $l) $ls[$i]['name'] = $n[$i]; }
        $sets[] = ['by' => (string) ((($u['name'] ?? '') ?: $e)), 'mine' => (string) $e === $me['email'], 'o' => sha1((string) $e), 'set' => ['id' => (string) ($s['id'] ?? ''), 'name' => (string) ($s['name'] ?? ''), 'campus' => mb_substr((string) ($s['campus'] ?? ''), 0, 60), 'learners' => $ls, 'marks' => (array) ($s['marks'] ?? []), 'subs' => (array) ($s['subs'] ?? [])]]; } }
    usort($sets, fn($x, $y) => strcasecmp($x['set']['name'] . $x['by'], $y['set']['name'] . $y['by']));
    out(200, ['sets' => $sets]);
}

/* internal verification: a leader samples a teacher's marking for one group and unit, and signs it off or sets actions. Nobody can verify their own marking.
   The record holds which learners were sampled by their id in the teacher's list, never their names */
if (in_array($action, ['ivlist', 'ivsave', 'ivdone', 'ivdel'], true)) {
    if (!ccRoom($acc, $me, 'learners')) out(403, ['error' => 'noperm']);
    if (!is_dir($ddir) && !@mkdir($ddir, 0700, true)) out(500, ['error' => 'save']);
    $ivf = $ddir . '/_iv.json'; $fh = @fopen($ivf . '.lock', 'c'); if (!$fh || !flock($fh, LOCK_EX)) out(500, ['error' => 'busy']);
    $all = json_decode((string) @file_get_contents($ivf), true); if (!is_array($all)) $all = []; $isLead = !empty($me['isLead']);
    $txt = fn($v, int $n): string => mb_substr(trim(str_replace("\0", '', (string) $v)), 0, $n);
    $keep = function () use (&$all, $ivf): void { $tmp = $ivf . '.' . bin2hex(random_bytes(4)) . '.tmp'; if (@file_put_contents($tmp, json_encode(array_values($all), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX) === false || !@rename($tmp, $ivf)) { @unlink($tmp); out(500, ['error' => 'save']); } };
    if ($action === 'ivsave') {
        if (!$isLead) out(403, ['error' => 'noperm']);
        $o = (string) ($body['o'] ?? ''); $owner = ''; foreach (array_merge(array_keys($acc['users']), ccSupers()) as $e) if (sha1((string) $e) === $o) $owner = (string) $e;
        if ($owner === '') out(404, ['error' => 'not_found']); if ($owner === $me['email']) out(400, ['error' => 'own']);
        $dec = ($body['decision'] ?? '') === 'action' ? 'action' : 'agreed'; $unit = $txt($body['unit'] ?? '', 160); $gid = preg_replace('/[^a-z0-9-]/', '', (string) ($body['gid'] ?? '')); if ($unit === '' || $gid === '') out(400, ['error' => 'unit']);
        $sam = array_slice(array_values(array_filter((array) ($body['sampled'] ?? []), fn($x) => is_string($x) && preg_match('/^[a-z0-9]{1,40}$/', $x))), 0, 40); if (!$sam) out(400, ['error' => 'sample']);
        $actions = $txt($body['actions'] ?? '', 1500); if ($dec === 'action' && $actions === '') out(400, ['error' => 'actions']);
        $rec = ['id' => bin2hex(random_bytes(6)), 'o' => $o, 'owner' => $owner, 'ownerName' => (string) (($acc['users'][$owner]['name'] ?? '') ?: $owner), 'gid' => $gid, 'group' => $txt($body['group'] ?? '', 60), 'unit' => $unit, 'unitName' => $txt($body['unitName'] ?? '', 160),
            'sampled' => $sam, 'of' => max(0, min(200, (int) ($body['of'] ?? 0))), 'decision' => $dec, 'notes' => $txt($body['notes'] ?? '', 1500), 'actions' => $dec === 'action' ? $actions : '', 'by' => $me['email'], 'byName' => (string) (($me['name'] ?? '') ?: $me['email']), 'at' => time(), 'done' => $dec === 'agreed', 'reply' => ''];
        array_unshift($all, $rec); $all = array_slice($all, 0, 800); $keep(); ccLog($dept, $me, $dec === 'agreed' ? 'verified the marking for' : 'set verification actions for', $rec['group'] . ', ' . $rec['unitName']);
        if ($dec === 'action') ccMail($owner, 'Internal verification: actions for you', "Hello,\n\n" . $rec['byName'] . " has sampled your marking for " . $rec['group'] . " (" . $rec['unitName'] . ") and set some actions. Open Learners, choose the group and unit, and look under Verification.\n\n" . ccAppUrl() . "\n");
    }
    if ($action === 'ivdone' || $action === 'ivdel') { $id = (string) ($body['id'] ?? ''); $hit = false;
        foreach ($all as $i => $r) { if (($r['id'] ?? '') !== $id) continue;
            if ($action === 'ivdone') { if (($r['owner'] ?? '') !== $me['email']) out(403, ['error' => 'noperm']); $all[$i]['done'] = true; $all[$i]['doneAt'] = time(); $all[$i]['reply'] = $txt($body['reply'] ?? '', 600); }
            else { if (($r['by'] ?? '') !== $me['email'] && $me['role'] !== 'superadmin') out(403, ['error' => 'noperm']); unset($all[$i]); }
            $hit = true; break; }
        if (!$hit) out(404, ['error' => 'not_found']); $keep(); }
    flock($fh, LOCK_UN);
    $o = []; foreach ($all as $r) { if (!$isLead && ($r['owner'] ?? '') !== $me['email']) continue; $mineR = ($r['owner'] ?? '') === $me['email']; $byMe = ($r['by'] ?? '') === $me['email']; unset($r['owner'], $r['by']); $o[] = $r + ['mine' => $mineR, 'byMe' => $byMe]; }
    out(200, ['iv' => $o]);
}

$lead = !empty($me['isLead']);
function pendFile(string $ddir, string $p): string { return $ddir . '/_pending/' . sha1($p) . '.json'; }
function pendRead(string $ddir, string $p): ?array { $j = json_decode((string) @file_get_contents(pendFile($ddir, $p)), true); return is_array($j) && is_array($j['data'] ?? null) ? $j : null; }
/* the leaders of this department (the super admins if it has none), for the emails that ask for a sign-off */
function leadersOf(array $acc, string $dept, string $not = ''): array {
    $to = []; foreach ($acc['users'] as $e => $u) if (($u['role'] ?? '') === 'hod' && ccActive($u) && ($u['dept'] ?? '') === $dept) $to[] = $e;
    if (!$to) { $c = ccCollegeOf($acc, $dept); foreach ($acc['users'] as $e => $u) if (($u['role'] ?? '') === 'owner' && ccActive($u) && ccCollegeOf($acc, (string) ($u['dept'] ?? '')) === $c) $to[] = $e; }   // no head of department: the institution's manager
    if (!$to) $to = ccSupers();
    return array_values(array_filter(array_unique($to), fn($e) => $e !== $not));
}
function once(string $key, int $secs): bool { $f = sys_get_temp_dir() . '/cc_' . sha1($key); if (is_file($f) && filemtime($f) > time() - $secs) return false; @touch($f); return true; }
/* ---------- the institution's own Word and PowerPoint templates ---------- */
const TPL = ['docx' => 'lessonplan.docx', 'pptx' => 'slides.pptx'];
function tplInfo(string $ddir): array { $j = json_decode((string) @file_get_contents($ddir . '/_tpl/info.json'), true); $j = is_array($j) ? $j : []; foreach (TPL as $k => $f) if (!is_file($ddir . '/_tpl/' . $f)) unset($j[$k]); return $j; }
if (in_array($action, ['tplinfo', 'tplput', 'tpldel', 'tplget'], true)) {
    $kind = (string) ($body['kind'] ?? ($_GET['kind'] ?? '')); $td = $ddir . '/_tpl';
    if ($action === 'tplinfo') out(200, ['tpl' => (object) tplInfo($ddir)]);
    if (!isset(TPL[$kind])) out(400, ['error' => 'Bad kind.']);
    $f = $td . '/' . TPL[$kind];
    if ($action === 'tplget') { if (!is_file($f)) out(404, ['error' => 'none']); header('Content-Type: application/octet-stream'); header('Content-Length: ' . filesize($f)); readfile($f); exit; }
    if (!$lead) out(403, ['error' => 'noperm']);
    $info = tplInfo($ddir);
    if ($action === 'tpldel') { @unlink($f); unset($info[$kind]); @file_put_contents($td . '/info.json', json_encode($info), LOCK_EX); ccLog($dept, $me, 'removed the institution ' . ($kind === 'docx' ? 'lesson plan form' : 'PowerPoint template')); out(200, ['tpl' => (object) $info]); }
    // tplput: the file arrives in pieces so it works on hosts with a small upload limit
    $i = (int) ($body['i'] ?? 0); $n = (int) ($body['n'] ?? 1); $bin = base64_decode((string) ($body['data'] ?? ''), true);
    if ($bin === false || $n < 1 || $n > 60 || $i < 0 || $i >= $n) out(400, ['error' => 'Bad piece.']);
    if (!is_dir($td) && !@mkdir($td, 0700, true)) out(500, ['error' => 'Could not save. Check the data folder is writable.']);
    $part = $f . '.' . preg_replace('/[^a-f0-9]/', '', sha1($me['email'])) . '.part';
    if ($i === 0) @unlink($part);
    if (@file_put_contents($part, $bin, FILE_APPEND | LOCK_EX) === false) out(500, ['error' => 'Could not save.']);
    if (filesize($part) > 45 * 1024 * 1024) { @unlink($part); out(413, ['error' => 'big']); }
    if ($i < $n - 1) out(200, ['ok' => true]);
    $h = (string) @file_get_contents($part, false, null, 0, 4); if ($h !== "PK\x03\x04") { @unlink($part); out(400, ['error' => 'type']); }
    @rename($part, $f); $name = mb_substr(trim((string) preg_replace('/[\x00-\x1F<>\/\\\\]+/u', ' ', (string) ($body['name'] ?? TPL[$kind]))), 0, 80);
    $info[$kind] = ['name' => $name, 'size' => filesize($f), 'at' => time(), 'by' => (string) (($me['name'] ?? '') ?: $me['email'])];
    @file_put_contents($td . '/info.json', json_encode($info, JSON_UNESCAPED_UNICODE), LOCK_EX);
    ccLog($dept, $me, 'uploaded the institution ' . ($kind === 'docx' ? 'lesson plan form' : 'PowerPoint template'), $name);
    out(200, ['tpl' => (object) $info]);
}
/* ---------- deleted lessons wait in the bin for 30 days ---------- */
if ($action === 'bin' || $action === 'restore') {
    if (!$lead) out(403, ['error' => 'noperm']);
    $bd = $ddir . '/_bin'; $list = [];
    if ($action === 'restore') {
        $f = $bd . '/' . preg_replace('/[^a-f0-9]/', '', (string) ($body['id'] ?? '')) . '.json'; $j = json_decode((string) @file_get_contents($f), true);
        if (!is_array($j) || !isset($j['path'], $j['data'])) out(404, ['error' => 'gone']);
        if (is_file(fileFor($ddir, (string) $j['path']))) out(409, ['error' => 'exists']);
        writeDoc($ddir, (string) $j['path'], ['data' => $j['data']]); @unlink($f);
        ccLog($dept, $me, 'restored', (string) ($j['data']['title'] ?? ''));
    }
    foreach (glob($bd . '/*.json') ?: [] as $f) { $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j) || !isset($j['path'])) continue;
        $at = (int) ($j['deleted']['at'] ?? filemtime($f)); if ($at < time() - 30 * 86400) { @unlink($f); continue; }
        $list[] = ['id' => basename($f, '.json'), 'title' => (string) ($j['data']['title'] ?? 'Lesson'), 'unit' => (string) ($j['data']['unit'] ?? ''), 'by' => (string) ($j['deleted']['by'] ?? ''), 'at' => $at]; }
    usort($list, fn($x, $y) => $y['at'] <=> $x['at']);
    out(200, ['bin' => $list]);
}
/* ---------- comments on a lesson: anyone in the department can leave one ---------- */
function notesFile(string $ddir, string $id): string { return $ddir . '/_notes/' . sha1($id) . '.json'; }
function notesRead(string $ddir, string $id): array { $j = json_decode((string) @file_get_contents(notesFile($ddir, $id)), true); return is_array($j) ? $j : []; }
if (in_array($action, ['notes', 'note', 'notedel'], true)) {
    $lid = (string) ($body['lesson'] ?? ''); if (!preg_match('#^[A-Za-z0-9_\-]{1,60}$#', $lid)) out(400, ['error' => 'Bad lesson.']);
    $list = notesRead($ddir, $lid);
    if ($action === 'note') {
        $t = trim((string) preg_replace('/[\x00-\x08\x0B-\x1F\x7F]+/u', ' ', (string) ($body['text'] ?? ''))); $t = mb_substr($t, 0, 600); if ($t === '') out(400, ['error' => 'empty']);
        $list[] = ['id' => bin2hex(random_bytes(5)), 'by' => (string) (($me['name'] ?? '') ?: $me['email']), 'email' => $me['email'], 'at' => time(), 'text' => $t];
        $list = array_slice($list, -100);
        ccLog($dept, $me, 'commented on', (string) ($body['title'] ?? $lid));
        $ld = readDoc($ddir, 'lessons/' . $lid); $lo = $ld ? (inner($ld['data'], 'lesson') ?? []) : []; $au = (string) ($lo['meta']['author']['email'] ?? '');
        if ($au !== '' && $au !== $me['email'] && isset($acc['users'][$au])) ccMail($au, 'New comment on your lesson', "Hello,\n\n" . (($me['name'] ?? '') ?: $me['email']) . " left a comment on \"" . (string) ($lo['title'] ?? 'your lesson') . "\":\n\n" . $t . "\n\nOpen the lesson's Comments tab to reply:\n" . ccAppUrl() . "\n");
    }
    if ($action === 'notedel') { $nid = (string) ($body['id'] ?? ''); $list = array_values(array_filter($list, fn($n) => !(($n['id'] ?? '') === $nid && ($lead || ($n['email'] ?? '') === $me['email'])))); }
    if ($action !== 'notes') { $f = notesFile($ddir, $lid); if (!is_dir(dirname($f))) @mkdir(dirname($f), 0700, true); @file_put_contents($f, json_encode($list, JSON_UNESCAPED_UNICODE), LOCK_EX); }
    out(200, ['notes' => $list]);
}
/* ---------- the department at a glance, for its head of department or subject leader ---------- */
if ($action === 'overview') {
    if (!$lead) out(403, ['error' => 'noperm']);
    $units = []; $lessons = []; $seqPaths = [];
    foreach (glob($ddir . '/*.json') ?: [] as $f) {
        $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j) || !isset($j['path'], $j['data']) || !is_array($j['data'])) continue;
        $k = kindOf((string) $j['path']); $o = ($k === 'lesson' || $k === 'seq') ? inner($j['data'], $k) : null; if (!$o) continue;
        if ($k === 'seq') { $rows = (array) ($o['rows'] ?? []); $seqPaths[$j['path']] = count($units);
            $units[] = ['course' => (string) ($o['course'] ?? ''), 'level' => (string) ($o['level'] ?? ''), 'unit' => (string) ($o['unit'] ?? ''), 'rows' => count($rows), 'built' => count(array_filter($rows, fn($r) => !empty($r['lessonId']))), 'taught' => []]; }
        else { $m = (array) ($o['meta'] ?? []); $lessons[$j['path']] = ['id' => substr($j['path'], 8), 'title' => (string) ($o['title'] ?? ''), 'unit' => (string) ($m['unit'] ?? ''), 'author' => (string) ($m['author']['email'] ?? ''), 'authorName' => (string) ($m['author']['name'] ?? ''),
            'status' => !empty($j['data']['pending']) ? 'draft' : (string) ($m['status'] ?? ''), 'draftBy' => (string) ($j['data']['pending']['name'] ?? ($m['draftBy']['name'] ?? '')), 'held' => !empty($j['data']['pending']), 'at' => (int) ($j['data']['updatedAt'] ?? 0)]; }
    }
    $staff = [];
    foreach ($acc['users'] as $email => $u) {
        $isSup = in_array($email, ccSupers(), true);
        if (!ccActive($u) || (!$isSup && ($u['dept'] ?? '') !== $dept)) continue;
        $ud = $base . '/u/' . sha1($email) . '/' . $dept; $taught = 0; $scores = []; $seen = false;
        foreach (glob($ud . '/*.json') ?: [] as $f) {
            $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j) || !isset($j['path']) || !is_array($j['own'] ?? null)) continue; $own = $j['own']; $seen = true;
            if (isset($seqPaths[$j['path']])) { $n = count((array) ($own['taught'] ?? [])); foreach ((array) ($own['g'] ?? []) as $g) if (is_array($g)) $n = max($n, count((array) ($g['taught'] ?? [])));
                if ($n) { $units[$seqPaths[$j['path']]]['taught'][] = ['name' => (string) (($u['name'] ?? '') ?: $email), 'n' => $n]; $taught += $n; } }
            foreach (array_merge([(array) ($own['lastResults'] ?? [])], array_values((array) ($own['resultsBy'] ?? []))) as $r) foreach ((array) ($r['pct'] ?? []) as $p) if (is_numeric($p)) $scores[] = (float) $p;
        }
        if ($isSup && !$seen && ($u['dept'] ?? '') !== $dept) continue;
        $staff[] = ['email' => $email, 'name' => (string) ($u['name'] ?? ''), 'role' => $isSup ? 'superadmin' : (string) ($u['role'] ?? 'teacher'), 'last' => (int) ($u['last'] ?? 0),
            'built' => count(array_filter($lessons, fn($l) => $l['author'] === $email)), 'taught' => $taught, 'quiz' => $scores ? (int) round(array_sum($scores) / count($scores)) : null, 'quizN' => count($scores)];
    }
    usort($units, fn($x, $y) => strnatcasecmp($x['course'] . $x['level'] . $x['unit'], $y['course'] . $y['level'] . $y['unit']));
    $drafts = array_values(array_filter($lessons, fn($l) => $l['status'] === 'draft')); usort($drafts, fn($x, $y) => $y['at'] <=> $x['at']);
    out(200, ['units' => $units, 'staff' => $staff, 'lessons' => count($lessons), 'drafts' => $drafts, 'prefs' => ccDeptPrefs($acc, $dept)]);
}

$path = (string) ($body['path'] ?? '');
if (!preg_match('#^[A-Za-z0-9_\-/.]{1,300}$#', $path) || strpos($path, '..') !== false) out(400, ['error' => 'Bad path.']);
$kind = kindOf($path);

/* ---------- a teacher's change that's waiting: a leader looks at it, then accepts it or turns it down ---------- */
if (in_array($action, ['pending', 'accept', 'reject'], true)) {
    if (!$lead || $kind !== 'lesson') out(403, ['error' => 'noperm']);
    $pf = pendRead($ddir, $path); $old = readDoc($ddir, $path);
    if (!$pf || !$old) { if ($old && !empty($old['data']['pending'])) { unset($old['data']['pending']); writeDoc($ddir, $path, ['data' => $old['data']]); } out(404, ['error' => 'gone']); }
    if ($action === 'pending') out(200, ['data' => $pf['data'], 'by' => $pf['by'] ?? null, 'at' => $pf['at'] ?? 0]);
    $by = (array) ($pf['by'] ?? []); $title = (string) ($old['data']['title'] ?? 'the lesson'); $who = ['name' => (string) (($me['name'] ?? '') ?: $me['email']), 'email' => $me['email']];
    if ($action === 'accept') {
        $d = $pf['data']; $o = inner($d, 'lesson') ?? []; $o['meta']['status'] = 'approved'; $o['meta']['approvedBy'] = $who + ['at' => time() * 1000]; unset($o['meta']['draftBy']);
        $d = withInner($d, 'lesson', $o); $d['status'] = 'approved'; $d['title'] = (string) ($o['title'] ?? $title); unset($d['pending'], $d['pendingMine']);
        writeDoc($ddir, $path, ['data' => $d]); ccLog($dept, $me, 'signed off a change to', (string) $d['title']);
        if (!empty($by['email'])) ccMail((string) $by['email'], 'Your change was signed off', "Hello,\n\n" . $who['name'] . " signed off your change to \"" . $d['title'] . "\". Everyone now sees your version, and it can be shared with students.\n\n" . ccAppUrl() . "\n");
    } else {
        unset($old['data']['pending']); writeDoc($ddir, $path, ['data' => $old['data']]); ccLog($dept, $me, 'turned down a change to', $title);
        if (!empty($by['email'])) ccMail((string) $by['email'], 'Your change wasn\'t used', "Hello,\n\n" . $who['name'] . " looked at your change to \"" . $title . "\" and kept the current version. Have a word with them if you'd like to know why.\n\n" . ccAppUrl() . "\n");
    }
    @unlink(pendFile($ddir, $path));
    out(200, ['ok' => true]);
}
if ($action === 'set') {
    if (!isset($body['data']) || !is_array($body['data'])) out(400, ['error' => 'Nothing to save.']);
    $data = $body['data'];
    if ($kind === 'own') { if ($anon) $data = anonDoc($path, $data); writeDoc($udir, $path, ['data' => $data]); out(200, ['ok' => true]); }
    if ($kind === 'dates' || $kind === 'subjects') { if (!$me[$kind === 'dates' ? 'canDates' : 'canSubjects']) out(403, ['error' => 'noperm']); writeDoc($ddir, $path, ['data' => $data]); out(200, ['ok' => true]); }
    if ($kind === 'edit') { if (!$me['canEdit']) out(403, ['error' => 'noperm']); writeDoc($ddir, $path, ['data' => $data]); out(200, ['ok' => true]); }
    $obj = inner($data, $kind);
    if (!$obj) { if (!$me['canEdit']) out(403, ['error' => 'noperm']); writeDoc($ddir, $path, ['data' => $data]); out(200, ['ok' => true]); }
    [$shared, $mine] = split($obj, $kind === 'lesson' ? LESSON_OWN : SEQ_OWN, $kind === 'seq');
    writeDoc($udir, $path, ['own' => $mine, 'updatedAt' => (int) ($obj['updatedAt'] ?? $data['updatedAt'] ?? 0) ?: time() * 1000]);
    $old = readDoc($ddir, $path);
    if ($kind === 'lesson') {
        $oo = $old ? (inner($old['data'], 'lesson') ?? []) : null; $om = (array) ($oo['meta'] ?? []); if (!is_array($shared['meta'] ?? null)) $shared['meta'] = [];
        $who = ['name' => (string) (($me['name'] ?? '') ?: $me['email']), 'email' => $me['email']];
        // a teacher's own copy of a lesson is theirs to change, even without edit rights
        $isCopy = !$old && !empty($shared['meta']['copyOf']);
        $isMine = $old && ($om['author']['email'] ?? '') === $me['email'] && !empty($om['copyOf']);
        if (!$me['canEdit'] && ($isCopy || $isMine)) { $shared['meta']['author'] = $isMine ? $om['author'] : $who + ['at' => time() * 1000]; if ($isMine) $shared['meta']['copyOf'] = $om['copyOf']; }
        if ($me['canEdit'] || $isCopy || $isMine) {
            $core = fn(array $l) => json_encode(array_intersect_key($l, array_flip(['title', 'starter', 'objectives', 'steps', 'doneBy', 'plan', 'studentPlan', 'howTo', 'tasks', 'teach', 'quiz', 'study', 'stretch', 'support', 'plenary'])));
            $changed = !$old || $core($oo) !== $core($shared);
            if (!$lead && !empty($dinfo['signoff']) && $changed && $old && ($om['status'] ?? '') !== 'draft') {
                // the lesson everyone uses stays as it is. This person's version waits for a leader to accept it.
                $shared['meta']['status'] = 'draft'; $shared['meta']['draftBy'] = $who + ['at' => time() * 1000]; unset($shared['meta']['approvedBy']);
                $pd = withInner($data, $kind, $shared); $pd['status'] = 'draft'; $pf = pendFile($ddir, $path);
                if (!is_dir(dirname($pf))) @mkdir(dirname($pf), 0700, true);
                @file_put_contents($pf, json_encode(['path' => $path, 'by' => $who, 'at' => time(), 'data' => $pd], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
                $od = $old['data']; $od['pending'] = $who + ['at' => time()]; writeDoc($ddir, $path, ['data' => $od]);
                if (once('held' . $me['email'] . $path, 1800)) { ccLog($dept, $me, 'changed (waiting for sign-off)', (string) ($shared['title'] ?? ''));
                    foreach (leadersOf($acc, $dept, $me['email']) as $to) ccMail($to, 'A lesson change needs your sign-off', "Hello,\n\n" . $who['name'] . " changed \"" . (string) ($shared['title'] ?? 'a lesson') . "\". Everyone else still sees the current version until you accept the change.\n\nOpen Results to see what's waiting:\n" . ccAppUrl() . "\n"); }
                out(200, ['ok' => true, 'status' => 'draft', 'held' => true]);
            }
            if (!$lead) { foreach (['status', 'draftBy', 'approvedBy'] as $k) { if (isset($om[$k])) $shared['meta'][$k] = $om[$k]; else unset($shared['meta'][$k]); }
                if (!empty($dinfo['signoff']) && $changed) { $shared['meta']['status'] = 'draft'; $shared['meta']['draftBy'] = $who + ['at' => time() * 1000]; unset($shared['meta']['approvedBy']); } }
            elseif (($shared['meta']['status'] ?? '') === 'approved' && ($om['status'] ?? '') === 'draft') { $shared['meta']['approvedBy'] = $who + ['at' => time() * 1000]; ccLog($dept, $me, 'signed off', (string) ($shared['title'] ?? '')); }
            $data['status'] = (string) ($shared['meta']['status'] ?? ''); if (!empty($shared['meta']['author']['name'])) $data['author'] = (string) $shared['meta']['author']['name'];
            if ($old && !empty($old['data']['pending']) && is_file(pendFile($ddir, $path))) $data['pending'] = $old['data']['pending']; else unset($data['pending']);
            unset($data['pendingMine']);
            if (!$old) ccLog($dept, $me, $isCopy ? 'made a copy of' : 'built', (string) ($shared['title'] ?? ''));
            if (!$lead && ($shared['meta']['status'] ?? '') === 'draft' && ($om['status'] ?? '') !== 'draft' && once('draft' . $me['email'] . $path, 1800))
                foreach (leadersOf($acc, $dept, $me['email']) as $to) ccMail($to, 'A lesson needs your sign-off', "Hello,\n\n" . $who['name'] . " has a draft waiting for sign-off: \"" . (string) ($shared['title'] ?? 'a lesson') . "\". It can't be shared with students until you sign it off.\n\nOpen Results to see what's waiting:\n" . ccAppUrl() . "\n");
            if ($lead && ($shared['meta']['status'] ?? '') === 'approved' && ($om['status'] ?? '') === 'draft' && !empty($om['draftBy']['email']) && $om['draftBy']['email'] !== $me['email'])
                ccMail((string) $om['draftBy']['email'], 'Your lesson was signed off', "Hello,\n\n" . $who['name'] . " signed off \"" . (string) ($shared['title'] ?? 'your lesson') . "\". It can be shared with students now.\n\n" . ccAppUrl() . "\n");
            elseif ($changed) { $lf = sys_get_temp_dir() . '/cc_ed_' . sha1($me['email'] . $path); if (!is_file($lf) || filemtime($lf) < time() - 900) { @touch($lf); ccLog($dept, $me, 'edited', (string) ($shared['title'] ?? '')); } }
            writeDoc($ddir, $path, ['data' => withInner($data, $kind, $shared)]); out(200, ['ok' => true, 'status' => $data['status']]);
        }
    }
    if ($me['canEdit']) { writeDoc($ddir, $path, ['data' => withInner($data, $kind, $shared)]); out(200, ['ok' => true]); }
    if ($kind === 'lesson') {
        // without edit rights only the shared download files can change
        if (!$old) out(200, ['ok' => true, 'shared' => false]);
        $oo = inner($old['data'], 'lesson') ?? []; $ch = false;
        foreach (LESSON_ANYONE as $k) if (array_key_exists($k, $shared) && ($oo[$k] ?? null) !== $shared[$k]) { $oo[$k] = $shared[$k]; $ch = true; }
        if ($ch) writeDoc($ddir, $path, ['data' => withInner($old['data'], 'lesson', $oo)]);
        out(200, ['ok' => true, 'shared' => $ch]);
    }
    // a sequence: a new starter sequence, or one that only gains row ids, can be saved by anyone
    $oo = $old ? inner($old['data'], 'seq') : null;
    if (!$oo) { writeDoc($ddir, $path, ['data' => withInner($data, 'seq', $shared)]); out(200, ['ok' => true]); }
    [$oshared] = split($oo, SEQ_OWN, true);
    if (json_encode(stripRowIds($oshared)) === json_encode(stripRowIds($shared))) { writeDoc($ddir, $path, ['data' => withInner($data, 'seq', $shared)]); out(200, ['ok' => true]); }
    out(200, ['ok' => true, 'shared' => false]);
}
if ($action === 'del') {
    if ($kind === 'own') { @unlink(fileFor($udir, $path)); out(200, ['ok' => true]); }
    if ($kind === 'lesson') {
        // deleting a lesson: with a department delete password, anyone who knows it can. Without one, people who can edit, and the owner of a copy.
        $old = readDoc($ddir, $path); if (!$old) { @unlink(fileFor($udir, $path)); out(200, ['ok' => true]); }
        $oo = inner($old['data'], 'lesson') ?? []; $om = (array) ($oo['meta'] ?? []); $hash = (string) ($dinfo['delHash'] ?? '');
        $isMine = ($om['author']['email'] ?? '') === $me['email'] && !empty($om['copyOf']);
        if ($hash !== '' && empty($me['admin'])) { if (!password_verify((string) ($body['pass'] ?? ''), $hash)) { usleep(400000); out(403, ['error' => 'delpass']); } }
        elseif (!$me['canEdit'] && !$isMine) out(403, ['error' => 'noperm']);
        ccLog($dept, $me, 'deleted', (string) ($oo['title'] ?? ''));
        $bd = $ddir . '/_bin'; if (!is_dir($bd)) @mkdir($bd, 0700, true); $od = $old['data']; unset($od['pending']);
        @file_put_contents($bd . '/' . sha1($path) . '.json', json_encode(['path' => $path, 'data' => $od, 'deleted' => ['by' => (string) (($me['name'] ?? '') ?: $me['email']), 'at' => time()]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
        @unlink(fileFor($ddir, $path)); @unlink(fileFor($udir, $path)); @unlink(pendFile($ddir, $path));
        out(200, ['ok' => true]);
    }
    if (!$me['canEdit'] || ($kind === 'dates' && !$me['canDates']) || ($kind === 'subjects' && !$me['canSubjects'])) out(403, ['error' => 'noperm']);
    @unlink(fileFor($ddir, $path)); @unlink(fileFor($udir, $path));
    out(200, ['ok' => true]);
}

out(400, ['error' => 'Unknown action.']);
