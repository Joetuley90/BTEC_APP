<?php
/*
 * Second Marker inside Crafticulum.
 *
 *   ?a=ask     passes a marking request to Claude using the site's API key, so the key never reaches the browser
 *   ?a=result  keeps which criteria were met for one finished learner. No names, IDs or work are kept, only the unit and the Y or N for each criterion
 *   ?a=stats   the criteria most often not met, for the department's leaders
 *
 * Learner work is not stored on this server. It is passed to Claude and forgotten.
 * Only staff whose department has Second Marker switched on can use any of this.
 */
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
function out(int $code, array $data): void { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit; }
/* the marking page reads an error the way Claude sends one */
function fail(int $code, string $msg): void { out($code, ['error' => ['message' => $msg]]); }
if (!is_file(__DIR__ . '/../config.php')) fail(500, 'Crafticulum isn\'t set up yet.');
require __DIR__ . '/../config.php';
require __DIR__ . '/../auth-lib.php';
@ini_set('memory_limit', '512M');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') fail(405, 'Use POST.');
ccSession();
$me = ccMe();
if (!$me) fail(401, 'You\'ve been signed out. Sign in to Crafticulum again, then come back.');
if (!empty($me['demo'])) fail(403, 'Second Marker is switched off in the demo.');
$acc = ccAccounts(); $set = ccMarker($acc, (string) $me['dept']);
if (!ccMarkerCan($me, $acc)) fail(403, $set['on'] ? 'Second Marker is only switched on for your head of department or subject leader.' : 'Second Marker isn\'t switched on for your department.');
$action = (string) ($_GET['a'] ?? '');
const MARKER_MAX = 30000000;   // a marked PDF can be large
$len = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
$raw = file_get_contents('php://input', false, null, 0, MARKER_MAX + 1);
if ($len > MARKER_MAX || ($raw !== false && strlen($raw) > MARKER_MAX)) fail(413, 'That file is too big to send (over 30 MB). Try a smaller file, or save the PDF at a lower quality.');
if (($raw === false || $raw === '') && $len > 0) fail(413, 'This server refused the file because of its size. Ask your web host to raise post_max_size in PHP to 40M, then try again.');
$body = json_decode($raw ?: '{}', true); unset($raw);
if (!is_array($body)) fail(400, 'Bad request.');
$file = ccDataDir() . '/_marker/' . (preg_replace('/[^a-z0-9-]/', '', (string) $me['dept']) ?: 'main') . '.json';

