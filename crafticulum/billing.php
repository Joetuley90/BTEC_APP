<?php
/*
 * Crafticulum: plans and payments.
 *
 * Every institution is on a plan (Free, Teacher, Department or Institution). Paid plans are monthly subscriptions taken by Stripe:
 * this file starts a payment (in Stripe's form inside the app when the publishable key is set, on Stripe's own page when it isn't),
 * shows the card and invoices, cancels and resumes, and listens for Stripe telling it that a subscription started, changed or ended.
 * No card details ever touch this server: they are typed into Stripe's own form.
 *
 * The Stripe keys are set in Settings, Site setup (super admin). Prices and limits are set in Settings, Plans and payments.
 */
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
function out(int $code, array $data): void { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit; }
if (!is_file(__DIR__ . '/config.php')) out(500, ['error' => 'setup']);
require __DIR__ . '/config.php';
require __DIR__ . '/auth-lib.php';

$action = (string) ($_GET['a'] ?? '');
$raw = (string) file_get_contents('php://input', false, null, 0, 400000);

/* ---------- talking to Stripe ---------- */
function stripeBase(): string { return rtrim(defined('STRIPE_API_BASE') ? (string) STRIPE_API_BASE : 'https://api.stripe.com', '/'); }
function stripe(string $method, string $path, array $params = []): array {
    $key = trim((string) ccConf('STRIPE_SECRET_KEY')); if ($key === '' || !function_exists('curl_init')) return ['ok' => false, 'error' => 'Stripe is not set up.'];
    $url = stripeBase() . $path; $q = http_build_query($params);
    $ch = curl_init($method === 'GET' && $q !== '' ? $url . '?' . $q : $url);
    $opt = [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 25, CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_CUSTOMREQUEST => $method, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $key, 'Stripe-Version: 2024-06-20']];
    if ($method !== 'GET') $opt[CURLOPT_POSTFIELDS] = $q;
    curl_setopt_array($ch, $opt); $res = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); $err = curl_error($ch); curl_close($ch);
    $j = is_string($res) ? json_decode($res, true) : null;
    if ($res === false || !is_array($j)) { ccErr('payment', 'Could not reach Stripe. ' . $err); return ['ok' => false, 'error' => 'Could not reach Stripe. Try again in a minute.']; }
    if ($code >= 400) { $m = (string) ($j['error']['message'] ?? 'Stripe said no.'); ccErr('payment', 'Stripe: ' . $m); return ['ok' => false, 'error' => $m, 'code' => $code]; }
    return ['ok' => true, 'data' => $j];
}
/* what Stripe says about a subscription becomes the institution's plan */
function billApply(string $college, array $sub): bool {
    $a = ccAccounts(); if (!isset($a['colleges'][$college])) return false;
    $c = $a['colleges'][$college]; $bill = (array) ($c['bill'] ?? []); $status = (string) ($sub['status'] ?? '');
    $plan = (string) ($sub['metadata']['plan'] ?? ($bill['plan'] ?? '')); if (!in_array($plan, ['pro', 'dept'], true)) $plan = '';
    $id = (string) ($sub['id'] ?? ''); $old = (string) ($bill['sub'] ?? '');
    $live = in_array($status, ['active', 'trialing', 'past_due', 'unpaid'], true) && $plan !== '';
    if (!$live && $old !== '' && $id !== '' && $id !== $old) return true;       // news about an old subscription that has since been replaced: nothing to change
    $end = (int) ($sub['current_period_end'] ?? ($sub['items']['data'][0]['current_period_end'] ?? 0));
    $bill = ['customer' => (string) (is_array($sub['customer'] ?? null) ? ($sub['customer']['id'] ?? '') : ($sub['customer'] ?? ($bill['customer'] ?? ''))), 'sub' => $live ? $id : '', 'status' => $live ? $status : 'ended',
             'plan' => $live ? $plan : '', 'periodEnd' => $end, 'cancelAt' => !empty($sub['cancel_at_period_end']) ? $end : 0, 'at' => time()];
    if ($live) { $amt = (int) ($sub['items']['data'][0]['price']['unit_amount'] ?? 0); $bill['mrr'] = $amt > 0 ? $amt : max(0, (int) (ccPlans($a)[$plan]['price'] ?? 0)); }
    $a['colleges'][$college]['bill'] = $bill;
    $a['colleges'][$college]['plan'] = $live ? $plan : 'free';
    if (!ccSaveAccounts($a)) return false;
    try { ccMoneySync($a, $college, 'card'); } catch (\Throwable $e) {}
    if ($live && $old !== '' && $old !== $id) stripe('DELETE', '/v1/subscriptions/' . rawurlencode($old));    // they changed plan: the earlier subscription stops
    /* tell whoever manages the institution, once, when something changes */
    $pl = ccPlans($a); $name = $live ? (string) $pl[$plan]['name'] : ''; $when = $end ? date('j F Y', $end) : ''; $where = "Settings, Plan and billing:\n" . ccAppUrl();
    if ($live && $old !== $id) ccCollegeMail($a, $college, "You're on the $name plan", "Thank you. Your Crafticulum account is now on the $name plan" . ($when ? ", and the next payment is on $when" : '') . ".\n\nThis month you have " . ($pl[$plan]['builds'] > 0 ? $pl[$plan]['builds'] : 'unlimited') . " builds with Crafticulum AI and " . ($pl[$plan]['checks'] > 0 ? $pl[$plan]['checks'] : 'unlimited') . " Second Marker checks.\n\nYou can see what you've used, change your card, get your invoices or cancel at any time in $where");
    elseif ($live && $bill['cancelAt'] && empty($c['bill']['cancelAt'])) ccCollegeMail($a, $college, "Your $name plan ends on $when", "Your $name plan has been cancelled and won't renew. You keep everything it includes until $when. After that your account moves to the Free plan.\n\nNothing you've made is deleted. If you change your mind before then, you can keep the plan in $where");
    elseif (!$live && $old !== '' && ($id === '' || $id === $old)) ccCollegeMail($a, $college, 'Your plan has ended', "Your paid plan has ended and your account is now on the Free plan. Your lessons, planning and people are all still there.\n\nYou can pick a plan again at any time in $where");
    return true;
}
function collegeByCustomer(array $a, string $customer): string { if ($customer === '') return ''; foreach ((array) $a['colleges'] as $id => $c) if ((string) ($c['bill']['customer'] ?? '') === $customer) return (string) $id; return ''; }

