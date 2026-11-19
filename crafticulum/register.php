<?php
/*
 * Official records, looked up from the server so nothing is typed from memory:
 *   - the Ofqual Register of Regulated Qualifications (a qualification's number, title, awarding organisation, level and whether it is still available)
 *   - the institution directory (see inst-lib.php)
 * Only signed-in staff can use it. What the register says is kept for a day so the same search isn't sent twice.
 */
declare(strict_types=1);
if (!is_file(__DIR__ . '/config.php')) { http_response_code(500); header('Content-Type: application/json; charset=utf-8'); echo json_encode(['error' => 'config.php is missing.']); exit; }
require __DIR__ . '/config.php';
require __DIR__ . '/auth-lib.php';
require __DIR__ . '/inst-lib.php';
header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store'); header('X-Content-Type-Options: nosniff');
function out(int $code, array $data): void { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(405, ['error' => 'post']);
ccSession(); $me = ccMe(); if (!$me) out(401, ['error' => 'login']);
$now = time(); $_SESSION['reg'] = array_values(array_filter((array) ($_SESSION['reg'] ?? []), fn($t) => $t > $now - 3600)); if (count($_SESSION['reg']) >= 600) out(429, ['error' => 'slow']); $_SESSION['reg'][] = $now;
session_write_close();
$body = json_decode((string) file_get_contents('php://input', false, null, 0, 20000), true); if (!is_array($body)) $body = [];
$action = (string) ($_GET['a'] ?? ''); ccEnsureData();

if ($action === 'qual') {
    $q = trim((string) preg_replace('/\s+/', ' ', (string) ($body['q'] ?? ''))); if (mb_strlen($q) < 3 || mb_strlen($q) > 120) out(400, ['error' => 'short']);
    $base = defined('OFQUAL_URL') ? OFQUAL_URL : 'https://register-api.ofqual.gov.uk'; $dir = ccDataDir() . '/_register'; if (!is_dir($dir)) @mkdir($dir, 0700, true);
    $isNum = (bool) preg_match('#^\d{3}/?\d{4}/?[\dXx]$#', $q); $cf = $dir . '/q-' . sha1(strtolower($q) . (!empty($body['all']) ? '|all' : '')) . '.json';
    $c = json_decode((string) @file_get_contents($cf), true);
    if (!is_array($c) || empty($body['fresh']) && (int) ($c['at'] ?? 0) < $now - 86400 || !empty($body['fresh'])) {
        $url = $base . '/api/Qualifications' . ($isNum ? '/' . preg_replace('/[^0-9Xx]/', '', $q) : '?title=' . rawurlencode($q) . '&page=1&limit=25');
        $ch = curl_init($url); curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20, CURLOPT_CONNECTTIMEOUT => 8, CURLOPT_HTTPHEADER => ['Accept: application/json'], CURLOPT_USERAGENT => 'Crafticulum qualification check']);
        $res = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
        if ($res === false || ($code !== 200 && $code !== 404)) out(502, ['error' => 'register', 'detail' => $me['role'] === 'superadmin' ? 'The register answered ' . $code . '. Ask your web host whether outgoing connections to register-api.ofqual.gov.uk are allowed.' : '']);
        $j = $code === 404 ? [] : json_decode((string) $res, true); if (!is_array($j)) out(502, ['error' => 'register']);
        $j = array_change_key_case($j, CASE_LOWER); $rows = $isNum ? ($j ? [$j] : []) : (array) ($j['results'] ?? []); $o = [];
        foreach ($rows as $r) { if (!is_array($r)) continue; $r = array_change_key_case($r, CASE_LOWER); $d = fn(string $k): string => substr((string) ($r[$k] ?? ''), 0, 10); if (empty($r['qualificationnumber']) || empty($r['title'])) continue;
            $o[] = ['qn' => (string) $r['qualificationnumber'], 'title' => mb_substr((string) $r['title'], 0, 200), 'status' => (string) ($r['status'] ?? ''), 'org' => trim((string) ($r['organisationname'] ?? '')), 'acr' => trim((string) ($r['organisationacronym'] ?? '')), 'type' => (string) ($r['type'] ?? ''), 'level' => (string) ($r['level'] ?? ''), 'sub' => (string) ($r['sublevel'] ?? ''),
                'grading' => (string) ($r['gradingtype'] ?? ''), 'glh' => (int) ($r['glh'] ?? 0), 'opStart' => $d('operationalstartdate'), 'opEnd' => $d('operationalenddate'), 'certEnd' => $d('certificationenddate'), 'eng' => !empty($r['offeredinengland']), 'ni' => !empty($r['offeredinnorthernireland']),
                'spec' => preg_match('#^https://#', (string) ($r['linktospecification'] ?? '')) ? mb_substr((string) $r['linktospecification'], 0, 300) : '', 'updated' => $d('lastupdateddate')]; }
        $c = ['at' => $now, 'count' => $isNum ? count($o) : (int) ($j['count'] ?? count($o)), 'results' => $o]; @file_put_contents($cf, json_encode($c, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
        if (random_int(1, 40) === 1) foreach ((array) glob($dir . '/q-*.json') as $old) if (@filemtime((string) $old) < $now - 7 * 86400) @unlink((string) $old);
    }
    out(200, $c + ['source' => 'Ofqual Register of Regulated Qualifications', 'where' => 'https://www.gov.uk/find-a-regulated-qualification']);
}

if ($action === 'inst') { $log = ccInstLog(); out(200, ['results' => ccInstSearch((string) ($body['q'] ?? ''), in_array($body['nation'] ?? '', ['eng', 'wal', 'sco', 'ni'], true) ? (string) $body['nation'] : ''), 'ready' => !empty($log['sources']), 'nations' => array_values(array_unique(array_map(fn($s) => (string) $s['nation'], (array) $log['sources'])))]); }

/* the rest is for the super admin: filling and refreshing the directory */
if ($me['role'] !== 'superadmin') out(403, ['error' => 'noperm']);
$state = function (): array { $l = ccInstLog(); $files = []; foreach ((array) glob(ccInstDir() . '/import/*.csv') as $f) if (strpos(basename((string) $f), '_dl-') !== 0) $files[] = basename((string) $f);
    return ['sources' => array_values((array) $l['sources']), 'runs' => array_slice((array) $l['runs'], 0, 12), 'files' => $files, 'presets' => array_map(fn($k) => ['id' => $k] + CC_INST_PRESETS[$k], array_keys(CC_INST_PRESETS))]; };
if ($action === 'inststate') out(200, $state());
if ($action === 'instimport') {
    $pre = CC_INST_PRESETS[(string) ($body['preset'] ?? '')] ?? null; $def = ['id' => (string) ($body['id'] ?? ($body['preset'] ?? '')), 'label' => (string) ($body['label'] ?? ($pre['label'] ?? '')), 'nation' => (string) ($body['nation'] ?? ($pre['nation'] ?? 'eng')),
        'url' => trim((string) ($body['url'] ?? ($pre['url'] ?? ''))), 'file' => (string) ($body['file'] ?? ''), 'map' => is_array($body['map'] ?? null) && array_filter($body['map']) ? array_map('strval', $body['map']) : ($pre['map'] ?? []), 'auto' => !empty($body['auto'])];
    $r = ccInstImport($def, (string) (($me['name'] ?? '') ?: $me['email'])); if (isset($r['error'])) out(400, $r);
    ccLog($me['dept'], $me, 'read ' . $r['rows'] . ' institutions into the directory from', $def['label'] ?: $def['id']); out(200, $r + $state());
}
if ($action === 'instdel') { $id = ccInstSlug((string) ($body['id'] ?? '')); if ($id === '') out(400, ['error' => 'id']); @unlink(ccInstDir() . '/' . $id . '.jsonl'); $l = ccInstLog(); unset($l['sources'][$id]); ccInstLogSave($l); out(200, $state()); }
out(404, ['error' => 'Unknown action.']);
