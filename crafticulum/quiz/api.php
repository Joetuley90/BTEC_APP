<?php
/*
 * Quiz Room API for the Course Creator.
 * Answers are held in a temporary file per room and deleted the moment the teacher ends the quiz.
 * Anything left behind (a quiz never ended) is deleted automatically after MAX_AGE seconds.
 */
declare(strict_types=1);

const DATA_DIR      = __DIR__ . '/data';
const MAX_AGE       = 6 * 3600;   // rooms older than this are deleted on the next request
const MAX_QUESTIONS = 10;
const MAX_RESPONSES = 80;
const MAX_BODY      = 65536;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function out(int $code, array $data): void { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }
function fail(int $code, string $msg): void { out($code, ['error' => $msg]); }
function clean(string $s, int $max): string {
    $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $s) ?? '';
    return mb_substr(trim($s), 0, $max);
}
function room_path(string $room): string { return DATA_DIR . '/' . $room . '.json'; }
function valid_room(string $r): bool { return (bool) preg_match('/^[a-z0-9]{8}$/', $r); }

if (!is_dir(DATA_DIR)) { @mkdir(DATA_DIR, 0700, true); }
if (!is_file(DATA_DIR . '/.htaccess')) { @file_put_contents(DATA_DIR . '/.htaccess', "Require all denied\nDeny from all\n"); }
if (!is_file(DATA_DIR . '/index.html')) { @file_put_contents(DATA_DIR . '/index.html', ''); }

// Housekeeping: delete abandoned rooms.
foreach (glob(DATA_DIR . '/*.json') ?: [] as $f) {
    if (filemtime($f) !== false && time() - (int) filemtime($f) > MAX_AGE) { @unlink($f); }
}

$action = $_GET['a'] ?? '';
$body = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input', false, null, 0, MAX_BODY + 1);
    if ($raw === false || strlen($raw) > MAX_BODY) fail(413, 'Request too large.');
    $body = json_decode($raw, true);
    if (!is_array($body)) fail(400, 'Bad request.');
}

/* Read-modify-write a room file under an exclusive lock. $fn returns [newRoomOrNullToDelete, response]. */
function with_room(string $room, callable $fn) {
    $path = room_path($room);
    $fh = @fopen($path, 'c+');
    if (!$fh) fail(500, 'Storage unavailable.');
    flock($fh, LOCK_EX);
    $raw = stream_get_contents($fh);
    $data = $raw ? json_decode($raw, true) : null;
    [$new, $resp] = $fn(is_array($data) ? $data : null);
    if ($new === null) {
        flock($fh, LOCK_UN); fclose($fh);
        if (!is_array($data) || ($resp['_delete'] ?? false)) { @unlink($path); }
    } else {
        ftruncate($fh, 0); rewind($fh);
        fwrite($fh, json_encode($new, JSON_UNESCAPED_UNICODE));
        fflush($fh); flock($fh, LOCK_UN); fclose($fh);
    }
    unset($resp['_delete']);
    return $resp;
}

