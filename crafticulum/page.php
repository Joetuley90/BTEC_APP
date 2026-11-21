<?php
/*
 * Crafticulum: clean addresses and search engine details for the public pages.
 *
 * .htaccess sends /, /about, /terms, /privacy, /cookies, /sitemap.xml, /robots.txt and /s/CODE here.
 * The pages themselves are still the plain .html files next to this one. This adds what a search engine
 * or a link preview needs (the full address, a share picture, structured data) using this site's own address.
 * Nothing private is used: only what is already on the public pages.
 */
declare(strict_types=1);
header('X-Content-Type-Options: nosniff');
$p = (string) ($_GET['p'] ?? '');
$dir = __DIR__;
$ready = is_file($dir . '/config.php');
if ($ready) { require $dir . '/config.php'; require $dir . '/auth-lib.php'; }

function rootPath(): string { $d = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/'))); return rtrim($d, '/') . '/'; }
function baseUrl(): string {
    global $ready;
    if ($ready) { try { $u = (string) ccSiteUrl(); if ($u !== '') return rtrim($u, '/') . '/'; } catch (Throwable $e) {} }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    return ($https ? 'https' : 'http') . '://' . preg_replace('/[^A-Za-z0-9.:\-\[\]]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost')) . rootPath();
}
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function plain(string $html): string { return trim((string) preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'))); }
function sendFile(string $f, string $type = 'text/html; charset=utf-8'): void { header('Content-Type: ' . $type); readfile($f); exit; }
function notFound(): void { http_response_code(404); header('Content-Type: text/plain; charset=utf-8'); echo 'Not found'; exit; }

/* is the front page switched on, and has anyone used the site yet? */
$front = false; $fresh = true; $pub = ['name' => '', 'contact' => ''];
if ($ready) { try { $a = ccAccounts(); $s = ccSite($a); $front = !empty($s['front']); $pub = ccSitePublic($a);
    $fresh = !array_filter((array) ($a['users'] ?? []), fn($u) => !empty($u['pass']) || !empty($u['last'])); } catch (Throwable $e) { $front = false; } }
$aboutAtRoot = true;                    // the page about Crafticulum is the site's own address. The app is at /app
/* this file is only reached through the clean addresses, so getting here proves they work: emails can then use /app */
if ($ready && strpos((string) ($_SERVER['REQUEST_URI'] ?? ''), 'page.php') === false && !is_file($dir . '/data/_pretty')) { @mkdir($dir . '/data', 0700, true); @file_put_contents($dir . '/data/_pretty', '1'); }
$base = baseUrl();
$PAGES = ['about' => 'index.html', 'contact' => 'contact.html', 'terms' => 'terms.html', 'privacy' => 'privacy.html', 'cookies' => 'cookies.html', 'dpa' => 'dpa.html', 'sample' => 'sample.html', 'security' => 'security.html', 'btec-it-lesson-plans' => 'btec-it-lesson-plans.html', 'btec-esports-lesson-plans' => 'btec-esports-lesson-plans.html', 't-level-digital-lesson-plans' => 't-level-digital-lesson-plans.html', 'gcse-computer-science-lesson-plans' => 'gcse-computer-science-lesson-plans.html', 'functional-skills-lesson-plans' => 'functional-skills-lesson-plans.html'];
$urlOf = fn(string $k) => $k === 'about' && $aboutAtRoot ? $base : $base . $k;

/* ---------- robots.txt and sitemap.xml ---------- */
if ($p === 'robots') {
    header('Content-Type: text/plain; charset=utf-8'); $r = rootPath();
    echo "User-agent: *\nDisallow: {$r}data/\nDisallow: {$r}s/\nDisallow: {$r}student.html\nDisallow: {$r}quiz/\n\nSitemap: {$base}sitemap.xml\n"; exit;
}
if ($p === 'sitemap') {
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($PAGES as $k => $f) { if (!is_file("$dir/$f")) continue; echo '  <url><loc>' . h($urlOf($k)) . '</loc><lastmod>' . date('Y-m-d', (int) filemtime("$dir/$f")) . '</lastmod>' . ($k === 'about' ? '<priority>1.0</priority>' : ($k === 'contact' ? '<priority>0.6</priority>' : '<priority>0.3</priority>')) . "</url>\n"; }
    echo "</urlset>\n"; exit;
}

/* ---------- a student's page at /s/CODE: the same page as student.html?id=CODE ---------- */
if ($p === 'student') {
    $id = preg_replace('/[^A-Za-z0-9]/', '', (string) ($_GET['id'] ?? '')); $f = "$dir/student.html";
    if ($id === '' || !is_file($f)) notFound();
    $html = (string) file_get_contents($f);
    $html = preg_replace('/<head>/i', '<head>' . "\n" . '<base href="' . h(rootPath()) . '">', $html, 1);   // so its own files are found from /s/
    header('Content-Type: text/html; charset=utf-8'); header('X-Robots-Tag: noindex'); header('Cache-Control: no-cache');
    echo $html; exit;
}

/* ---------- the front door: the page about Crafticulum. Old links that carried something for the app are sent on to it ---------- */
if ($p === 'home') {
    $extra = $_GET; unset($extra['p']);
    if (array_intersect(array_keys($extra), ['signin', 'code', 'state', 'open'])) { header('Location: ' . rootPath() . 'app' . ($extra ? '?' . http_build_query($extra) : ''), true, 302); exit; }
    if (!$ready) { header('Location: ' . rootPath() . 'app', true, 302); exit; }      // not set up yet: the app walks them through it
    $p = 'about'; $atRoot = true;
}
if (!isset($PAGES[$p]) || !is_file($dir . '/' . $PAGES[$p])) notFound();
$html = (string) file_get_contents($dir . '/' . $PAGES[$p]);
$url = $urlOf($p);
$title = preg_match('#<title>(.*?)</title>#s', $html, $m) ? plain($m[1]) : 'Crafticulum';
$desc = preg_match('#<meta name="description" content="([^"]*)"#', $html, $m) ? html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8') : '';
$org = 'Crafticulum';                       // the structured data describes Crafticulum itself. An institution's own name, logo and details are never put in it
$branded = $pub['name'] !== '';
$img = $base . 'og.jpg';

/* structured data, built from what the page itself says */
$graph = [];
$orgNode = ['@type' => 'Organization', '@id' => $base . '#org', 'name' => $org, 'url' => $base, 'logo' => ['@type' => 'ImageObject', 'url' => $base . 'icon-512.png', 'width' => 512, 'height' => 512]];
if (!$branded && !empty($pub['contact'])) $orgNode['email'] = (string) $pub['contact'];
$graph[] = $orgNode;
$graph[] = ['@type' => 'WebSite', '@id' => $base . '#site', 'name' => 'Crafticulum', 'url' => $base, 'inLanguage' => 'en-GB', 'publisher' => ['@id' => $base . '#org']];
$page = ['@type' => $p === 'contact' ? 'ContactPage' : 'WebPage', '@id' => $url . '#page', 'url' => $url, 'name' => $title, 'description' => $desc, 'inLanguage' => 'en-GB', 'isPartOf' => ['@id' => $base . '#site'], 'primaryImageOfPage' => ['@type' => 'ImageObject', 'url' => $img, 'width' => 1200, 'height' => 630]];
if ($p === 'about') {
    $features = []; if (preg_match_all('#<div class="card"><div class="ico">.*?</div><h3>(.*?)</h3>#s', $html, $mm)) foreach ($mm[1] as $t) { $t = plain($t); if ($t !== '') $features[] = $t; }
    if (!$features && preg_match('#<ul class="gets">(.*?)</ul>#s', $html, $gm) && preg_match_all('#<li><b>(.*?)</b>#s', $gm[1], $mm)) foreach ($mm[1] as $t) { $t = plain($t); if ($t !== '') $features[] = $t; }
    $app = ['@type' => 'SoftwareApplication', '@id' => $base . '#app', 'name' => 'Crafticulum', 'url' => $base, 'description' => $desc, 'image' => $img, 'applicationCategory' => 'EducationalApplication', 'operatingSystem' => 'Any, in a web browser',
            'inLanguage' => 'en-GB', 'audience' => ['@type' => 'EducationalAudience', 'educationalRole' => 'teacher'], 'provider' => ['@id' => $base . '#org']];
    if (function_exists('ccBillingOn') && ccBillingOn()) { $app['offers'] = []; foreach (ccPlans() as $pl) if ($pl['price'] >= 0) $app['offers'][] = ['@type' => 'Offer', 'name' => $pl['name'], 'price' => number_format($pl['price'] / 100, 2, '.', ''), 'priceCurrency' => 'GBP']; }
    if ($features) $app['featureList'] = $features;
    $graph[] = $app; $page['about'] = ['@id' => $base . '#app'];
    $faq = [];
    $plansOn = function_exists('ccBillingOn') && ccBillingOn();
    if (preg_match_all('#<details class="faq-i"([^>]*)>\s*<summary>(.*?)</summary>(.*?)</details>#s', $html, $mm, PREG_SET_ORDER)) foreach ($mm as $q) { if (!$plansOn && strpos($q[1], 'data-if-plans') !== false) continue; $qq = plain($q[2]); $aa = plain($q[3]); if ($qq !== '' && $aa !== '') $faq[] = ['@type' => 'Question', 'name' => $qq, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $aa]]; }
    if ($faq) $graph[] = ['@type' => 'FAQPage', '@id' => $url . '#faq', 'mainEntity' => $faq, 'isPartOf' => ['@id' => $url . '#page']];
} else {
    $short = trim(explode('·', $title)[0]);
    $graph[] = ['@type' => 'BreadcrumbList', '@id' => $url . '#crumbs', 'itemListElement' => [['@type' => 'ListItem', 'position' => 1, 'name' => 'Crafticulum', 'item' => $base], ['@type' => 'ListItem', 'position' => 2, 'name' => $short, 'item' => $url]]];
    $page['breadcrumb'] = ['@id' => $url . '#crumbs'];
}
$graph[] = $page;
$ld = json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);

