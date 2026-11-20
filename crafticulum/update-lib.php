<?php
/*
 * Keeps this site up to date from its release on GitHub, so nobody has to upload a zip.
 *
 * The release is the same zip you would have uploaded. It is fetched over https, checked, unpacked beside the site and
 * only then moved into place. config.php and the data folder are never written to, whatever the zip contains.
 *
 * Nothing in config.php is needed. Two optional lines exist if you ever move the release somewhere else:
 *   const UPDATE_REPO = 'owner/repository';          // a different GitHub repository
 *   const UPDATE_OFF = true;                          // switch updating off altogether
 */
declare(strict_types=1);
function ccUpRepo(): string { return defined('UPDATE_REPO') ? (string) UPDATE_REPO : 'Joetuley90/BTEC_APP'; }
function ccUpBase(string $ref, bool $old = false): string { if (defined('UPDATE_BASE')) return rtrim((string) UPDATE_BASE, '/') . ($old ? '/old/' . $ref : ''); return 'https://raw.githubusercontent.com/' . ccUpRepo() . '/' . $ref; }   // UPDATE_BASE is for the test bench
function ccUpDir(): string { return ccDataDir() . '/_update'; }
function ccUpLocal(): array { $j = json_decode((string) @file_get_contents(__DIR__ . '/version.json'), true); return is_array($j) ? ['v' => (int) ($j['v'] ?? 0), 'date' => (string) ($j['date'] ?? ''), 'note' => (string) ($j['note'] ?? '')] : ['v' => 0, 'date' => '', 'note' => '']; }
function ccUpState(): array { $j = json_decode((string) @file_get_contents(ccUpDir() . '/state.json'), true); return (is_array($j) ? $j : []) + ['auto' => false, 'checked' => 0, 'latest' => null, 'runs' => []]; }
function ccUpSave(array $s): void { if (!is_dir(ccUpDir())) @mkdir(ccUpDir(), 0700, true); $s['runs'] = array_slice((array) ($s['runs'] ?? []), 0, 20); @file_put_contents(ccUpDir() . '/state.json', json_encode($s, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX); }
function ccUpKey(): string { return substr(hash('sha256', 'deploy|' . ccSiteId()), 0, 32); }
function ccUpGet(string $url, string $toFile = ''): array { $test = defined('UPDATE_BASE'); if (!$test && strpos($url, 'https://') !== 0) return ['ok' => false, 'why' => 'The address has to be https.'];
    $fh = null; if ($toFile !== '') { $fh = @fopen($toFile, 'w'); if (!$fh) return ['ok' => false, 'why' => 'The data folder can\'t be written to.']; }
    $ch = curl_init($url); curl_setopt_array($ch, [CURLOPT_FOLLOWLOCATION => true, CURLOPT_MAXREDIRS => 4, CURLOPT_TIMEOUT => 120, CURLOPT_CONNECTTIMEOUT => 15, CURLOPT_USERAGENT => 'Crafticulum updater', CURLOPT_MAXFILESIZE => 60 * 1024 * 1024]);
    if (!$test && defined('CURLPROTO_HTTPS')) { curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS); curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTPS); }
    if ($fh) curl_setopt($ch, CURLOPT_FILE, $fh); else curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $r = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); $err = curl_error($ch); curl_close($ch); if ($fh) fclose($fh);
    if ($r === false || $code !== 200) return ['ok' => false, 'why' => $code ? 'GitHub answered ' . $code . '.' : mb_substr($err, 0, 160)];
    return ['ok' => true, 'body' => $fh ? '' : (string) $r]; }
/* what the newest release is, without changing anything */
function ccUpCheck(string $ref = 'main'): array { $r = ccUpGet(ccUpBase($ref) . '/crafticulum/version.json' . ($ref === 'main' && !defined('UPDATE_BASE') ? '?t=' . time() : '')); if (!$r['ok']) return ['error' => 'fetch', 'detail' => $r['why']];
    $j = json_decode($r['body'], true); if (!is_array($j) || (int) ($j['v'] ?? 0) < 1) return ['error' => 'fetch', 'detail' => 'The release has no version number.'];
    $latest = ['v' => (int) $j['v'], 'date' => mb_substr((string) ($j['date'] ?? ''), 0, 20), 'note' => mb_substr((string) ($j['note'] ?? ''), 0, 300)]; $s = ccUpState(); $s['checked'] = time(); $s['latest'] = $latest; ccUpSave($s); return ['latest' => $latest, 'newer' => $latest['v'] > ccUpLocal()['v']]; }
