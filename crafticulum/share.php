<?php
/*
 * Student links for the Crafticulum.
 *
 * Teachers (signed in with the password in config.php) publish a lesson page,
 * a unit page or an assessment, and get a link to send to students by email or
 * Discord. Students open the link without signing in.
 *
 * Assessments are marked here on the server, so the correct answers are never
 * sent to a student's browser before they hand in. Each answer is kept with the
 * name the student typed, until the teacher clears the results or stops sharing.
 *
 * Live lessons: the teacher's board sends which slide, task and timer is showing,
 * and students' pages follow along. Discord: links are posted to channel webhooks
 * that only the server knows.
 *
 * Everything lives in data/share/ next to this file, which the .htaccess in
 * data/ blocks from the web.
 */
declare(strict_types=1);
if (!is_file(__DIR__ . '/config.php')) {
    http_response_code(500); header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'config.php is missing. Copy config.example.php to config.php and add your API key and password.']); exit;
}
require __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

function out(int $code, array $data): void { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit; }
function clean(string $s, int $max): string { $s = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $s) ?? ''; $s = trim(preg_replace('/\s+/u', ' ', $s) ?? ''); return mb_substr($s, 0, $max); }

$action = $_GET['a'] ?? '';
$teacherActions = ['publish', 'unpublish', 'results', 'clear', 'settings', 'list', 'upfile', 'delfiles', 'live', 'hookget', 'hookadd', 'hookdel', 'discord', 'progget', 'progclear', 'upres', 'delres'];
$isTeacher = false;
$me = null;
if (in_array($action, $teacherActions, true)) {
    require __DIR__ . '/auth-lib.php';
    ccSession();
    $me = ccMe();
    if (!$me) out(401, ['error' => 'sign_in']);
    if (!empty($me['demo']) && in_array($action, ['publish', 'upfile', 'hookadd', 'discord', 'upres', 'live'], true)) out(403, ['error' => 'Sharing with students is switched off in the demo.']);
    session_write_close();
    $isTeacher = true;
}

$base = __DIR__ . '/data';
$dir = $base . '/share';
if (!is_dir($dir) && !@mkdir($dir, 0700, true)) out(500, ['error' => 'The server could not create data/share. Make the data folder writable.']);
if (!is_file($base . '/.htaccess')) {
    @file_put_contents($base . '/.htaccess', "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n");
    @file_put_contents($base . '/index.html', '');
}

