<?php
/* a member of staff's profile page, at /<institution>/team/<person>.
   Signed-in colleagues from the same institution see how to reach them. Anyone else sees it only if that person has switched their public profile on,
   and then only who they are and what they teach: never an email address, phone number, room or where they are today. */
declare(strict_types=1);
if (!is_file(__DIR__ . '/config.php')) { http_response_code(404); exit; }
require __DIR__ . '/config.php';
require __DIR__ . '/auth-lib.php';
ccSession(); $me = ccMe(); session_write_close();
$h = fn($s) => htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$root = rtrim(str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/'))), '/') . '/';
$cs = strtolower((string) ($_GET['c'] ?? '')); $ps = strtolower((string) ($_GET['p'] ?? ''));
$a = ccAccounts(); $col = (string) (array_search($cs, ccCollegeSlugs($a), true) ?: ''); $em = $col !== '' ? (string) (array_search($ps, ccPersonSlugs($a, $col), true) ?: '') : '';
$u = $em !== '' ? ($a['users'][$em] ?? null) : null; $inst = $col !== '' ? (string) (ccCollege($a, $col)['name'] ?? '') : '';
$ab = []; if ($u) { $ab = json_decode((string) @file_get_contents(ccDataDir() . '/u/' . sha1($em) . '/_about.json'), true); if (!is_array($ab)) $ab = [];
    $pf = (array) ($u['profile'] ?? []); if (empty($ab['headline']) && !empty($pf['job'])) $ab['headline'] = (string) $pf['job']; if (empty($ab['pronouns']) && !empty($pf['pronouns'])) $ab['pronouns'] = (string) $pf['pronouns']; if (empty($ab['campuses']) && !empty($pf['site'])) $ab['campuses'] = [(string) $pf['site']]; }
$colleague = $me && empty($me['demo']) && $u && ($me['role'] === 'superadmin' || (string) ($me['college'] ?? '') === $col);
$pub = $u && (array_key_exists('public', $ab) ? !empty($ab['public']) : !empty($a['colleges'][$col]['profilesPublic']));   /* the person's own choice first, then the institution's */
$show = $u && ($colleague || $pub);
header('Content-Type: text/html; charset=utf-8'); header('X-Robots-Tag: noindex'); header('Cache-Control: private, no-store'); if (!$show) http_response_code($u || $col !== '' ? 403 : 404);
$name = $show ? (string) (($u['name'] ?? '') ?: 'Member of staff') : ''; $title = $show ? $name . ' · ' . $inst : 'Profile';
$deptIds = $show ? array_merge([(string) ($u['dept'] ?? '')], array_map('strval', array_keys(ccAlso($a, $u)))) : []; $depts = array_values(array_filter(array_map(fn($d) => (string) ($a['depts'][$d]['name'] ?? ''), $deptIds), 'strlen'));
$roles = ['owner' => 'Institution manager', 'hod' => 'Head of department', 'teacher' => 'Teacher']; $role = $show ? ($roles[(string) ($u['role'] ?? 'teacher')] ?? 'Teacher') : '';
$photo = ''; if ($show) { $f = ccPhotoFile($em); if (is_file($f) && filesize($f) < 400000) $photo = 'data:image/jpeg;base64,' . base64_encode((string) file_get_contents($f)); }
$ini = ''; foreach (array_slice(preg_split('/\s+/', trim($name)) ?: [], 0, 2) as $w) $ini .= mb_strtoupper(mb_substr($w, 0, 1));
$tags = fn($k) => implode('', array_map(fn($x) => '<span class="tag">' . $h($x) . '</span>', array_slice(array_values(array_filter(array_map('strval', (array) ($ab[$k] ?? [])), 'strlen')), 0, 12)));
$status = ['in' => 'In today', 'class' => 'Teaching now', 'away' => 'Away', 'wfh' => 'Working from home'];
?><!doctype html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="robots" content="noindex">
<title><?= $h($title) ?> · Crafticulum</title>
<link rel="icon" href="<?= $h($root) ?>favicon.ico?v=3" sizes="any">
<link rel="icon" type="image/svg+xml" href="<?= $h($root) ?>favicon.svg?v=3">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
<style>
:root{--bg:#f8fafc;--card:#fff;--ink:#0f172a;--mute:#64748b;--line:#e2e8f0;--pri:#4f46e5;--pri-t:#4338ca;--tint:#eef2ff}
@media (prefers-color-scheme:dark){:root{--bg:#0b1020;--card:#131a2e;--ink:#e2e8f0;--mute:#94a3b8;--line:#263050;--pri:#6366f1;--pri-t:#a5b4fc;--tint:#1e2444}}
*{box-sizing:border-box}html,body{margin:0}
body{background:var(--bg);color:var(--ink);font:400 16px/1.5 "Plus Jakarta Sans",ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;min-height:100vh}
a{color:var(--pri-t)}
.top{display:flex;align-items:center;justify-content:space-between;gap:12px;max-width:860px;margin:0 auto;padding:16px 20px}
.brand{display:flex;align-items:center;gap:9px;font-weight:800;font-size:1.15rem;letter-spacing:-.02em;color:var(--ink);text-decoration:none}.brand b{color:var(--pri-t)}.brand svg{width:26px;height:26px}
.btn{display:inline-flex;align-items:center;gap:6px;min-height:40px;padding:0 14px;border-radius:6px;border:1px solid var(--line);background:var(--card);color:var(--ink);font-weight:600;font-size:.9rem;text-decoration:none}
.btn.pri{background:linear-gradient(135deg,#4f46e5,#7c3aed);border:0;color:#fff}
.wrap{max-width:860px;margin:0 auto;padding:0 20px 56px}
.card{background:var(--card);border:1px solid var(--line);border-radius:10px;overflow:hidden}
.cover{height:132px;background:linear-gradient(120deg,#4f46e5,#7c3aed 60%,#a855f7)}
.head{display:flex;flex-wrap:wrap;gap:16px 20px;align-items:flex-end;padding:0 24px 20px;margin-top:-52px}
.av{width:112px;height:112px;border-radius:50%;border:4px solid var(--card);background:var(--tint);color:var(--pri-t);display:grid;place-items:center;font-weight:800;font-size:2.2rem;overflow:hidden;flex:none}.av img{width:100%;height:100%;object-fit:cover;display:block}
h1{font-size:clamp(1.5rem,4vw,2rem);line-height:1.15;letter-spacing:-.02em;margin:0}
.sub{color:var(--mute);margin:2px 0 0}.who{flex:1 1 260px;min-width:0;padding:64px 0 4px}.head .btn{margin-bottom:6px}
.pill{display:inline-block;font-size:.76rem;font-weight:700;padding:.2rem .6rem;border-radius:99px;background:var(--tint);color:var(--pri-t);margin-left:6px;vertical-align:middle}
.body{padding:4px 24px 26px;display:grid;gap:22px}
h2{font-size:.72rem;letter-spacing:.07em;text-transform:uppercase;color:var(--mute);margin:0 0 8px;font-weight:700}
.tag{display:inline-block;margin:0 6px 6px 0;padding:.25rem .65rem;border-radius:6px;background:var(--tint);color:var(--pri-t);font-weight:600;font-size:.86rem}
.rows{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px 24px}.rows div b{display:block;font-size:.78rem;color:var(--mute);font-weight:600}
.bio{white-space:pre-line;margin:0}
.note{font-size:.84rem;color:var(--mute);margin:14px 0 0;text-align:center}
.msg{padding:44px 24px;text-align:center}.msg p{color:var(--mute);max-width:46ch;margin:8px auto 18px}
</style>
</head>
<body>
<header class="top"><a class="brand" href="<?= $h($root) ?>"><svg viewBox="0 0 100 100" aria-hidden="true"><path d="M22 8 H64 V36 H36 V64 H92 V78 Q92 92 78 92 H22 Q8 92 8 78 V22 Q8 8 22 8 Z" fill="#4f46e5"/><path d="M64 8 L92 36 H64 Z" fill="#a5b4fc"/></svg><span>craft<b>iculum</b></span></a>
  <?php if ($colleague): $self = $me['email'] === $em; $boss = $me['role'] === 'superadmin' || $me['role'] === 'owner' || (!empty($me['isLead']) && in_array((string) $me['dept'], $deptIds, true)); ?><span style="display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end"><?php if ($show && ($self || $boss)): ?><a class="btn" id="record" href="<?= $h($root) ?>our-team?who=<?= $h($ps) ?>"><?= $self ? 'Edit my profile' : 'Open their staff record' ?></a><?php endif; ?><a class="btn" id="back" href="<?= $h($root) ?>our-team">&larr; Back to the team</a></span><?php elseif (!$me): ?><a class="btn" href="<?= $h($root) ?>signin">Sign in</a><?php endif; ?></header>
<main class="wrap">
<?php if (!$show): ?>
  <div class="card msg"><h1><?= $u || $col !== '' ? 'This profile is private' : 'No profile here' ?></h1>
    <p><?= $u || $col !== '' ? ($me ? 'Only people in the same institution can see it.' : 'Sign in with your work account to see your colleagues\' profiles.') : 'Check the address and try again.' ?></p>
    <?php if (!$me): ?><a class="btn pri" href="<?= $h($root) ?>signin">Sign in</a><?php endif; ?></div>
<?php else: ?>
  <article class="card" id="profile" data-level="<?= $colleague ? 'colleague' : 'public' ?>">
    <div class="cover"></div>
    <div class="head"><div class="av"><?php if ($photo !== ''): ?><img src="<?= $h($photo) ?>" alt=""><?php else: ?><?= $h($ini) ?><?php endif; ?></div>
      <div class="who"><h1><?= $h($name) ?><?php if (!empty($ab['pronouns'])): ?><span class="pill"><?= $h($ab['pronouns']) ?></span><?php endif; ?></h1>
        <p class="sub"><?= $h(($ab['headline'] ?? '') !== '' ? $ab['headline'] : $role) ?> · <?= $h($inst) ?></p></div>
      <?php if ($colleague): ?><a class="btn pri" href="mailto:<?= $h($em) ?>">Email</a><?php endif; ?></div>
    <div class="body">
      <?php if (!empty($ab['bio'])): ?><section><h2>About</h2><p class="bio"><?= $h($ab['bio']) ?></p></section><?php endif; ?>
      <section><h2><?= count($depts) > 1 ? 'Departments' : 'Department' ?></h2><?= implode('', array_map(fn($d) => '<span class="tag">' . $h($d) . '</span>', $depts)) ?><span class="tag" style="background:none;border:1px solid var(--line);color:var(--mute)"><?= $h($role) ?></span></section>
      <?php if ($tags('skills') !== ''): ?><section><h2>Teaches and specialisms</h2><?= $tags('skills') ?></section><?php endif; ?>
      <?php if ($tags('held') !== ''): ?><section><h2>Qualifications</h2><?= $tags('held') ?></section><?php endif; ?>
      <?php if ($tags('campuses') !== ''): ?><section><h2>Campus</h2><?= $tags('campuses') ?></section><?php endif; ?>
      <?php if ($colleague): ?><section id="reach"><h2>How to reach them</h2><div class="rows">
        <div><b>Email</b><a href="mailto:<?= $h($em) ?>"><?= $h($em) ?></a></div>
        <?php if (!empty($ab['phone'])): ?><div><b>Phone</b><?= $h($ab['phone']) ?></div><?php endif; ?>
        <?php if (!empty($ab['room'])): ?><div><b>Room</b><?= $h($ab['room']) ?></div><?php endif; ?>
        <?php if (!empty($ab['hours'])): ?><div><b>Usual hours</b><?= $h($ab['hours']) ?></div><?php endif; ?>
        <?php if (!empty($status[$ab['status'] ?? ''])): ?><div><b>Today</b><?= $h($status[$ab['status']]) ?></div><?php endif; ?></div></section><?php endif; ?>
      <?php if (!empty($ab['link']) && preg_match('#^https://#i', (string) $ab['link'])): ?><section><h2>Link</h2><a href="<?= $h($ab['link']) ?>" rel="noopener nofollow" target="_blank"><?= $h($ab['link']) ?></a></section><?php endif; ?>
    </div></article>
  <p class="note"><?= $colleague ? ($pub ? 'This profile is public. People who aren\'t signed in see it without the contact details.' : 'Only people signed in at ' . $h($inst) . ' can see this page.') : 'Shared by ' . $h($name) . '. Contact details are only shown to colleagues.' ?></p>
<?php endif; ?>
</main>
</body>
</html>
