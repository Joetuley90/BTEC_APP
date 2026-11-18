<?php
/*
 * Crafticulum: first-time setup.
 *
 * Runs once, on a new site that has no config.php yet. It makes the first super admin's account and writes config.php.
 * Once config.php exists this file refuses to do anything, so it can't be used to take a site over.
 * Everything else (Claude's API key, Google and Microsoft sign-in, email) is then set on screen in Settings, Site setup.
 */
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
function out(int $code, array $data): void { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit; }
$cfg = __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(405, ['error' => 'Use POST.']);
if (is_file($cfg)) out(403, ['error' => 'done']);
$body = json_decode((string) file_get_contents('php://input', false, null, 0, 20001), true);
if (($_GET['a'] ?? '') === 'check') out(200, ['needed' => true, 'writable' => is_writable(__DIR__), 'php' => PHP_VERSION, 'curl' => function_exists('curl_init')]);
if (!is_array($body)) out(400, ['error' => 'Bad request.']);
$clean = fn(string $s, int $n) => mb_substr(trim((string) preg_replace('/\s+/u', ' ', (string) preg_replace('/[\x00-\x1F\x7F<>]+/u', '', $s))), 0, $n);
$email = strtolower(trim((string) ($body['email'] ?? ''))); $first = $clean((string) ($body['first'] ?? ''), 40); $last = $clean((string) ($body['last'] ?? ''), 40);
$pass = (string) ($body['password'] ?? ''); $college = $clean((string) ($body['college'] ?? ''), 60); $dept = $clean((string) ($body['dept'] ?? ''), 60); $key = trim((string) ($body['apiKey'] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 120) out(400, ['error' => 'email']);
if ($first === '') out(400, ['error' => 'name']);
if (strlen($pass) < 10 || strlen($pass) > 200) out(400, ['error' => 'pass_short']);
if ($key !== '' && !preg_match('/^[A-Za-z0-9_\-]{20,300}$/', $key)) out(400, ['error' => 'key']);
if (!is_writable(__DIR__)) out(500, ['error' => 'folder']);
$php = "<?php\n/*\n * Crafticulum settings. Made by the first-time setup on " . date('j M Y') . ".\n * The super admin is set here. Everything else is changed on screen in Settings, Site setup.\n */\n"
     . "const SUPER_ADMINS = [" . var_export($email, true) . "];\n"
     . "const TEACHER_PASSWORD = " . var_export(bin2hex(random_bytes(16)), true) . ";   // not used to sign in\n"
     . "const PASSWORD_BACKUP = false;\n";
$fh = @fopen($cfg, 'x');   // fails if someone else got there first
if (!$fh) out(403, ['error' => 'done']);
fwrite($fh, $php); fclose($fh); @chmod($cfg, 0600);
require $cfg; require __DIR__ . '/auth-lib.php';
ccEnsureData();
$a = ccAccounts();
$id = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($dept !== '' ? $dept : 'main')), '-') ?: 'main'; $id = substr($id, 0, 24);
$a['depts'] = [$id => ['name' => $dept !== '' ? $dept : 'My department', 'created' => time(), 'college' => 'main'] + ($id === 'main' ? ['legacy' => true] : [])];
$a['colleges'] = ['main' => ['name' => $college, 'plain' => true, 'created' => time()]];
$a['users'][$email] = ['name' => trim($first . ' ' . $last), 'profile' => ['first' => $first, 'last' => $last], 'status' => 'active', 'verified' => true, 'role' => 'superadmin', 'dept' => $id,
    'pass' => password_hash($pass, PASSWORD_DEFAULT), 'methods' => ['password'], 'added' => time(), 'last' => time()];
$a['site'] = ['name' => $college, 'plain' => true, 'conf' => $key !== '' ? ['ANTHROPIC_API_KEY' => $key] : []];
if (!ccSaveAccounts($a)) { @unlink($cfg); out(500, ['error' => 'data']); }
ccSession(); session_regenerate_id(false); $_SESSION = ['uid' => $email, 'teacher' => true, 'hits' => [], 'dept' => $id];
ccLog($id, ['name' => trim($first . ' ' . $last), 'email' => $email], 'set up Crafticulum');
out(200, ['ok' => true]);
