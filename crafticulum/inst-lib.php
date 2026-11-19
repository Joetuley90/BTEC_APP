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
    else { $tpl = (string) ($def['url'] ?? ''); $tries = strpos($tpl, '{date}') !== false ? [0, 1, 2, 3] : [0]; $tmp = $dir . '/import/_dl-' . bin2hex(random_bytes(4)) . '.tmp'; $why = '';   // today's file may not be out yet, so the last few days are tried in turn
        foreach ($tries as $back) { $url = str_replace('{date}', date('Ymd', time() - $back * 86400), $tpl); if (!ccInstUrlOk($url)) return ['error' => 'url']; $fh = @fopen($tmp, 'w'); if (!$fh) return ['error' => 'save'];
            $ch = curl_init($url); curl_setopt_array($ch, [CURLOPT_FILE => $fh, CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 240, CURLOPT_CONNECTTIMEOUT => 15, CURLOPT_USERAGENT => 'Crafticulum institution directory', CURLOPT_MAXFILESIZE => 300 * 1024 * 1024]);
            $ok = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); $err = curl_error($ch); curl_close($ch); fclose($fh);
            if ($ok && $code === 200 && filesize($tmp) >= 50) { $from = $url; break; } $why = $code ? 'The address answered ' . $code . '.' : mb_substr($err, 0, 160); }
        if ($from === '') { @unlink($tmp); return ['error' => 'fetch', 'detail' => $why]; }
        $src = $tmp; }
    $rows = ccInstRows($src); $head = null; $lead = [];
    while ($rows->valid()) { $r = (array) $rows->current(); $rows->next(); $r = array_map(fn($h) => trim((string) preg_replace('/^\xEF\xBB\xBF/', '', (string) $h)), $r); if (count(array_filter($r, fn($x) => $x !== '')) >= 2 && (count($lead) >= 30 || preg_match('/name|school|establishment|institution|provider/i', implode(' ', $r)))) { $head = $r; break; } $lead[] = $r; if (count($lead) > 40) break; }   // spreadsheets often have a title above the real first row
    if (!is_array($head)) { if ($tmp) @unlink($tmp); return ['error' => 'empty']; }
    $col = []; $used = []; $guess = ccInstGuess($head);
    foreach (['name', 'town', 'postcode', 'ref', 'type', 'status', 'phase', 'region'] as $k) { $want = strtolower(trim((string) ($map[$k] ?? ''))); $col[$k] = -1; if ($want !== '') foreach ($head as $i => $h) if (strtolower($h) === $want) { $col[$k] = $i; break; }
        if ($col[$k] < 0 && $want === '' && isset($guess[$k])) $col[$k] = $guess[$k]; if ($col[$k] >= 0) $used[$k] = $head[$col[$k]]; }
    if ($col['name'] < 0) { if ($tmp) @unlink($tmp); return ['error' => 'columns', 'columns' => array_values(array_filter(array_slice($head, 0, 80), fn($x) => $x !== ''))]; }
    $db = ccInstDb(); $outF = $dir . '/' . $id . '.jsonl'; $out = null; $ins = null;
    if ($db) { $db->beginTransaction(); $db->prepare('DELETE FROM institutions WHERE source = ?')->execute([$id]); $ins = $db->prepare('INSERT INTO institutions (source, ref, name, town, postcode, pc_key, type, nation, closed, search) VALUES (?,?,?,?,?,?,?,?,?,?)'); @unlink($outF); }
    else { $out = @fopen($outF . '.tmp', 'w'); if (!$out) { if ($tmp) @unlink($tmp); return ['error' => 'save']; } }
    $seen = []; $n = 0; $closed = 0; $g = fn(array $r, string $k): string => $col[$k] >= 0 && isset($r[$col[$k]]) ? ccInstUtf((string) $r[$col[$k]]) : '';
    while ($rows->valid()) { $r = (array) $rows->current(); $rows->next(); $name = $g($r, 'name'); if ($name === '') continue; $pc = strtoupper($g($r, 'postcode')); $ref = $g($r, 'ref'); $key = $ref !== '' ? 'r' . $ref : strtolower($name . '|' . $pc); if (isset($seen[$key])) continue; $seen[$key] = 1;
        $st = $g($r, 'status'); $shut = $st !== '' && (bool) preg_match('/closed|merged|ceased/i', $st) && !preg_match('/proposed to close/i', $st); if ($shut) $closed++;
        $nat = $nation; if ($col['region'] >= 0 && stripos($g($r, 'region'), 'wales') === 0) $nat = 'wal';
        $type = $g($r, 'type'); $ph = $g($r, 'phase'); if ($ph !== '' && strcasecmp($ph, 'Not applicable') !== 0 && stripos($type, $ph) === false) $type = trim($type . ($type !== '' ? ', ' : '') . $ph);
        $town = $g($r, 'town');
        if ($ins) $ins->execute([$id, $ref, $name, $town, $pc, strtolower(str_replace(' ', '', $pc)), $type, $nat, $shut ? 1 : 0, mb_strtolower($name . ' ' . $town . ' ' . $pc . ' ' . str_replace(' ', '', $pc) . ' ' . $ref . ' ' . $type)]);
        else fwrite($out, json_encode([$ref, $name, $town, $pc, $type, $nat, $shut ? 1 : 0], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
        $n++; if ($n >= 400000) break; }
    if ($tmp) @unlink($tmp);
    if ($db) { if ($n < 1) { $db->rollBack(); return ['error' => 'norows']; } $db->prepare('INSERT INTO institution_import_logs (source, origin, rows, closed, error, at, by) VALUES (?,?,?,?,?,?,?)')->execute([$id, $from, $n, $closed, '', time(), $by]); $db->commit(); }
    else { fclose($out); if ($n < 1) { @unlink($outF . '.tmp'); return ['error' => 'norows']; } if (!@rename($outF . '.tmp', $outF)) return ['error' => 'save']; }
    $log = ccInstLog(); $log['sources'][$id] = ['id' => $id, 'label' => mb_substr((string) ($def['label'] ?? $id), 0, 80), 'nation' => $nation, 'url' => (string) ($def['url'] ?? ''), 'file' => !empty($def['file']) ? basename((string) $def['file']) : '', 'map' => $map, 'rows' => $n, 'closed' => $closed, 'at' => time(), 'by' => $by, 'auto' => !empty($def['auto']) && empty($def['file'])];
    array_unshift($log['runs'], ['id' => $id, 'from' => $from, 'rows' => $n, 'closed' => $closed, 'at' => time(), 'by' => $by]); ccInstLogSave($log);
    ccInstCacheClear();
    return ['rows' => $n, 'closed' => $closed, 'id' => $id, 'used' => $used, 'from' => $from]; }
/* lists set to refresh themselves are read again once a week, from the daily scheduled job */
function ccInstAuto(): int { $n = 0; $l0 = ccInstLog();
    if (empty($l0['sources']) && empty($l0['runs'])) { $r = ccInstImport(['id' => 'gias', 'auto' => true] + CC_INST_PRESETS['gias'], 'the first automatic fill'); if (isset($r['error'])) { $l = ccInstLog(); array_unshift($l['runs'], ['id' => 'gias', 'from' => CC_INST_PRESETS['gias']['url'], 'error' => $r['error'], 'at' => time(), 'by' => 'the first automatic fill']); ccInstLogSave($l); return 0; } return 1; }   // a new site fills its own directory the first time the daily job runs
    foreach ((array) (ccInstLog()['sources'] ?? []) as $s) { if (empty($s['auto']) || empty($s['url']) || time() - (int) ($s['at'] ?? 0) < 7 * 86400) continue; $r = ccInstImport($s, 'the weekly refresh'); if (!isset($r['error'])) $n++; else { $l = ccInstLog(); array_unshift($l['runs'], ['id' => $s['id'], 'from' => (string) $s['url'], 'error' => $r['error'], 'at' => time(), 'by' => 'the weekly refresh']); ccInstLogSave($l); } if ($n >= 1) break; } return $n; }
/* search every list: all the words typed have to appear in the name, town, postcode or reference. Open ones come first */
function ccInstSearch(string $q, string $nation = '', int $max = 12): array {
    $words = array_values(array_filter(preg_split('/\s+/', mb_strtolower(trim($q))) ?: [], fn($w) => $w !== '')); if (!$words || mb_strlen(implode('', $words)) < 2) return [];
    $pcq = strtolower((string) preg_replace('/\s+/', '', $q)); $open = []; $shut = []; $seen = [];
    if ($db = ccInstDb()) { $w = []; $args = []; foreach (array_slice($words, 0, 6) as $x) { $w[] = "search LIKE ? ESCAPE '\\'"; $args[] = '%' . addcslashes($x, '%_\\') . '%'; } $sql = '(' . implode(' AND ', $w) . ')';
        if (strlen($pcq) >= 5) { $sql = '(' . $sql . ' OR pc_key = ?)'; $args[] = $pcq; } if ($nation !== '') { $sql .= ' AND nation = ?'; $args[] = $nation; }
        $st = $db->prepare('SELECT ref, name, town, postcode, type, nation, closed, source FROM institutions WHERE ' . $sql . ' ORDER BY closed, length(name) LIMIT 400'); $st->execute($args);
        foreach ($st as $r) { $k = strtolower($r['name'] . '|' . $r['postcode']); if (isset($seen[$k])) continue; $seen[$k] = 1; $row = ['ref' => (string) $r['ref'], 'name' => (string) $r['name'], 'town' => (string) $r['town'], 'postcode' => (string) $r['postcode'], 'type' => (string) $r['type'], 'nation' => (string) $r['nation'], 'closed' => !empty($r['closed']), 'source' => (string) $r['source']]; if ($row['closed']) { if (count($shut) < $max) $shut[] = $row; } else $open[] = $row; } }
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

/* ---------- a file's rows, whether it is a CSV or an Excel workbook (.xlsx): the first sheet is read ---------- */
function ccInstRows(string $file): Generator { $fh = @fopen($file, 'r'); if (!$fh) return; $sig = fread($fh, 4); fclose($fh);
    if ($sig === "PK\x03\x04" && class_exists('ZipArchive')) { $z = new ZipArchive(); if ($z->open($file) !== true) return; $ss = [];
        $x = @simplexml_load_string((string) $z->getFromName('xl/sharedStrings.xml')); if ($x) foreach ($x->si as $si) { $t = isset($si->t) ? (string) $si->t : ''; if (!isset($si->t)) foreach ($si->r as $run) $t .= (string) $run->t; $ss[] = $t; }
        $name = 'xl/worksheets/sheet1.xml'; if ($z->locateName($name) === false) for ($i = 0; $i < $z->numFiles; $i++) { $nm = (string) $z->getNameIndex($i); if (preg_match('#^xl/worksheets/[^/]+\.xml$#', $nm)) { $name = $nm; break; } }
        $sh = @simplexml_load_string((string) $z->getFromName($name)); $z->close(); if (!$sh) return;
        foreach ($sh->sheetData->row as $row) { $o = []; foreach ($row->c as $c) { $ref = (string) $c['r']; $ci = 0; foreach (str_split((string) preg_replace('/\d+/', '', $ref)) as $ch) $ci = $ci * 26 + (ord($ch) - 64); $ci = max(0, $ci - 1); $t = (string) $c['t'];
                $v = $t === 's' ? ($ss[(int) $c->v] ?? '') : ($t === 'inlineStr' ? (string) $c->is->t : (string) $c->v); while (count($o) < $ci) $o[] = ''; $o[$ci] = $v; } yield $o; }
        return; }
    $in = @fopen($file, 'r'); if (!$in) return; $first = (string) fgets($in); rewind($in); $sep = substr_count($first, "\t") > substr_count($first, ',') ? "\t" : (substr_count($first, ';') > substr_count($first, ',') ? ';' : ',');
    while (($r = fgetcsv($in, 0, $sep, '"', '')) !== false) yield $r; fclose($in); }
/* which column is which, worked out from the words in the first row when nobody has said */
function ccInstGuess(array $head): array { $g = []; $pat = ['name' => ['/^(establishment|school|institution|provider|centre|college|university|organisation)[ _]*name$/i', '/^(name|school|institution|provider|establishment)$/i', '/(school|institution|provider|establishment|legal|trading).*name/i'],
        'postcode' => ['/^post[ _]?code$/i', '/post[ _]?code/i'], 'town' => ['/^(town|locality|city|post[ _]?town)$/i', '/town|locality|city/i'], 'ref' => ['/^(urn|ukprn|seed[ _]?code|de[ _]?ref(erence)?|school[ _]?(number|code|no)|institution[ _]?(ref(erence)?|number|code)|reference)$/i', '/urn|ukprn|seed|reference/i'],
        'type' => ['/^(type ?of ?establishment.*|school[ _]?type|institution[ _]?type|provider[ _]?type|sector|type)$/i', '/type|sector|phase/i'], 'status' => ['/^(establishment)?[ _]?status( \(name\))?$/i', '/status/i']];
    foreach ($pat as $k => $ps) foreach ($ps as $p) { foreach ($head as $i => $h) if (!in_array($i, $g, true) && preg_match($p, trim((string) $h)) && !preg_match('/head|contact|trust|first|last|\(code\)|email|phone|web/i', (string) $h)) { $g[$k] = $i; break 2; } }
    return $g; }
/* ---------- the relational store: a small SQLite database beside the lists. If the server can't run SQLite, files are used instead and nothing else changes ---------- */
function ccInstDb(): ?PDO { static $db = false; if ($db !== false) return $db; $db = null; if (defined('INST_NO_SQLITE') || !class_exists('PDO') || !in_array('sqlite', PDO::getAvailableDrivers(), true)) return null;
    $dir = ccInstDir(); if (!is_dir($dir) && !@mkdir($dir, 0700, true)) return null;
    try { $p = new PDO('sqlite:' . $dir . '/directory.sqlite'); $p->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); $p->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); $p->exec('PRAGMA journal_mode = WAL; PRAGMA foreign_keys = ON');
        $p->exec("CREATE TABLE IF NOT EXISTS institutions (id INTEGER PRIMARY KEY, source TEXT NOT NULL, ref TEXT, name TEXT NOT NULL, town TEXT, postcode TEXT, pc_key TEXT, type TEXT, nation TEXT, closed INTEGER DEFAULT 0, search TEXT);
            CREATE INDEX IF NOT EXISTS inst_source ON institutions (source); CREATE INDEX IF NOT EXISTS inst_pc ON institutions (pc_key); CREATE INDEX IF NOT EXISTS inst_ref ON institutions (ref);
            CREATE TABLE IF NOT EXISTS institution_campuses (id INTEGER PRIMARY KEY, institution_id INTEGER NOT NULL REFERENCES institutions (id) ON DELETE CASCADE, name TEXT NOT NULL, town TEXT, postcode TEXT);
            CREATE TABLE IF NOT EXISTS institution_import_logs (id INTEGER PRIMARY KEY, source TEXT, origin TEXT, rows INTEGER, closed INTEGER, error TEXT, at INTEGER, by TEXT);
            CREATE TABLE IF NOT EXISTS user_education_profiles (user TEXT PRIMARY KEY, country TEXT, setting TEXT, role TEXT, solo INTEGER DEFAULT 0, done INTEGER DEFAULT 0, seen INTEGER DEFAULT 0, updated INTEGER);
            CREATE TABLE IF NOT EXISTS user_subjects (user TEXT NOT NULL REFERENCES user_education_profiles (user) ON DELETE CASCADE, pos INTEGER, subject TEXT NOT NULL);
            CREATE TABLE IF NOT EXISTS user_teaching_levels (user TEXT NOT NULL REFERENCES user_education_profiles (user) ON DELETE CASCADE, pos INTEGER, kind TEXT NOT NULL, value TEXT NOT NULL);
            CREATE TABLE IF NOT EXISTS user_awarding_organisations (user TEXT NOT NULL REFERENCES user_education_profiles (user) ON DELETE CASCADE, pos INTEGER, organisation TEXT NOT NULL);
            CREATE TABLE IF NOT EXISTS user_qualifications (user TEXT NOT NULL REFERENCES user_education_profiles (user) ON DELETE CASCADE, pos INTEGER, qualification TEXT NOT NULL);
            CREATE TABLE IF NOT EXISTS institution_memberships (id INTEGER PRIMARY KEY, user TEXT NOT NULL REFERENCES user_education_profiles (user) ON DELETE CASCADE, pos INTEGER, ref TEXT, name TEXT NOT NULL, town TEXT, postcode TEXT, type TEXT, nation TEXT, source TEXT, manual INTEGER DEFAULT 0, is_default INTEGER DEFAULT 0, status TEXT DEFAULT 'profile', college TEXT, requested_at INTEGER, decided_at INTEGER, decided_by TEXT);
            CREATE INDEX IF NOT EXISTS mem_user ON institution_memberships (user);");
        @chmod($dir . '/directory.sqlite', 0600); $db = $p; } catch (Throwable $e) { $db = null; }
    return $db; }
/* a person's education profile, as rows in those tables. A membership row is only ever information: access to a workspace is decided elsewhere */
const CC_PROFILE_LISTS = ['subjects' => 20, 'stages' => 20, 'levels' => 20, 'orgs' => 30, 'quals' => 20];
function ccProfileClean(array $p): array { $t = fn($v, int $n): string => mb_substr(trim(str_replace(["\0", '<', '>'], '', (string) $v)), 0, $n); $o = ['country' => in_array($p['country'] ?? '', ['eng', 'wal', 'sco', 'ni', 'other'], true) ? $p['country'] : '', 'setting' => preg_match('/^[a-z]{2,10}$/', (string) ($p['setting'] ?? '')) ? $p['setting'] : '', 'role' => $t($p['role'] ?? '', 40), 'solo' => !empty($p['solo']), 'done' => (int) ($p['done'] ?? 0), 'seen' => (int) ($p['seen'] ?? 0), 'def' => max(0, (int) ($p['def'] ?? 0)), 'insts' => []];
    foreach (CC_PROFILE_LISTS as $k => $max) $o[$k] = array_slice(array_values(array_unique(array_filter(array_map(fn($x) => $t($x, 80), (array) ($p[$k] ?? [])), fn($x) => $x !== ''))), 0, $max);
    foreach (array_slice((array) ($p['insts'] ?? []), 0, 6) as $i) { if (!is_array($i) || $t($i['name'] ?? '', 120) === '') continue; $o['insts'][] = ['ref' => $t($i['ref'] ?? '', 30), 'name' => $t($i['name'], 120), 'town' => $t($i['town'] ?? '', 80), 'postcode' => $t($i['postcode'] ?? '', 12), 'type' => $t($i['type'] ?? '', 120), 'nation' => $t($i['nation'] ?? '', 6), 'source' => $t($i['source'] ?? '', 40), 'manual' => !empty($i['manual'])]; }
    $o['def'] = min($o['def'], max(0, count($o['insts']) - 1)); return $o; }
function ccProfileFile(string $user): string { return ccInstDir() . '/profiles/' . sha1($user) . '.json'; }
function ccProfileGet(string $user): ?array { $db = ccInstDb();
    if (!$db) { $j = json_decode((string) @file_get_contents(ccProfileFile($user)), true); return is_array($j) ? ccProfileClean($j) : null; }
    $st = $db->prepare('SELECT * FROM user_education_profiles WHERE user = ?'); $st->execute([$user]); $r = $st->fetch(); if (!$r) return null;
    $list = function (string $sql) use ($db, $user): array { $q = $db->prepare($sql); $q->execute([$user]); return $q->fetchAll(PDO::FETCH_COLUMN); };
    $p = ['country' => (string) $r['country'], 'setting' => (string) $r['setting'], 'role' => (string) $r['role'], 'solo' => !empty($r['solo']), 'done' => (int) $r['done'], 'seen' => (int) $r['seen'], 'insts' => [], 'def' => 0,
        'subjects' => $list('SELECT subject FROM user_subjects WHERE user = ? ORDER BY pos'), 'stages' => $list("SELECT value FROM user_teaching_levels WHERE user = ? AND kind = 'stage' ORDER BY pos"), 'levels' => $list("SELECT value FROM user_teaching_levels WHERE user = ? AND kind = 'level' ORDER BY pos"),
        'orgs' => $list('SELECT organisation FROM user_awarding_organisations WHERE user = ? ORDER BY pos'), 'quals' => $list('SELECT qualification FROM user_qualifications WHERE user = ? ORDER BY pos')];
    $q = $db->prepare('SELECT * FROM institution_memberships WHERE user = ? ORDER BY pos'); $q->execute([$user]); foreach ($q as $n => $m) { if (!empty($m['is_default'])) $p['def'] = count($p['insts']); $p['insts'][] = ['ref' => (string) $m['ref'], 'name' => (string) $m['name'], 'town' => (string) $m['town'], 'postcode' => (string) $m['postcode'], 'type' => (string) $m['type'], 'nation' => (string) $m['nation'], 'source' => (string) $m['source'], 'manual' => !empty($m['manual']), 'status' => (string) $m['status']]; }
    return $p; }
function ccProfileSet(string $user, array $in): array { $p = ccProfileClean($in); $db = ccInstDb();
    if (!$db) { $f = ccProfileFile($user); if (!is_dir(dirname($f))) @mkdir(dirname($f), 0700, true); @file_put_contents($f, json_encode($p, JSON_UNESCAPED_UNICODE), LOCK_EX); return $p; }
    $old = []; $q = $db->prepare('SELECT ref, name, postcode, status, college, requested_at, decided_at, decided_by FROM institution_memberships WHERE user = ?'); $q->execute([$user]); foreach ($q as $m) $old[strtolower($m['ref'] . '|' . $m['name'] . '|' . $m['postcode'])] = $m;   // a request already made for an institution is kept when the profile is saved again
    $db->beginTransaction(); $db->prepare('DELETE FROM user_education_profiles WHERE user = ?')->execute([$user]);
    $db->prepare('INSERT INTO user_education_profiles (user, country, setting, role, solo, done, seen, updated) VALUES (?,?,?,?,?,?,?,?)')->execute([$user, $p['country'], $p['setting'], $p['role'], $p['solo'] ? 1 : 0, $p['done'], $p['seen'], time()]);
    $put = function (string $sql, array $vals, array $extra = []) use ($db, $user): void { $st = $db->prepare($sql); foreach ($vals as $i => $v) $st->execute(array_merge([$user, $i], $extra, [$v])); };
    $put('INSERT INTO user_subjects (user, pos, subject) VALUES (?,?,?)', $p['subjects']); $put("INSERT INTO user_teaching_levels (user, pos, kind, value) VALUES (?,?,?,?)", $p['stages'], ['stage']); $put("INSERT INTO user_teaching_levels (user, pos, kind, value) VALUES (?,?,?,?)", $p['levels'], ['level']);
    $put('INSERT INTO user_awarding_organisations (user, pos, organisation) VALUES (?,?,?)', $p['orgs']); $put('INSERT INTO user_qualifications (user, pos, qualification) VALUES (?,?,?)', $p['quals']);
    $st = $db->prepare('INSERT INTO institution_memberships (user, pos, ref, name, town, postcode, type, nation, source, manual, is_default, status, college, requested_at, decided_at, decided_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    foreach ($p['insts'] as $n => $i) { $o = $old[strtolower($i['ref'] . '|' . $i['name'] . '|' . $i['postcode'])] ?? []; $st->execute([$user, $n, $i['ref'], $i['name'], $i['town'], $i['postcode'], $i['type'], $i['nation'], $i['source'], $i['manual'] ? 1 : 0, $n === $p['def'] ? 1 : 0, (string) ($o['status'] ?? 'profile'), (string) ($o['college'] ?? ''), (int) ($o['requested_at'] ?? 0), (int) ($o['decided_at'] ?? 0), (string) ($o['decided_by'] ?? '')]); }
    $db->commit(); return $p; }
function ccMemberStatus(string $user, array $inst, string $status, string $college, string $by = ''): void { $db = ccInstDb(); if (!$db) return; try { $db->prepare('UPDATE institution_memberships SET status = ?, college = ?, ' . ($status === 'requested' ? 'requested_at' : 'decided_at') . ' = ?, decided_by = ? WHERE user = ? AND ((ref <> \'\' AND ref = ?) OR (lower(name) = lower(?) AND postcode = ?))')->execute([$status, $college, time(), $by, $user, (string) ($inst['ref'] ?? ''), (string) ($inst['name'] ?? ''), (string) ($inst['postcode'] ?? '')]); } catch (Throwable $e) {} }
function ccInstSame(array $a, array $b): bool { $ra = trim((string) ($a['ref'] ?? '')); $rb = trim((string) ($b['ref'] ?? '')); if ($ra !== '' && $rb !== '') return $ra === $rb && (string) ($a['source'] ?? '') === (string) ($b['source'] ?? '') || $ra === $rb && (($a['source'] ?? '') === '' || ($b['source'] ?? '') === '');
    return strtolower(trim((string) ($a['name'] ?? ''))) === strtolower(trim((string) ($b['name'] ?? ''))) && strtoupper(str_replace(' ', '', (string) ($a['postcode'] ?? ''))) === strtoupper(str_replace(' ', '', (string) ($b['postcode'] ?? ''))) && trim((string) ($a['postcode'] ?? '')) !== ''; }

/* ---------- as-you-type suggestions for the sign-up pages: open institutions only, names that start with what was typed first.
   Short searches are kept on disk until the next import, so the common ones cost nothing ---------- */
function ccInstCacheClear(): void { foreach ((array) glob(ccInstDir() . '/cache/*.json') as $f) @unlink((string) $f); }
function ccInstSuggest(string $q, int $max = 8): array {
    $q = trim((string) preg_replace('/\s+/', ' ', mb_strtolower($q))); if (mb_strlen($q) < 2 || mb_strlen($q) > 60) return [];
    $cdir = ccInstDir() . '/cache'; $cf = $cdir . '/' . sha1($q . '|' . $max) . '.json'; $short = mb_strlen($q) <= 8;
    if ($short && is_file($cf)) { $j = json_decode((string) @file_get_contents($cf), true); if (is_array($j)) return $j; }
    $words = explode(' ', $q); $pcq = str_replace(' ', '', $q); $out = [];
    if ($db = ccInstDb()) { $w = []; $args = []; $esc = fn(string $x): string => addcslashes($x, '%_\\');
        foreach (array_slice($words, 0, 6) as $x) { $w[] = "search LIKE ? ESCAPE '\\'"; $args[] = '%' . $esc($x) . '%'; } $sql = '(' . implode(' AND ', $w) . ')'; if (strlen($pcq) >= 5) { $sql = '(' . $sql . ' OR pc_key = ?)'; $args[] = $pcq; }
        $st = $db->prepare("SELECT ref, name, town, postcode, type, nation, source FROM institutions WHERE closed = 0 AND $sql ORDER BY (lower(name) LIKE ? ESCAPE '\\') DESC, (search LIKE ? ESCAPE '\\') DESC, length(name), name LIMIT " . ($max * 3));
        $st->execute(array_merge($args, [$esc($q) . '%', '% ' . $esc($words[0]) . '%'])); $seen = [];
        foreach ($st as $r) { $k = strtolower($r['name'] . '|' . $r['postcode']); if (isset($seen[$k])) continue; $seen[$k] = 1; $out[] = array_map('strval', $r); if (count($out) >= $max) break; } }
    else { $all = array_values(array_filter(ccInstSearch($q, '', $max * 4), fn($r) => empty($r['closed']))); $rank = fn(array $r): int => (strpos(mb_strtolower($r['name']), $q) === 0 ? 0 : (preg_match('/\b' . preg_quote($words[0], '/') . '/iu', $r['name']) ? 1 : 2)) * 1000 + mb_strlen($r['name']);
        usort($all, fn($a, $b) => $rank($a) <=> $rank($b)); foreach (array_slice($all, 0, $max) as $r) { unset($r['closed']); $out[] = $r; } }
    if ($short) { if (!is_dir($cdir)) @mkdir($cdir, 0700, true); @file_put_contents($cf, json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX); }
    return $out; }
