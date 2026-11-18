<?php
/*
 * Sends Crafticulum's prompts to Crafticulum AI using the API key in config.php,
 * so the key never reaches the browser. Only heads of department and super
 * admins can use it (see auth.php). Teachers use the lessons they build.
 */
declare(strict_types=1);
if (!is_file(__DIR__ . '/config.php')) {
    http_response_code(500); header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'config.php is missing. Copy config.example.php to config.php and add your API key and password.']); exit;
}
require __DIR__ . '/config.php';
require __DIR__ . '/auth-lib.php';
ccSession();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function out(int $code, array $data): void { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(405, ['error' => 'Use POST.']);
$raw = file_get_contents('php://input', false, null, 0, 400001);
if ($raw === false || strlen($raw) > 400000) out(413, ['error' => 'Request too large.']);
$body = json_decode($raw ?: '{}', true);
if (!is_array($body)) out(400, ['error' => 'Bad request.']);

$action = $_GET['a'] ?? '';

if ($action === 'logout') { $_SESSION = []; session_destroy(); out(200, ['ok' => true]); }

$me = ccMe();
if (!$me) out(401, ['error' => 'login']);
if (!$me['canGen']) out(403, ['error' => 'noperm']);
if ($action !== 'ask') out(404, ['error' => 'Unknown action.']);

// Rate limit per signed-in session
$now = time();
$_SESSION['hits'] = array_values(array_filter($_SESSION['hits'] ?? [], fn($t) => $t > $now - 3600));
if (count($_SESSION['hits']) >= ((int) ccConf('MAX_REQUESTS_PER_HOUR') ?: 60)) out(429, ['error' => 'Too many requests this hour.']);
$_SESSION['hits'][] = $now;
session_write_close(); // don't hold the session lock while Crafticulum AI is writing

if (!empty($me['demo'])) { if (ccDemoBuildsLeft() < 1) out(402, ['error' => (int) ccConf('DEMO_BUILDS') > 0 ? 'The demo has built all the lessons it can today. Open the example lesson to see what one looks like, or come back tomorrow.' : 'Building with Crafticulum AI is switched off in the demo. Open the example lesson to see what one looks like.']); ccDemoBuilt(); }
if (($why = ccUsageBlocked($me)) !== '') out(402, ['error' => $why] + (array) ($GLOBALS['ccBlock'] ?? []));
$prompt = (string) ($body['prompt'] ?? '');
if ($prompt === '' || strlen($prompt) > 200000) out(400, ['error' => 'Empty or oversized prompt.']);
if (in_array(ccConf('ANTHROPIC_API_KEY'), ['sk-ant-REPLACE-ME', ''], true)) out(500, ['error' => 'Crafticulum AI isn\'t set up yet. The site admin adds the API key in Settings, Site setup.']);

@set_time_limit(320); @ignore_user_abort(true);   // a full lesson can take a couple of minutes
$model = ccConf('CLAUDE_MODEL') ?: 'claude-sonnet-5-5';
$ch = curl_init(defined('ANTHROPIC_URL') ? ANTHROPIC_URL : 'https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 290,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_HTTPHEADER     => [
        'content-type: application/json',
        'x-api-key: ' . ccConf('ANTHROPIC_API_KEY'),
        'anthropic-version: 2023-06-01',
    ],
    CURLOPT_POSTFIELDS     => json_encode([
        'model'      => $model,
        'max_tokens' => 16000,
        'messages'   => [['role' => 'user', 'content' => $prompt]],
    ]),
]);
$res  = curl_exec($ch);
$code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);
curl_close($ch);

if ($res === false) ccErr('claude', 'Could not reach Crafticulum AI: ' . $err, ['role' => $me['role'], 'dept' => (string) $me['deptName']]);
if ($res === false) out(502, ['error' => stripos($err, 'timed out') !== false ? 'Crafticulum AI took too long to answer. Try again in a minute.' : 'The server could not reach Crafticulum AI.' . ($me['role'] === 'superadmin' ? ' (' . $err . ') Ask your web host whether outgoing connections to api.anthropic.com are allowed.' : ' Try again in a minute, and tell whoever looks after the site if it keeps happening.')]);
$j = json_decode($res, true);
$said = is_array($j) ? trim((string) ($j['error']['message'] ?? '')) : '';
if ($code === 429) out(429, ['error' => 'Crafticulum AI is busy. Try again in a minute.']);
if ($code !== 200 || !is_array($j)) {
    // Crafticulum AI's own reason, in plain words where it is a common one
    $low = strtolower($said);
    if ($code === 401) $why = $me['role'] === 'superadmin' ? 'The AI key was not accepted. Check it was copied in full, and that it has not been switched off or replaced.' : 'Crafticulum AI isn\'t set up properly. Tell whoever looks after the site.';
    elseif (strpos($low, 'credit balance') !== false || strpos($low, 'billing') !== false) $why = $me['role'] === 'superadmin' ? 'The AI account has run out of credit. Add credit at console.anthropic.com, under Billing, then try again.' : 'Crafticulum AI can\'t build anything just now. Tell whoever looks after the site.';
    elseif ($code === 404 || strpos($low, 'model') !== false) $why = $me['role'] === 'superadmin' ? 'The AI model "' . $model . '" isn\'t known. Change the model in Settings, Site setup, or CLAUDE_MODEL in config.php.' : 'Crafticulum AI isn\'t set up properly. Tell whoever looks after the site.';
    elseif ($code === 529 || $code === 503 || strpos($low, 'overloaded') !== false) $why = 'Crafticulum AI is very busy right now. Try again in a minute.';
    elseif ($code === 413 || strpos($low, 'too long') !== false) $why = 'That was too much text for Crafticulum AI in one go.';
    else $why = 'Crafticulum AI returned an error (' . $code . ').';
    ccErr('claude', $why . ($said !== '' ? ' The AI service said: ' . mb_substr($said, 0, 240) : ''), ['role' => $me['role'], 'dept' => (string) $me['deptName']]);
    out(502, ['error' => $why . ($said !== '' && $me['role'] === 'superadmin' ? ' The AI service said: ' . mb_substr($said, 0, 240) : '')]);   // the service's own words are for the super admin only
}

$text = '';
foreach (($j['content'] ?? []) as $block) { if (($block['type'] ?? '') === 'text') $text .= $block['text']; }
ccUsageAdd($me, (int) ($j['usage']['input_tokens'] ?? 0) + (int) ($j['usage']['cache_creation_input_tokens'] ?? 0) + (int) ($j['usage']['cache_read_input_tokens'] ?? 0), (int) ($j['usage']['output_tokens'] ?? 0), $model);
out(200, ['text' => $text]);