function ccUpRm(string $d): void { if (!is_dir($d)) return; foreach ((array) scandir($d) as $f) { if ($f === '.' || $f === '..') continue; $p = $d . '/' . $f; is_dir($p) && !is_link($p) ? ccUpRm($p) : @unlink($p); } @rmdir($d); }
/* a file in the release that must never be written: the settings, anything in the data folder, and anything that tries to step outside the site */
function ccUpSkip(string $n): ?bool { if ($n === '' || strpos($n, "\0") !== false || strpos($n, '\\') !== false || $n[0] === '/' || preg_match('#(^|/)\.\.(/|$)#', $n) || preg_match('#^[A-Za-z]:#', $n)) return null;   // null: refuse the whole release
    $l = strtolower($n); return $l === 'config.php' || strpos($l, 'data/') === 0 || $l === 'data' || $l === '.user.ini' || $l === 'php.ini' || substr($l, -1) === '/'; }
/* fetch the release and put it in place. $ref is "main", or the exact commit GitHub has just been sent */
function ccUpRun(string $ref, string $by, bool $back = false): array {
    if (defined('UPDATE_OFF') && UPDATE_OFF) return ['error' => 'off']; if (!class_exists('ZipArchive') || !function_exists('curl_init')) return ['error' => 'server', 'detail' => 'This server is missing PHP\'s zip or curl extension.'];
    $site = __DIR__; if (!is_writable($site) || !is_writable($site . '/app.html')) return ['error' => 'perm', 'detail' => 'The web server isn\'t allowed to change the site\'s own files.'];
    @set_time_limit(300); $dir = ccUpDir(); if (!is_dir($dir) && !@mkdir($dir, 0700, true)) return ['error' => 'perm', 'detail' => 'The data folder can\'t be written to.'];
    $lock = @fopen($dir . '/lock', 'c'); if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) return ['error' => 'busy'];
    $done = function (array $r) use ($lock, $dir, $by, $ref): array { ccUpRm($dir . '/stage'); @unlink($dir . '/release.zip'); flock($lock, LOCK_UN); fclose($lock); $s = ccUpState(); array_unshift($s['runs'], ['at' => time(), 'by' => $by, 'ref' => $ref === 'main' ? '' : substr($ref, 0, 10)] + ($back ? ['back' => true] : []) + (isset($r['error']) ? ['error' => $r['error'], 'detail' => (string) ($r['detail'] ?? '')] : ['v' => $r['v'], 'from' => $r['from'], 'files' => $r['files']])); if (!isset($r['error'])) { if ($back) $s['hold'] = max((int) ($s['hold'] ?? 0), (int) $r['from']); else unset($s['hold']); } ccUpSave($s); return $r; };
    $was = ccUpLocal(); $zf = $dir . '/release.zip'; $r = ccUpGet(ccUpBase($ref, $back) . '/crafticulum-release.zip', $zf); if (!$r['ok']) return $done(['error' => 'fetch', 'detail' => $r['why']]);
    $z = new ZipArchive(); if ($z->open($zf) !== true) return $done(['error' => 'zip', 'detail' => 'What came back isn\'t a zip file.']);
    $ver = json_decode((string) $z->getFromName('version.json'), true); $v = is_array($ver) ? (int) ($ver['v'] ?? 0) : 0;
    if ($v < 1 || $z->locateName('app.html') === false || $z->locateName('auth-lib.php') === false || $z->numFiles > 3000) { $z->close(); return $done(['error' => 'zip', 'detail' => 'That zip isn\'t a Crafticulum release.']); }
    if ($back ? $v === $was['v'] : $v <= $was['v']) { $z->close(); flock($lock, LOCK_UN); fclose($lock); @unlink($zf); return ['same' => true, 'v' => $was['v']]; }   // never goes backwards, and does nothing when there's nothing new
    $list = []; $size = 0; for ($i = 0; $i < $z->numFiles; $i++) { $st = $z->statIndex($i); $n = (string) $st['name']; $sk = ccUpSkip($n); if ($sk === null) { $z->close(); return $done(['error' => 'zip', 'detail' => 'The release has a file path that isn\'t allowed, so nothing was changed.']); } if ($sk) continue; $size += (int) $st['size']; $list[] = [$i, $n]; }
    if ($size > 120 * 1024 * 1024) { $z->close(); return $done(['error' => 'zip', 'detail' => 'The release is far bigger than it should be.']); }
    /* unpack everything beside the site first, so a release that fails half way through changes nothing */
    $stage = $dir . '/stage'; ccUpRm($stage); if (!@mkdir($stage, 0700, true)) { $z->close(); return $done(['error' => 'perm', 'detail' => 'The data folder can\'t be written to.']); }
    foreach ($list as [$i, $n]) { $t = $stage . '/' . $n; if (!is_dir(dirname($t)) && !@mkdir(dirname($t), 0700, true)) { $z->close(); return $done(['error' => 'perm', 'detail' => 'Couldn\'t unpack the release.']); } $in = $z->getStream($n); $out = $in ? @fopen($t, 'w') : null; if (!$in || !$out || stream_copy_to_stream($in, $out) === false) { $z->close(); return $done(['error' => 'zip', 'detail' => 'Couldn\'t unpack ' . basename($n) . '.']); } fclose($in); fclose($out); }
    $z->close();
    foreach ($list as [$i, $n]) { $d = dirname($site . '/' . $n); if (!is_dir($d) && !@mkdir($d, 0755, true)) return $done(['error' => 'perm', 'detail' => 'Couldn\'t make the folder ' . dirname($n) . '.']); if (is_file($site . '/' . $n) && !is_writable($site . '/' . $n)) return $done(['error' => 'perm', 'detail' => basename($n) . ' can\'t be replaced.']); }
    ccUpKeep($site, array_map(fn($x) => $x[1], $list), $was);   // a copy of what's about to be replaced, so the last update can be undone
    $n2 = 0; usort($list, fn($a, $b) => ($a[1] === 'version.json') <=> ($b[1] === 'version.json'));   // the version number goes in last
    foreach ($list as [$i, $n]) { $t = $site . '/' . $n; $perm = is_file($t) ? (fileperms($t) & 0777) : 0644; if (!@rename($stage . '/' . $n, $t)) { if (!@copy($stage . '/' . $n, $t)) return $done(['error' => 'perm', 'detail' => 'Stopped at ' . basename($n) . '. Upload the zip by hand to finish.']); } @chmod($t, $perm); $n2++; }
    if (function_exists('opcache_reset')) @opcache_reset();
    return $done(['v' => $v, 'from' => $was['v'], 'files' => $n2, 'date' => (string) ($ver['date'] ?? ''), 'note' => (string) ($ver['note'] ?? '')]); }