if ($action === 'result') {
    if (!$set['feedback']) out(200, ['ok' => true, 'kept' => false]);
    $clean = fn($s, int $n) => mb_substr(trim((string) preg_replace('/[\x00-\x1F\x7F<>]+/u', ' ', (string) $s)), 0, $n);
    $id = substr(sha1((string) ($body['id'] ?? '') . '|' . $me['email']), 0, 16); $unit = $clean($body['unit'] ?? '', 160);
    $rows = []; foreach ((array) ($body['rows'] ?? []) as $r) { $c = $clean($r['code'] ?? '', 16); $y = (string) ($r['met'] ?? ''); if ($c !== '' && in_array($y, ['Y', 'N'], true) && count($rows) < 60) $rows[$c] = $y; }
    if ($unit === '' || !$rows || ($body['id'] ?? '') === '') out(200, ['ok' => true, 'kept' => false]);
    if (!is_dir(dirname($file))) @mkdir(dirname($file), 0700, true);
    $fh = @fopen($file, 'c+'); if (!$fh) out(200, ['ok' => true, 'kept' => false]);
    if (flock($fh, LOCK_EX)) {
        $j = json_decode((string) stream_get_contents($fh), true); if (!is_array($j)) $j = [];
        $gk = (string) ($body['gradeKey'] ?? ''); $prev = (array) ($j[$id] ?? []);
        $j[$id] = ['at' => (int) ($prev['at'] ?? time()), 'unit' => $unit, 'subject' => $clean($body['subject'] ?? '', 60), 'level' => $clean($body['level'] ?? '', 30), 'rows' => $rows,
                   'group' => $clean($body['group'] ?? '', 40), 'brief' => preg_replace('/[^A-Za-z0-9_.-]/', '', mb_substr((string) ($body['brief'] ?? ''), 0, 80)), 'assignment' => $clean($body['assignment'] ?? '', 120),
                   'grade' => $clean($body['grade'] ?? '', 40), 'gk' => in_array($gk, ['U', 'L1', 'P', 'M', 'D', 'X'], true) ? $gk : '', 'resub' => !empty($body['resub']), 'by' => (string) ($me['name'] ?? ''), 'byEmail' => (string) $me['email']];
        if (count($j) > 4000) { uasort($j, fn($x, $y) => $y['at'] <=> $x['at']); $j = array_slice($j, 0, 3500, true); }
        ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode($j, JSON_UNESCAPED_UNICODE)); fflush($fh); flock($fh, LOCK_UN);
    }
    fclose($fh);
    out(200, ['ok' => true, 'kept' => true]);
}
if ($action === 'stats' || $action === 'statsclear') {
    if (!$me['isLead']) fail(403, 'Only heads of department and subject leaders can see this.');
    if ($action === 'statsclear') { @unlink($file); out(200, ['units' => [], 'learners' => 0]); }
    $j = is_file($file) ? json_decode((string) @file_get_contents($file), true) : []; if (!is_array($j)) $j = [];
    $since = time() - 86400 * max(7, min(730, (int) ($body['days'] ?? 365))); $units = []; $n = 0;
    foreach ($j as $e) { if (($e['at'] ?? 0) < $since) continue; $n++; $u = (string) $e['unit'];
        if (!isset($units[$u])) $units[$u] = ['unit' => $u, 'subject' => (string) ($e['subject'] ?? ''), 'level' => (string) ($e['level'] ?? ''), 'learners' => 0, 'crit' => []];
        $units[$u]['learners']++;
        foreach ((array) $e['rows'] as $c => $y) { $units[$u]['crit'][$c] = $units[$u]['crit'][$c] ?? ['code' => (string) $c, 'n' => 0, 'no' => 0]; $units[$u]['crit'][$c]['n']++; if ($y === 'N') $units[$u]['crit'][$c]['no']++; } }
    foreach ($units as &$u) { $u['crit'] = array_values($u['crit']); usort($u['crit'], fn($x, $y) => [$y['no'] / max(1, $y['n']), $y['no']] <=> [$x['no'] / max(1, $x['n']), $x['no']]); } unset($u);
    $units = array_values($units); usort($units, fn($x, $y) => $y['learners'] <=> $x['learners']);
    out(200, ['units' => $units, 'learners' => $n]);
}
/* ---------- the marking tracker and the IV plan. Counts only: no learner names, IDs or work are kept ---------- */
$yearStart = function (): int { $y = (int) date('Y'); if ((int) date('n') < 8) $y--; return (int) mktime(0, 0, 0, 8, 1, $y); };
$readJson = function (string $f): array { $j = is_file($f) ? json_decode((string) @file_get_contents($f), true) : []; return is_array($j) ? $j : []; };
$writeJson = function (string $f, callable $change): bool { if (!is_dir(dirname($f))) @mkdir(dirname($f), 0700, true); $fh = @fopen($f, 'c+'); if (!$fh) return false; $ok = false;
    if (flock($fh, LOCK_EX)) { $j = json_decode((string) stream_get_contents($fh), true); if (!is_array($j)) $j = []; $j = $change($j); ftruncate($fh, 0); rewind($fh); $ok = fwrite($fh, json_encode($j, JSON_UNESCAPED_UNICODE)) !== false; fflush($fh); flock($fh, LOCK_UN); }
    fclose($fh); return $ok; };