function idOk(string $id): bool { return (bool) preg_match('/^[A-Za-z0-9]{10,24}$/', $id); }
function fileOf(string $id): string { global $dir; return $dir . '/' . $id . '.json'; }
function resOf(string $id): string { global $dir; return $dir . '/' . $id . '.results.json'; }
function readJson(string $f): ?array { if (!is_file($f)) return null; $j = json_decode((string) @file_get_contents($f), true); return is_array($j) ? $j : null; }
function writeJson(string $f, array $data): bool {
    $tmp = $f . '.' . bin2hex(random_bytes(4)) . '.tmp';
    $ok = @file_put_contents($tmp, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
    if ($ok === false || !@rename($tmp, $f)) { @unlink($tmp); return false; }
    return true;
}
function newId(): string { $a = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789'; $s = ''; for ($i = 0; $i < 12; $i++) $s .= $a[random_int(0, strlen($a) - 1)]; return $s; }

/* ---------- lesson downloads: the PowerPoint, task list and worksheets students can save ---------- */
$fdir = $dir . '/files';
const SLOTS = ['pptx' => 'pptx', 'tasks' => 'docx', 'sheet' => 'docx', 'support' => 'docx'];
const MIME = ['pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$key = (string) ($_GET['k'] ?? '');
$slot = (string) ($_GET['f'] ?? '');
if ($action === 'upfile') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !idOk($key) || !isset(SLOTS[$slot])) out(400, ['error' => 'Bad request.']);
    if (!is_dir($fdir) && !@mkdir($fdir, 0700, true)) out(500, ['error' => 'Could not create data/share/files.']);
    $raw = file_get_contents('php://input', false, null, 0, 20000001);
    if ($raw === false || $raw === '' || strlen($raw) > 20000000) out(413, ['error' => 'File too large.']);
    if (substr($raw, 0, 2) !== 'PK') out(400, ['error' => 'Not an Office file.']);
    $name = clean((string) ($_GET['name'] ?? ''), 120); $name = preg_replace('/[^A-Za-z0-9 ._()-]+/', '', $name) ?: ($slot . '.' . SLOTS[$slot]);
    $f = $fdir . '/' . $key . '.' . $slot . '.' . SLOTS[$slot];
    if (@file_put_contents($f, $raw, LOCK_EX) === false) out(500, ['error' => 'Could not save the file.']);
    $man = readJson($fdir . '/' . $key . '.json') ?? [];
    $man[$slot] = ['name' => $name, 'size' => strlen($raw), 'at' => time()];
    writeJson($fdir . '/' . $key . '.json', $man);
    out(200, ['ok' => true]);
}
if ($action === 'delfiles') {
    if (!idOk($key)) out(400, ['error' => 'Bad request.']);
    foreach (glob($fdir . '/' . $key . '.*') ?: [] as $f) @unlink($f);
    out(200, ['ok' => true]);
}
if ($action === 'file') {
    if (!idOk($key) || !isset(SLOTS[$slot])) { http_response_code(404); exit; }
    $f = $fdir . '/' . $key . '.' . $slot . '.' . SLOTS[$slot];
    $man = readJson($fdir . '/' . $key . '.json') ?? [];
    if (!is_file($f)) { header('Content-Type: text/plain; charset=utf-8'); http_response_code(404); echo 'This file is no longer shared. Ask your teacher.'; exit; }
    $name = (string) ($man[$slot]['name'] ?? ($slot . '.' . SLOTS[$slot]));
    header('Content-Type: ' . MIME[SLOTS[$slot]]);
    header('Content-Length: ' . filesize($f));
    header('Content-Disposition: attachment; filename="' . str_replace('"', '', $name) . '"; filename*=UTF-8\'\'' . rawurlencode($name));
    header('Cache-Control: private, max-age=300');
    readfile($f); exit;
}

/* ---------- lesson resources: files a teacher attaches (PDFs, images, video, Office files) ---------- */
$rdir = $dir . '/res';
const RES_TYPES = ['pdf' => 'application/pdf', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'webp' => 'image/webp',
    'mp4' => 'video/mp4', 'webm' => 'video/webm', 'mp3' => 'audio/mpeg', 'txt' => 'text/plain; charset=utf-8',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
const RES_INLINE = ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'mp4', 'webm', 'mp3', 'txt'];
$rf = (string) ($_GET['f'] ?? '');
$rext = strtolower((string) ($_GET['x'] ?? ''));
if ($action === 'upres') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !idOk($key) || !preg_match('/^[A-Za-z0-9]{6,16}$/', $rf) || !isset(RES_TYPES[$rext])) out(400, ['error' => 'Bad request.']);
    if (!is_dir($rdir) && !@mkdir($rdir, 0700, true)) out(500, ['error' => 'Could not create data/share/res.']);
    $raw = file_get_contents('php://input', false, null, 0, 30000001);
    if ($raw === false || $raw === '') out(413, ['error' => 'too_large']);
    if (strlen($raw) > 30000000) out(413, ['error' => 'too_large']);
    if (@file_put_contents($rdir . '/' . $key . '.' . $rf . '.' . $rext, $raw, LOCK_EX) === false) out(500, ['error' => 'Could not save the file.']);
    out(200, ['ok' => true, 'size' => strlen($raw)]);
}
if ($action === 'delres') {
    if (!idOk($key) || !preg_match('/^[A-Za-z0-9]{6,16}$/', $rf)) out(400, ['error' => 'Bad request.']);
    foreach (glob($rdir . '/' . $key . '.' . $rf . '.*') ?: [] as $f) @unlink($f);
    out(200, ['ok' => true]);
}
if ($action === 'res') {
    if (!idOk($key) || !preg_match('/^[A-Za-z0-9]{6,16}$/', $rf)) { http_response_code(404); exit; }
    $m = glob($rdir . '/' . $key . '.' . $rf . '.*') ?: [];
    if (!$m) { header('Content-Type: text/plain; charset=utf-8'); http_response_code(404); echo 'This file is no longer shared. Ask your teacher.'; exit; }
    $f = $m[0]; $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION)); if (!isset(RES_TYPES[$ext])) { http_response_code(404); exit; }
    $name = clean((string) ($_GET['n'] ?? ''), 120); $name = preg_replace('/[^A-Za-z0-9 ._()-]+/', '', $name) ?: ('resource.' . $ext);
    $size = filesize($f);
    header('Content-Type: ' . RES_TYPES[$ext]);
    header('Content-Disposition: ' . (in_array($ext, RES_INLINE, true) ? 'inline' : 'attachment') . '; filename="' . str_replace('"', '', $name) . '"');
    header('Cache-Control: private, max-age=3600');
    header('Accept-Ranges: bytes');
    if (isset($_SERVER['HTTP_RANGE']) && preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $mm)) {
        $start = $mm[1] === '' ? max(0, $size - (int) $mm[2]) : (int) $mm[1];
        $end = $mm[1] !== '' && $mm[2] !== '' ? min((int) $mm[2], $size - 1) : $size - 1;
        if ($start > $end || $start >= $size) { http_response_code(416); header("Content-Range: bytes */$size"); exit; }
        http_response_code(206); header("Content-Range: bytes $start-$end/$size"); header('Content-Length: ' . ($end - $start + 1));
        $fh = fopen($f, 'rb'); fseek($fh, $start); $left = $end - $start + 1;
        while ($left > 0 && !feof($fh)) { $chunk = fread($fh, (int) min(65536, $left)); echo $chunk; $left -= strlen($chunk); }
        fclose($fh); exit;
    }
    header('Content-Length: ' . $size); readfile($f); exit;
}

