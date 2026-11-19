<?php
/*
 * Suggestions for the "your college or school" box on the sign-up pages. Open to anyone, because people use it before they have an account:
 * it only ever gives out the names and addresses of institutions from the public lists, and whether one already has a workspace here.
 * No session is started, answers can be kept by the browser, and each address is limited to a sensible number of searches a minute.
 */
declare(strict_types=1);
if (!is_file(__DIR__ . '/config.php')) { http_response_code(500); exit; }
require __DIR__ . '/config.php';
require __DIR__ . '/auth-lib.php';
require __DIR__ . '/inst-lib.php';
@ini_set('display_errors', '0');   // nothing but the answer goes out
header('Content-Type: application/json; charset=utf-8'); header('X-Content-Type-Options: nosniff');
if ($_SERVER['REQUEST_METHOD'] !== 'GET') { http_response_code(405); echo '{"error":"get"}'; exit; }
$q = (string) ($_GET['q'] ?? ''); if (mb_strlen(trim($q)) < 2 || strlen($q) > 80) { header('Cache-Control: no-store'); echo '{"results":[]}'; exit; }
$dir = ccInstDir() . '/rate'; if (!is_dir($dir)) @mkdir($dir, 0700, true); $rf = $dir . '/' . sha1((string) ($_SERVER['REMOTE_ADDR'] ?? '')) . '.txt'; $min = (int) floor(time() / 60);
$r = explode(':', (string) @file_get_contents($rf)); $n = ((int) ($r[0] ?? 0) === $min) ? (int) ($r[1] ?? 0) + 1 : 1; @file_put_contents($rf, $min . ':' . $n, LOCK_EX);
if ($n > 240) { http_response_code(429); header('Retry-After: 30'); echo '{"error":"slow"}'; exit; }
if (random_int(1, 200) === 1) foreach ((array) glob($dir . '/*.txt') as $f) if (@filemtime((string) $f) < time() - 3600) @unlink((string) $f);
$res = ccInstSuggest($q);
/* which of them already have a workspace here: only institutions whose manager has said "this is us" */
$linked = []; foreach ((array) (ccAccounts()['colleges'] ?? []) as $cid => $c) if (!empty($c['inst']) && empty($c['off'])) foreach ((array) $c['inst'] as $li) $linked[] = [(string) $cid, $li];
foreach ($res as $i => $x) { $res[$i]['cols'] = []; foreach ($linked as [$cid, $li]) if (ccInstSame($li, $x)) $res[$i]['cols'][] = $cid; }
header('Cache-Control: ' . (!$res ? 'no-store' : ($linked ? 'private, max-age=300' : 'public, max-age=3600')));   // an empty answer is never kept: the list may be read in a minute later
echo json_encode(['results' => $res], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
