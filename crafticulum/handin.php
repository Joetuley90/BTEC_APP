<?php
/*
 * Crafticulum: hand-in links. A teacher makes a link for an assignment; students use it to hand in a file.
 *
 * This is the ONE place learner work is kept on the server, and only when a department's leader has switched it on
 * (Settings, Department rules). It is off to begin with. Files are kept for CC_HANDIN_DAYS days and then deleted.
 * A file is stored under a random name with no extension, inside the data folder (which the web server never serves),
 * and is only ever sent back to a signed-in member of the same department, as a download.
 */
declare(strict_types=1);
if (!is_file(__DIR__ . '/config.php')) { http_response_code(500); header('Content-Type: application/json; charset=utf-8'); echo json_encode(['error' => 'config']); exit; }
require __DIR__ . '/config.php';
require __DIR__ . '/auth-lib.php';
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
function out(int $code, array $data): void { http_response_code($code); header('Content-Type: application/json; charset=utf-8'); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }
function hiName(string $s, int $n = 60): string { $s = trim((string) preg_replace('/\s+/u', ' ', (string) preg_replace('/[\x00-\x1F\x7F<>"\/\\\\]+/u', '', $s))); return mb_substr($s, 0, $n); }
function hiFile(string $code): ?string { if (!preg_match('/^[a-z0-9]{12}$/', $code)) return null; $m = glob(ccHandinDir() . '/*/' . $code . '-*.json'); return $m ? (string) $m[0] : null; }   // the file's own name has a second part that isn't in the link, in case a host ever served the data folder
function hiRead(string $f): ?array { $j = json_decode((string) @file_get_contents($f), true); return is_array($j) && !empty($j['code']) ? $j : null; }
function hiSave(string $f, array $j): bool { $tmp = $f . '.' . bin2hex(random_bytes(4)) . '.tmp'; if (@file_put_contents($tmp, json_encode($j, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX) === false || !@rename($tmp, $f)) { @unlink($tmp); return false; } return true; }
function hiOpen(array $j): bool { return empty($j['closed']) && (empty($j['closes']) || time() <= (int) $j['closes']); }
function hiOn(string $dept): bool { $a = ccAccounts(); return !empty(ccDeptPrefs($a, $dept)['handin']) && !empty(ccRoomsCollege($a, ccCollegeOf($a, $dept))['handin']); }   // the department has switched it on, and the room hasn't been closed above it
const HI_EXT = ['pdf', 'docx', 'doc', 'pptx', 'xlsx', 'txt', 'rtf', 'odt', 'png', 'jpg', 'jpeg', 'zip'];
const HI_MAX = 20 * 1024 * 1024;
/* the host's own upload limit can be lower than ours, so students are told the real one */
function hiLimit(): int { $b = function (string $v): int { $v = trim($v); $n = (float) $v; $u = strtolower(substr($v, -1)); return (int) ($n * ($u === 'g' ? 1073741824 : ($u === 'm' ? 1048576 : ($u === 'k' ? 1024 : 1)))); };
    $l = [HI_MAX]; foreach (['upload_max_filesize', 'post_max_size'] as $k) { $x = $b((string) ini_get($k)); if ($x > 0) $l[] = $x; } return min($l); }

$action = (string) ($_GET['a'] ?? '');

/* ---------- students: no sign-in, just the link ---------- */
if ($action === 'info') {
    $f = hiFile((string) ($_GET['c'] ?? '')); $j = $f ? hiRead($f) : null; if (!$j || !hiOn((string) $j['dept'])) out(404, ['error' => 'not_found']);
    $a = ccAccounts(); $col = ccCollege($a, ccCollegeOf($a, (string) $j['dept']));
    out(200, ['title' => (string) $j['title'], 'group' => (string) ($j['group'] ?? ''), 'college' => (string) ($col['name'] ?? ''), 'open' => hiOpen($j), 'closes' => (int) ($j['closes'] ?? 0), 'days' => CC_HANDIN_DAYS, 'max' => max(1, (int) floor(hiLimit() / 1048576)), 'types' => HI_EXT, 'anon' => ccAnon($a, ccCollegeOf($a, (string) $j['dept']))]);
}
if ($action === 'send') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(405, ['error' => 'post']);
    $f = hiFile((string) ($_POST['c'] ?? '')); if (!$f) out(404, ['error' => 'not_found']);
    $up = $_FILES['file'] ?? null; $name = hiName((string) ($_POST['name'] ?? ''));
    if (mb_strlen($name) < 2) out(400, ['error' => 'name']);
    if (!is_array($up) || ($up['error'] ?? 1) !== UPLOAD_ERR_OK || !is_uploaded_file((string) $up['tmp_name'])) out(400, ['error' => in_array($up['error'] ?? 0, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) ? 'big' : 'file']);
    $size = (int) filesize((string) $up['tmp_name']); if ($size < 1) out(400, ['error' => 'file']); if ($size > HI_MAX) out(400, ['error' => 'big']);
    $orig = hiName((string) $up['name'], 120); $ext = strtolower((string) pathinfo($orig, PATHINFO_EXTENSION)); if (!in_array($ext, HI_EXT, true)) out(400, ['error' => 'type']);
    $fh = @fopen($f . '.lock', 'c'); if (!$fh || !flock($fh, LOCK_EX)) out(500, ['error' => 'busy']);
    $j = hiRead($f); if (!$j || !hiOn((string) $j['dept'])) { flock($fh, LOCK_UN); out(404, ['error' => 'not_found']); }
    if (!hiOpen($j)) { flock($fh, LOCK_UN); out(403, ['error' => 'closed']); }
    $ac = ccAccounts(); if (ccAnon($ac, ccCollegeOf($ac, (string) $j['dept']))) { if (!ccAnonOk($name)) { flock($fh, LOCK_UN); out(400, ['error' => 'number']); } $orig = preg_replace('/[^A-Za-z0-9]+/', '', $name) . '.' . $ext; }   // anonymous learners: a number, and the file is renamed after it
    $j['files'] = (array) ($j['files'] ?? []); if (count($j['files']) >= 120) { flock($fh, LOCK_UN); out(429, ['error' => 'full']); }
    $ip = substr(hash('sha256', ccSiteId() . '|' . ($_SERVER['REMOTE_ADDR'] ?? '')), 0, 16); $recent = count(array_filter($j['files'], fn($x) => ($x['ip'] ?? '') === $ip && time() - (int) ($x['at'] ?? 0) < 3600));
    if ($recent >= 8) { flock($fh, LOCK_UN); out(429, ['error' => 'slow']); }
    $dir = substr($f, 0, -5); if (!is_dir($dir) && !@mkdir($dir, 0700, true)) { flock($fh, LOCK_UN); out(500, ['error' => 'save']); }
    $id = bin2hex(random_bytes(12)); if (!@move_uploaded_file((string) $up['tmp_name'], $dir . '/' . $id . '.bin')) { flock($fh, LOCK_UN); out(500, ['error' => 'save']); }
    @chmod($dir . '/' . $id . '.bin', 0600);
    $j['files'][] = ['id' => $id, 'name' => $name, 'orig' => $orig, 'ext' => $ext, 'size' => $size, 'at' => time(), 'ip' => $ip];
    $ok = hiSave($f, $j); flock($fh, LOCK_UN); if (!$ok) { @unlink($dir . '/' . $id . '.bin'); out(500, ['error' => 'save']); }
    ccNotify((string) ($j['by'] ?? ''), 'review', 'Work handed in', 'A file was handed in for "' . (string) $j['title'] . '". Open Learners to download it.');
    out(200, ['ok' => true, 'ref' => strtoupper(substr($id, 0, 6)), 'days' => CC_HANDIN_DAYS]);
}

