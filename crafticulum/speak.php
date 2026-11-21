<?php
/*
 * Crafticulum: a voice for video lessons.
 *
 * Turns one sentence of a video lesson's narration into speech with Google Cloud Text-to-Speech, using the API key the
 * super admin saved in Admin, System, Site setup. The key never reaches the browser. Only signed-in staff can use it,
 * the same sentence in the same voice is only ever paid for once, and a monthly ceiling on characters keeps the cost
 * inside what the super admin has agreed to.
 */
declare(strict_types=1);
if (!is_file(__DIR__ . '/config.php')) { http_response_code(500); exit; }
require __DIR__ . '/config.php';
require __DIR__ . '/auth-lib.php';
ccSession();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
function out(int $code, array $data): void { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit; }
const TTS_CAT = [['en-GB-Chirp3-HD-Aoede', 'Aoede', 'Chirp 3 HD', 'British', 'F'], ['en-GB-Chirp3-HD-Kore', 'Kore', 'Chirp 3 HD', 'British', 'F'], ['en-GB-Chirp3-HD-Leda', 'Leda', 'Chirp 3 HD', 'British', 'F'], ['en-GB-Chirp3-HD-Zephyr', 'Zephyr', 'Chirp 3 HD', 'British', 'F'], ['en-GB-Chirp3-HD-Charon', 'Charon', 'Chirp 3 HD', 'British', 'M'], ['en-GB-Chirp3-HD-Fenrir', 'Fenrir', 'Chirp 3 HD', 'British', 'M'], ['en-GB-Chirp3-HD-Orus', 'Orus', 'Chirp 3 HD', 'British', 'M'], ['en-GB-Chirp3-HD-Puck', 'Puck', 'Chirp 3 HD', 'British', 'M'], ['en-US-Chirp3-HD-Kore', 'Kore', 'Chirp 3 HD', 'American', 'F'], ['en-US-Chirp3-HD-Charon', 'Charon', 'Chirp 3 HD', 'American', 'M'], ['en-AU-Chirp3-HD-Aoede', 'Aoede', 'Chirp 3 HD', 'Australian', 'F'], ['en-AU-Chirp3-HD-Charon', 'Charon', 'Chirp 3 HD', 'Australian', 'M'], ['en-GB-Studio-B', 'Studio B', 'Studio', 'British', 'M'], ['en-GB-Studio-C', 'Studio C', 'Studio', 'British', 'F'], ['en-GB-Neural2-A', 'Neural2 A', 'Neural2', 'British', 'F'], ['en-GB-Neural2-B', 'Neural2 B', 'Neural2', 'British', 'M'], ['en-GB-Neural2-C', 'Neural2 C', 'Neural2', 'British', 'F'], ['en-GB-Neural2-D', 'Neural2 D', 'Neural2', 'British', 'M'], ['en-GB-Neural2-F', 'Neural2 F', 'Neural2', 'British', 'F'], ['en-GB-Wavenet-A', 'WaveNet A', 'WaveNet', 'British', 'F'], ['en-GB-Wavenet-B', 'WaveNet B', 'WaveNet', 'British', 'M'], ['en-GB-Wavenet-C', 'WaveNet C', 'WaveNet', 'British', 'F'], ['en-GB-Wavenet-D', 'WaveNet D', 'WaveNet', 'British', 'M'], ['en-GB-Wavenet-F', 'WaveNet F', 'WaveNet', 'British', 'F'], ['en-GB-Standard-A', 'Standard A', 'Standard', 'British', 'F'], ['en-GB-Standard-B', 'Standard B', 'Standard', 'British', 'M'], ['en-GB-Standard-C', 'Standard C', 'Standard', 'British', 'F'], ['en-GB-Standard-D', 'Standard D', 'Standard', 'British', 'M'], ['en-GB-Standard-F', 'Standard F', 'Standard', 'British', 'F']];
function ttsVoices(): array { $o = []; foreach (TTS_CAT as $v) $o[$v[0]] = $v[1] . ' (' . $v[3] . ', ' . ($v[4] === 'F' ? 'female' : 'male') . ', ' . $v[2] . ')'; return $o; }
define('TTS_VOICES', ttsVoices());
/* the front-page film's voice-over. Anyone can fetch it (it's on the public page). The super admin makes it once, from Site setup, and it is only
   used while it matches the film's captions (assets/demo-cues.json), so a new film never plays with an old voice. */
