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
/* where the college keeps learners anonymous, names never reach the disk and never leave it: class lists hold numbers */
$anon = ccAnon($acc, ccCollegeOf($acc, (string) $dept));
function anonDoc(string $p, $data) { if (!is_array($data)) return $data;
    if (preg_match('#^data/users/[^/]+/learners/#', $p)) { $s = json_decode((string) ($data['json'] ?? ''), true); if (!is_array($s) || !is_array($s['learners'] ?? null)) return $data;
        $s['learners'] = array_values(array_filter($s['learners'], 'is_array'));
        foreach ($s['learners'] as $i => $l) { unset($s['learners'][$i]['first'], $s['learners'][$i]['last']); $rf = trim((string) ($l['ref'] ?? '')); if ($rf !== '' && ccAnonOk($rf)) $s['learners'][$i]['name'] = $rf; else unset($s['learners'][$i]['ref']); }   // names from an imported list go too; a reference number stands in for the name
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
if ($action === 'deptlearners') {
    if (empty($me['isLead'])) out(403, ['error' => 'noperm']);
    $sets = []; $who = $acc['users']; foreach (ccSupers() as $e) if (!isset($who[$e])) $who[$e] = ['name' => ''];
    foreach ($who as $e => $u) { foreach (glob($base . '/u/' . sha1((string) $e) . '/' . $dept . '/*.json') ?: [] as $f) {
        $j = json_decode((string) @file_get_contents($f), true); if (!is_array($j) || !preg_match('#^data/users/[^/]+/learners/#', (string) ($j['path'] ?? ''))) continue;
        $s = json_decode((string) ($j['data']['json'] ?? ''), true); if (!is_array($s) || empty($s['learners']) || !is_array($s['learners'])) continue;
        $ls = array_slice(array_values(array_filter($s['learners'], 'is_array')), 0, 80); if ($anon) { $n = ccAnonNames(array_map(fn($l) => (string) ($l['name'] ?? ''), $ls)); foreach ($ls as $i => $l) $ls[$i]['name'] = $n[$i]; }
        $sets[] = ['by' => (string) ((($u['name'] ?? '') ?: $e)), 'mine' => (string) $e === $me['email'], 'o' => sha1((string) $e), 'set' => ['id' => (string) ($s['id'] ?? ''), 'name' => (string) ($s['name'] ?? ''), 'learners' => $ls, 'marks' => (array) ($s['marks'] ?? []), 'subs' => (array) ($s['subs'] ?? [])]]; } }
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
    if (!$to) { $c = ccCollegeOf($acc, $dept); foreach ($acc['users'] as $e => $u) if (($u['role'] ?? '') === 'owner' && ccActive($u) && ccCollegeOf($acc, (string) ($u['dept'] ?? '')) === $c) $to[] = $e; }   // no head of department: the college's manager
    if (!$to) $to = ccSupers();
    return array_values(array_filter(array_unique($to), fn($e) => $e !== $not));
}
function once(string $key, int $secs): bool { $f = sys_get_temp_dir() . '/cc_' . sha1($key); if (is_file($f) && filemtime($f) > time() - $secs) return false; @touch($f); return true; }
/* ---------- the college's own Word and PowerPoint templates ---------- */
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
    if ($action === 'tpldel') { @unlink($f); unset($info[$kind]); @file_put_contents($td . '/info.json', json_encode($info), LOCK_EX); ccLog($dept, $me, 'removed the college ' . ($kind === 'docx' ? 'lesson plan form' : 'PowerPoint template')); out(200, ['tpl' => (object) $info]); }
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
    ccLog($dept, $me, 'uploaded the college ' . ($kind === 'docx' ? 'lesson plan form' : 'PowerPoint template'), $name);
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
