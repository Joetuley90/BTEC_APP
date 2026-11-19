<?php
/*
 * The institution directory: schools, colleges and other providers, read in from official lists so staff can
 * find theirs by name, town, postcode or reference number. Each list is kept as its own file under data/_inst,
 * with a log of where it came from and when. Nothing here gives anyone access to anything: it is only a list.
 */
declare(strict_types=1);
function ccInstDir(): string { return ccDataDir() . '/_inst'; }
function ccInstLog(): array { $j = json_decode((string) @file_get_contents(ccInstDir() . '/_log.json'), true); return is_array($j) ? $j : ['sources' => [], 'runs' => []]; }
function ccInstLogSave(array $l): void { $l['runs'] = array_slice((array) ($l['runs'] ?? []), 0, 60); @file_put_contents(ccInstDir() . '/_log.json', json_encode($l, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX); }
function ccInstSlug(string $s): string { return substr(trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($s)), '-'), 0, 40); }
function ccInstUtf(string $s): string { $s = trim($s); if ($s !== '' && !mb_check_encoding($s, 'UTF-8')) $s = (string) mb_convert_encoding($s, 'UTF-8', 'Windows-1252'); return mb_substr(str_replace(["\t", "\n", "\r", '"'], [' ', ' ', '', "'"], $s), 0, 160); }
/* only public web addresses can be fetched: never this server or its network */
function ccInstUrlOk(string $url): bool { $p = parse_url($url); $h = strtolower((string) ($p['host'] ?? '')); if ($h === '' || strlen($url) > 600 || isset($p['user'])) return false;
    if (defined('INST_TEST_HOST') && ($p['scheme'] ?? '') === 'http' && $h . ':' . ($p['port'] ?? 80) === INST_TEST_HOST) return true;   // the test bench only
    if (($p['scheme'] ?? '') !== 'https' || (isset($p['port']) && (int) $p['port'] !== 443)) return false;
    $ip = filter_var($h, FILTER_VALIDATE_IP) ? $h : gethostbyname($h); return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE); }
/* the column names of the lists we know. Any other list works too: the super admin says which column is which */
const CC_INST_PRESETS = ['gias' => ['label' => 'Get Information About Schools (England)', 'nation' => 'eng', 'url' => 'https://ea-edubase-api-prod.azurewebsites.net/edubase/downloads/public/edubasealldata{date}.csv',
    'map' => ['name' => 'EstablishmentName', 'town' => 'Town', 'postcode' => 'Postcode', 'ref' => 'URN', 'type' => 'TypeOfEstablishment (name)', 'status' => 'EstablishmentStatus (name)', 'phase' => 'PhaseOfEducation (name)', 'region' => 'GOR (name)']]];