$body = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input', false, null, 0, 3000001);
    if ($raw === false || strlen($raw) > 3000000) out(413, ['error' => 'Too large.']);
    $body = json_decode($raw ?: '{}', true);
    if (!is_array($body)) out(400, ['error' => 'Bad request.']);
}
$id = (string) ($_GET['id'] ?? ($body['id'] ?? ''));

/* ---------- live lessons: the board says what's showing, students' pages follow ---------- */
function liveOf(string $k): string { global $dir; return $dir . '/live/' . $k . '.json'; }
if ($action === 'live') {
    $k = (string) ($body['key'] ?? '');
    if (!idOk($k)) out(400, ['error' => 'Bad key.']);
    if (!is_dir($dir . '/live') && !@mkdir($dir . '/live', 0700, true)) out(500, ['error' => 'Could not create data/share/live.']);
    if (empty($body['on'])) { @unlink(liveOf($k)); out(200, ['ok' => true]); }
    $st = ['on' => true, 'slide' => clean((string) ($body['slide'] ?? ''), 20), 'task' => max(0, (int) ($body['task'] ?? 0)),
           'taskTitle' => clean((string) ($body['taskTitle'] ?? ''), 160), 'left' => isset($body['left']) && is_numeric($body['left']) ? max(0, (int) $body['left']) : null,
           'running' => !empty($body['running']), 'at' => microtime(true)];
    if (!writeJson(liveOf($k), $st)) out(500, ['error' => 'Could not save.']);
    out(200, ['ok' => true]);
}
if ($action === 'liveget') {
    $keys = array_slice(array_values(array_filter(explode(',', (string) ($_GET['k'] ?? '')), 'idOk')), 0, 80);
    foreach ($keys as $k) {
        $s = readJson(liveOf($k));
        if ($s && !empty($s['on']) && microtime(true) - (float) $s['at'] < 900) {
            $s['key'] = $k; $s['age'] = round(microtime(true) - (float) $s['at'], 2); unset($s['at']); out(200, $s);
        }
    }
    out(200, ['on' => false]);
}