$filmCues = __DIR__ . '/assets/demo-cues.json'; $filmDir = ccDataDir() . '/_film';
$filmHash = fn() => is_file($filmCues) ? substr(sha1((string) file_get_contents($filmCues)), 0, 12) : '';
$filmMeta = function () use ($filmDir): array { $j = json_decode((string) @file_get_contents($filmDir . '/voice.json'), true); return is_array($j) ? $j : []; };
if (($_GET['a'] ?? '') === 'filminfo') { session_write_close(); $m = $filmMeta(); $h = $filmHash(); $on = $h !== '' && ($m['hash'] ?? '') === $h && is_file($filmDir . '/voice.wav');
    out(200, ['on' => $on, 'v' => $on ? $h . substr(sha1((string) ($m['voice'] ?? '')), 0, 4) : '', 'voice' => $on ? (string) ($m['voice'] ?? '') : '', 'at' => $on ? (int) ($m['at'] ?? 0) : 0]); }
if (($_GET['a'] ?? '') === 'filmwav') { session_write_close(); $f = $filmDir . '/voice.wav'; if (!is_file($f)) { http_response_code(404); exit; }
    $size = filesize($f); $from = 0; $to = $size - 1;
    header_remove('Content-Type'); header('Content-Type: audio/wav'); header('Accept-Ranges: bytes'); header('Cache-Control: public, max-age=31536000, immutable');
    if (!empty($_GET['dl'])) header('Content-Disposition: attachment; filename="crafticulum-film-voice.wav"');
    if (preg_match('/bytes=(\d*)-(\d*)/', (string) ($_SERVER['HTTP_RANGE'] ?? ''), $r)) { if ($r[1] !== '') $from = (int) $r[1]; if ($r[2] !== '') $to = min($size - 1, (int) $r[2]); if ($r[1] === '' && $r[2] !== '') { $from = max(0, $size - (int) $r[2]); $to = $size - 1; }
        if ($from > $to || $from >= $size) { http_response_code(416); header("Content-Range: bytes */$size"); exit; } http_response_code(206); header("Content-Range: bytes $from-$to/$size"); }
    header('Content-Length: ' . ($to - $from + 1)); $fh = fopen($f, 'rb'); fseek($fh, $from); $left = $to - $from + 1; while ($left > 0 && !feof($fh)) { $chunk = fread($fh, min(65536, $left)); echo $chunk; $left -= strlen($chunk); } fclose($fh); exit; }