$ivFile = preg_replace('/\.json$/', '-iv.json', $file); $trackFile = preg_replace('/\.json$/', '-track.json', $file);
$cleanS = fn($s, int $n) => mb_substr(trim((string) preg_replace('/[\x00-\x1F\x7F<>]+/u', ' ', (string) $s)), 0, $n);
if ($action === 'iv') {   // one piece of marked work was sampled by an internal verifier
    if (!$me['isLead'] || !$set['iv']) fail(403, 'IV sampling is for heads of department and subject leaders.');
    $unit = $cleanS($body['unit'] ?? '', 160); if ($unit === '' || ($body['id'] ?? '') === '') out(200, ['ok' => true, 'kept' => false]);
    $id = substr(sha1((string) $body['id'] . '|' . $me['email']), 0, 16); $gk = fn($k) => in_array((string) ($body[$k] ?? ''), ['U', 'L1', 'P', 'M', 'D', 'X'], true) ? (string) $body[$k] : '';
    $ok = $writeJson($ivFile, function ($j) use ($id, $unit, $body, $cleanS, $gk, $me) {
        $prev = (array) ($j[$id] ?? []);
        $j[$id] = ['at' => (int) ($prev['at'] ?? time()), 'unit' => $unit, 'subject' => $cleanS($body['subject'] ?? '', 60), 'level' => $cleanS($body['level'] ?? '', 30), 'group' => $cleanS($body['group'] ?? '', 40),
                   'brief' => preg_replace('/[^A-Za-z0-9_.-]/', '', mb_substr((string) ($body['brief'] ?? ''), 0, 80)), 'assignment' => $cleanS($body['assignment'] ?? '', 120), 'assessor' => $cleanS($body['assessor'] ?? '', 80),
                   'asrGrade' => $gk('asrGrade'), 'ivGrade' => $gk('ivGrade'), 'agree' => max(0, min(99, (int) ($body['agree'] ?? 0))), 'total' => max(0, min(99, (int) ($body['total'] ?? 0))), 'by' => (string) ($me['name'] ?? '')];
        if (count($j) > 3000) { uasort($j, fn($x, $y) => $y['at'] <=> $x['at']); $j = array_slice($j, 0, 2500, true); }
        return $j; });
    out(200, ['ok' => true, 'kept' => $ok]);
}
if ($action === 'ivtarget') {   // the share of marked work the department aims to sample
    if (!$me['isLead']) fail(403, 'Only heads of department and subject leaders can change this.');
    $acc['depts'][(string) $me['dept']]['marker']['ivTarget'] = max(0, min(100, (int) ($body['target'] ?? 0)));
    if (!ccSaveAccounts($acc)) fail(500, 'Could not save that.');
    $set = ccMarker($acc, (string) $me['dept']); $action = 'ivplan';
}
if ($action === 'trackset') {   // how many learners handed work in: typed by staff, as work can be marked outside Second Marker
    $key = $cleanS($body['group'] ?? '', 40) . '|' . $cleanS($body['key'] ?? '', 200);
    $writeJson($trackFile, function ($j) use ($key, $body) { $o = (array) ($j[$key] ?? []);
        foreach (['in', 'size', 'marked', 'iv'] as $k) if (array_key_exists($k, $body)) { if ($body[$k] === '' || $body[$k] === null) unset($o[$k]); else $o[$k] = max(0, min(999, (int) $body[$k])); }
        if ($o) $j[$key] = $o; else unset($j[$key]); return $j; });
    $action = 'tracker';
}
if ($action === 'tracker' || $action === 'ivplan') {
    if ($action === 'ivplan' && !$me['isLead']) fail(403, 'Only heads of department and subject leaders can see this.');
    $since = $yearStart(); $marks = array_filter($readJson($file), fn($e) => ($e['at'] ?? 0) >= $since); $ivs = array_filter($readJson($ivFile), fn($e) => ($e['at'] ?? 0) >= $since);
    if ($action === 'tracker') {
        $cells = [];
        $cell = function (array $e) use (&$cells): string { $k = ($e['group'] ?? '') . '|' . (($e['brief'] ?? '') !== '' ? 'b:' . $e['brief'] : 'u:' . ($e['unit'] ?? '') . '|' . ($e['assignment'] ?? ''));
            if (!isset($cells[$k])) $cells[$k] = ['group' => (string) ($e['group'] ?? ''), 'brief' => (string) ($e['brief'] ?? ''), 'unit' => (string) ($e['unit'] ?? ''), 'assignment' => (string) ($e['assignment'] ?? ''), 'marked' => 0, 'notYet' => 0, 'resubs' => 0, 'resubMet' => 0, 'iv' => 0, 'last' => 0, 'grades' => []];
            return $k; };
        foreach ($marks as $e) { $k = $cell($e); $ny = in_array('N', (array) ($e['rows'] ?? []), true);
            if (!empty($e['resub'])) { $cells[$k]['resubs']++; if (!$ny) $cells[$k]['resubMet']++; } else { $cells[$k]['marked']++; if ($ny) $cells[$k]['notYet']++; }
            $g = (string) ($e['gk'] ?? ''); if ($g !== '') $cells[$k]['grades'][$g] = ($cells[$k]['grades'][$g] ?? 0) + 1; $cells[$k]['last'] = max($cells[$k]['last'], (int) $e['at']); }
        foreach ($ivs as $e) { $k = $cell($e); $cells[$k]['iv']++; }
        $manual = $readJson($trackFile); $rows = [];
        foreach ($cells as $k => $c) { $tk = ($c['brief'] !== '' ? 'b:' . $c['brief'] : 'u:' . $c['unit'] . '|' . $c['assignment']); $m = (array) ($manual[$c['group'] . '|' . $tk] ?? []); $rows[] = $c + ['key' => $tk, 'manual' => (object) $m]; }
        out(200, ['rows' => $rows, 'manualBy' => (object) $manual, 'since' => $since]);
    }
    $norm = fn($s) => mb_strtolower(trim((string) preg_replace('/\s+/u', ' ', (string) $s)));
    $people = []; $units = []; $bands = ['U' => [0, 0], 'L1' => [0, 0], 'P' => [0, 0], 'M' => [0, 0], 'D' => [0, 0]];
    foreach ($marks as $e) { if (!empty($e['resub'])) continue; $n = $norm($e['by'] ?? ''); if ($n === '') $n = '?';
        if (!isset($people[$n])) $people[$n] = ['name' => (string) (($e['by'] ?? '') ?: 'Unknown'), 'marked' => 0, 'sampled' => 0, 'agree' => 0, 'total' => 0, 'changed' => 0];
        $people[$n]['marked']++; $u = (string) $e['unit']; if (!isset($units[$u])) $units[$u] = ['unit' => $u, 'marked' => 0, 'sampled' => 0]; $units[$u]['marked']++;
        $g = (string) ($e['gk'] ?? ''); if (isset($bands[$g])) $bands[$g][0]++; }
    $log = [];
    foreach ($ivs as $e) { $n = $norm($e['assessor'] ?? ''); if ($n === '') $n = '?';
        if (!isset($people[$n])) $people[$n] = ['name' => (string) (($e['assessor'] ?? '') ?: 'Unknown'), 'marked' => 0, 'sampled' => 0, 'agree' => 0, 'total' => 0, 'changed' => 0];
        $people[$n]['sampled']++; $people[$n]['agree'] += (int) ($e['agree'] ?? 0); $people[$n]['total'] += (int) ($e['total'] ?? 0);
        if (($e['asrGrade'] ?? '') !== '' && ($e['ivGrade'] ?? '') !== '' && $e['asrGrade'] !== $e['ivGrade']) $people[$n]['changed']++;
        $u = (string) $e['unit']; if (!isset($units[$u])) $units[$u] = ['unit' => $u, 'marked' => 0, 'sampled' => 0]; $units[$u]['sampled']++;
        $g = (string) ($e['asrGrade'] ?? ''); if (isset($bands[$g])) $bands[$g][1]++;
        $log[] = ['at' => (int) $e['at'], 'unit' => $u, 'assignment' => (string) ($e['assignment'] ?? ''), 'group' => (string) ($e['group'] ?? ''), 'assessor' => (string) ($e['assessor'] ?? ''), 'asrGrade' => (string) ($e['asrGrade'] ?? ''), 'ivGrade' => (string) ($e['ivGrade'] ?? ''), 'agree' => (int) ($e['agree'] ?? 0), 'total' => (int) ($e['total'] ?? 0), 'by' => (string) ($e['by'] ?? '')]; }
    usort($log, fn($x, $y) => $y['at'] <=> $x['at']); $people = array_values($people); usort($people, fn($x, $y) => $y['marked'] <=> $x['marked']); $units = array_values($units); usort($units, fn($x, $y) => $y['marked'] <=> $x['marked']);
    $b = []; foreach ($bands as $k => $v) $b[] = ['band' => $k, 'marked' => $v[0], 'sampled' => $v[1]];
    out(200, ['people' => $people, 'units' => $units, 'bands' => $b, 'log' => array_slice($log, 0, 60), 'marked' => array_sum(array_column($people, 'marked')), 'sampled' => count($ivs), 'target' => (int) ($set['ivTarget'] ?? 0), 'since' => $since]);
}
if ($action !== 'ask') fail(404, 'Unknown action.');