/* from the daily scheduled job: where the super admin has switched automatic updates on, look for a newer release and take it */
function ccUpAuto(): int { $s = ccUpState(); if (empty($s['auto']) || (defined('UPDATE_OFF') && UPDATE_OFF)) return 0; $c = ccUpCheck(); if (isset($c['error']) || empty($c['newer'])) return 0; if ((int) $c['latest']['v'] <= (int) ($s['hold'] ?? 0)) return 0;   /* you went back from this release: wait for the next one */ $r = ccUpRun('main', 'the daily check'); return isset($r['v']) ? (int) $r['v'] : 0; }
/* ---------- going back. Before each update the files it replaces are copied to data/_update/prev, so the last update can be undone on the spot.
   If that copy isn't there (the update was uploaded by hand), the release before this one is fetched from GitHub's history instead. ---------- */
function ccUpKeep(string $site, array $names, array $was): void { $prev = ccUpDir() . '/prev'; $tmp = ccUpDir() . '/prev.new'; ccUpRm($tmp); if (!@mkdir($tmp, 0700, true)) return; $n = 0;
    foreach ($names as $f) { $src = $site . '/' . $f; if (!is_file($src)) continue; $t = $tmp . '/files/' . $f; if (!is_dir(dirname($t)) && !@mkdir(dirname($t), 0700, true)) { ccUpRm($tmp); return; } if (!@copy($src, $t)) { ccUpRm($tmp); return; } $n++; }
    if (!$n || !is_file($tmp . '/files/app.html')) { ccUpRm($tmp); return; }
    @file_put_contents($tmp . '/meta.json', json_encode(['v' => (int) $was['v'], 'date' => (string) $was['date'], 'note' => (string) $was['note'], 'at' => time(), 'files' => $n])); ccUpRm($prev); @rename($tmp, $prev); }
