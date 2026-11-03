<?php
require_once __DIR__ . '/../app/bootstrap.php';
if (App\Auth::check()) {
    redirect('/admin/dashboard.php');
}

$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    $email = trim((string) ($_POST['email'] ?? ''));
    $pw    = (string) ($_POST['password'] ?? '');
    $r = App\Auth::attempt($email, $pw);
    if ($r['ok']) {
        if (!empty($r['user']['must_change_pw'])) {
            redirect('/admin/settings.php?tab=password&first=1');
        }
        redirect('/admin/dashboard.php');
    }
    $error = $r['message'];
}
$site = App\DataAdapter::site();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('X-Robots-Tag: noindex, nofollow');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#f5f7f4">
  <title>Admin sign in — <?= h($site['brand']['full_name']) ?></title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
  <style>
    :root { color-scheme: light; font-family: Inter, ui-sans-serif, system-ui, sans-serif; color: #1e2924; background: #f5f7f4; }
    * { box-sizing: border-box; }
    body { min-height: 100vh; margin: 0; display: grid; place-items: center; padding: 24px; background: radial-gradient(ellipse at 15% 10%, #e2f1e7 0, transparent 40%), radial-gradient(ellipse at 90% 90%, #e9efe7 0, transparent 36%), #f5f7f4; }
    .login-shell { width: min(100%, 460px); }
    .brand { display: flex; align-items: center; justify-content: center; min-height: 76px; margin-bottom: 22px; }
    .brand img { display: block; width: auto; height: auto; max-width: min(260px, 75vw); max-height: 72px; object-fit: contain; }
    .brand-fallback { color: #143d2b; text-align: center; }
    .brand-name { font-family: "Playfair Display", Georgia, serif; font-size: 27px; font-weight: 700; letter-spacing: -.04em; }
    .brand-tagline { margin-top: 3px; color: #738177; font-size: 10px; font-weight: 700; letter-spacing: .24em; text-transform: uppercase; }
    .card { overflow: hidden; border: 1px solid #e3e9e3; border-radius: 20px; background: #fff; box-shadow: 0 24px 70px -32px rgba(28, 60, 39, .25), 0 4px 14px rgba(28, 60, 39, .05); }
    .card-head { padding: 30px 36px 0; }
    .eyebrow { display: flex; align-items: center; gap: 9px; color: #52705e; font-size: 10px; font-weight: 700; letter-spacing: .19em; text-transform: uppercase; }
    .eyebrow::before { width: 20px; height: 1px; background: #93b39b; content: ""; }
    h1 { margin: 13px 0 7px; color: #183426; font-family: "Playfair Display", Georgia, serif; font-size: clamp(28px, 7vw, 34px); font-weight: 700; letter-spacing: -.035em; }
    .intro { margin: 0; color: #758078; font-size: 13px; line-height: 1.65; }
    form { display: grid; gap: 18px; padding: 26px 36px 34px; }
    .field { display: grid; gap: 8px; }
    label { color: #39463e; font-size: 12px; font-weight: 600; }
    input { width: 100%; height: 48px; border: 1px solid #dce4dd; border-radius: 9px; outline: none; background: #fff; padding: 0 14px; color: #1e2924; font: inherit; font-size: 14px; transition: border-color .18s, box-shadow .18s; }
    input::placeholder { color: #a2aaa4; }
    input:focus { border-color: #16824d; box-shadow: 0 0 0 3px rgba(0, 104, 55, .12); }
    .error { border: 1px solid #f1c9c6; border-radius: 9px; background: #fff5f4; padding: 11px 13px; color: #a3322b; font-size: 12px; line-height: 1.55; }
    button { width: 100%; min-height: 49px; display: flex; align-items: center; justify-content: center; gap: 9px; border: 0; border-radius: 9px; background: #006837; color: #fff; cursor: pointer; font: inherit; font-size: 13px; font-weight: 700; box-shadow: 0 5px 12px rgba(0, 104, 55, .18); transition: background .18s, transform .18s, box-shadow .18s; }
    button:hover { transform: translateY(-1px); background: #004d28; box-shadow: 0 8px 18px rgba(0, 77, 40, .22); }
    button:focus-visible { outline: 3px solid #9dc8a8; outline-offset: 3px; }
    button svg { width: 16px; height: 16px; }
    .footnote { padding: 15px 24px; border-top: 1px solid #edf0ed; color: #859087; text-align: center; font-size: 11px; }
    @media (max-width: 480px) { body { padding: 18px; } .card-head { padding: 25px 24px 0; } form { padding: 22px 24px 27px; } }
  </style>
</head>
<body>
  <main class="login-shell">
    <div class="brand">
      <?php if (!empty($site['brand']['logo_url'])): ?>
        <img src="<?= h($site['brand']['logo_url']) ?>" alt="<?= h($site['brand']['full_name']) ?>">
      <?php else: ?>
        <div class="brand-fallback">
          <div class="brand-name"><?= h($site['brand']['name']) ?> <span style="color:#006837"><?= h($site['brand']['tagline']) ?></span></div>
          <div class="brand-tagline">Secure administration</div>
        </div>
      <?php endif; ?>
    </div>
    <section class="card" aria-labelledby="login-title">
      <div class="card-head">
        <div class="eyebrow">Authorized access</div>
        <h1 id="login-title">Welcome back</h1>
        <p class="intro">Sign in with your administrator account to continue.</p>
      </div>
      <form method="post" action="" autocomplete="on">
        <?= App\Csrf::field() ?>
        <?php if ($error): ?>
          <div class="error" role="alert"><?= h($error) ?></div>
        <?php endif; ?>
        <div class="field">
          <label for="email">Email address</label>
          <input id="email" name="email" type="email" required autofocus autocomplete="username" inputmode="email" placeholder="you@example.com" value="<?= h($email ?? '') ?>">
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Enter your password">
        </div>
        <button type="submit">
          <span>Sign in securely</span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </button>
      </form>
      <div class="footnote">Access is limited to authorized administrators.</div>
    </section>
  </main>
</body>
</html>