/* ---------- Stripe tells us something happened ---------- */
if ($action === 'hook') {
    $secret = trim((string) ccConf('STRIPE_WEBHOOK_SECRET')); $sig = (string) ($_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '');
    if ($secret === '') out(400, ['error' => 'not_set_up']);
    $t = 0; $v1 = []; foreach (explode(',', $sig) as $part) { $kv = explode('=', trim($part), 2); if (count($kv) < 2) continue; if ($kv[0] === 't') $t = (int) $kv[1]; if ($kv[0] === 'v1') $v1[] = $kv[1]; }
    $want = hash_hmac('sha256', $t . '.' . $raw, $secret); $ok = false; foreach ($v1 as $v) if (hash_equals($want, $v)) $ok = true;
    if (!$ok || abs(time() - $t) > 600) { ccErr('payment', 'A message claiming to be from Stripe was refused: its signature did not match.'); out(400, ['error' => 'signature']); }
    $ev = json_decode($raw, true); if (!is_array($ev)) out(400, ['error' => 'json']);
    $type = (string) ($ev['type'] ?? ''); $o = (array) ($ev['data']['object'] ?? []); $a = ccAccounts();
    if ($type === 'checkout.session.completed') {
        $college = (string) ($o['client_reference_id'] ?? ($o['metadata']['college'] ?? '')); $sid = (string) (is_array($o['subscription'] ?? null) ? ($o['subscription']['id'] ?? '') : ($o['subscription'] ?? ''));
        if ($college !== '' && $sid !== '') { $s = stripe('GET', '/v1/subscriptions/' . rawurlencode($sid)); if ($s['ok']) billApply($college, $s['data']); }
    } elseif (in_array($type, ['customer.subscription.created', 'customer.subscription.updated', 'customer.subscription.deleted'], true)) {
        $college = (string) ($o['metadata']['college'] ?? ''); if ($college === '' || !isset($a['colleges'][$college])) $college = collegeByCustomer($a, (string) ($o['customer'] ?? ''));
        if ($college !== '') { if ($type === 'customer.subscription.deleted') $o['status'] = 'canceled'; billApply($college, $o); }
    } elseif ($type === 'invoice.payment_failed') {
        $college = collegeByCustomer($a, (string) ($o['customer'] ?? ''));
        if ($college !== '') { $tell = time() - (int) ($a['colleges'][$college]['bill']['failMail'] ?? 0) > 3 * 86400;
            $a['colleges'][$college]['bill']['status'] = 'past_due'; $a['colleges'][$college]['bill']['at'] = time(); if ($tell) $a['colleges'][$college]['bill']['failMail'] = time(); ccSaveAccounts($a);
            if ($tell) ccCollegeMail($a, $college, "Your payment didn't go through", "The latest payment for your Crafticulum plan didn't go through. It will be tried again over the next few days.\n\nTo keep your plan, check or change your card in Settings, Plan and billing:\n" . ccAppUrl() . "\n\nIf it still can't be taken, your account moves to the Free plan. Nothing you've made is deleted."); }
    }
    out(200, ['received' => true]);
}

