<?php
/*
 * Crafticulum: the money side, for the super admin.
 *
 * - The Business dashboard: monthly recurring revenue, what moved it this month, who pays what, sign-ups, who is active and what Crafticulum AI cost.
 * - Invoices for institutions that pay by bank transfer against a purchase order: a manager asks for one, the super admin issues it,
 *   and marking it paid puts the institution on its plan for the term. Each invoice has its own private address to print or save as a PDF.
 */
declare(strict_types=1);
if (!is_file(__DIR__ . '/config.php')) { http_response_code(500); exit; }
require __DIR__ . '/config.php';
require __DIR__ . '/auth-lib.php';
require __DIR__ . '/invoice-lib.php';
$action = (string) ($_GET['a'] ?? '');
$h = fn($s) => htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$invTotals = fn(array $inv): array => ccInvTotals($inv);

/* ---------- the same invoice as a PDF file, at the same private address ---------- */
if ($action === 'pdf') {
    $tok = (string) ($_GET['i'] ?? ''); $inv = null; if (preg_match('/^[a-f0-9]{32}$/', $tok)) foreach (ccInvRead() as $x) if (is_array($x) && hash_equals((string) ($x['token'] ?? ''), $tok)) { $inv = $x; break; }
    if (!$inv || in_array($inv['status'] ?? '', ['requested'], true)) { http_response_code(404); header('Content-Type: text/plain; charset=utf-8'); echo 'Invoice not found.'; exit; }
    $pdf = ccInvPdf($inv, ccInvSet(), (string) (ccSite()['contact'] ?? ''));
    header('Content-Type: application/pdf'); header('Content-Disposition: ' . (!empty($_GET['dl']) ? 'attachment' : 'inline') . '; filename="' . ccInvPdfName($inv) . '"'); header('Content-Length: ' . strlen($pdf)); header('Cache-Control: no-store'); header('X-Robots-Tag: noindex, nofollow'); echo $pdf; exit;
}
/* ---------- how many people look at the front page: counted once a day per visitor, with no cookies and nothing kept that could say who they are ---------- */
if ($action === 'hit') {
    header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store');
    $ua = (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''); if ($ua === '' || preg_match('/bot|crawl|spider|slurp|preview|monitor|curl|wget|python|headless/i', $ua)) { echo '{"ok":false}'; exit; }
    $dir = ccDataDir() . '/_visits'; if (!is_dir($dir)) { @mkdir($dir, 0700, true); @file_put_contents($dir . '/.htaccess', "Require all denied\n"); }
    $day = date('Y-m-d'); $f = "$dir/$day.json"; $key = substr(hash('sha256', $day . '|' . ccSiteId() . '|' . ($_SERVER['REMOTE_ADDR'] ?? '') . '|' . $ua), 0, 16);   // changes every day, so nobody can be followed from one day to the next
    $fh = @fopen($f, 'c+'); if ($fh && flock($fh, LOCK_EX)) { $j = json_decode((string) stream_get_contents($fh), true); $j = is_array($j) ? $j : ['n' => 0, 'seen' => []];
        if (!isset($j['seen'][$key])) { $j['seen'][$key] = 1; $j['n'] = (int) $j['n'] + 1; ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode($j)); fflush($fh); } flock($fh, LOCK_UN); fclose($fh); }
    foreach (glob("$dir/*.json") ?: [] as $old) if (basename($old, '.json') < $day) { $j = json_decode((string) @file_get_contents($old), true); if (is_array($j) && isset($j['seen'])) @file_put_contents($old, json_encode(['n' => (int) ($j['n'] ?? 0)])); }   // yesterday's list goes, only the count stays
    echo '{"ok":true}'; exit;
}
/* ---------- an invoice as a page: anyone with its private address can open and print it ---------- */
if ($action === 'view') {
    header('Content-Type: text/html; charset=utf-8'); header('Cache-Control: no-store'); header('X-Robots-Tag: noindex, nofollow'); header('X-Content-Type-Options: nosniff'); header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; img-src 'self' data:");
    $tok = (string) ($_GET['i'] ?? ''); $inv = null; if (preg_match('/^[a-f0-9]{32}$/', $tok)) foreach (ccInvRead() as $x) if (is_array($x) && hash_equals((string) ($x['token'] ?? ''), $tok)) { $inv = $x; break; }
    if (!$inv || in_array($inv['status'] ?? '', ['requested'], true)) { http_response_code(404); echo '<!doctype html><meta charset="utf-8"><title>Invoice not found</title><p style="font-family:system-ui;margin:3rem">This invoice address is not right, or the invoice has not been issued yet.</p>'; exit; }
    $set = ccInvSet(); $t = $invTotals($inv); $st = (string) $inv['status']; $due = (int) ($inv['due'] ?? 0); $over = $st === 'sent' && $due && $due < time();
    $label = $st === 'paid' ? 'Paid ' . date('j M Y', (int) ($inv['paidAt'] ?? time())) : ($st === 'void' ? 'Cancelled' : ($st === 'draft' ? 'Draft' : ($over ? 'Overdue' : 'Awaiting payment')));
    $d = fn($ts) => $ts ? date('j F Y', (int) $ts) : '';
    echo '<!doctype html><html lang="en-GB"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Invoice ' . $h($inv['number'] ?: 'draft') . ' · Crafticulum</title><style>
*{box-sizing:border-box}body{font:15px/1.5 system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;color:#1e1b4b;margin:0;background:#f5f5fb}
.sheet{max-width:820px;margin:28px auto;background:#fff;border-radius:16px;padding:44px 48px;box-shadow:0 10px 40px rgba(79,70,229,.10)}
.top{display:flex;justify-content:space-between;gap:24px;flex-wrap:wrap;border-bottom:3px solid #4f46e5;padding-bottom:22px}
.brand{font-weight:800;font-size:26px;letter-spacing:-.02em;color:#4f46e5}.brand span{color:#7c3aed}
h1{font-size:30px;margin:0 0 4px;letter-spacing:-.02em}.mut{color:#6b6b8a}.pre{white-space:pre-line}
.badge{display:inline-block;padding:4px 12px;border-radius:999px;font-weight:700;font-size:13px;background:#eef2ff;color:#4338ca}.badge.paid{background:#dcfce7;color:#166534}.badge.over{background:#fee2e2;color:#991b1b}.badge.void{background:#f3f4f6;color:#4b5563}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:28px;margin:26px 0}.k{font-size:12px;text-transform:uppercase;letter-spacing:.06em;color:#6b6b8a;font-weight:700;margin-bottom:4px}
table{width:100%;border-collapse:collapse;margin-top:8px}th{font-size:12px;text-transform:uppercase;letter-spacing:.06em;color:#6b6b8a;text-align:left;border-bottom:1px solid #e0e0f0;padding:8px 6px}td{padding:12px 6px;border-bottom:1px solid #eeeef7;vertical-align:top}
.r{text-align:right;white-space:nowrap}.tot td{border:0;padding:6px}.tot .big td{font-size:19px;font-weight:800;border-top:2px solid #1e1b4b;padding-top:12px}
.pay{margin-top:28px;padding:18px 20px;border-radius:12px;background:#f5f5ff;border:1px solid #e0e0ff}.foot{margin-top:26px;font-size:12.5px;color:#6b6b8a}
.bar{max-width:820px;margin:18px auto 0;display:flex;justify-content:flex-end;gap:10px;padding:0 8px}.bar .pdfb{font-weight:700;color:#4338ca;background:#fff;border:1px solid #c7d2fe;border-radius:10px;padding:10px 18px;text-decoration:none}.bar button{font:inherit;font-weight:700;color:#fff;background:linear-gradient(135deg,#4f46e5,#7c3aed);border:0;border-radius:10px;padding:10px 18px;cursor:pointer}
@media (max-width:640px){.sheet{margin:0;border-radius:0;padding:24px 18px}.grid{grid-template-columns:1fr}}
@media print{body{background:#fff}.sheet{box-shadow:none;margin:0;max-width:none;padding:0}.bar{display:none}}
</style></head><body><div class="bar"><a class="pdfb" href="money.php?a=pdf&amp;dl=1&amp;i=' . $h($inv['token']) . '">Download the PDF</a><button type="button" onclick="window.print()">Print</button></div><div class="sheet">
<div class="top"><div><div class="brand">craft<span>iculum</span></div><div class="mut pre" style="margin-top:8px">' . $h($set['from']) . ($set['vatNo'] !== '' ? "\nVAT number " . $h($set['vatNo']) : '') . '</div></div>
<div style="text-align:right"><h1>Invoice</h1><div style="font-weight:700">' . $h($inv['number'] ?: 'Draft') . '</div><div style="margin-top:8px"><span class="badge ' . ($st === 'paid' ? 'paid' : ($st === 'void' ? 'void' : ($over ? 'over' : ''))) . '">' . $h($label) . '</span></div></div></div>
<div class="grid"><div><div class="k">Invoice to</div><div style="font-weight:700">' . $h($inv['to']['name'] ?? '') . '</div><div class="pre">' . $h($inv['to']['address'] ?? '') . '</div><div class="mut">' . $h($inv['to']['email'] ?? '') . '</div></div>
<div><div class="k">Details</div><div>Date: <b>' . $h($d($inv['issued'] ?? 0) ?: 'Not issued yet') . '</b></div><div>Pay by: <b>' . $h($d($due)) . '</b></div>' . (($inv['po'] ?? '') !== '' ? '<div>Purchase order: <b>' . $h($inv['po']) . '</b></div>' : '') . (!empty($inv['from']) ? '<div>Covers: <b>' . $h($d($inv['from'])) . ' to ' . $h($d($inv['until'] ?? 0)) . '</b></div>' : '') . '</div></div>
<table><thead><tr><th>Description</th><th class="r">Quantity</th><th class="r">Price</th><th class="r">Amount</th></tr></thead><tbody>';
    foreach ((array) ($inv['lines'] ?? []) as $l) { $q = (float) ($l['q'] ?? 1); $u = (int) ($l['u'] ?? 0); echo '<tr><td>' . $h($l['d'] ?? '') . '</td><td class="r">' . $h(rtrim(rtrim(number_format($q, 2), '0'), '.')) . '</td><td class="r">' . ccPounds($u) . '</td><td class="r">' . ccPounds((int) round($q * $u)) . '</td></tr>'; }
    echo '</tbody></table><table class="tot" style="width:auto;margin-left:auto;min-width:280px"><tr><td>Subtotal</td><td class="r">' . ccPounds($t['net']) . '</td></tr>'
        . ((float) ($inv['vatRate'] ?? 0) > 0 ? '<tr><td>VAT at ' . $h(rtrim(rtrim(number_format((float) $inv['vatRate'], 1), '0'), '.')) . '%</td><td class="r">' . ccPounds($t['vat']) . '</td></tr>' : '')
        . '<tr class="big"><td>Total' . ($st === 'paid' ? ' paid' : ' to pay') . '</td><td class="r">' . ccPounds($t['total']) . '</td></tr></table>'
        . ($st !== 'paid' && $st !== 'void' ? '<div class="pay"><div class="k">How to pay</div><div class="pre">' . ($set['bank'] !== '' ? $h($set['bank']) : 'By bank transfer. Bank details will be sent with this invoice.') . '</div><div style="margin-top:6px">Please quote <b>' . $h($inv['number'] ?: 'the invoice number') . '</b> with your payment.</div></div>' : '')
        . (($inv['note'] ?? '') !== '' ? '<p class="pre" style="margin-top:22px">' . $h($inv['note']) . '</p>' : '')
        . '<div class="foot">' . ((float) ($inv['vatRate'] ?? 0) > 0 ? '' : 'No VAT has been charged on this invoice. ') . 'Questions about this invoice: ' . $h($set['email'] !== '' ? $set['email'] : (string) (ccSite()['contact'] ?? '')) . '</div></div></body></html>';
    exit;
}

ccSession();
header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store'); header('X-Content-Type-Options: nosniff');
function out(int $code, array $data): void { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit; }
$me = ccMe(); session_write_close();
if (!$me) out(401, ['error' => 'login']); if (!empty($me['demo'])) out(403, ['error' => 'demo']);
$body = []; if ($_SERVER['REQUEST_METHOD'] === 'POST') { $body = json_decode((string) file_get_contents('php://input', false, null, 0, 60000) ?: '{}', true); if (!is_array($body)) $body = []; }
$a = ccAccounts(); $super = $me['role'] === 'superadmin'; $cid = (string) ($me['college'] ?? '');
$line = fn($s, int $n) => mb_substr(trim((string) preg_replace('/[\x00-\x1F\x7F<>]+/u', ' ', (string) $s)), 0, $n);
$multi = fn($s, int $n) => mb_substr(trim((string) preg_replace('/[\x00-\x09\x0B-\x1F\x7F<>]+/u', '', str_replace("\r", '', (string) $s))), 0, $n);
$pub = function (array $inv) use ($invTotals, $a, $super) { $t = $invTotals($inv); $st = (string) ($inv['status'] ?? ''); $o = ['id' => $inv['id'], 'number' => (string) ($inv['number'] ?? ''), 'status' => $st, 'overdue' => $st === 'sent' && !empty($inv['due']) && $inv['due'] < time(), 'plan' => (string) ($inv['plan'] ?? ''), 'months' => (int) ($inv['months'] ?? 0),
        'po' => (string) ($inv['po'] ?? ''), 'to' => (array) ($inv['to'] ?? []), 'lines' => (array) ($inv['lines'] ?? []), 'vatRate' => (float) ($inv['vatRate'] ?? 0), 'note' => (string) ($inv['note'] ?? ''), 'issued' => (int) ($inv['issued'] ?? 0), 'due' => (int) ($inv['due'] ?? 0), 'paidAt' => (int) ($inv['paidAt'] ?? 0), 'from' => (int) ($inv['from'] ?? 0), 'until' => (int) ($inv['until'] ?? 0), 'at' => (int) ($inv['at'] ?? 0),
        'reminded' => max((int) ($inv['rem7'] ?? 0), (int) ($inv['rem14'] ?? 0)), 'reminders' => (int) !empty($inv['rem7']) + (int) !empty($inv['rem14']), 'pdf' => $st === 'requested' ? '' : str_replace('a=view', 'a=pdf', ccInvUrl($inv)), 'college' => (string) ($inv['college'] ?? ''), 'collegeName' => (string) ($a['colleges'][(string) ($inv['college'] ?? '')]['name'] ?? ''), 'url' => $st === 'requested' ? '' : ccInvUrl($inv)] + $t;
    if ($super) $o['ask'] = (string) ($inv['ask'] ?? ''); return $o; };
$sorted = function (array $all) { $all = array_values(array_filter($all, 'is_array')); usort($all, fn($x, $y) => ($y['at'] ?? 0) <=> ($x['at'] ?? 0)); return $all; };

/* ---------- an institution's manager: ask to pay by invoice, and see their invoices ---------- */
if ($action === 'mine') { if (empty($me['admin']) && !$super) out(403, ['error' => 'noperm']); out(200, ['invoices' => array_map($pub, array_values(array_filter($sorted(ccInvRead()), fn($i) => ($i['college'] ?? '') === $cid && ($i['status'] ?? '') !== 'draft' && ($i['status'] ?? '') !== 'void'))), 'on' => ccBillingOn()]); }
if ($action === 'request') {
    if (!in_array($me['role'], ['owner', 'superadmin'], true) || $cid === '' || !isset($a['colleges'][$cid])) out(403, ['error' => 'noperm']);
    if (!ccBillingOn()) out(400, ['error' => 'off']);
    $plan = in_array($body['plan'] ?? '', ['dept', 'inst'], true) ? (string) $body['plan'] : 'inst'; $months = max(1, min(36, (int) ($body['months'] ?? 12)));
    $to = ['name' => $line($body['name'] ?? '', 120), 'address' => $multi($body['address'] ?? '', 400), 'email' => strtolower($line($body['email'] ?? '', 160))];
    if ($to['name'] === '' || !filter_var($to['email'], FILTER_VALIDATE_EMAIL)) out(400, ['error' => 'details']);
    $open = array_filter(ccInvRead(), fn($i) => is_array($i) && ($i['college'] ?? '') === $cid && ($i['status'] ?? '') === 'requested'); if (count($open) >= 3) out(429, ['error' => 'waiting']);
    $pl = ccPlans($a)[$plan]; $set = ccInvSet($a); $id = bin2hex(random_bytes(6)); $unit = $pl['price'] > 0 ? (int) $pl['price'] : 0;
    $inv = ['id' => $id, 'token' => bin2hex(random_bytes(16)), 'number' => '', 'college' => $cid, 'status' => 'requested', 'plan' => $plan, 'months' => $months, 'po' => $line($body['po'] ?? '', 60), 'to' => $to,
            'lines' => [['d' => 'Crafticulum ' . $pl['name'] . ' plan, ' . $months . ($months === 1 ? ' month' : ' months'), 'q' => $months, 'u' => $unit]], 'vatRate' => $set['vat'], 'note' => '', 'ask' => $multi($body['note'] ?? '', 600), 'at' => time(), 'by' => (string) $me['email']];
    if (!ccInvChange(function ($j) use ($inv, $id) { $j[$id] = $inv; return $j; })) out(500, ['error' => 'save']);
    $cn = (string) ($a['colleges'][$cid]['name'] ?? ''); ccLog($me['dept'], $me, 'asked to pay by invoice');
    foreach (ccSupers() as $s) { ccNotify((string) $s, 'people', "Invoice asked for by $cn", 'Open Admin, Money, Invoices.'); if (ccMailReady() && filter_var($s, FILTER_VALIDATE_EMAIL)) ccMail((string) $s, "Invoice asked for by $cn", "{$me['name']} <{$me['email']}> at $cn has asked to pay by invoice.\n\nPlan: {$pl['name']}\nMonths: $months\nPurchase order: " . ($inv['po'] !== '' ? $inv['po'] : 'not given yet') . "\nInvoice to: {$to['name']} <{$to['email']}>\n" . ($inv['ask'] !== '' ? "\nThey said: {$inv['ask']}\n" : '') . "\nOpen Admin, Money, Invoices to set the price and send it.", (string) $me['email']); }
    out(200, ['ok' => true, 'invoice' => $pub($inv)]);
}

/* ---------- give a month, get a month: an institution's own link and what it has earned ---------- */
if ($action === 'refer') {
    if (!in_array($me['role'], ['owner', 'superadmin'], true) || $cid === '' || !isset($a['colleges'][$cid])) out(403, ['error' => 'noperm']);
    $had = (string) ($a['colleges'][$cid]['refCode'] ?? ''); $code = ccRefCode($a, $cid); if ($had === '') ccSaveAccounts($a);
    $signed = 0; $paying = 0; foreach ($a['colleges'] as $id => $c) if (($c['ref'] ?? '') === $cid) { $signed++; if (!empty($c['refDone'])) $paying++; }
    out(200, ['code' => $code, 'url' => ccSiteUrl() . '?ref=' . $code, 'signed' => $signed, 'paying' => $paying, 'waiting' => (int) ($a['colleges'][$cid]['credits'] ?? 0), 'used' => (int) ($a['colleges'][$cid]['creditsUsed'] ?? 0), 'on' => ccBillingOn()]);
}
if (!$super) out(403, ['error' => 'noperm']);
function mzStripeBase(): string { return rtrim(defined('STRIPE_API_BASE') ? (string) STRIPE_API_BASE : 'https://api.stripe.com', '/'); }
function mzStripe(string $method, string $path, array $params = []): array {
    $key = trim((string) ccConf('STRIPE_SECRET_KEY')); if ($key === '' || !function_exists('curl_init')) return ['ok' => false, 'error' => 'Stripe is not set up.'];
    $url = mzStripeBase() . $path; $q = http_build_query($params);
    $ch = curl_init($method === 'GET' && $q !== '' ? $url . '?' . $q : $url);
    $opt = [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 25, CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_CUSTOMREQUEST => $method, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $key, 'Stripe-Version: 2024-06-20']];
    if ($method !== 'GET') $opt[CURLOPT_POSTFIELDS] = $q;
    curl_setopt_array($ch, $opt); $res = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); $err = curl_error($ch); curl_close($ch);
    $j = is_string($res) ? json_decode($res, true) : null;
    if ($res === false || !is_array($j)) { ccErr('payment', 'Could not reach Stripe. ' . $err); return ['ok' => false, 'error' => 'Could not reach Stripe. Try again in a minute.']; }
    if ($code >= 400) { $m = (string) ($j['error']['message'] ?? 'Stripe said no.'); ccErr('payment', 'Stripe: ' . $m); return ['ok' => false, 'error' => $m, 'code' => $code]; }
    return ['ok' => true, 'data' => $j];
}

/* ---------- the super admin: card payments, refunds and credit ---------- */
if (in_array($action, ['payments', 'refund', 'credit'], true)) {
    $col = (string) ($body['college'] ?? ''); if (!isset($a['colleges'][$col])) out(404, ['error' => 'gone']); $cus = (string) ($a['colleges'][$col]['bill']['customer'] ?? ''); $cn = (string) ($a['colleges'][$col]['name'] ?? '');
    $note = function (array $row) { @file_put_contents(ccDataDir() . '/_refunds.jsonl', json_encode($row + ['t' => time()]) . "\n", FILE_APPEND | LOCK_EX); };
    if ($action === 'credit') {   // money off their next bills, or a free month banked for later
        $months = max(0, min(12, (int) ($body['months'] ?? 0))); $amt = max(0, min(10000000, (int) ($body['amount'] ?? 0)));
        if ($months > 0) { $a['colleges'][$col]['credits'] = (int) ($a['colleges'][$col]['credits'] ?? 0) + $months; if (!ccSaveAccounts($a)) out(500, ['error' => 'save']); $note(['college' => $col, 'kind' => 'months', 'months' => $months, 'by' => $me['email']]); $b = (array) ($a['colleges'][$col]['bill'] ?? []); $per = (int) ($b['mrr'] ?? 0);
            if ($cus !== '' && !empty($b['sub']) && in_array((string) ($b['status'] ?? ''), ['active', 'trialing'], true) && $per > 0) { $n = (int) $a['colleges'][$col]['credits']; for ($i = 0; $i < min($n, 12); $i++) { $r = mzStripe('POST', '/v1/customers/' . rawurlencode($cus) . '/balance_transactions', ['amount' => -$per, 'currency' => 'gbp', 'description' => 'A free month from Crafticulum']); if (!$r['ok']) break; $a['colleges'][$col]['credits']--; $a['colleges'][$col]['creditsUsed'] = (int) ($a['colleges'][$col]['creditsUsed'] ?? 0) + 1; } ccSaveAccounts($a); } ccLog($me['dept'], $me, "gave $cn $months free month" . ($months === 1 ? '' : 's')); }
        elseif ($amt > 0) { if ($cus === '') out(400, ['error' => 'nocard']); $r = mzStripe('POST', '/v1/customers/' . rawurlencode($cus) . '/balance_transactions', ['amount' => -$amt, 'currency' => 'gbp', 'description' => $line($body['note'] ?? '', 120) ?: 'Credit from Crafticulum']); if (!$r['ok']) out(502, ['error' => 'stripe', 'why' => (string) ($r['error'] ?? '')]); $note(['college' => $col, 'kind' => 'credit', 'amount' => $amt, 'by' => $me['email']]); ccLog($me['dept'], $me, "gave $cn " . ccPounds($amt) . ' credit'); }
        else out(400, ['error' => 'amount']);
    }
    if ($cus === '' && $action === 'refund') out(400, ['error' => 'nocard']);
    if ($cus === '') out(200, ['charges' => [], 'credits' => (int) ($a['colleges'][$col]['credits'] ?? 0), 'card' => false]);
    if ($action === 'refund') { $ch = (string) ($body['charge'] ?? ''); if (!preg_match('/^(ch|py)_[A-Za-z0-9_]+$/', $ch)) out(400, ['error' => 'charge']);
        $list = mzStripe('GET', '/v1/charges', ['customer' => $cus, 'limit' => 24]); $mine = null; foreach ((array) ($list['data']['data'] ?? []) as $c) if (($c['id'] ?? '') === $ch) $mine = $c; if (!$mine) out(404, ['error' => 'charge']);   // only ever a payment that belongs to this institution
        $left = (int) $mine['amount'] - (int) ($mine['amount_refunded'] ?? 0); $amt = (int) ($body['amount'] ?? 0) ?: $left; if ($amt <= 0 || $amt > $left) out(400, ['error' => 'amount']);
        $r = mzStripe('POST', '/v1/refunds', ['charge' => $ch, 'amount' => $amt, 'reason' => 'requested_by_customer']); if (!$r['ok']) out(502, ['error' => 'stripe', 'why' => (string) ($r['error'] ?? '')]);
        $note(['college' => $col, 'kind' => 'refund', 'amount' => $amt, 'charge' => $ch, 'by' => $me['email']]); ccLog($me['dept'], $me, 'refunded ' . ccPounds($amt) . " to $cn");
        ccCollegeMail($a, $col, 'Your refund is on its way', 'We have refunded ' . ccPounds($amt) . ' to the card you paid with. It usually shows within 5 to 10 working days.'); }
    $list = mzStripe('GET', '/v1/charges', ['customer' => $cus, 'limit' => 12]); if (!$list['ok']) out(502, ['error' => 'stripe', 'why' => (string) ($list['error'] ?? '')]);
    out(200, ['card' => true, 'credits' => (int) (ccAccounts()['colleges'][$col]['credits'] ?? 0), 'charges' => array_values(array_map(fn($c) => ['id' => (string) $c['id'], 'amount' => (int) $c['amount'], 'refunded' => (int) ($c['amount_refunded'] ?? 0), 'at' => (int) ($c['created'] ?? 0), 'paid' => !empty($c['paid'])], array_filter((array) ($list['data']['data'] ?? []), fn($c) => !empty($c['paid']))))]);
}
/* ---------- the super admin: invoices ---------- */
$mail = function (array $inv, string $subject, string $text, bool $pdf = false) use ($a) { $to = (string) ($inv['to']['email'] ?? ''); if (!ccMailReady() || !filter_var($to, FILTER_VALIDATE_EMAIL)) return false;
    $files = $pdf ? [['name' => ccInvPdfName($inv), 'type' => 'application/pdf', 'data' => ccInvPdf($inv, ccInvSet($a), (string) (ccSite($a)['contact'] ?? ''))]] : []; return ccMail($to, $subject, $text, ccInvSet($a)['email'], $files); };
if ($action === 'settingsset') {
    $s = (array) ($body['set'] ?? []); $site = (array) ($a['site'] ?? []); $em = strtolower($line($s['email'] ?? '', 160)); if ($em !== '' && !filter_var($em, FILTER_VALIDATE_EMAIL)) out(400, ['error' => 'email']);
    $site['invoice'] = ['from' => $multi($s['from'] ?? '', 500), 'bank' => $multi($s['bank'] ?? '', 500), 'terms' => max(0, min(120, (int) ($s['terms'] ?? 30))), 'vat' => max(0, min(30, round((float) ($s['vat'] ?? 0), 1))), 'vatNo' => $line($s['vatNo'] ?? '', 30), 'prefix' => (string) preg_replace('/[^A-Za-z0-9]/', '', $line($s['prefix'] ?? 'CR', 8)) ?: 'CR', 'email' => $em];
    if ($site['invoice']['from'] === '') unset($site['invoice']['from']);
    $a['site'] = $site; if (!ccSaveAccounts($a)) out(500, ['error' => 'save']); ccLog($me['dept'], $me, 'changed the invoice details'); $action = 'invoices';
}
if ($action === 'invoiceset') {
    $id = (string) ($body['id'] ?? ''); $new = $id === ''; $all = ccInvRead(); if (!$new && !isset($all[$id])) out(404, ['error' => 'gone']);
    $col = (string) ($new ? ($body['college'] ?? '') : $all[$id]['college']); if (!isset($a['colleges'][$col])) out(400, ['error' => 'college']);
    if (!$new && in_array($all[$id]['status'], ['paid', 'void'], true)) out(400, ['error' => 'closed']);
    $lines = []; foreach (array_slice((array) ($body['lines'] ?? []), 0, 12) as $l) { $d = $line($l['d'] ?? '', 200); $u = (int) round((float) ($l['u'] ?? 0)); $q = max(0, min(100000, round((float) ($l['q'] ?? 1), 2))); if ($d === '' && $u === 0) continue; $lines[] = ['d' => $d, 'q' => $q, 'u' => max(-100000000, min(100000000, $u))]; }
    if (!$lines) out(400, ['error' => 'lines']);
    $to = ['name' => $line($body['to']['name'] ?? '', 120), 'address' => $multi($body['to']['address'] ?? '', 400), 'email' => strtolower($line($body['to']['email'] ?? '', 160))]; if ($to['name'] === '') out(400, ['error' => 'details']); if ($to['email'] !== '' && !filter_var($to['email'], FILTER_VALIDATE_EMAIL)) out(400, ['error' => 'email']);
    $from = (int) ($body['from'] ?? 0); $months = max(1, min(36, (int) ($body['months'] ?? 12)));
    $row = ['college' => $col, 'plan' => in_array($body['plan'] ?? '', ['pro', 'dept', 'inst'], true) ? (string) $body['plan'] : 'inst', 'months' => $months, 'po' => $line($body['po'] ?? '', 60), 'to' => $to, 'lines' => $lines, 'vatRate' => max(0, min(30, round((float) ($body['vatRate'] ?? ccInvSet($a)['vat']), 1))), 'note' => $multi($body['note'] ?? '', 600), 'from' => $from > 0 ? $from : 0];
    if ($new) { $id = bin2hex(random_bytes(6)); $row += ['id' => $id, 'token' => bin2hex(random_bytes(16)), 'number' => '', 'status' => 'draft', 'at' => time(), 'by' => (string) $me['email']]; }
    if (!ccInvChange(function ($j) use ($row, $id, $new) { $old = $new ? [] : (array) ($j[$id] ?? []); $j[$id] = $row + $old; if (($j[$id]['status'] ?? '') === 'requested') $j[$id]['status'] = 'draft'; return $j; })) out(500, ['error' => 'save']);
    $action = 'invoices';
}
if (in_array($action, ['invoicesend', 'invoicepaid', 'invoicevoid', 'invoicedel'], true)) {
    $id = (string) ($body['id'] ?? ''); $all = ccInvRead(); if (!isset($all[$id]) || !is_array($all[$id])) out(404, ['error' => 'gone']); $inv = $all[$id]; $set = ccInvSet($a); $col = (string) $inv['college']; $st = (string) $inv['status'];
    if ($action === 'invoicedel') { if (!in_array($st, ['draft', 'requested'], true)) out(400, ['error' => 'issued']); ccInvChange(function ($j) use ($id) { unset($j[$id]); return $j; }); }
    if ($action === 'invoicevoid') { if ($st === 'paid') out(400, ['error' => 'closed']); ccInvChange(function ($j) use ($id) { $j[$id]['status'] = 'void'; return $j; }); }
    if ($action === 'invoicesend') {
        if (in_array($st, ['paid', 'void'], true)) out(400, ['error' => 'closed']); if ($invTotals($inv)['total'] <= 0) out(400, ['error' => 'zero']);
        $sentTo = '';
        ccInvChange(function ($j) use ($id, $set, &$inv) { $x = $j[$id];
            if (($x['number'] ?? '') === '') { $y = date('Y'); $used = []; foreach ($j as $o) if (is_array($o) && ($o['number'] ?? '') !== '') $used[(string) $o['number']] = true;   /* the end is random, so an invoice doesn't show how many have been sent before it */
                $abc = '23456789ABCDEFGHJKMNPQRSTUVWXYZ'; do { $tail = ''; for ($k = 0; $k < 6; $k++) $tail .= $abc[random_int(0, strlen($abc) - 1)]; $num = $set['prefix'] . '-' . $y . '-' . $tail; } while (isset($used[$num])); $x['number'] = $num; }
            if (empty($x['issued'])) { $x['issued'] = time(); $x['due'] = strtotime('+' . $set['terms'] . ' days', $x['issued']); }
            $x['status'] = 'sent'; $x['sentAt'] = time(); $j[$id] = $x; $inv = $x; return $j; });
        $t = $invTotals($inv);
        if ($mail($inv, "Invoice {$inv['number']} from Crafticulum", "Hello,\n\nHere is invoice {$inv['number']} for " . ccPounds($t['total']) . ', due by ' . date('j F Y', (int) $inv['due']) . '.' . ($inv['po'] !== '' ? "\nPurchase order: {$inv['po']}" : '') . "\n\nThe invoice is attached as a PDF. You can also open it online:\n" . ccInvUrl($inv) . "\n\nHow to pay is on the invoice. Please quote {$inv['number']} with your payment.\n\nThank you,\nCrafticulum", true)) $sentTo = (string) $inv['to']['email'];
        ccLog($me['dept'], $me, 'sent invoice ' . $inv['number']); $GLOBALS['sentTo'] = $sentTo;
    }
    if ($action === 'invoicepaid') {
        if ($st === 'paid') out(400, ['error' => 'closed']); if ($st === 'void') out(400, ['error' => 'closed']); if (($inv['number'] ?? '') === '') out(400, ['error' => 'notsent']);
        $t = $invTotals($inv); $months = max(1, (int) $inv['months']); $b = (array) ($a['colleges'][$col]['bill'] ?? []);
        $carry = ($b['method'] ?? '') === 'invoice' && ($b['status'] ?? '') === 'active' && (int) ($b['periodEnd'] ?? 0) > time() ? (int) $b['periodEnd'] : 0;   // paid early for the next term: it starts when this one ends
        $from = (int) ($inv['from'] ?? 0) ?: ($carry ?: time()); $until = strtotime("+$months months", $from); $paidAt = (int) ($body['paidAt'] ?? 0) ?: time();
        ccInvChange(function ($j) use ($id, $from, $until, $paidAt, &$inv) { $j[$id]['status'] = 'paid'; $j[$id]['paidAt'] = $paidAt; $j[$id]['from'] = $from; $j[$id]['until'] = $until; $inv = $j[$id]; return $j; });
        if (isset($a['colleges'][$col])) { $plan = (string) $inv['plan'];
            $a['colleges'][$col]['plan'] = $plan; $a['colleges'][$col]['bill'] = ['customer' => (string) ($b['customer'] ?? ''), 'sub' => (string) ($b['sub'] ?? ''), 'status' => 'active', 'plan' => $plan, 'method' => 'invoice', 'invoice' => $id, 'periodEnd' => $until, 'cancelAt' => 0, 'mrr' => (int) round($t['net'] / $months), 'at' => time()];
            if (!empty($b['sub'])) { $a['colleges'][$col]['bill']['sub'] = ''; }   // a card subscription they had before is left for the super admin to cancel in Stripe
            if (!ccSaveAccounts($a)) out(500, ['error' => 'save']); ccMoneySync($a, $col, 'invoice');
            $pn = ccPlans($a)[$plan]['name']; ccCollegeMail($a, $col, "Thank you: invoice {$inv['number']} is paid", "We've received payment for invoice {$inv['number']}. Your institution is on the $pn plan until " . date('j F Y', $until) . ".\n\nYour receipt is the invoice itself, now marked as paid:\n" . ccInvUrl($inv)); }
        ccLog($me['dept'], $me, 'marked invoice ' . $inv['number'] . ' as paid');
    }
    $action = 'invoices';
}
if ($action === 'invoices') { $all = array_map($pub, $sorted(ccInvRead())); $set = ccInvSet($a);
    out(200, ['invoices' => $all, 'set' => $set, 'mail' => ccMailReady(), 'sentTo' => $GLOBALS['sentTo'] ?? null, 'colleges' => array_map(fn($id) => ['id' => (string) $id, 'name' => (string) ($a['colleges'][$id]['name'] ?? ''), 'plan' => ccCollege($a, (string) $id)['plan']], ccCollegeIds($a)), 'plans' => array_map(fn($k, $p) => ['id' => $k, 'name' => $p['name'], 'price' => $p['price']], array_keys(ccPlans($a)), array_values(ccPlans($a)))]); }

/* ---------- the super admin: the business at a glance ---------- */
if ($action === 'backupcheck') out(200, ['check' => ccBackupCheck(true)]);
if ($action === 'overview') {
    ccMoneySync($a); $L = ccMoneyRead(); $now = time(); $supers = ccSupers(); $plans = ccPlans($a); $u = ccUsageRead(); $uset = ccUsageSet($a); $seen = ccSeenRead();
    $m0 = strtotime(date('Y-m-01 00:00:00')); $ids = array_map('strval', ccCollegeIds($a));
    $rows = []; $mrr = 0; $paying = 0; $risk = 0; $pastDue = 0; $pastDueN = 0; $mix = []; $free = 0;
    $tts = json_decode((string) @file_get_contents(ccDataDir() . '/tts/_used.json'), true); $ttsBy = is_array($tts) && ($tts['m'] ?? '') === date('Y-m') ? (array) ($tts['c'] ?? []) : []; $ttsAll = is_array($tts) && ($tts['m'] ?? '') === date('Y-m') ? (int) ($tts['n'] ?? 0) : 0;
    foreach ($ids as $id) { $c = ccCollege($a, $id); $b = $c['bill']; $m = ccMoneyNow($a, $id); $depts = ccCollegeDepts($a, $id); $people = 0; $last = 0; $act = 0; $mgr = '';
        foreach ($a['users'] as $e => $x) { if (in_array((string) $e, $supers, true) || !empty($x['demo']) || !ccActive($x) || !in_array((string) ($x['dept'] ?? ''), $depts, true)) continue; $people++; $s = max((int) ($seen[$e] ?? 0), (int) ($x['last'] ?? 0)); $last = max($last, $s); if ($now - $s < 30 * 86400) $act++; if ($mgr === '' && ($x['role'] ?? '') === 'owner') $mgr = (string) $e; }
        $ai = 0.0; $calls = 0; foreach ($depts as $d) { $r = (array) ($u['depts'][$d] ?? []); $ai += ccUsageCost($r, $uset); $calls += (int) ($r['calls'] ?? 0); }
        $used = ccPlanUsed($id, $u); $first = 0; foreach ($L as $e) if (($e['c'] ?? '') === $id && (int) ($e['mrr'] ?? 0) > 0) { $first = (int) $e['t']; break; }
        $method = $m > 0 ? ((($b['method'] ?? '') === 'invoice') ? 'invoice' : 'card') : '';
        $status = $m > 0 ? (($b['status'] ?? '') === 'past_due' ? 'past_due' : (!empty($b['cancelAt']) ? 'cancelling' : ($method === 'invoice' && (int) ($b['periodEnd'] ?? 0) < $now ? 'renewal_due' : 'active'))) : ($c['plan'] === 'free' ? (ccOnTrial($a['colleges'][$id] ?? []) ? 'trial' : 'free') : 'arranged');
        if ($m > 0) { $paying++; $mrr += $m; if ($status === 'cancelling' || $status === 'renewal_due') $risk += $m; if ($status === 'past_due') { $pastDue += $m; $pastDueN++; } } elseif ($c['plan'] === 'free') $free++;
        $mix[$c['plan']] = ['n' => (int) ($mix[$c['plan']]['n'] ?? 0) + 1, 'mrr' => (int) ($mix[$c['plan']]['mrr'] ?? 0) + $m];
        $rows[] = ['id' => $id, 'name' => $c['name'], 'plan' => $c['plan'], 'planName' => $plans[$c['plan']]['name'], 'mrr' => $m, 'method' => $method, 'status' => $status, 'since' => $first, 'renews' => $m > 0 ? (int) ($b['periodEnd'] ?? 0) : 0, 'created' => $c['created'], 'people' => $people, 'active' => $act, 'last' => $last, 'manager' => $mgr,
                   'card' => !empty($b['customer']), 'credits' => (int) ($a['colleges'][$id]['credits'] ?? 0), 'builds' => $used['builds'], 'buildsMax' => (int) $plans[$c['plan']]['builds'], 'checks' => $used['checks'], 'calls' => $calls, 'ai' => round($ai, 2), 'voice' => (int) ($ttsBy[$id] ?? 0), 'self' => !empty($a['colleges'][$id]['self']), 'trialEnd' => $c['trialEnd']]; }
    /* this month: what moved the monthly figure */
    $mv = ['new' => 0, 'back' => 0, 'up' => 0, 'down' => 0, 'lost' => 0, 'newN' => 0, 'lostN' => 0]; $startMrr = 0; $startN = 0;
    foreach ($ids as $id) { $was = ccMoneyLast($L, $id, $m0 - 1); $is = ccMoneyNow($a, $id); if ($was > 0) { $startMrr += $was; $startN++; } if ($was === $is) continue;
        if ($was === 0) { $ever = false; foreach ($L as $e) if (($e['c'] ?? '') === $id && (int) ($e['t'] ?? 0) < $m0 && (int) ($e['mrr'] ?? 0) > 0) $ever = true; $mv[$ever ? 'back' : 'new'] += $is; $mv['newN']++; } elseif ($is === 0) { $mv['lost'] += $was; $mv['lostN']++; } elseif ($is > $was) $mv['up'] += $is - $was; else $mv['down'] += $was - $is; }
    foreach ($L as $e) { $id = (string) ($e['c'] ?? ''); if (!in_array($id, $ids, true) && (int) ($e['t'] ?? 0) >= $m0 && ($e['k'] ?? '') === 'lost') { /* an institution removed this month */ } }
    $gone = []; foreach ($L as $e) { $id = (string) ($e['c'] ?? ''); if (!in_array($id, $ids, true)) $gone[$id] = true; }
    foreach (array_keys($gone) as $id) { $was = ccMoneyLast($L, (string) $id, $m0 - 1); if ($was > 0) { $startMrr += $was; $startN++; $mv['lost'] += $was; $mv['lostN']++; } }
    $mv['net'] = $mv['new'] + $mv['back'] + $mv['up'] - $mv['down'] - $mv['lost'];
    $churn = $startN > 0 ? round($mv['lostN'] / $startN * 100, 1) : null; $arpa = $paying > 0 ? (int) round($mrr / $paying) : 0;
    /* twelve months: the monthly figure at the end of each month, who was paying, and who signed up */
    $series = []; $allC = array_unique(array_merge($ids, array_map('strval', array_keys($gone))));
    for ($i = 11; $i >= 0; $i--) { $start = strtotime(date('Y-m-01', strtotime("-$i months", $m0))); $end = $i === 0 ? $now : strtotime('+1 month', $start) - 1; $sm = 0; $sn = 0;
        foreach ($allC as $id) { $x = $i === 0 && in_array($id, $ids, true) ? ccMoneyNow($a, $id) : ccMoneyLast($L, (string) $id, $end); if ($x > 0) { $sm += $x; $sn++; } }
        $su = 0; foreach ($ids as $id) { $cr = (int) ($a['colleges'][$id]['created'] ?? 0); if ($cr >= $start && $cr <= $end) $su++; }
        $series[] = ['ym' => date('Y-m', $start), 'label' => date('M', $start), 'mrr' => $sm, 'paying' => $sn, 'signups' => $su]; }
    /* thirty days: new accounts and new people, day by day */
    $days = []; for ($i = 29; $i >= 0; $i--) { $d0 = strtotime(date('Y-m-d 00:00:00', $now - $i * 86400)); $days[date('Y-m-d', $d0)] = ['d' => date('Y-m-d', $d0), 'label' => date('j M', $d0), 'accounts' => 0, 'people' => 0]; }
    foreach ($ids as $id) { $k = date('Y-m-d', (int) ($a['colleges'][$id]['created'] ?? 0)); if (isset($days[$k])) $days[$k]['accounts']++; }
    $pt = 0; $a7 = 0; $a30 = 0; $new30 = 0; foreach ($a['users'] as $e => $x) { if (in_array((string) $e, $supers, true) || !empty($x['demo']) || !ccActive($x)) continue; $pt++; $s = max((int) ($seen[$e] ?? 0), (int) ($x['last'] ?? 0)); if ($now - $s < 7 * 86400) $a7++; if ($now - $s < 30 * 86400) $a30++;
        $ad = (int) ($x['added'] ?? ($x['requested'] ?? 0)); $k = date('Y-m-d', $ad); if (isset($days[$k])) { $days[$k]['people']++; $new30++; } }
    $builders = 0; foreach ((array) ($u['users'] ?? []) as $e => $r) if (!in_array((string) $e, $supers, true) && (int) ($r['calls'] ?? 0) > 0) $builders++;
    $aiTotal = round(ccUsageCost((array) $u['total'], $uset), 2); $inv = array_values(array_filter(ccInvRead(), 'is_array')); $owed = 0; $owedN = 0; $overN = 0; $asked = 0; $paidMonth = 0;
    foreach ($inv as $i) { $t = $invTotals($i); if (($i['status'] ?? '') === 'sent') { $owed += $t['total']; $owedN++; if (!empty($i['due']) && $i['due'] < $now) $overN++; } if (($i['status'] ?? '') === 'requested') $asked++; if (($i['status'] ?? '') === 'paid' && (int) ($i['paidAt'] ?? 0) >= $m0) $paidMonth += $t['total']; }
    /* the next three months: what is expected if nothing changes, and how much of it needs someone to renew or is already cancelling */
    $fc = []; $renew = []; for ($i = 1; $i <= 3; $i++) { $end = strtotime("+$i months", $m0) + (int) date('t', strtotime("+$i months", $m0)) * 86400 - 1; $sure = 0; $risk = 0;
        foreach ($ids as $id) { $x = ccMoneyNow($a, $id); if ($x <= 0) continue; $b = (array) ($a['colleges'][$id]['bill'] ?? []); $inv2 = ($b['method'] ?? '') === 'invoice'; $stop = !empty($b['cancelAt']) ? (int) $b['cancelAt'] : 0;
            if ($stop && $stop <= $end) continue; if ($inv2 && (int) ($b['periodEnd'] ?? 0) <= $end) $risk += $x; elseif (($b['status'] ?? '') === 'past_due') $risk += $x; else $sure += $x; }
        $fc[] = ['label' => date('F', strtotime("+$i months", $m0)), 'sure' => $sure, 'risk' => $risk]; }
    foreach ($rows as $r) { if ($r['mrr'] > 0 && $r['renews'] > 0 && $r['renews'] < $now + 90 * 86400 && ($r['method'] === 'invoice' || $r['status'] !== 'active')) $renew[] = ['name' => $r['name'], 'at' => $r['renews'], 'mrr' => $r['mrr'], 'method' => $r['method'], 'status' => $r['status']]; }
    usort($renew, fn($x, $y) => $x['at'] <=> $y['at']);
    /* what people said about what was built for them, last 30 days */
    $fb = ['n' => 0, 'good' => 0, 'why' => [], 'recent' => []]; foreach (array_reverse(@file(ccFeedbackFile(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: []) as $l) { $j = json_decode($l, true); if (!is_array($j) || (int) ($j['t'] ?? 0) < $now - 30 * 86400) continue; $fb['n']++; if (!empty($j['good'])) $fb['good']++; else { $w = (string) ($j['why'] ?? '') ?: 'other'; $fb['why'][$w] = (int) ($fb['why'][$w] ?? 0) + 1; if (count($fb['recent']) < 8) $fb['recent'][] = ['t' => (int) $j['t'], 'why' => $w, 'note' => (string) ($j['note'] ?? ''), 'subject' => (string) ($j['subject'] ?? ''), 'level' => (string) ($j['level'] ?? ''), 'name' => (string) ($a['colleges'][(string) ($j['college'] ?? '')]['name'] ?? '')]; } }
    $fb['pct'] = $fb['n'] > 0 ? (int) round($fb['good'] / $fb['n'] * 100) : null;
    $bk = json_decode((string) @file_get_contents(ccDataDir() . '/backups/check.json'), true); $zs = array_values(array_filter(glob(ccDataDir() . '/backups/*.zip') ?: [], fn($f) => preg_match('/\/\d{4}-\d{2}-\d{2}\.zip$/', $f))); rsort($zs);
    $backup = ['last' => $zs ? (int) @filemtime($zs[0]) : 0, 'count' => count($zs), 'check' => is_array($bk) ? $bk : null];
    $refunded = 0; foreach (@file(ccDataDir() . '/_refunds.jsonl', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $l) { $j = json_decode($l, true); if (is_array($j) && ($j['kind'] ?? '') === 'refund' && (int) ($j['t'] ?? 0) >= $m0) $refunded += (int) ($j['amount'] ?? 0); }
    $refs = 0; $refPaid = 0; foreach ($a['colleges'] as $c) { if (!empty($c['ref'])) $refs++; if (!empty($c['refDone'])) $refPaid++; }
    /* from looking to paying, last 30 days: front page visitors, who started an account on their own, who built a lesson, who pays */
    $vis = 0; $visDays = []; for ($i = 29; $i >= 0; $i--) { $dk = date('Y-m-d', $now - $i * 86400); $vj = json_decode((string) @file_get_contents(ccDataDir() . "/_visits/$dk.json"), true); $vn = is_array($vj) ? (int) ($vj['n'] ?? 0) : 0; $vis += $vn; $visDays[] = $vn; }
    $fs = 0; $fb2 = 0; $fp = 0; $fpOld = 0; foreach ($ids as $id) { $c = (array) $a['colleges'][$id]; if (empty($c['self'])) continue; $paid = ccMoneyNow($a, $id) > 0;
        if ((int) ($c['created'] ?? 0) < $now - 30 * 86400) { if ($paid) $fpOld++; continue; } $fs++; $built = false; foreach (ccCollegeDepts($a, $id) as $d) if (ccDeptLessons($a, (string) $d, 1)) { $built = true; break; } if ($built) $fb2++; if ($paid) $fp++; }
    $funnel = ['visitors' => $vis, 'visitorDays' => $visDays, 'signups' => $fs, 'built' => $fb2, 'paid' => $fp, 'paidOlder' => $fpOld];
    $accounts = count($ids); $ltv = $churn !== null && $churn > 0 ? (int) round($arpa / ($churn / 100)) : null;
    out(200, ['on' => ccBillingOn(), 'cur' => '£', 'at' => $now, 'month' => date('F Y'), 'mrr' => $mrr, 'arr' => $mrr * 12, 'paying' => $paying, 'arpa' => $arpa, 'free' => $free, 'accounts' => $accounts, 'conversion' => $accounts > 0 ? round($paying / $accounts * 100, 1) : 0,
              'risk' => $risk, 'pastDue' => $pastDue, 'pastDueN' => $pastDueN, 'moves' => $mv, 'startMrr' => $startMrr, 'churn' => $churn, 'ltv' => $ltv, 'series' => $series, 'days' => array_values($days),
              'people' => ['total' => $pt, 'active7' => $a7, 'active30' => $a30, 'new30' => $new30, 'builders' => $builders], 'ai' => ['cost' => $aiTotal, 'calls' => (int) ($u['total']['calls'] ?? 0), 'rates' => $uset['in'] > 0 || $uset['out'] > 0, 'cur' => $uset['cur']], 'voice' => ['chars' => $ttsAll, 'cap' => (int) ccConf('TTS_CAP') ?: 900000, 'on' => trim((string) ccConf('TTS_KEY')) !== ''],
              'margin' => $mrr > 0 ? round(($mrr / 100 - $aiTotal) / ($mrr / 100) * 100, 1) : null, 'invoices' => ['owed' => $owed, 'owedN' => $owedN, 'overdue' => $overN, 'asked' => $asked, 'paidMonth' => $paidMonth], 'funnel' => $funnel, 'forecast' => $fc, 'renewals' => array_slice($renew, 0, 12), 'feedback' => $fb, 'backup' => $backup, 'refunded' => $refunded, 'referrals' => ['signed' => $refs, 'paying' => $refPaid], 'mix' => array_map(fn($k, $v) => ['id' => $k, 'name' => $plans[$k]['name'] ?? $k] + $v, array_keys($mix), array_values($mix)), 'rows' => $rows,
              'events' => array_map(fn($e) => $e + ['name' => (string) ($a['colleges'][(string) ($e['c'] ?? '')]['name'] ?? 'An institution that has gone')], array_slice(array_reverse($L), 0, 12))]);
}
out(400, ['error' => 'Unknown action.']);