$head = "\n" . '<link rel="canonical" href="' . h($url) . '">' . "\n"
    . '<meta property="og:url" content="' . h($url) . '">' . "\n"
    . '<meta property="og:image" content="' . h($img) . '">' . "\n" . '<meta property="og:image:width" content="1200">' . "\n" . '<meta property="og:image:height" content="630">' . "\n"
    . '<meta property="og:image:alt" content="Crafticulum: a full lesson from one sentence">' . "\n"
    . '<meta name="twitter:image" content="' . h($img) . '">' . "\n"
    . '<link rel="sitemap" type="application/xml" href="' . h($base . 'sitemap.xml') . '">' . "\n"
    . '<script type="application/ld+json">' . $ld . '</script>' . "\n";
/* links between the pages use the clean addresses */
$html = preg_replace_callback('#href="(terms|privacy|cookies|dpa|sample|security|btec-it-lesson-plans|btec-esports-lesson-plans|t-level-digital-lesson-plans|gcse-computer-science-lesson-plans|functional-skills-lesson-plans|contact|app)\.html([?\#][^"]*)?"#', fn($m) => 'href="' . $m[1] . ($m[2] ?? '') . '"', $html);
$html = str_replace('location.replace("app.html"+q+h)', 'location.replace("app"+q+h)', $html);
$html = preg_replace('#</head>#i', $head . '</head>', $html, 1);
header('Content-Type: text/html; charset=utf-8'); header('Cache-Control: no-cache');
echo $html;
