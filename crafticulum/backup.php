<?php
/*
 * Crafticulum: daily backups and student data clean-up.
 *
 * The first time a signed-in teacher opens Crafticulum each day, this zips the
 * data folder into data/backups/YYYY-MM-DD.zip and keeps the last 14. Student
 * downloads (data/share/files) are left out because they rebuild themselves.
 *
 * It also deletes students' names and answers older than the limit set in
 * Settings > Student data, and always clears live lesson progress after a day.
 * Only signed-in staff can trigger it. Only super admins can see or download backups.
 */
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
if (!is_file(__DIR__ . '/config.php')) { http_response_code(500); echo json_encode(['error' => 'config']); exit; }
require __DIR__ . '/config.php';

function out(int $code, array $data): void { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_SLASHES); exit; }

require __DIR__ . '/auth-lib.php';

$base = __DIR__ . '/data';
$bdir = $base . '/backups';
/* a private link another computer can fetch each night, so a copy always lives somewhere other than this server.
   Nobody signs in to use it: the long code in the link is the key, and making a new one stops the old one working. */
if (($_GET['a'] ?? '') === 'pull') {
    $t = (string) ccConf('BACKUP_TOKEN'); $g = (string) ($_GET['t'] ?? '');
    if (strlen($t) < 24 || !hash_equals($t, $g)) { usleep(400000); header('Content-Type: text/plain'); http_response_code(404); echo 'Not found'; exit; }
    if (!class_exists('ZipArchive')) { header('Content-Type: text/plain'); http_response_code(500); echo 'No zip extension'; exit; }
    $f = $bdir . '/' . date('Y-m-d') . '.zip'; if (!is_file($f)) makeBackup();
    if (!is_file($f)) { header('Content-Type: text/plain'); http_response_code(500); echo 'Could not make the backup'; exit; }
    offsiteNote(['pulled' => time()]);
    header('Content-Type: application/zip'); header('Content-Length: ' . filesize($f)); header('Content-Disposition: attachment; filename="crafticulum-' . date('Y-m-d') . '.zip"');
    readfile($f); exit;
}
ccSession();
$me = ccMe();
if (!$me) out(401, ['error' => 'sign_in']);
session_write_close();
// the daily backup runs whoever opens the page first; seeing, downloading and the student data settings are for super admins
if (($_GET['a'] ?? '') !== 'auto' && $me['role'] !== 'superadmin') out(403, ['error' => 'noperm']);

$share = $base . '/share';
$retFile = $share . '/retention.json';
const KEEP = 14;
$action = $_GET['a'] ?? '';

function backups(): array {
    global $bdir; $out = [];
    foreach (glob($bdir . '/*.zip') ?: [] as $f) {
        $n = basename($f); if (!preg_match('/^(\d{4}-\d{2}-\d{2})\.zip$/', $n, $m)) continue;
        $out[] = ['name' => $n, 'day' => $m[1], 'size' => filesize($f)];
    }
    usort($out, fn($a, $b) => strcmp($b['day'], $a['day']));
    return $out;
}
function retention(): int { global $retFile; $j = is_file($retFile) ? json_decode((string) @file_get_contents($retFile), true) : null; return is_array($j) ? max(0, (int) ($j['days'] ?? 0)) : 0; }
function makeBackup(): void {
    global $base, $bdir;
    if (!class_exists('ZipArchive')) out(500, ['error' => 'nozip']);
    if (!is_dir($base)) return;
    if (!is_dir($bdir) && !@mkdir($bdir, 0700, true)) out(500, ['error' => 'mkdir']);
    $day = date('Y-m-d'); $tmp = $bdir . '/' . $day . '.zip.tmp'; @unlink($tmp);
    $zip = new ZipArchive();
    if ($zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) out(500, ['error' => 'zip']);
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
    $baseLen = strlen($base) + 1;
    foreach ($it as $f) {
        if (!$f->isFile()) continue;
        $rel = str_replace('\\', '/', substr($f->getPathname(), $baseLen));
        if (strpos($rel, 'backups/') === 0 || strpos($rel, '_sessions/') === 0 || strpos($rel, 'share/files/') === 0 || substr($rel, -4) === '.tmp') continue;
        $zip->addFile($f->getPathname(), $rel);
    }
    $zip->close();
    if (!is_file($tmp)) { $z = new ZipArchive(); $z->open($tmp, ZipArchive::CREATE); $z->addFromString('empty.txt', 'Nothing saved yet.'); $z->close(); }
    @rename($tmp, $bdir . '/' . $day . '.zip');
    foreach (array_slice(backups(), KEEP) as $old) @unlink($bdir . '/' . $old['name']);
}
/* ---------- a copy off this server: sent to a folder you own each day, or fetched with the private link ---------- */
function offsiteState(): array { global $bdir; $j = json_decode((string) @file_get_contents($bdir . '/offsite.json'), true); return is_array($j) ? $j : []; }
function offsiteNote(array $ch): void { global $bdir; if (!is_dir($bdir)) @mkdir($bdir, 0700, true); @file_put_contents($bdir . '/offsite.json', json_encode($ch + offsiteState()), LOCK_EX); }
function offsiteInfo(): array { $s = offsiteState(); $url = trim((string) ccConf('BACKUP_URL')); $tok = (string) ccConf('BACKUP_TOKEN');
    return ['push' => $url !== '', 'host' => $url !== '' ? (string) parse_url($url, PHP_URL_HOST) : '', 'sent' => (int) ($s['sent'] ?? 0), 'sentDay' => (string) ($s['sentDay'] ?? ''), 'err' => (string) ($s['err'] ?? ''), 'errAt' => (int) ($s['errAt'] ?? 0), 'pulled' => (int) ($s['pulled'] ?? 0),
            'link' => strlen($tok) >= 24 ? ccSiteUrl() . 'backup.php?a=pull&t=' . $tok : '']; }