$me = ccMe(); session_write_close();
if (!$me) out(401, ['error' => 'login']);
$action = (string) ($_GET['a'] ?? '');
$key = trim((string) ccConf('TTS_KEY')); $voice = trim((string) ccConf('TTS_VOICE')); if (!preg_match('/^[a-z]{2}-[A-Z]{2}-[A-Za-z0-9-]{3,40}$/', $voice)) $voice = 'en-GB-Chirp3-HD-Charon';
$cap = (int) ccConf('TTS_CAP'); if ($cap <= 0) $cap = 900000;   /* Google's free allowance for these voices is a million characters a month */
$dir = ccDataDir() . '/tts'; $meter = $dir . '/_used.json'; $month = date('Y-m');
$used = function () use ($meter, $month): int { $j = json_decode((string) @file_get_contents($meter), true); return is_array($j) && ($j['m'] ?? '') === $month ? (int) ($j['n'] ?? 0) : 0; };
$choose = (string) ccConf('TTS_LOCK') !== '1';   /* staff may pick their own voice from the list unless the super admin has locked it */
if ($action === 'info') out(200, ['on' => $key !== '' && empty($me['demo']), 'voice' => $voice, 'voices' => TTS_VOICES, 'cat' => array_map(fn($v) => ['id' => $v[0], 'name' => $v[1], 'model' => $v[2], 'accent' => $v[3], 'sex' => $v[4]], TTS_CAT), 'choose' => $choose || $me['role'] === 'superadmin', 'used' => !empty($me['admin']) ? $used() : null, 'cap' => $cap]);
/* the super admin voices the front-page film: each caption is spoken (from the cache when it can be) and laid on a timeline at the moment it appears */
if ($action === 'film') { if ($me['role'] !== 'superadmin' || $_SERVER['REQUEST_METHOD'] !== 'POST') out(403, ['error' => 'noperm']); if ($key === '' || !function_exists('curl_init')) out(503, ['error' => 'off']);
    $fb = json_decode((string) file_get_contents('php://input', false, null, 0, 2000), true); if (is_array($fb) && isset(TTS_VOICES[(string) ($fb['voice'] ?? '')])) $voice = (string) $fb['voice'];
    $cj = json_decode((string) @file_get_contents($filmCues), true); if (!is_array($cj) || empty($cj['cues'])) out(404, ['error' => 'nofilm']);
    @set_time_limit(240); if (!is_dir($dir)) { @mkdir($dir, 0700, true); @file_put_contents($dir . '/.htaccess', "Require all denied\n"); }
    $rate = 24000; $pcm = ''; $cursor = 0; $chars = 0; $late = 0.0; $fast = 0; $cues = array_values(array_filter((array) $cj['cues'], 'is_array')); $total = (float) ($cj['dur'] ?? 0);
    $base = rtrim(defined('TTS_API_BASE') ? (string) TTS_API_BASE : 'https://texttospeech.googleapis.com', '/');
    /* one sentence at one speed: from the cache when it's there, from Google when it isn't */
    $say = function (string $text, float $speed) use ($dir, $voice, $key, $base, $rate, $used, $cap, &$chars): string {
        $file = $dir . '/' . sha1($voice . "\n" . $text . ($speed !== 1.0 ? "\n@" . number_format($speed, 2) : '')) . '.wav';
        if (is_file($file) && filesize($file) > 44) return (string) file_get_contents($file);
        if ($used() + $chars + mb_strlen($text) > $cap) out(402, ['error' => 'cap']);
        $cfg = ['audioEncoding' => 'LINEAR16', 'sampleRateHertz' => $rate]; if ($speed !== 1.0) $cfg['speakingRate'] = $speed;
        $ch = curl_init($base . '/v1/text:synthesize');
        curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 40, CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'X-Goog-Api-Key: ' . $key],
            CURLOPT_POSTFIELDS => json_encode(['input' => ['text' => $text], 'voice' => ['languageCode' => substr($voice, 0, 5), 'name' => $voice], 'audioConfig' => $cfg])]);
        $res = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch); $j = is_string($res) ? json_decode($res, true) : null; $wav = is_array($j) ? (string) base64_decode((string) ($j['audioContent'] ?? ''), true) : '';
        if ($code !== 200 || strlen($wav) < 100) { if ($speed !== 1.0) return ''; $why = is_array($j) ? (string) ($j['error']['message'] ?? '') : ''; ccErr('voice', 'Voicing the front-page film did not work. ' . ($why !== '' ? $why : 'Google answered ' . $code . '.')); out(502, ['error' => 'voice', 'why' => $why !== '' ? $why : 'Google answered ' . $code . '.']); }
        @file_put_contents($file, $wav, LOCK_EX); $chars += mb_strlen($text); return $wav; };
    $pcmOf = function (string $wav): string { $p = strpos($wav, 'data'); $d = $p !== false ? substr($wav, $p + 8) : substr($wav, 44); return strlen($d) % 2 ? substr($d, 0, -1) : $d; };
    foreach ($cues as $i => $cue) { $text = trim((string) preg_replace('/[<>{}\x00-\x1F]/u', '', (string) ($cue['text'] ?? ''))); if ($text === '' || mb_strlen($text) > 900) continue;
        $t = (float) ($cue['t'] ?? 0); $next = isset($cues[$i + 1]) ? (float) ($cues[$i + 1]['t'] ?? 0) : $total; $slot = max(1.0, $next - $t - 0.45);   /* how long this line has before the next caption appears */
        $data = $pcmOf($say($text, 1.0)); $secs = strlen($data) / 2 / $rate;
        /* too long for its caption: ask for the same line said a little faster, so the voice never falls behind the picture */
        if ($secs > $slot) { $speed = min(1.35, ceil($secs / $slot * 102) / 100); $quick = $say($text, $speed); if ($quick !== '') { $data = $pcmOf($quick); $fast++; } }
        $want = (int) round($t * $rate) + (int) ($rate * 0.2); $at = max($want, $cursor); $late = max($late, ($at - $want) / $rate);
        if (strlen($pcm) < $at * 2) $pcm .= str_repeat("\0", $at * 2 - strlen($pcm));
        $pcm .= $data; $cursor = (int) (strlen($pcm) / 2); }
    $end = (int) round((float) ($cj['dur'] ?? 0) * $rate); if (strlen($pcm) < $end * 2) $pcm .= str_repeat("\0", $end * 2 - strlen($pcm));
    if ($chars) { $fh = @fopen($meter, 'c+'); if ($fh) { flock($fh, LOCK_EX); $m = json_decode((string) stream_get_contents($fh), true); $same = is_array($m) && ($m['m'] ?? '') === $month; $m = ['m' => $month, 'n' => ($same ? (int) ($m['n'] ?? 0) : 0) + $chars, 'c' => $same ? (array) ($m['c'] ?? []) : []]; ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode($m)); fflush($fh); flock($fh, LOCK_UN); fclose($fh); } }
    if (!is_dir($filmDir)) { @mkdir($filmDir, 0755, true); }
    $wavOut = 'RIFF' . pack('V', 36 + strlen($pcm)) . 'WAVEfmt ' . pack('VvvVVvv', 16, 1, 1, $rate, $rate * 2, 2, 16) . 'data' . pack('V', strlen($pcm)) . $pcm;
    if (@file_put_contents($filmDir . '/voice.wav', $wavOut, LOCK_EX) === false) out(500, ['error' => 'write']);
    @file_put_contents($filmDir . '/voice.json', json_encode(['hash' => $filmHash(), 'voice' => $voice, 'at' => time()]), LOCK_EX);
    out(200, ['ok' => true, 'voice' => $voice, 'secs' => round(strlen($pcm) / 2 / $rate, 1), 'chars' => $chars, 'fast' => $fast, 'late' => round($late, 2)]); }