/* ---------- class progress in a live lesson: ticks and help requests from students ---------- */
function progOf(string $k): string { global $dir; return $dir . '/live/' . $k . '.prog.json'; }
if ($action === 'prog') {
    $k = (string) ($body['key'] ?? ''); $wid = (string) ($body['wid'] ?? '');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !idOk($k) || !preg_match('/^[A-Za-z0-9]{8,24}$/', $wid)) out(400, ['error' => 'Bad request.']);
    $lv = readJson(liveOf($k));
    if (!$lv || empty($lv['on']) || microtime(true) - (float) $lv['at'] > 900) out(403, ['error' => 'not_live']);
    $who = clean((string) ($body['who'] ?? ''), 30); if ($who === '') out(400, ['error' => 'name']);
    $steps = array_slice(array_map(fn($v) => max(0, min(50, (int) $v)), is_array($body['steps'] ?? null) ? array_values($body['steps']) : []), 0, 20);
    $fin = array_slice(array_values(array_unique(array_map(fn($v) => max(0, min(19, (int) $v)), is_array($body['fin'] ?? null) ? $body['fin'] : []))), 0, 20);
    $fh = @fopen(progOf($k), 'c+'); if (!$fh) out(500, ['error' => 'Could not save.']);
    flock($fh, LOCK_EX);
    $all = json_decode(stream_get_contents($fh) ?: '{}', true); if (!is_array($all)) $all = [];
    $old = $all[$wid] ?? [];
    $help = !empty($body['help']);
    $all[$wid] = ['who' => $who, 'steps' => $steps, 'fin' => $fin, 'help' => $help, 'helpAt' => $help ? ($old['helpAt'] ?? time()) : null, 'at' => time()];
    if (count($all) > 80) { uasort($all, fn($a, $b) => $b['at'] <=> $a['at']); $all = array_slice($all, 0, 80, true); }
    ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode($all, JSON_UNESCAPED_UNICODE)); fflush($fh); flock($fh, LOCK_UN); fclose($fh);
    out(200, ['ok' => true]);
}
if ($action === 'progget') {
    $k = (string) ($body['key'] ?? ''); if (!idOk($k)) out(400, ['error' => 'Bad key.']);
    $all = readJson(progOf($k)) ?? []; $now = time(); $list = [];
    foreach ($all as $wid => $p) if ($now - (int) ($p['at'] ?? 0) < 7200) $list[] = ['wid' => $wid] + $p;
    out(200, ['list' => $list, 'now' => $now]);
}
if ($action === 'progclear') {
    $k = (string) ($body['key'] ?? ''); if (!idOk($k)) out(400, ['error' => 'Bad key.']);
    $wid = (string) ($body['wid'] ?? '');
    if ($wid === '') { @unlink(progOf($k)); out(200, ['ok' => true]); }
    $fh = @fopen(progOf($k), 'c+'); if (!$fh) out(200, ['ok' => true]);
    flock($fh, LOCK_EX); $all = json_decode(stream_get_contents($fh) ?: '{}', true); if (!is_array($all)) $all = [];
    if (isset($all[$wid])) { $all[$wid]['help'] = false; $all[$wid]['helpAt'] = null; }
    ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode($all, JSON_UNESCAPED_UNICODE)); fflush($fh); flock($fh, LOCK_UN); fclose($fh);
    out(200, ['ok' => true]);
}