/* reads one list (a CSV at a web address, or a file put in data/_inst/import) into the directory. Returns ['rows' => n, ...] or ['error' => why] */
function ccInstImport(array $def, string $by): array {
    @set_time_limit(600); @ini_set('memory_limit', '256M'); $dir = ccInstDir(); if (!is_dir($dir . '/import') && !@mkdir($dir . '/import', 0700, true)) return ['error' => 'save'];
    $id = ccInstSlug((string) ($def['id'] ?? '')); if ($id === '') return ['error' => 'id']; $map = (array) ($def['map'] ?? []); $nation = in_array($def['nation'] ?? '', ['eng', 'wal', 'sco', 'ni', 'other'], true) ? $def['nation'] : 'eng';
    $from = ''; $tmp = '';
    if (!empty($def['file'])) { $f = $dir . '/import/' . basename((string) $def['file']); if (!is_file($f)) return ['error' => 'nofile']; $src = $f; $from = 'file ' . basename($f); }
    else { $url = str_replace('{date}', date('Ymd'), (string) ($def['url'] ?? '')); if (!ccInstUrlOk($url)) return ['error' => 'url']; $tmp = $dir . '/import/_dl-' . bin2hex(random_bytes(4)) . '.csv'; $fh = @fopen($tmp, 'w'); if (!$fh) return ['error' => 'save'];
        $ch = curl_init($url); curl_setopt_array($ch, [CURLOPT_FILE => $fh, CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 240, CURLOPT_CONNECTTIMEOUT => 15, CURLOPT_USERAGENT => 'Crafticulum institution directory', CURLOPT_MAXFILESIZE => 300 * 1024 * 1024]);
        $ok = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); $err = curl_error($ch); curl_close($ch); fclose($fh);
        if (!$ok || $code !== 200 || filesize($tmp) < 50) { @unlink($tmp); return ['error' => 'fetch', 'detail' => $code ? 'The address answered ' . $code . '.' : mb_substr($err, 0, 160)]; }
        $src = $tmp; $from = $url; }
    $in = @fopen($src, 'r'); if (!$in) { if ($tmp) @unlink($tmp); return ['error' => 'read']; }
    $head = fgetcsv($in, 0, ',', '"', ''); if (!is_array($head)) { fclose($in); if ($tmp) @unlink($tmp); return ['error' => 'empty']; }
    $head = array_map(fn($h) => trim((string) preg_replace('/^\xEF\xBB\xBF/', '', (string) $h)), $head); $col = [];
    foreach (['name', 'town', 'postcode', 'ref', 'type', 'status', 'phase', 'region'] as $k) { $want = strtolower(trim((string) ($map[$k] ?? ''))); $col[$k] = -1; if ($want !== '') foreach ($head as $i => $h) if (strtolower($h) === $want) { $col[$k] = $i; break; } }
    if ($col['name'] < 0) { fclose($in); if ($tmp) @unlink($tmp); return ['error' => 'columns', 'columns' => array_slice($head, 0, 60)]; }
    $outF = $dir . '/' . $id . '.jsonl'; $out = @fopen($outF . '.tmp', 'w'); if (!$out) { fclose($in); if ($tmp) @unlink($tmp); return ['error' => 'save']; }
    $seen = []; $n = 0; $closed = 0; $g = fn(array $r, string $k): string => $col[$k] >= 0 && isset($r[$col[$k]]) ? ccInstUtf((string) $r[$col[$k]]) : '';
    while (($r = fgetcsv($in, 0, ',', '"', '')) !== false) { $name = $g($r, 'name'); if ($name === '') continue; $pc = strtoupper($g($r, 'postcode')); $ref = $g($r, 'ref'); $key = $ref !== '' ? 'r' . $ref : strtolower($name . '|' . $pc); if (isset($seen[$key])) continue; $seen[$key] = 1;
        $st = $g($r, 'status'); $shut = $st !== '' && (bool) preg_match('/closed|merged|ceased/i', $st) && !preg_match('/proposed to close/i', $st); if ($shut) $closed++;
        $nat = $nation; if ($col['region'] >= 0 && stripos($g($r, 'region'), 'wales') === 0) $nat = 'wal';
        $type = $g($r, 'type'); $ph = $g($r, 'phase'); if ($ph !== '' && strcasecmp($ph, 'Not applicable') !== 0 && stripos($type, $ph) === false) $type = trim($type . ($type !== '' ? ', ' : '') . $ph);
        fwrite($out, json_encode([$ref, $name, $g($r, 'town'), $pc, $type, $nat, $shut ? 1 : 0], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n"); $n++; if ($n >= 400000) break; }
    fclose($in); fclose($out); if ($tmp) @unlink($tmp); if ($n < 1) { @unlink($outF . '.tmp'); return ['error' => 'norows']; }
    if (!@rename($outF . '.tmp', $outF)) return ['error' => 'save'];
    $log = ccInstLog(); $log['sources'][$id] = ['id' => $id, 'label' => mb_substr((string) ($def['label'] ?? $id), 0, 80), 'nation' => $nation, 'url' => (string) ($def['url'] ?? ''), 'file' => !empty($def['file']) ? basename((string) $def['file']) : '', 'map' => $map, 'rows' => $n, 'closed' => $closed, 'at' => time(), 'by' => $by, 'auto' => !empty($def['auto']) && empty($def['file'])];
    array_unshift($log['runs'], ['id' => $id, 'from' => $from, 'rows' => $n, 'closed' => $closed, 'at' => time(), 'by' => $by]); ccInstLogSave($log);
    return ['rows' => $n, 'closed' => $closed, 'id' => $id]; }
/* lists set to refresh themselves are read again once a week, from the daily scheduled job */
function ccInstAuto(): int { $n = 0; foreach ((array) (ccInstLog()['sources'] ?? []) as $s) { if (empty($s['auto']) || empty($s['url']) || time() - (int) ($s['at'] ?? 0) < 7 * 86400) continue; $r = ccInstImport($s, 'the weekly refresh'); if (!isset($r['error'])) $n++; else { $l = ccInstLog(); array_unshift($l['runs'], ['id' => $s['id'], 'from' => (string) $s['url'], 'error' => $r['error'], 'at' => time(), 'by' => 'the weekly refresh']); ccInstLogSave($l); } if ($n >= 1) break; } return $n; }
/* search every list: all the words typed have to appear in the name, town, postcode or reference. Open ones come first */
function ccInstSearch(string $q, string $nation = '', int $max = 12): array {
    $words = array_values(array_filter(preg_split('/\s+/', mb_strtolower(trim($q))) ?: [], fn($w) => $w !== '')); if (!$words || mb_strlen(implode('', $words)) < 2) return [];
    $pcq = strtolower((string) preg_replace('/\s+/', '', $q)); $open = []; $shut = []; $seen = [];
    foreach ((array) glob(ccInstDir() . '/*.jsonl') as $f) { $src = basename((string) $f, '.jsonl'); $in = @fopen((string) $f, 'r'); if (!$in) continue;
        while (($line = fgets($in)) !== false) { $low = mb_strtolower($line); $hit = true; foreach ($words as $w) if (strpos($low, $w) === false) { $hit = false; break; }
            if (!$hit && !(strlen($pcq) >= 5 && strpos(str_replace(' ', '', $low), $pcq) !== false)) continue;
            $r = json_decode($line, true); if (!is_array($r) || count($r) < 7) continue; if ($nation !== '' && $r[5] !== $nation) continue;
            $k = strtolower($r[1] . '|' . $r[3]); if (isset($seen[$k])) continue; $seen[$k] = 1;
            $row = ['ref' => (string) $r[0], 'name' => (string) $r[1], 'town' => (string) $r[2], 'postcode' => (string) $r[3], 'type' => (string) $r[4], 'nation' => (string) $r[5], 'closed' => !empty($r[6]), 'source' => $src];
            if ($row['closed']) { if (count($shut) < $max) $shut[] = $row; } else { $open[] = $row; if (count($open) >= $max * 4) break; } }
        fclose($in); }
    $rank = function (array $r) use ($words): int { $n = mb_strtolower($r['name']); return (strpos($n, $words[0]) === 0 ? 0 : 1) * 1000 + mb_strlen($n); }; usort($open, fn($a, $b) => $rank($a) <=> $rank($b));
    return array_slice(array_merge($open, $shut), 0, $max); }