$body = json_decode($raw !== '' ? $raw : '{}', true); if (!is_array($body)) $body = [];
$money = fn(int $p) => $p < 0 ? '' : ($p === 0 ? '£0' : '£' . (($p % 100) === 0 ? (string) intdiv($p, 100) : number_format($p / 100, 2)));
$a = ccAccounts(); $plans = ccPlans($a);
$pubPlans = function () use ($plans, $money) { $o = []; foreach ($plans as $k => $p) $o[] = ['id' => $k, 'name' => $p['name'], 'for' => $p['for'], 'price' => $p['price'], 'priceText' => $p['price'] < 0 ? 'Custom quote' : $money($p['price']), 'people' => $p['people'], 'depts' => $p['depts'], 'builds' => $p['builds'], 'checks' => $p['checks']]; return $o; };

/* ---------- anyone: what the plans are (for the front page) ---------- */
if ($action === 'plans') out(200, ['on' => ccBillingOn(), 'plans' => ccBillingOn() ? $pubPlans() : [], 'canPay' => ccStripeReady()]);

ccSession(); $me = ccMe();
if (!$me) out(401, ['error' => 'sign_in']);
if (!empty($me['demo'])) out(403, ['error' => 'demo']);
$cid = (string) $me['college']; $col = ccCollege($a, $cid);
$status = function () use (&$a, $cid, $me, $pubPlans, $money) { $c = ccCollege($a, $cid); $p = ccPlanOf($a, $cid); $b = $c['bill'];
    return ['on' => ccBillingOn(), 'canPay' => ccStripeReady(), 'college' => $c['name'], 'plan' => ['id' => $p['id'], 'name' => $p['name'], 'priceText' => $p['price'] < 0 ? 'Custom' : $money($p['price']), 'people' => $p['people'], 'depts' => $p['depts'], 'builds' => $p['builds'], 'checks' => $p['checks']],
            'used' => ccPlanUsed($cid) + ['people' => ccCollegePeople($a, $cid), 'depts' => count(ccCollegeDepts($a, $cid))], 'plans' => $pubPlans(),
            'bill' => ['status' => (string) ($b['status'] ?? ''), 'paid' => !empty($b['sub']), 'periodEnd' => (int) ($b['periodEnd'] ?? 0), 'cancelAt' => (int) ($b['cancelAt'] ?? 0), 'hasCustomer' => !empty($b['customer'])],
            'pk' => !empty($me['admin']) ? trim((string) ccConf('STRIPE_PUBLISHABLE_KEY')) : '',   // with this, paying and changing the card happen inside the app
            'canManage' => !empty($me['admin'])]; };
if ($action === 'status') { if (empty($me['isLead'])) out(403, ['error' => 'noperm']); out(200, $status()); }

