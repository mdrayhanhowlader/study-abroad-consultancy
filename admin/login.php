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
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign in — Manami Education Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-navy via-navy-dark to-brand flex items-center justify-center p-4 font-sans">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
    <div class="bg-navy text-white p-8 text-center">
      <div class="w-14 h-14 mx-auto rounded-xl bg-white text-navy grid place-items-center font-display font-extrabold text-2xl mb-3">M</div>
      <div class="font-display text-2xl font-bold">Manami <span class="text-accent">Education</span></div>
      <div class="text-xs tracking-[0.3em] text-white/70 uppercase mt-1">Admin Panel</div>
    </div>
    <form method="post" class="p-8 space-y-5">
      <?= App\Csrf::field() ?>
      <?php if ($error): ?>
        <div class="bg-accent/10 text-accent border border-accent/30 rounded-md px-4 py-3 text-sm"><?= h($error) ?></div>
      <?php endif; ?>
      <label class="block">
        <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Email</span>
        <input name="email" type="email" required autofocus autocomplete="username"
               class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none">
      </label>
      <label class="block">
        <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Password</span>
        <input name="password" type="password" required autocomplete="current-password"
               class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none">
      </label>
      <button type="submit" class="w-full bg-brand hover:bg-brand-dark text-white font-semibold text-sm px-6 py-3 rounded-md shadow-sm transition">
        Sign in
      </button>
      <div class="text-center text-xs text-gray-500 pt-2 border-t border-gray-100">
        Default: <code class="text-navy">admin@manami.education</code> / <code class="text-navy">admin123</code>
      </div>
    </form>
  </div>
</body>
</html>