/* ---------- Discord: channel webhooks kept on the server, links posted from the share window ---------- */
function hooksFile(): string { global $dir, $me; $d = $me ? (string) $me['dept'] : 'main'; return $dir . ($d === 'main' ? '/discord.json' : '/discord-' . preg_replace('/[^a-z0-9-]/', '', $d) . '.json'); }
function hooksList(): array { $h = readJson(hooksFile()) ?? []; return array_values(array_filter($h, fn($x) => is_array($x) && isset($x['name'], $x['url']))); }
function hooksPublic(array $h): array { return array_map(fn($x) => ['name' => $x['name'], 'tail' => '...' . substr($x['url'], -4)], $h); }
if ($action === 'hookget') out(200, ['hooks' => hooksPublic(hooksList())]);
if (($action === 'hookadd' || $action === 'hookdel') && !$me['canGen']) out(403, ['error' => 'noperm']);
if ($action === 'hookadd') {
    $name = clean((string) ($body['name'] ?? ''), 40); $url = trim((string) ($body['url'] ?? ''));
    if ($name === '') out(400, ['error' => 'name']);
    if (!preg_match('~^https://(?:canary\.|ptb\.)?(?:discord|discordapp)\.com/api/webhooks/\d{5,25}/[A-Za-z0-9_-]{20,120}$~', $url)) out(400, ['error' => 'url']);
    $h = hooksList(); if (count($h) >= 20) out(400, ['error' => 'full']);
    $h[] = ['name' => $name, 'url' => $url];
    if (!writeJson(hooksFile(), $h)) out(500, ['error' => 'Could not save.']);
    out(200, ['hooks' => hooksPublic($h)]);
}
if ($action === 'hookdel') {
    $h = hooksList(); $i = (int) ($body['i'] ?? -1);
    if (!isset($h[$i])) out(404, ['error' => 'not_found']);
    array_splice($h, $i, 1); writeJson(hooksFile(), $h);
    out(200, ['hooks' => hooksPublic($h)]);
}
if ($action === 'discord') {
    $h = hooksList(); $i = (int) ($body['i'] ?? -1);
    if (!isset($h[$i])) out(404, ['error' => 'not_found']);
    if (!function_exists('curl_init')) out(500, ['error' => 'This server needs the PHP curl extension to post to Discord.']);
    $content = mb_substr(trim((string) ($body['content'] ?? '')), 0, 1900);
    if ($content === '') out(400, ['error' => 'Bad request.']);
    $payload = json_encode(['content' => $content, 'allowed_mentions' => ['parse' => !empty($body['everyone']) ? ['everyone'] : []]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $fields = ['payload_json' => $payload];
    $png = base64_decode((string) ($body['png'] ?? ''), true);
    $tmp = null;
    if ($png !== false && $png !== '' && strlen($png) < 2000000 && substr($png, 0, 8) === "\x89PNG\r\n\x1a\n") {
        $tmp = tempnam(sys_get_temp_dir(), 'ccqr'); file_put_contents($tmp, $png);
        $fields['files[0]'] = new CURLFile($tmp, 'image/png', 'qr-code.png');
    }
    $ch = curl_init((defined('DISCORD_TEST_URL') ? DISCORD_TEST_URL : $h[$i]['url']) . '?wait=true');
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $fields, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20]);
    $res = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    if ($tmp) @unlink($tmp);
    if ($code < 200 || $code >= 300) out(502, ['error' => $code === 404 ? 'gone' : 'discord', 'status' => $code]);
    out(200, ['ok' => true]);
}

/* ---------- public: open a link ---------- */
if ($action === 'get') {
    if (!idOk($id)) out(404, ['error' => 'not_found']);
    $s = readJson(fileOf($id));
    if (!$s) out(404, ['error' => 'not_found']);
    $d = $s['data'];
    if ($s['kind'] === 'test') {
        // the answers stay on the server until the student hands in
        $d['questions'] = array_map(fn($q) => ['q' => $q['q'], 'options' => $q['options']], $d['questions'] ?? []);
        $d['open'] = !empty($s['open']);
    }
    // the page carries the name, logo and colour of the institution whose teacher shared it
    if (!function_exists('ccSite')) require __DIR__ . '/auth-lib.php';
    $site = null; if (function_exists('ccSite')) { $acc = ccAccounts(); $sd = (string) ($s['dept'] ?? ''); $site = ccSite($acc, $sd !== '' && isset($acc['depts'][$sd]) ? ccCollegeOf($acc, $sd) : null); unset($site['conf']); }
    out(200, ['kind' => $s['kind'], 'data' => $d, 'updatedAt' => $s['updatedAt'] ?? 0, 'site' => $site]);
}