if ($action !== 'say' || $_SERVER['REQUEST_METHOD'] !== 'POST') out(404, ['error' => 'not_found']);
if (!empty($me['demo'])) out(403, ['error' => 'noperm']);
if ($key === '' || !function_exists('curl_init')) out(503, ['error' => 'off']);
$body = json_decode((string) file_get_contents('php://input', false, null, 0, 20000), true); if (!is_array($body)) out(400, ['error' => 'json']);
if (($choose || $me['role'] === 'superadmin') && isset(TTS_VOICES[(string) ($body['voice'] ?? '')])) $voice = (string) $body['voice'];   // a voice picked from the list (the super admin can always try one before saving it)
$text = trim((string) preg_replace('/\s+/u', ' ', (string) ($body['text'] ?? ''))); $text = (string) preg_replace('/[<>{}\x00-\x1F]/u', '', $text);
if ($text === '' || mb_strlen($text) > 900) out(400, ['error' => 'text']);
if (!is_dir($dir)) { @mkdir($dir, 0700, true); @file_put_contents($dir . '/.htaccess', "Require all denied\n"); }
$file = $dir . '/' . sha1($voice . "\n" . $text) . '.wav';
if (is_file($file) && filesize($file) > 44) { @touch($file); out(200, ['audio' => base64_encode((string) file_get_contents($file)), 'rate' => 24000, 'cached' => true]); }
$n = mb_strlen($text); if ($used() + $n > $cap) out(402, ['error' => 'cap']);
$base = rtrim(defined('TTS_API_BASE') ? (string) TTS_API_BASE : 'https://texttospeech.googleapis.com', '/');
$ch = curl_init($base . '/v1/text:synthesize');
curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 40, CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'X-Goog-Api-Key: ' . $key],
    CURLOPT_POSTFIELDS => json_encode(['input' => ['text' => $text], 'voice' => ['languageCode' => substr($voice, 0, 5), 'name' => $voice], 'audioConfig' => ['audioEncoding' => 'LINEAR16', 'sampleRateHertz' => 24000]])]);
$res = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); $err = curl_error($ch); curl_close($ch);
$j = is_string($res) ? json_decode($res, true) : null; $wav = is_array($j) ? base64_decode((string) ($j['audioContent'] ?? ''), true) : false;
if ($code !== 200 || !is_string($wav) || strlen($wav) < 100) { $why = is_array($j) ? (string) ($j['error']['message'] ?? '') : $err; ccErr('voice', 'The voice for video lessons did not work. ' . ($why !== '' ? $why : 'Google answered ' . $code . '.')); out(502, ['error' => 'voice', 'why' => $code === 400 || $code === 403 ? 'Google refused the key or the voice. Check Admin, System, Site setup.' : 'The voice service did not answer. Try again in a minute.']); }
@file_put_contents($file, $wav, LOCK_EX);
$fh = @fopen($meter, 'c+'); if ($fh) { flock($fh, LOCK_EX); $m = json_decode((string) stream_get_contents($fh), true); $now = is_array($m) && ($m['m'] ?? '') === $month ? (int) ($m['n'] ?? 0) : 0; ftruncate($fh, 0); rewind($fh); $by = is_array($m) && ($m['m'] ?? '') === $month ? (array) ($m['c'] ?? []) : []; $ck = (string) ($me['college'] ?? ''); if ($ck !== '' && $me['role'] !== 'superadmin') $by[$ck] = (int) ($by[$ck] ?? 0) + $n; fwrite($fh, json_encode(['m' => $month, 'n' => $now + $n, 'c' => $by])); flock($fh, LOCK_UN); fclose($fh); }
if (random_int(1, 40) === 1) { $all = glob($dir . '/*.wav') ?: []; foreach ($all as $f) if (filemtime($f) < time() - 60 * 86400) @unlink($f); }   /* sentences nobody has asked for in two months */
out(200, ['audio' => base64_encode($wav), 'rate' => 24000]);
