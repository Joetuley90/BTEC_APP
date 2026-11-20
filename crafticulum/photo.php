<?php
/* staff photos: only for people who are signed in, never public */
declare(strict_types=1);
if (!is_file(__DIR__ . '/config.php')) { http_response_code(404); exit; }
require __DIR__ . '/config.php';
require __DIR__ . '/auth-lib.php';
ccSession(); $me = ccMe(); session_write_close();
if (!$me) { http_response_code(401); exit; }
$u = (string) ($_GET['u'] ?? ''); if (!preg_match('/^[0-9a-f]{40}$/', $u)) { http_response_code(400); exit; }
$f = ccDataDir() . '/u/' . $u . '/photo.jpg'; if (!is_file($f)) { http_response_code(404); exit; }
header('Content-Type: image/jpeg'); header('Cache-Control: private, max-age=86400'); header('X-Content-Type-Options: nosniff'); header('Content-Length: ' . filesize($f));
readfile($f);
