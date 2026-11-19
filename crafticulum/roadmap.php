<?php
/*
 * The roadmap: what's coming over the next 18 months, and what people have voted for.
 *
 * The list of things itself lives in the app (it's the same for every site). What's kept here is this site's own state:
 * which items have been released, when each is planned for, the votes, anything the super admin has added, and ideas people have sent in.
 * A vote is one per account per item, kept against a hash of the account, never the email address.
 */
declare(strict_types=1);
if (!is_file(__DIR__ . '/config.php')) { http_response_code(500); header('Content-Type: application/json; charset=utf-8'); echo json_encode(['error' => 'config.php is missing.']); exit; }
require __DIR__ . '/config.php';
require __DIR__ . '/auth-lib.php';
header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store'); header('X-Content-Type-Options: nosniff');
function out(int $code, array $data): void { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit; }
const RM_WHEN = ['2027-Q1', '2027-Q2', '2027-Q3', '2027-Q4', '2028-Q1', '2028-Q2', 'later'];
function rmFile(): string { return ccDataDir() . '/_roadmap.json'; }
function rmRead(): array { $j = json_decode((string) @file_get_contents(rmFile()), true); if (!is_array($j)) $j = []; foreach (['released', 'when', 'votes'] as $k) if (!is_array($j[$k] ?? null)) $j[$k] = []; foreach (['custom', 'ideas'] as $k) if (!is_array($j[$k] ?? null)) $j[$k] = []; $j['all'] = !empty($j['all']); return $j; }
/* read, change and write as one step, so two votes at once can't lose one */
function rmChange(callable $fn): array { ccEnsureData(); $fh = @fopen(rmFile() . '.lock', 'c'); if (!$fh || !flock($fh, LOCK_EX)) out(500, ['error' => 'busy']); $s = rmRead(); $r = $fn($s); if ($r === false) { flock($fh, LOCK_UN); fclose($fh); return $s; }
    $tmp = rmFile() . '.' . bin2hex(random_bytes(4)) . '.tmp'; if (@file_put_contents($tmp, json_encode($s, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX) === false || !@rename($tmp, rmFile())) { @unlink($tmp); flock($fh, LOCK_UN); fclose($fh); out(500, ['error' => 'save']); }
    flock($fh, LOCK_UN); fclose($fh); return $s; }
$txt = fn($v, int $n): string => mb_substr(trim((string) preg_replace('/\s+/u', ' ', str_replace(["\0", '<', '>'], '', (string) $v))), 0, $n);
$idOk = fn(string $id): bool => (bool) preg_match('/^[a-z0-9][a-z0-9_-]{0,39}$/', $id);
ccSession(); $me = ccMe(); if ($me) session_write_close();
$who = $me ? substr(hash('sha256', 'vote|' . ccSiteId() . '|' . $me['email']), 0, 20) : '';
$super = $me && $me['role'] === 'superadmin'; $demo = $me && !empty($me['demo']);
$view = function (array $s) use ($who, $super): array { $counts = []; $mine = []; foreach ($s['votes'] as $id => $v) { if (!is_array($v)) continue; $counts[(string) $id] = count($v); if ($who !== '' && isset($v[$who])) $mine[] = (string) $id; }
    $o = ['all' => $s['all'], 'released' => (object) $s['released'], 'when' => (object) $s['when'], 'custom' => array_values($s['custom']), 'counts' => (object) $counts, 'mine' => $mine, 'signedIn' => $who !== '', 'admin' => $super];
    if ($super) $o['ideas'] = array_map(fn($i) => ['at' => (int) ($i['at'] ?? 0), 'text' => (string) ($i['text'] ?? ''), 'name' => (string) ($i['name'] ?? ''), 'college' => (string) ($i['college'] ?? '')], array_values($s['ideas']));
    return $o; };
$action = (string) ($_GET['a'] ?? 'list');
if ($action === 'list') out(200, $view(rmRead()));   // anyone can read it: the released list decides which tools show, and the plan is on the public page
if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(405, ['error' => 'Use POST.']);
if (!$me) out(401, ['error' => 'sign_in']); if ($demo) out(403, ['error' => 'demo']);
$body = json_decode((string) file_get_contents('php://input', false, null, 0, 20000) ?: '{}', true); if (!is_array($body)) $body = [];
if ($action === 'vote') { $id = (string) ($body['id'] ?? ''); if (!$idOk($id)) out(400, ['error' => 'id']); $on = !empty($body['on']);
    $s = rmChange(function (array &$s) use ($id, $on, $who) { if ($on) { $held = 0; foreach ($s['votes'] as $v) if (is_array($v) && isset($v[$who])) $held++; if ($held >= 100 || count($s['votes']) >= 400 && !isset($s['votes'][$id])) return false; $s['votes'][$id][$who] = time(); } else { unset($s['votes'][$id][$who]); if (empty($s['votes'][$id])) unset($s['votes'][$id]); } });
    out(200, $view($s)); }
if ($action === 'idea') { $t = $txt($body['text'] ?? '', 400); if (mb_strlen($t) < 8) out(400, ['error' => 'short']); $a = ccAccounts(); $col = (string) (ccCollege($a, ccCollegeOf($a, (string) $me['dept']))['name'] ?? '');
    $full = false; rmChange(function (array &$s) use ($t, $who, $me, $col, &$full) { $day = time() - 86400; if (count(array_filter($s['ideas'], fn($i) => ($i['who'] ?? '') === $who && ($i['at'] ?? 0) > $day)) >= 5) { $full = true; return false; } array_unshift($s['ideas'], ['at' => time(), 'who' => $who, 'name' => (string) (($me['name'] ?? '') ?: ''), 'college' => $col, 'text' => $t]); $s['ideas'] = array_slice($s['ideas'], 0, 300); });
    if ($full) out(429, ['error' => 'many']); out(200, ['ok' => true]); }
if (!$super) out(403, ['error' => 'noperm']);
if ($action === 'release') { $id = (string) ($body['id'] ?? ''); if (!$idOk($id)) out(400, ['error' => 'id']); $on = !empty($body['on']); $s = rmChange(function (array &$s) use ($id, $on) { if ($on) $s['released'][$id] = time(); else unset($s['released'][$id]); }); ccLog((string) $me['dept'], $me, $on ? 'released from the roadmap' : 'put back on the roadmap', $id); out(200, $view($s)); }
if ($action === 'when') { $id = (string) ($body['id'] ?? ''); $w = (string) ($body['when'] ?? ''); if (!$idOk($id) || !in_array($w, RM_WHEN, true)) out(400, ['error' => 'when']); out(200, $view(rmChange(function (array &$s) use ($id, $w) { $s['when'][$id] = $w; }))); }
if ($action === 'all') { $on = !empty($body['on']); $s = rmChange(function (array &$s) use ($on) { $s['all'] = $on; }); ccLog((string) $me['dept'], $me, $on ? 'switched every roadmap tool on' : 'put the roadmap tools back on the roadmap'); out(200, $view($s)); }
if ($action === 'custom') { $title = $txt($body['title'] ?? '', 70); $blurb = $txt($body['blurb'] ?? '', 300); $w = in_array($body['when'] ?? '', RM_WHEN, true) ? (string) $body['when'] : 'later'; $kind = in_array($body['kind'] ?? '', ['feature', 'link', 'ai'], true) ? (string) $body['kind'] : 'feature'; if ($title === '' || $blurb === '') out(400, ['error' => 'text']);
    out(200, $view(rmChange(function (array &$s) use ($title, $blurb, $w, $kind) { if (count($s['custom']) >= 60) return false; $s['custom'][] = ['id' => 'c-' . bin2hex(random_bytes(4)), 'title' => $title, 'blurb' => $blurb, 'kind' => $kind, 'when' => $w, 'at' => time()]; }))); }
if ($action === 'customdel') { $id = (string) ($body['id'] ?? ''); out(200, $view(rmChange(function (array &$s) use ($id) { $s['custom'] = array_values(array_filter($s['custom'], fn($c) => ($c['id'] ?? '') !== $id)); unset($s['votes'][$id], $s['released'][$id], $s['when'][$id]); }))); }
if ($action === 'ideadel') { $at = (int) ($body['at'] ?? 0); $t = (string) ($body['text'] ?? ''); out(200, $view(rmChange(function (array &$s) use ($at, $t) { $s['ideas'] = array_values(array_filter($s['ideas'], fn($i) => !((int) ($i['at'] ?? 0) === $at && (string) ($i['text'] ?? '') === $t))); }))); }
out(404, ['error' => 'Unknown action.']);