/* ---------- the institution's manager: pay, or change the card ---------- */
if (in_array($action, ['checkout', 'portal', 'confirm', 'manage', 'cancel', 'resume', 'card', 'cardconfirm'], true)) {
    if (empty($me['admin'])) out(403, ['error' => 'noperm']);
    if (!ccBillingOn() || !ccStripeReady()) out(400, ['error' => 'off']);
    session_write_close();
    $app = ccAppUrl(true);
    if ($action === 'checkout') {
        $plan = (string) ($body['plan'] ?? ''); if (!in_array($plan, ['pro', 'dept'], true)) out(400, ['error' => 'plan']);
        $p = $plans[$plan]; if ($p['price'] < 30) out(400, ['error' => 'price']);
        if ($col['plan'] === $plan && !empty($col['bill']['sub'])) out(400, ['error' => 'same']);
        if ($p['people'] > 0 && ccCollegePeople($a, $cid) > $p['people']) out(400, ['error' => 'too_many', 'people' => $p['people']]);
        $inApp = !empty($body['embedded']) && trim((string) ccConf('STRIPE_PUBLISHABLE_KEY')) !== '';
        $params = ($inApp ? ['ui_mode' => 'embedded', 'redirect_on_completion' => 'if_required', 'return_url' => $app . '?billing=done&session={CHECKOUT_SESSION_ID}'] : ['success_url' => $app . '?billing=done&session={CHECKOUT_SESSION_ID}', 'cancel_url' => $app . '?billing=cancel'])
            + ['mode' => 'subscription', 'client_reference_id' => $cid, 'allow_promotion_codes' => 'true',
            'line_items' => [['quantity' => 1, 'price_data' => ['currency' => 'gbp', 'unit_amount' => $p['price'], 'recurring' => ['interval' => 'month'], 'product_data' => ['name' => 'Crafticulum ' . $p['name']]]]],
            'metadata' => ['college' => $cid, 'plan' => $plan], 'subscription_data' => ['metadata' => ['college' => $cid, 'plan' => $plan]]];
        if (!empty($col['bill']['customer'])) $params['customer'] = (string) $col['bill']['customer']; elseif (filter_var($me['email'], FILTER_VALIDATE_EMAIL)) $params['customer_email'] = $me['email'];
        $r = stripe('POST', '/v1/checkout/sessions', $params);
        if (!$r['ok'] || ($inApp ? empty($r['data']['client_secret']) : empty($r['data']['url']))) out(502, ['error' => 'stripe', 'why' => (string) ($r['error'] ?? '')]);
        ccLog($me['dept'], $me, 'started paying for the ' . $p['name'] . ' plan');
        out(200, $inApp ? ['clientSecret' => (string) $r['data']['client_secret'], 'session' => (string) $r['data']['id'], 'plan' => $p['name'], 'priceText' => $money($p['price'])] : ['url' => (string) $r['data']['url']]);
    }
    if ($action === 'portal') {
        if (empty($col['bill']['customer'])) out(400, ['error' => 'no_customer']);
        $r = stripe('POST', '/v1/billing_portal/sessions', ['customer' => (string) $col['bill']['customer'], 'return_url' => $app . '?billing=back']);
        if (!$r['ok'] || empty($r['data']['url'])) out(502, ['error' => 'stripe', 'why' => (string) ($r['error'] ?? '')]);
        out(200, ['url' => (string) $r['data']['url']]);
    }
    /* the card on file, and the invoices so far */
    $cus = (string) ($col['bill']['customer'] ?? ''); $subId = (string) ($col['bill']['sub'] ?? '');
    if ($action === 'manage') {
        if ($cus === '') out(200, ['card' => null, 'invoices' => []]);
        $card = null;
        if ($subId !== '') { $s = stripe('GET', '/v1/subscriptions/' . rawurlencode($subId), ['expand' => ['default_payment_method']]); $pm = $s['ok'] ? ($s['data']['default_payment_method'] ?? null) : null;
            if (!is_array($pm)) { $cu = stripe('GET', '/v1/customers/' . rawurlencode($cus), ['expand' => ['invoice_settings.default_payment_method']]); $pm = $cu['ok'] ? ($cu['data']['invoice_settings']['default_payment_method'] ?? null) : null; }
            if (is_array($pm) && is_array($pm['card'] ?? null)) $card = ['brand' => ucfirst((string) ($pm['card']['brand'] ?? 'Card')), 'last4' => (string) ($pm['card']['last4'] ?? ''), 'exp' => sprintf('%02d/%02d', (int) ($pm['card']['exp_month'] ?? 0), ((int) ($pm['card']['exp_year'] ?? 0)) % 100)];
            elseif (is_array($pm)) $card = ['brand' => ucfirst(str_replace('_', ' ', (string) ($pm['type'] ?? 'Saved'))), 'last4' => '', 'exp' => '']; }
        $inv = []; $l = stripe('GET', '/v1/invoices', ['customer' => $cus, 'limit' => 12]);
        if ($l['ok']) foreach ((array) ($l['data']['data'] ?? []) as $i) { if (($i['status'] ?? '') === 'draft') continue; $inv[] = ['id' => (string) ($i['id'] ?? ''), 'number' => (string) ($i['number'] ?? ''), 'at' => (int) ($i['created'] ?? 0), 'total' => $money((int) ($i['total'] ?? 0)), 'status' => (string) ($i['status'] ?? ''), 'pdf' => (string) ($i['invoice_pdf'] ?? '')]; }
        out(200, ['card' => $card, 'invoices' => $inv]);
    }
    /* stop the plan renewing, or keep it after all */
    if ($action === 'cancel' || $action === 'resume') {
        if ($subId === '') out(400, ['error' => 'no_sub']);
        $r = stripe('POST', '/v1/subscriptions/' . rawurlencode($subId), ['cancel_at_period_end' => $action === 'cancel' ? 'true' : 'false']);
        if (!$r['ok']) out(502, ['error' => 'stripe', 'why' => (string) ($r['error'] ?? '')]);
        billApply($cid, $r['data']); ccLog($me['dept'], $me, $action === 'cancel' ? 'cancelled the plan' : 'kept the plan after all');
        $a = ccAccounts(); out(200, $status());
    }
    /* a new card, typed into Stripe's form inside the app */
    if ($action === 'card') {
        if ($cus === '' || trim((string) ccConf('STRIPE_PUBLISHABLE_KEY')) === '') out(400, ['error' => 'no_customer']);
        $r = stripe('POST', '/v1/checkout/sessions', ['mode' => 'setup', 'currency' => 'gbp', 'customer' => $cus, 'ui_mode' => 'embedded', 'redirect_on_completion' => 'if_required', 'return_url' => $app . '?billing=card&session={CHECKOUT_SESSION_ID}', 'client_reference_id' => $cid, 'metadata' => ['college' => $cid, 'for' => 'card']]);
        if (!$r['ok'] || empty($r['data']['client_secret'])) out(502, ['error' => 'stripe', 'why' => (string) ($r['error'] ?? '')]);
        out(200, ['clientSecret' => (string) $r['data']['client_secret'], 'session' => (string) $r['data']['id']]);
    }
    if ($action === 'cardconfirm') {
        $sid = preg_replace('/[^A-Za-z0-9_]/', '', (string) ($body['session'] ?? '')); if ($sid === '' || $cus === '') out(400, ['error' => 'session']);
        $r = stripe('GET', '/v1/checkout/sessions/' . $sid, ['expand' => ['setup_intent']]); if (!$r['ok']) out(502, ['error' => 'stripe', 'why' => (string) ($r['error'] ?? '')]);
        $s = $r['data']; if ((string) ($s['client_reference_id'] ?? '') !== $cid || (string) ($s['status'] ?? '') !== 'complete') out(400, ['error' => 'not_done']);
        $pm = is_array($s['setup_intent'] ?? null) ? ($s['setup_intent']['payment_method'] ?? '') : ''; $pm = is_array($pm) ? (string) ($pm['id'] ?? '') : (string) $pm; if ($pm === '') out(400, ['error' => 'not_done']);
        $u = stripe('POST', '/v1/customers/' . rawurlencode($cus), ['invoice_settings' => ['default_payment_method' => $pm]]); if (!$u['ok']) out(502, ['error' => 'stripe', 'why' => (string) ($u['error'] ?? '')]);
        if ($subId !== '') { $u = stripe('POST', '/v1/subscriptions/' . rawurlencode($subId), ['default_payment_method' => $pm, 'expand' => ['latest_invoice']]);
            if ($u['ok']) { $li = $u['data']['latest_invoice'] ?? null; if (is_array($li) && ($li['status'] ?? '') === 'open' && !empty($li['id'])) { stripe('POST', '/v1/invoices/' . rawurlencode((string) $li['id']) . '/pay', ['payment_method' => $pm]); $g = stripe('GET', '/v1/subscriptions/' . rawurlencode($subId)); if ($g['ok']) billApply($cid, $g['data']); } } }   // a payment that was waiting is tried again on the new card
        ccLog($me['dept'], $me, 'changed the card'); $a = ccAccounts(); out(200, $status());
    }
    /* back from Stripe's payment page: ask Stripe what happened, so the plan is right even before its own message arrives */
    $sid = preg_replace('/[^A-Za-z0-9_]/', '', (string) ($body['session'] ?? '')); if ($sid === '') out(400, ['error' => 'session']);
    $r = stripe('GET', '/v1/checkout/sessions/' . $sid, ['expand' => ['subscription']]);
    if (!$r['ok']) out(502, ['error' => 'stripe', 'why' => (string) ($r['error'] ?? '')]);
    $s = $r['data']; if ((string) ($s['client_reference_id'] ?? '') !== $cid) out(403, ['error' => 'noperm']);
    $sub = is_array($s['subscription'] ?? null) ? $s['subscription'] : null;
    if (!$sub && is_string($s['subscription'] ?? null) && $s['subscription'] !== '') { $g = stripe('GET', '/v1/subscriptions/' . rawurlencode($s['subscription'])); if ($g['ok']) $sub = $g['data']; }
    if ($sub && in_array((string) ($s['status'] ?? ''), ['complete'], true)) { if (empty($sub['customer']) && !empty($s['customer'])) $sub['customer'] = $s['customer']; billApply($cid, $sub); ccLog($me['dept'], $me, 'moved to the ' . (ccPlans()[(string) ($sub['metadata']['plan'] ?? '')]['name'] ?? 'new') . ' plan'); }
    $a = ccAccounts(); out(200, $status());
}