/* send today's backup to the folder set in Settings (WebDAV, or anything that takes a PUT over HTTPS). Once a day, unless forced */
function offsiteSend(bool $force = false): void {
    global $bdir; $url = trim((string) ccConf('BACKUP_URL')); if ($url === '' || !function_exists('curl_init')) return;
    $day = date('Y-m-d'); $f = $bdir . '/' . $day . '.zip'; $s = offsiteState();
    if (!is_file($f)) return;
    if (!$force && (($s['sentDay'] ?? '') === $day || (int) ($s['tryAt'] ?? 0) > time() - 3600)) return;   // done today, or tried in the last hour
    offsiteNote(['tryAt' => time()]);
    $fh = @fopen($f, 'rb'); if (!$fh) return;
    $ch = curl_init(rtrim($url, '/') . '/crafticulum-' . $day . '.zip');
    $opt = [CURLOPT_UPLOAD => true, CURLOPT_INFILE => $fh, CURLOPT_INFILESIZE => filesize($f), CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 180, CURLOPT_CONNECTTIMEOUT => 15, CURLOPT_FOLLOWLOCATION => false, CURLOPT_HTTPHEADER => ['Content-Type: application/zip']];
    $user = (string) ccConf('BACKUP_USER'); if ($user !== '') { $opt[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC; $opt[CURLOPT_USERPWD] = $user . ':' . (string) ccConf('BACKUP_PASS'); }
    curl_setopt_array($ch, $opt); $res = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); $err = curl_error($ch); curl_close($ch); fclose($fh);
    if ($res !== false && $code >= 200 && $code < 300) { offsiteNote(['sent' => time(), 'sentDay' => $day, 'err' => '', 'errAt' => 0]); return; }
    $why = $res === false ? 'Could not reach ' . (string) parse_url($url, PHP_URL_HOST) . ' (' . $err . ').' : ($code === 401 || $code === 403 ? 'The user name or password was refused (' . $code . ').' : ($code === 404 || $code === 409 ? 'That folder was not found (' . $code . '). Check the address ends at a folder that exists.' : ($code === 405 || $code === 501 ? 'That address does not take uploads (' . $code . '). It needs to be a WebDAV folder address.' : ($code === 507 || $code === 413 ? 'The other end is full or the file is too big (' . $code . ').' : 'The other end answered ' . $code . '.'))));
    offsiteNote(['err' => $why, 'errAt' => time()]); ccErr('backup', 'The backup could not be sent off this server. ' . $why);
}

