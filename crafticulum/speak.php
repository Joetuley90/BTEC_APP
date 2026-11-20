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
const TTS_VOICES = ['en-GB-Chirp3-HD-Charon' => 'Charon (British, male)', 'en-GB-Chirp3-HD-Kore' => 'Kore (British, female)', 'en-GB-Chirp3-HD-Puck' => 'Puck (British, male, brighter)', 'en-GB-Chirp3-HD-Aoede' => 'Aoede (British, female, warmer)'];
$me = ccMe(); session_write_close();
if (!$me) out(401, ['error' => 'login']);
$action = (string) ($_GET['a'] ?? '');
$key = trim((string) ccConf('TTS_KEY')); $voice = trim((string) ccConf('TTS_VOICE')); if (!preg_match('/^[a-z]{2}-[A-Z]{2}-[A-Za-z0-9-]{3,40}$/', $voice)) $voice = 'en-GB-Chirp3-HD-Charon';
$cap = (int) ccConf('TTS_CAP'); if ($cap <= 0) $cap = 900000;   /* Google's free allowance for these voices is a million characters a month */
$dir = ccDataDir() . '/tts'; $meter = $dir . '/_used.json'; $month = date('Y-m');
$used = function () use ($meter, $month): int { $j = json_decode((string) @file_get_contents($meter), true); return is_array($j) && ($j['m'] ?? '') === $month ? (int) ($j['n'] ?? 0) : 0; };
if ($action === 'info') out(200, ['on' => $key !== '' && empty($me['demo']), 'voice' => $voice, 'voices' => TTS_VOICES, 'used' => !empty($me['admin']) ? $used() : null, 'cap' => $cap]);
if ($action !== 'say' || $_SERVER['REQUEST_METHOD'] !== 'POST') out(404, ['error' => 'not_found']);
if (!empty($me['demo'])) out(403, ['error' => 'noperm']);
if ($key === '' || !function_exists('curl_init')) out(503, ['error' => 'off']);
$body = json_decode((string) file_get_contents('php://input', false, null, 0, 20000), true); if (!is_array($body)) out(400, ['error' => 'json']);
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
$fh = @fopen($meter, 'c+'); if ($fh) { flock($fh, LOCK_EX); $m = json_decode((string) stream_get_contents($fh), true); $now = is_array($m) && ($m['m'] ?? '') === $month ? (int) ($m['n'] ?? 0) : 0; ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode(['m' => $month, 'n' => $now + $n])); flock($fh, LOCK_UN); fclose($fh); }
if (random_int(1, 40) === 1) { $all = glob($dir . '/*.wav') ?: []; foreach ($all as $f) if (filemtime($f) < time() - 60 * 86400) @unlink($f); }   /* sentences nobody has asked for in two months */
out(200, ['audio' => base64_encode($wav), 'rate' => 24000]);
