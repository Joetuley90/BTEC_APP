<?php
/* Updates: see update-lib.php. The super admin checks and updates from Settings, Site setup. GitHub can call the hook address when a release is pushed. */
declare(strict_types=1);
if (!is_file(__DIR__ . '/config.php')) { http_response_code(500); header('Content-Type: application/json; charset=utf-8'); echo json_encode(['error' => 'config.php is missing.']); exit; }
require __DIR__ . '/config.php';
require __DIR__ . '/auth-lib.php';
require __DIR__ . '/update-lib.php';
header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store'); header('X-Content-Type-Options: nosniff');
function out(int $code, array $data): void { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit; }
$action = (string) ($_GET['a'] ?? '');
/* GitHub calls this when a release is pushed: update.php?a=hook&k=<the key in Settings>&sha=<the commit>. All it can do is make the site fetch its own release */
if ($action === 'hook') { if (!hash_equals(ccUpKey(), (string) ($_GET['k'] ?? ''))) out(403, ['error' => 'key']); $sha = (string) ($_GET['sha'] ?? ''); if ($sha !== '' && !preg_match('/^[0-9a-f]{40}$/', $sha)) out(400, ['error' => 'sha']);
    $s = ccUpState(); if (time() - (int) ($s['hookAt'] ?? 0) < 30) out(429, ['error' => 'wait']); $s['hookAt'] = time(); ccUpSave($s);
    $r = ccUpRun($sha !== '' ? $sha : 'main', 'GitHub'); if (isset($r['error'])) out($r['error'] === 'busy' ? 409 : 500, ['error' => $r['error'], 'detail' => (string) ($r['detail'] ?? '')]); out(200, isset($r['same']) ? ['updated' => false, 'v' => $r['v']] : ['updated' => true, 'v' => $r['v'], 'from' => $r['from'], 'files' => $r['files']]); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(405, ['error' => 'Use POST.']);
ccSession(); $me = ccMe(); if (!$me) out(401, ['error' => 'sign_in']); session_write_close();
if ($me['role'] !== 'superadmin') out(403, ['error' => 'noperm']);
$body = json_decode((string) file_get_contents('php://input', false, null, 0, 20000) ?: '{}', true); if (!is_array($body)) $body = [];
$state = function (array $extra = []): array { $s = ccUpState(); $l = ccUpLocal(); return $extra + ['version' => $l, 'latest' => $s['latest'], 'newer' => is_array($s['latest']) && (int) $s['latest']['v'] > $l['v'], 'checked' => (int) $s['checked'], 'auto' => !empty($s['auto']), 'prev' => ccUpPrev(), 'hold' => (int) ($s['hold'] ?? 0), 'runs' => array_slice((array) $s['runs'], 0, 6), 'repo' => ccUpRepo(), 'hook' => ccSiteUrl() . 'update.php?a=hook&k=' . ccUpKey(), 'can' => class_exists('ZipArchive') && function_exists('curl_init') && is_writable(__DIR__) && is_writable(__DIR__ . '/app.html') && !(defined('UPDATE_OFF') && UPDATE_OFF), 'off' => defined('UPDATE_OFF') && UPDATE_OFF]; };
if ($action === 'state') out(200, $state());
if ($action === 'check') { $c = ccUpCheck(); if (isset($c['error'])) out(502, $c); out(200, $state()); }
if ($action === 'auto') { $s = ccUpState(); $s['auto'] = !empty($body['on']); ccUpSave($s); ccLog($me['dept'], $me, $s['auto'] ? 'switched automatic updates on' : 'switched automatic updates off'); out(200, $state()); }
if ($action === 'run') { $r = ccUpRun('main', (string) (($me['name'] ?? '') ?: $me['email'])); if (isset($r['error'])) out($r['error'] === 'busy' ? 409 : 500, $r + $state()); if (empty($r['same'])) ccLog($me['dept'], $me, 'updated the site to version', (string) $r['v']); ccUpCheck(); out(200, $state(['updated' => empty($r['same']), 'files' => (int) ($r['files'] ?? 0)])); }
if ($action === 'restore') { $r = ccUpRestore((string) (($me['name'] ?? '') ?: $me['email'])); if (isset($r['error'])) out($r['error'] === 'busy' ? 409 : ($r['error'] === 'none' ? 404 : 500), $r + $state()); if (!empty($r['same'])) out(200, $state(['restored' => false])); ccLog($me['dept'], $me, 'went back to version', (string) $r['v']); out(200, $state(['restored' => true, 'files' => (int) ($r['files'] ?? 0), 'source' => (string) ($r['source'] ?? '')])); }
out(404, ['error' => 'Unknown action.']);