/* delete students' names and answers older than the limit; live progress after a day */
function tidy(int $days, bool $all = false): int {
    global $share; $removed = 0; $now = time();
    foreach (glob($share . '/live/*.prog.json') ?: [] as $f) if ($all || $now - filemtime($f) > 86400) { @unlink($f); }
    foreach (glob($share . '/live/*.json') ?: [] as $f) if (substr($f, -10) !== '.prog.json' && $now - filemtime($f) > 86400) @unlink($f);
    if (!$all && $days <= 0) return 0;
    $cut = $now - $days * 86400;
    foreach (glob($share . '/*.results.json') ?: [] as $f) {
        $fh = @fopen($f, 'c+'); if (!$fh) continue;
        flock($fh, LOCK_EX);
        $res = json_decode(stream_get_contents($fh) ?: '[]', true); if (!is_array($res)) $res = [];
        $keep = $all ? [] : array_values(array_filter($res, fn($r) => (int) ($r['at'] ?? 0) >= $cut));
        $removed += count($res) - count($keep);
        if (!$keep) { flock($fh, LOCK_UN); fclose($fh); @unlink($f); continue; }
        ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode($keep, JSON_UNESCAPED_UNICODE)); fflush($fh); flock($fh, LOCK_UN); fclose($fh);
    }
    return $removed;
}

if ($action === 'auto') {
    $made = false; $day = date('Y-m-d');
    if (class_exists('ZipArchive') && !is_file($bdir . '/' . $day . '.zip')) { makeBackup(); $made = true; }
    tidy(retention());
    if (empty($me['demo'])) offsiteSend();
    out(200, ['made' => $made] + ($me['role'] === 'superadmin' ? ['list' => backups(), 'days' => retention(), 'off' => offsiteInfo()] : []));
}
if ($action === 'list') {
    if (!class_exists('ZipArchive')) out(500, ['error' => 'nozip']);
    out(200, ['list' => backups(), 'days' => retention(), 'off' => offsiteInfo()]);
}
if ($action === 'now') { makeBackup(); out(200, ['list' => backups(), 'off' => offsiteInfo()]); }
if ($action === 'send') { if (!is_file($bdir . '/' . date('Y-m-d') . '.zip')) makeBackup(); offsiteSend(true); out(200, ['off' => offsiteInfo(), 'list' => backups()]); }
/* make (or replace, or remove) the private link */
if ($action === 'token') {
    $raw = file_get_contents('php://input', false, null, 0, 10000); $b = json_decode($raw ?: '{}', true);
    $a = ccAccounts(); $site = (array) ($a['site'] ?? []); $conf = (array) ($site['conf'] ?? []);
    if (!empty($b['off'])) unset($conf['BACKUP_TOKEN']); else $conf['BACKUP_TOKEN'] = bin2hex(random_bytes(24));
    $site['conf'] = $conf; $a['site'] = $site; if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
    $tok = (string) ($conf['BACKUP_TOKEN'] ?? '');
    out(200, ['off' => ['link' => $tok !== '' ? ccSiteUrl() . 'backup.php?a=pull&t=' . $tok : ''] + offsiteInfo()]);
}
if ($action === 'get') {
    $n = (string) ($_GET['f'] ?? '');
    if (!preg_match('/^\d{4}-\d{2}-\d{2}\.zip$/', $n) || !is_file($bdir . '/' . $n)) { header('Content-Type: text/plain'); http_response_code(404); echo 'Not found'; exit; }
    header('Content-Type: application/zip');
    header('Content-Length: ' . filesize($bdir . '/' . $n));
    header('Content-Disposition: attachment; filename="Crafticulum backup ' . $n . '"');
    readfile($bdir . '/' . $n); exit;
}
if ($action === 'retention') {
    $raw = file_get_contents('php://input', false, null, 0, 10000); $b = json_decode($raw ?: '{}', true);
    $days = max(0, min(3650, (int) ($b['days'] ?? 0)));
    if (!is_dir($share) && !@mkdir($share, 0700, true)) out(500, ['error' => 'mkdir']);
    @file_put_contents($retFile, json_encode(['days' => $days]), LOCK_EX);
    out(200, ['days' => $days, 'removed' => tidy($days)]);
}
if ($action === 'purge') out(200, ['removed' => tidy(0, true)]);
out(400, ['error' => 'Unknown action.']);