/* ---------- staff ---------- */
ccSession();
$me = ccMe(); if (!$me) out(401, ['error' => 'login']);
if (!empty($me['demo'])) out(403, ['error' => 'demo']);
$dept = (string) $me['dept']; if (!hiOn($dept) || !ccRoom(ccAccounts(), $me, 'handin')) out(403, ['error' => 'off']);
if (!preg_match('/^[a-z0-9][a-z0-9-]*$/', $dept)) out(400, ['error' => 'dept']);
$ddir = ccHandinDir() . '/' . $dept;
$hiAnon = ccAnon(ccAccounts(), ccCollegeOf(ccAccounts(), $dept));
$mine = function (array $j) use ($me): bool { return !empty($me['isLead']) || ($j['by'] ?? '') === $me['email']; };   // leaders see every link in the department; a teacher sees their own
$view = function (array $j) use ($hiAnon): array { if ($hiAnon) foreach ((array) ($j['files'] ?? []) as $i => $x) if (!ccAnonOk((string) $x['name'])) { $j['files'][$i]['name'] = 'Learner'; $j['files'][$i]['orig'] = 'work.' . $x['ext']; } return ['code' => $j['code'], 'title' => $j['title'], 'group' => (string) ($j['group'] ?? ''), 'by' => (string) ($j['byName'] ?? ''), 'created' => (int) $j['created'], 'closes' => (int) ($j['closes'] ?? 0), 'open' => hiOpen($j),
    'files' => array_map(fn($x) => ['id' => $x['id'], 'name' => $x['name'], 'orig' => $x['orig'], 'size' => (int) $x['size'], 'at' => (int) $x['at'], 'left' => max(0, (int) ceil(((int) $x['at'] + CC_HANDIN_DAYS * 86400 - time()) / 86400))], (array) ($j['files'] ?? []))]; };