function ccUpPrev(): ?array { $j = json_decode((string) @file_get_contents(ccUpDir() . '/prev/meta.json'), true); return is_array($j) && (int) ($j['v'] ?? 0) > 0 && (int) $j['v'] !== ccUpLocal()['v'] && is_file(ccUpDir() . '/prev/files/app.html') ? $j : null; }
/* the commit of the release before the one running, from GitHub's history of the version file */
function ccUpOlder(): array { $url = defined('UPDATE_BASE') ? rtrim((string) UPDATE_BASE, '/') . '/commits.json' : 'https://api.github.com/repos/' . ccUpRepo() . '/commits?path=crafticulum/version.json&per_page=12';
    $r = ccUpGet($url); if (!$r['ok']) return ['error' => 'fetch', 'detail' => $r['why']]; $list = json_decode($r['body'], true); if (!is_array($list)) return ['error' => 'fetch', 'detail' => 'GitHub\'s history couldn\'t be read.'];
    $now = ccUpLocal()['v']; foreach ($list as $c) { $sha = (string) ($c['sha'] ?? ''); if (!preg_match('/^[0-9a-f]{40}$/', $sha)) continue; $vr = ccUpGet(ccUpBase($sha, true) . '/crafticulum/version.json'); if (!$vr['ok']) continue; $j = json_decode($vr['body'], true); $v = is_array($j) ? (int) ($j['v'] ?? 0) : 0; if ($v > 0 && $v < $now) return ['sha' => $sha, 'v' => $v, 'date' => (string) ($j['date'] ?? '')]; }
    return ['error' => 'none', 'detail' => 'There\'s no earlier release to go back to.']; }
function ccUpRestore(string $by): array {
    if (defined('UPDATE_OFF') && UPDATE_OFF) return ['error' => 'off']; $site = __DIR__; if (!is_writable($site) || !is_writable($site . '/app.html')) return ['error' => 'perm', 'detail' => 'The web server isn\'t allowed to change the site\'s own files.'];
    $p = ccUpPrev();
    if (!$p) { if (!class_exists('ZipArchive') || !function_exists('curl_init')) return ['error' => 'server', 'detail' => 'This server is missing PHP\'s zip or curl extension.']; $o = ccUpOlder(); if (isset($o['error'])) return $o; $r = ccUpRun($o['sha'], $by, true); if (isset($r['v'])) $r['source'] = 'github'; return $r; }
    @set_time_limit(300); $dir = ccUpDir(); $lock = @fopen($dir . '/lock', 'c'); if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) return ['error' => 'busy'];
    $was = ccUpLocal(); $src = $dir . '/prev/files'; $names = []; $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS)); foreach ($it as $f) { if (!$f->isFile()) continue; $n = str_replace('\\', '/', substr($f->getPathname(), strlen($src) + 1)); if (ccUpSkip($n) === false) $names[] = $n; }
    $fail = function (string $d) use ($lock, $by, $was): array { flock($lock, LOCK_UN); fclose($lock); $s = ccUpState(); array_unshift($s['runs'], ['at' => time(), 'by' => $by, 'ref' => '', 'back' => true, 'error' => 'perm', 'detail' => $d]); ccUpSave($s); return ['error' => 'perm', 'detail' => $d]; };
    foreach ($names as $n) { $t = $site . '/' . $n; if (is_file($t) && !is_writable($t)) return $fail(basename($n) . ' can\'t be replaced.'); }
    /* swap: what's running now becomes the copy, so going back can itself be undone */
    $stage = $dir . '/stage'; ccUpRm($stage); if (!@rename($dir . '/prev', $stage)) return $fail('The safety copy couldn\'t be read.');
    ccUpKeep($site, $names, $was); usort($names, fn($a, $b) => ($a === 'version.json') <=> ($b === 'version.json')); $n2 = 0;
    foreach ($names as $n) { $t = $site . '/' . $n; $perm = is_file($t) ? (fileperms($t) & 0777) : 0644; if (!is_dir(dirname($t))) @mkdir(dirname($t), 0755, true); if (!@copy($stage . '/files/' . $n, $t)) { ccUpRm($stage); return $fail('Stopped at ' . basename($n) . '. Upload the zip by hand to finish.'); } @chmod($t, $perm); $n2++; }
    ccUpRm($stage); if (function_exists('opcache_reset')) @opcache_reset(); clearstatcache(); $now = ccUpLocal();
    flock($lock, LOCK_UN); fclose($lock); $s = ccUpState(); array_unshift($s['runs'], ['at' => time(), 'by' => $by, 'ref' => '', 'back' => true, 'v' => $now['v'], 'from' => $was['v'], 'files' => $n2]); $s['hold'] = max((int) ($s['hold'] ?? 0), (int) $was['v']); ccUpSave($s);
    return ['v' => $now['v'], 'from' => $was['v'], 'files' => $n2, 'source' => 'copy']; }
