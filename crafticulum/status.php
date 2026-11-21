<?php
/*
 * Crafticulum: is it working? A public page that checks the site as it loads, and lists anything that has gone wrong lately.
 * The checks say only "working" or "problem", never why. The super admin writes the incident notes in Admin, System.
 */
declare(strict_types=1);
if (!is_file(__DIR__ . '/config.php')) { http_response_code(503); echo 'Not set up yet.'; exit; }
require __DIR__ . '/config.php';
require __DIR__ . '/auth-lib.php';
$action = (string) ($_GET['a'] ?? '');
$h = fn($s) => htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
function stChecks(): array {
    $dir = ccDataDir(); $w = is_dir($dir) && is_writable($dir); $t = $dir . '/_status_probe'; if ($w) { $w = @file_put_contents($t, '1') !== false; @unlink($t); }
    $acc = is_file($dir . '/_accounts.json') ? json_decode((string) @file_get_contents($dir . '/_accounts.json'), true) : null;
    $zs = array_values(array_filter(glob($dir . '/backups/*.zip') ?: [], fn($f) => preg_match('/\/\d{4}-\d{2}-\d{2}\.zip$/', $f))); rsort($zs); $bk = $zs ? time() - (int) @filemtime($zs[0]) < 3 * 86400 : null;
    $out = [['Signing in and the app', is_array($acc)], ['Saving lessons and planning', $w], ['Crafticulum AI', trim((string) ccConf('ANTHROPIC_API_KEY')) !== ''], ['Email', ccMailReady()]];
    if (ccBillingOn()) $out[] = ['Payments', trim((string) ccConf('STRIPE_SECRET_KEY')) !== ''];
    if ($bk !== null) $out[] = ['Backups', $bk];
    return array_map(fn($x) => ['name' => $x[0], 'ok' => (bool) $x[1]], $out);
}
function stIncidents(): array { $l = (array) ((ccAccounts()['site'] ?? [])['incidents'] ?? []); $l = array_values(array_filter($l, 'is_array')); usort($l, fn($x, $y) => ($y['t'] ?? 0) <=> ($x['t'] ?? 0)); return $l; }
if ($action === '' || $action === 'json') {
    $checks = stChecks(); $inc = stIncidents(); $open = array_values(array_filter($inc, fn($i) => empty($i['done']))); $allOk = !array_filter($checks, fn($c) => !$c['ok']) && !$open;
    if ($action === 'json') { header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store'); header('Access-Control-Allow-Origin: *'); http_response_code($allOk ? 200 : 503); echo json_encode(['ok' => $allOk, 'at' => time(), 'checks' => $checks, 'open' => count($open)]); exit; }
    header('Content-Type: text/html; charset=utf-8'); header('Cache-Control: no-store'); header('X-Content-Type-Options: nosniff'); header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; img-src 'self'");
    $d = fn($t) => date('j M Y, H:i', (int) $t);
    echo '<!doctype html><html lang="en-GB"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Status · Crafticulum</title><meta name="description" content="Whether Crafticulum is working right now, and anything that has gone wrong lately."><link rel="icon" href="favicon.svg?v=5" type="image/svg+xml"><style>
*{box-sizing:border-box}body{font:16px/1.55 system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;color:#1e1b4b;margin:0;background:#f7f7fc}main{max-width:760px;margin:0 auto;padding:32px 16px 60px}
a{color:#4f46e5}.brand{font-weight:800;font-size:22px;letter-spacing:-.02em;color:#4f46e5;text-decoration:none}.brand span{color:#7c3aed}
.now{margin:22px 0;border-radius:16px;padding:22px 24px;color:#fff;background:linear-gradient(135deg,#16a34a,#15803d)}.now.bad{background:linear-gradient(135deg,#d97706,#b45309)}.now h1{margin:0;font-size:1.5rem}.now p{margin:4px 0 0;opacity:.9}
.card{background:#fff;border:1px solid #e6e6f2;border-radius:14px;padding:6px 18px;margin-bottom:18px}.row{display:flex;justify-content:space-between;gap:12px;padding:12px 0;border-bottom:1px solid #eeeef7}.row:last-child{border:0}
.ok,.no{font-weight:700;white-space:nowrap}.ok{color:#15803d}.no{color:#b45309}.ok:before{content:"\25CF  "}.no:before{content:"\25B2  "}
h2{font-size:1.1rem;margin:26px 0 10px}.inc{padding:14px 0;border-bottom:1px solid #eeeef7}.inc:last-child{border:0}.inc b{display:block}.inc small{color:#6b6b8a}.inc p{margin:6px 0 0;white-space:pre-line}.tag{display:inline-block;font-size:.75rem;font-weight:700;border-radius:999px;padding:2px 10px;background:#fef3c7;color:#92400e;margin-left:8px}.tag.done{background:#dcfce7;color:#166534}
footer{color:#6b6b8a;font-size:.85rem;margin-top:26px}</style></head><body><main><a class="brand" href="./">craft<span>iculum</span></a>
<div class="now' . ($allOk ? '' : ' bad') . '"><h1>' . ($allOk ? 'Everything is working' : ($open ? 'We are looking into a problem' : 'Something needs attention')) . '</h1><p>Checked just now, ' . $h(date('j M Y, H:i')) . '. This page checks the site each time it loads.</p></div>
<div class="card">' . implode('', array_map(fn($c) => '<div class="row"><span>' . $h($c['name']) . '</span><span class="' . ($c['ok'] ? 'ok">Working' : 'no">Problem') . '</span></div>', $checks)) . '</div>
<h2>What has happened lately</h2><div class="card">' . ($inc ? implode('', array_map(fn($i) => '<div class="inc"><b>' . $h($i['title'] ?? '') . '<span class="tag' . (empty($i['done']) ? '">Being looked at' : ' done">Fixed ' . $h($d($i['done']))) . '</span></b><small>' . $h($d($i['t'] ?? 0)) . '</small>' . (($i['text'] ?? '') !== '' ? '<p>' . $h($i['text']) . '</p>' : '') . '</div>', array_slice($inc, 0, 20))) : '<div class="row"><span>Nothing has gone wrong in the last 90 days.</span></div>') . '</div>
<footer>Something not right that is not shown here? <a href="contact">Tell us</a>. Machines can read this page at <a href="status.php?a=json">status.php?a=json</a>.</footer></main></body></html>';
    exit;
}
/* ---------- the super admin writes the notes ---------- */
ccSession(); header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store');
$me = ccMe(); session_write_close(); $out = function (int $c, array $d) { http_response_code($c); echo json_encode($d, JSON_UNESCAPED_UNICODE); exit; };
if (!$me || $me['role'] !== 'superadmin') $out(403, ['error' => 'noperm']);
$body = json_decode((string) file_get_contents('php://input', false, null, 0, 8000) ?: '{}', true); if (!is_array($body)) $body = [];
if (in_array($action, ['add', 'done', 'del'], true)) { $a = ccAccounts(); $site = (array) ($a['site'] ?? []); $l = array_values(array_filter((array) ($site['incidents'] ?? []), fn($i) => is_array($i) && (int) ($i['t'] ?? 0) > time() - 90 * 86400)); $id = (string) ($body['id'] ?? '');
    $clean = fn($s, $n) => mb_substr(trim((string) preg_replace('/[\x00-\x09\x0B-\x1F<>]+/u', ' ', (string) $s)), 0, $n);
    if ($action === 'add') { $t = $clean($body['title'] ?? '', 120); if ($t === '') $out(400, ['error' => 'title']); $l[] = ['id' => bin2hex(random_bytes(5)), 't' => time(), 'title' => $t, 'text' => $clean($body['text'] ?? '', 800), 'done' => 0]; }
    if ($action === 'done') foreach ($l as $k => $i) if (($i['id'] ?? '') === $id) { $l[$k]['done'] = time(); if (($body['text'] ?? '') !== '') $l[$k]['text'] = trim(($i['text'] ?? '') . "\n\n" . $clean($body['text'], 600)); }
    if ($action === 'del') $l = array_values(array_filter($l, fn($i) => ($i['id'] ?? '') !== $id));
    $site['incidents'] = array_slice($l, -40); $a['site'] = $site; if (!ccSaveAccounts($a)) $out(500, ['error' => 'save']); $action = 'list'; }
if ($action === 'list') $out(200, ['incidents' => stIncidents(), 'checks' => stChecks(), 'url' => ccSiteUrl() . 'status']);
$out(400, ['error' => 'Unknown action.']);