// the same hourly limit as the rest of Crafticulum, for each signed-in person
$now = time();
$_SESSION['hits'] = array_values(array_filter($_SESSION['hits'] ?? [], fn($t) => $t > $now - 3600));
if (count($_SESSION['hits']) >= ((int) ccConf('MAX_REQUESTS_PER_HOUR') ?: 60)) fail(429, 'You\'ve reached this hour\'s limit for requests to Claude. Try again in a little while.');
$_SESSION['hits'][] = $now;
session_write_close();
if (($why = ccUsageBlocked($me, 'marker')) !== '') out(402, ['error' => ['message' => $why]] + (array) ($GLOBALS['ccBlock'] ?? []));
$key = (string) ccConf('ANTHROPIC_API_KEY');
if (in_array($key, ['sk-ant-REPLACE-ME', ''], true)) fail(500, 'Claude isn\'t set up yet. The site admin adds the API key in Settings, Site setup.');
$messages = $body['messages'] ?? null; if (!is_array($messages) || !$messages || count($messages) > 6) fail(400, 'Nothing to check.');
$model = $set['model'] !== '' ? $set['model'] : (in_array($body['model'] ?? '', CC_MARKER_MODELS, true) ? (string) $body['model'] : ((string) ccConf('CLAUDE_MODEL') ?: 'claude-sonnet-5-5'));
$system = mb_substr((string) ($body['system'] ?? ''), 0, 60000);
if ($set['guide'] !== '') $system .= "\n\nThis department's own marking guidance, set by its head of department or subject leader. Follow it where it applies:\n" . $set['guide'];
$send = json_encode(['model' => $model, 'max_tokens' => max(256, min(16000, (int) ($body['max_tokens'] ?? 8000))), 'system' => $system, 'messages' => $messages], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
unset($body, $messages);
if ($send === false) fail(400, 'That file couldn\'t be read. Try saving it again as a PDF or Word file.');

@set_time_limit(320); @ignore_user_abort(true);
$ch = curl_init(defined('ANTHROPIC_URL') ? ANTHROPIC_URL : 'https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 290, CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_HTTPHEADER => ['content-type: application/json', 'x-api-key: ' . $key, 'anthropic-version: 2023-06-01'], CURLOPT_POSTFIELDS => $send]);
$res = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); $err = curl_error($ch); curl_close($ch);
if ($res === false) fail(502, stripos($err, 'timed out') !== false ? 'Claude took too long to answer. Try again in a minute.' : 'The server could not reach Claude (' . $err . ').');
$j = json_decode((string) $res, true);
if ($code !== 200 || !is_array($j)) {
    $said = is_array($j) ? trim((string) ($j['error']['message'] ?? '')) : ''; $low = strtolower($said);
    if ($code === 401) $why = 'Claude did not accept the site\'s API key. Tell your site admin.';
    elseif (strpos($low, 'credit balance') !== false || strpos($low, 'billing') !== false) $why = 'The site\'s Claude account has run out of credit. Tell your site admin.';
    elseif ($code === 429 || $code === 529 || $code === 503 || strpos($low, 'overloaded') !== false) $why = 'Claude is very busy right now. Try again in a minute.';
    elseif ($code === 413 || strpos($low, 'too long') !== false || strpos($low, 'too large') !== false) $why = 'That was too much for Claude in one go. Try a shorter file.';
    elseif ($code === 404 || strpos($low, 'model') !== false) $why = 'Claude does not know the model "' . $model . '". Your head of department can pick another in Settings, Second Marker.';
    else $why = 'Claude returned an error (' . $code . ').';
    fail(502, $why . ($said !== '' ? ' Claude said: ' . mb_substr($said, 0, 240) : ''));
}
ccUsageAdd($me, (int) ($j['usage']['input_tokens'] ?? 0) + (int) ($j['usage']['cache_creation_input_tokens'] ?? 0) + (int) ($j['usage']['cache_read_input_tokens'] ?? 0), (int) ($j['usage']['output_tokens'] ?? 0), $model, 'marker');
out(200, ['content' => $j['content'] ?? [], 'model' => $model, 'stop_reason' => $j['stop_reason'] ?? null]);