/* ---------- the super admin: prices and limits ---------- */
if ($action === 'plansget' || $action === 'plansset') {
    if ($me['role'] !== 'superadmin') out(403, ['error' => 'noperm']);
    if ($action === 'plansset') {
        $in = (array) ($body['plans'] ?? []); $site = (array) ($a['site'] ?? []); $set = [];
        foreach (CC_PLANS as $k => $def) { $r = (array) ($in[$k] ?? []); $row = [];
            foreach (['price' => 10000000, 'people' => 100000, 'depts' => 10000, 'builds' => 10000000, 'checks' => 10000000] as $f => $max) { if (!array_key_exists($f, $r) || ($f === 'price' && in_array($k, ['free', 'inst'], true))) continue; $row[$f] = max(0, min($max, (int) round((float) $r[$f]))); }
            if (isset($row['price']) && $row['price'] > 0 && $row['price'] < 30) out(400, ['error' => 'price']);
            if ($row) $set[$k] = $row; }
        $site['plans'] = $set; if (array_key_exists('freeCap', $body)) $site['freeCap'] = max(0, min(100000000, (int) $body['freeCap'])); $a['site'] = $site; if (!ccSaveAccounts($a)) out(500, ['error' => 'save']);
        ccLog($me['dept'], $me, 'changed the plans');
    }
    $paying = 0; $income = 0; $now = ccPlans($a); foreach (ccCollegeIds($a) as $id) { $c = ccCollege($a, (string) $id); if (!empty($c['bill']['sub'])) { $paying++; $income += $now[$c['plan']]['price'] > 0 ? $now[$c['plan']]['price'] : 0; } }
    out(200, ['on' => ccBillingOn(), 'canPay' => ccStripeReady(), 'hookSet' => trim((string) ccConf('STRIPE_WEBHOOK_SECRET')) !== '', 'hook' => ccSiteUrl() . 'billing.php?a=hook', 'testKey' => strpos((string) ccConf('STRIPE_SECRET_KEY'), 'sk_test_') === 0, 'pk' => trim((string) ccConf('STRIPE_PUBLISHABLE_KEY')),
              'plans' => array_map(fn($k, $p) => ['id' => $k] + $p, array_keys($now), array_values($now)), 'defaults' => CC_PLANS, 'paying' => $paying, 'income' => $income, 'freeCap' => ccFreeCap($a), 'freeUsed' => (int) (ccUsageRead()['free']['calls'] ?? 0),
              'freeAccounts' => count(array_filter(ccCollegeIds($a), fn($id) => ccCollege($a, (string) $id)['plan'] === 'free')), 'mail' => ccMailReady()]);
}
out(400, ['error' => 'Unknown action.']);