/* ---------- public: hand in an assessment ---------- */
if ($action === 'submit') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !idOk($id)) out(400, ['error' => 'Bad request.']);
    $s = readJson(fileOf($id));
    if (!$s || $s['kind'] !== 'test') out(404, ['error' => 'not_found']);
    if (empty($s['open'])) out(403, ['error' => 'closed']);
    $name = clean((string) ($body['name'] ?? ''), 60);
    if ($name === '') out(400, ['error' => 'name']);
    $qs = $s['data']['questions'] ?? [];
    $ans = is_array($body['answers'] ?? null) ? array_values($body['answers']) : [];
    $marks = []; $score = 0;
    foreach ($qs as $i => $q) {
        $a = isset($ans[$i]) && is_int($ans[$i]) && $ans[$i] >= 0 && $ans[$i] < count($q['options']) ? $ans[$i] : -1;
        $ok = $a === (int) $q['answer'];
        if ($ok) $score++;
        $marks[] = $a;
    }
    $resFile = resOf($id);
    $fh = @fopen($resFile, 'c+');
    if (!$fh) out(500, ['error' => 'Could not save. Check the data folder is writable.']);
    flock($fh, LOCK_EX);
    $res = json_decode(stream_get_contents($fh) ?: '[]', true); if (!is_array($res)) $res = [];
    if (count($res) >= 2000) { flock($fh, LOCK_UN); fclose($fh); out(429, ['error' => 'full']); }
    $res[] = ['name' => $name, 'answers' => $marks, 'score' => $score, 'of' => count($qs), 'at' => time()];
    ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode($res, JSON_UNESCAPED_UNICODE)); fflush($fh); flock($fh, LOCK_UN); fclose($fh);
    $review = [];
    foreach ($qs as $i => $q) {
        $r = ['correct' => $marks[$i] === (int) $q['answer']];
        if (!empty($s['showAnswers'])) { $r['answer'] = (int) $q['answer']; $r['why'] = (string) ($q['why'] ?? ''); }
        $review[] = $r;
    }
    out(200, ['score' => $score, 'of' => count($qs), 'review' => $review, 'showAnswers' => !empty($s['showAnswers'])]);
}

/* ---------- teacher: publish or update a link ---------- */
if ($action === 'publish') {
    $kind = (string) ($body['kind'] ?? '');
    if (!in_array($kind, ['lesson', 'unit', 'test', 'cover', 'class'], true) || !is_array($body['data'] ?? null)) out(400, ['error' => 'Bad request.']);
    if ($id !== '' && !idOk($id)) out(400, ['error' => 'Bad id.']);
    $old = $id !== '' ? readJson(fileOf($id)) : null;
    if (!$old) $id = newId();
    $s = ['kind' => $kind, 'data' => $body['data'], 'updatedAt' => time(), 'createdAt' => $old['createdAt'] ?? time(), 'dept' => (string) ($old['dept'] ?? ($me['dept'] ?? '')),
          'open' => array_key_exists('open', $body) ? !empty($body['open']) : ($old['open'] ?? true),
          'showAnswers' => array_key_exists('showAnswers', $body) ? !empty($body['showAnswers']) : ($old['showAnswers'] ?? false)];
    if (!writeJson(fileOf($id), $s)) out(500, ['error' => 'Could not save. Check the data folder is writable.']);
    out(200, ['id' => $id, 'open' => $s['open'], 'showAnswers' => $s['showAnswers']]);
}
if ($action === 'settings') {
    if (!idOk($id)) out(400, ['error' => 'Bad id.']);
    $s = readJson(fileOf($id)); if (!$s) out(404, ['error' => 'not_found']);
    if (array_key_exists('open', $body)) $s['open'] = !empty($body['open']);
    if (array_key_exists('showAnswers', $body)) $s['showAnswers'] = !empty($body['showAnswers']);
    writeJson(fileOf($id), $s);
    out(200, ['open' => $s['open'], 'showAnswers' => $s['showAnswers']]);
}
if ($action === 'unpublish') {
    if (!idOk($id)) out(400, ['error' => 'Bad id.']);
    @unlink(fileOf($id)); @unlink(resOf($id));
    out(200, ['ok' => true]);
}
if ($action === 'results') {
    if (!idOk($id)) out(400, ['error' => 'Bad id.']);
    $s = readJson(fileOf($id)); if (!$s) out(404, ['error' => 'not_found']);
    out(200, ['results' => readJson(resOf($id)) ?? [], 'open' => !empty($s['open']), 'showAnswers' => !empty($s['showAnswers'])]);
}
if ($action === 'clear') {
    if (!idOk($id)) out(400, ['error' => 'Bad id.']);
    @unlink(resOf($id));
    out(200, ['ok' => true]);
}

out(400, ['error' => 'Unknown action.']);