switch ($action) {

case 'open': {   // teacher opens (or reopens) a room
    $room = (string) ($body['room'] ?? ''); $secret = (string) ($body['secret'] ?? '');
    if (!valid_room($room) || !preg_match('/^[a-f0-9]{32}$/', $secret)) fail(400, 'Bad room.');
    $quiz = $body['quiz'] ?? null;
    if (!is_array($quiz) || !is_array($quiz['questions'] ?? null)) fail(400, 'No quiz.');
    $qs = [];
    foreach (array_slice($quiz['questions'], 0, MAX_QUESTIONS) as $q) {
        if (!is_array($q) || !is_array($q['options'] ?? null)) continue;
        $opts = array_values(array_map(fn($o) => clean((string) $o, 200), array_slice($q['options'], 0, 4)));
        if (count($opts) < 2) continue;
        $qs[] = ['q' => clean((string) ($q['q'] ?? ''), 400), 'options' => $opts,
                 'answer' => max(0, min(count($opts) - 1, (int) ($q['answer'] ?? 0))), 'why' => clean((string) ($q['why'] ?? ''), 300)];
    }
    if (!$qs) fail(400, 'No questions.');
    $hash = hash('sha256', $secret);
    $resp = with_room($room, function ($d) use ($hash, $qs, $quiz) {
        if ($d && !hash_equals($d['secret'], $hash)) return [$d, ['error' => 'in_use']];
        if ($d) return [$d, ['ok' => true, 'resumed' => true, 'count' => count($d['responses'])]];
        return [['secret' => $hash, 'created' => time(), 'title' => clean((string) ($quiz['title'] ?? 'Exit quiz'), 160),
                 'questions' => $qs, 'responses' => new stdClass()], ['ok' => true, 'resumed' => false, 'count' => 0]];
    });
    if (($resp['error'] ?? '') === 'in_use') fail(409, 'This room is already open somewhere else.');
    out(200, $resp);
}

case 'quiz': {   // student loads questions (no answers sent)
    $room = (string) ($_GET['room'] ?? '');
    if (!valid_room($room) || !is_file(room_path($room))) out(404, ['state' => 'closed']);
    $d = json_decode((string) file_get_contents(room_path($room)), true);
    if (!is_array($d)) out(404, ['state' => 'closed']);
    out(200, ['state' => 'open', 'title' => $d['title'],
              'questions' => array_map(fn($q) => ['q' => $q['q'], 'options' => $q['options']], $d['questions'])]);
}

case 'submit': { // student sends initials + answers, gets their own marking back
    $room = (string) ($body['room'] ?? ''); $token = (string) ($body['token'] ?? '');
    $initials = strtoupper(preg_replace('/[^A-Za-z]/', '', (string) ($body['initials'] ?? '')) ?? '');
    $answers = $body['answers'] ?? null;
    if (!valid_room($room) || !preg_match('/^[a-f0-9]{24}$/', $token)) fail(400, 'Bad request.');
    if (strlen($initials) < 1 || strlen($initials) > 4) fail(400, 'Initials must be 1 to 4 letters.');
    if (!is_array($answers)) fail(400, 'No answers.');
    if (!is_file(room_path($room))) out(404, ['state' => 'closed']);
    $resp = with_room($room, function ($d) use ($token, $initials, $answers) {
        if (!$d) return [null, ['state' => 'closed']];
        $n = count($d['questions']);
        $a = [];
        for ($i = 0; $i < $n; $i++) { $v = $answers[$i] ?? null; $a[] = is_int($v) && $v >= 0 && $v < count($d['questions'][$i]['options']) ? $v : null; }
        $resps = (array) $d['responses'];
        if (!isset($resps[$token]) && count($resps) >= MAX_RESPONSES) return [$d, ['error' => 'full']];
        $resps[$token] = ['initials' => $initials, 'answers' => $a, 'at' => time()];
        $d['responses'] = $resps;
        $results = []; $score = 0;
        foreach ($d['questions'] as $i => $q) { $ok = $a[$i] === $q['answer']; $score += $ok ? 1 : 0; $results[] = ['correct' => $ok, 'answer' => $q['answer'], 'why' => $q['why']]; }
        return [$d, ['ok' => true, 'score' => $score, 'total' => $n, 'results' => $results]];
    });
    if (($resp['error'] ?? '') === 'full') fail(429, 'This quiz is full.');
    if (($resp['state'] ?? '') === 'closed') out(404, $resp);
    out(200, $resp);
}

case 'status': { // teacher's live count (initials only)
    $room = (string) ($body['room'] ?? ''); $secret = (string) ($body['secret'] ?? '');
    if (!valid_room($room) || !is_file(room_path($room))) out(404, ['state' => 'closed']);
    $d = json_decode((string) file_get_contents(room_path($room)), true);
    if (!is_array($d) || !hash_equals($d['secret'], hash('sha256', $secret))) fail(403, 'Not allowed.');
    $list = array_values(array_map(fn($r) => $r['initials'], (array) $d['responses']));
    out(200, ['state' => 'open', 'count' => count($list), 'initials' => $list]);
}

case 'end': {    // teacher ends: returns everything once, then deletes it
    $room = (string) ($body['room'] ?? ''); $secret = (string) ($body['secret'] ?? '');
    if (!valid_room($room) || !is_file(room_path($room))) out(404, ['state' => 'closed']);
    $hash = hash('sha256', $secret);
    $resp = with_room($room, function ($d) use ($hash) {
        if (!$d) return [null, ['state' => 'closed']];
        if (!hash_equals($d['secret'], $hash)) return [$d, ['error' => 'forbidden']];
        return [null, ['_delete' => true, 'ok' => true, 'title' => $d['title'], 'questions' => $d['questions'],
                       'responses' => array_values(array_map(fn($r) => ['initials' => $r['initials'], 'answers' => $r['answers']], (array) $d['responses']))]];
    });
    if (($resp['error'] ?? '') === 'forbidden') fail(403, 'Not allowed.');
    out(200, $resp);
}

default:
    fail(404, 'Unknown action.');
}