if ($action === 'get') {   // one handed-in file, as a download
    $f = hiFile((string) ($_GET['c'] ?? '')); $j = $f ? hiRead($f) : null; if (!$j || $j['dept'] !== $dept || !$mine($j)) out(404, ['error' => 'not_found']);
    $id = (string) ($_GET['f'] ?? ''); if (!preg_match('/^[a-f0-9]{24}$/', $id)) out(400, ['error' => 'file']);
    $x = null; foreach ((array) $j['files'] as $y) if ($y['id'] === $id) $x = $y; $p = substr($f, 0, -5) . '/' . $id . '.bin'; if (!$x || !is_file($p)) out(404, ['error' => 'gone']);
    $safe = preg_replace('/[^A-Za-z0-9 ._()-]+/', '_', hiName((string) $x['name'], 40) . ' - ' . (string) $x['orig']) ?: 'work.' . $x['ext'];
    header('Content-Type: application/octet-stream'); header('Content-Disposition: attachment; filename="' . $safe . '"'); header('Content-Length: ' . filesize($p)); header("Content-Security-Policy: default-src 'none'; sandbox");
    readfile($p); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(405, ['error' => 'post']);
$body = json_decode((string) file_get_contents('php://input', false, null, 0, 20000), true); if (!is_array($body)) $body = [];
if ($action === 'list') { $o = []; foreach ((array) glob($ddir . '/*.json') as $f) { $j = hiRead((string) $f); if ($j && $mine($j)) $o[] = $view($j); } usort($o, fn($a, $b) => $b['created'] <=> $a['created']); out(200, ['links' => $o, 'days' => CC_HANDIN_DAYS]); }
if ($action === 'open') {
    $title = hiName((string) ($body['title'] ?? ''), 100); if (mb_strlen($title) < 2) out(400, ['error' => 'title']);
    if (count((array) glob($ddir . '/*.json')) >= 300) out(429, ['error' => 'full']);
    if (!is_dir($ddir)) { ccEnsureData(); if (!@mkdir($ddir, 0700, true) && !is_dir($ddir)) out(500, ['error' => 'save']); }
    $al = 'abcdefghjkmnpqrstuvwxyz23456789'; $code = ''; for ($i = 0; $i < 12; $i++) $code .= $al[random_int(0, strlen($al) - 1)];
    $closes = 0; if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($body['closes'] ?? ''))) { $t = strtotime((string) $body['closes'] . ' 23:59:59 Europe/London'); if ($t && $t > time()) $closes = $t; }
    $j = ['code' => $code, 'dept' => $dept, 'title' => $title, 'group' => hiName((string) ($body['group'] ?? ''), 40), 'by' => $me['email'], 'byName' => (string) (($me['name'] ?? '') ?: $me['email']), 'created' => time(), 'closes' => $closes, 'files' => []];
    if (!hiSave($ddir . '/' . $code . '-' . substr(hash('sha256', ccSiteId() . '|handin|' . $code . '|' . bin2hex(random_bytes(8))), 0, 20) . '.json', $j)) out(500, ['error' => 'save']);
    ccLog($dept, $me, 'made a hand-in link for', $title); out(200, ['link' => $view($j)]);
}
$f = hiFile((string) ($body['code'] ?? '')); $j = $f ? hiRead($f) : null; if (!$j || $j['dept'] !== $dept || !$mine($j)) out(404, ['error' => 'not_found']);
if ($action === 'close') { $j['closed'] = empty($body['reopen']); if (!empty($body['reopen']) && !empty($j['closes']) && (int) $j['closes'] < time()) $j['closes'] = 0; hiSave($f, $j); out(200, ['link' => $view($j)]); }
if ($action === 'del') {
    $dir = substr($f, 0, -5); $id = (string) ($body['file'] ?? '');
    if ($id !== '') { if (!preg_match('/^[a-f0-9]{24}$/', $id)) out(400, ['error' => 'file']); @unlink($dir . '/' . $id . '.bin'); $j['files'] = array_values(array_filter((array) $j['files'], fn($x) => $x['id'] !== $id)); hiSave($f, $j); out(200, ['link' => $view($j)]); }
    foreach ((array) glob($dir . '/*.bin') as $p) @unlink((string) $p); @rmdir($dir); @unlink($f); @unlink($f . '.lock'); ccLog($dept, $me, 'deleted a hand-in link and its files', (string) $j['title']); out(200, ['gone' => true]);
}
out(400, ['error' => 'unknown']);
