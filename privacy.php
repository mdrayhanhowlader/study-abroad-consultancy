<?php
require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';
$site = App\DataAdapter::site();
$page_title = 'Privacy Policy — ' . $site['brand']['full_name'];
$page_desc  = 'Privacy policy for Manami Education.';
$canonical  = $site['brand']['domain'] . '/privacy.php';
?>
<!doctype html>
<html lang="<?= h(current_lang()) ?>" dir="<?= h(\App\I18n::dir(current_lang())) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= h($page_title) ?></title>
  <meta name="description" content="<?= h($page_desc) ?>">
  <link rel="canonical" href="<?= h($canonical) ?>">
  <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
  <script>
    tailwind.config = { theme: { extend: {
      colors: { paper:'#FAF8F4','paper-2':'#F4F1EA',ink:{DEFAULT:'#1E242B',2:'#4A5159',3:'#8A857C'},hair:{DEFAULT:'#E5E1D9',2:'#D7D2C7'},brand:{DEFAULT:'#006837',dark:'#004d28',light:'#cce8d6'},navy:{DEFAULT:'#1A2B6D',dark:'#101f4f',light:'#d6dcec'},accent:{DEFAULT:'#E31E24',dark:'#a8131a',light:'#f7c8ca'},gold:'#B8923B' },
      fontFamily: { display:['"Playfair Display"','Georgia','serif'], sans:['Inter','ui-sans-serif','system-ui','sans-serif'] }
    }}}
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,500;1,600;1,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <!-- PWA -->
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="#006837">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Manami Education">
  <?php require __DIR__ . '/partials/favicon.php'; ?>
  <script defer src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js"></script>
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => navigator.serviceWorker.register('./sw.js'));
    }
  </script>
</head>
<body class="font-sans text-ink antialiased bg-paper">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <?php require __DIR__ . '/partials/header.php'; ?>

  <main class="section">
    <div class="mx-auto max-w-3xl px-4 lg:px-6">
      <div class="eyebrow" data-num="10">Legal</div>
      <h1 class="mt-4 font-display text-4xl sm:text-5xl font-bold text-ink leading-tight">Privacy Policy</h1>
      <p class="mt-3 text-ink-3 text-sm">Last updated: <?= date('F Y') ?></p>

      <div class="mt-10 prose prose-sm max-w-none text-ink-2 space-y-5 leading-relaxed">
        <p>Manami Education ("we", "us", "our") operates the website manamieducation.com.bd (the "Site"). This page informs you of our policies regarding the collection, use, and disclosure of personal information we receive from users of the Site.</p>

        <h2 class="font-display text-2xl font-bold text-ink pt-4">Information we collect</h2>
        <p>We collect personal information that you voluntarily provide when filling out forms on our Site, including: name, phone number, email address, postal address, and any information you choose to include in your message.</p>

        <h2 class="font-display text-2xl font-bold text-ink pt-4">How we use your information</h2>
        <ul class="list-disc pl-6 space-y-1">
          <li>To respond to your enquiries and provide counselling services</li>
          <li>To process orders for books, digital packs, and courses</li>
          <li>To send you updates about events and services (you can opt out at any time)</li>
          <li>To improve our Site and services</li>
        </ul>

        <h2 class="font-display text-2xl font-bold text-ink pt-4">bKash payments</h2>
        <p>All payments are processed via bKash manual verification. We do not store your bKash PIN or full account number. We retain only the bKash Transaction ID and (optionally) a screenshot of the payment for reconciliation.</p>

        <h2 class="font-display text-2xl font-bold text-ink pt-4">Cookies</h2>
        <p>The Site uses only essential cookies for session management and language preference. We do not use third-party advertising cookies.</p>

        <h2 class="font-display text-2xl font-bold text-ink pt-4">Data sharing</h2>
        <p>We do not sell, trade, or rent your personal information to third parties. We may share information only with: (a) Japanese schools, employers, or immigration authorities when you have authorised us to do so as part of your application; (b) service providers that help us operate (e.g. email delivery), bound by confidentiality.</p>

        <h2 class="font-display text-2xl font-bold text-ink pt-4">Your rights</h2>
        <p>You may request access to, correction of, or deletion of your personal data at any time by emailing <a href="mailto:<?= h($site['contact']['email']) ?>" class="text-brand"><?= h($site['contact']['email']) ?></a>.</p>

        <h2 class="font-display text-2xl font-bold text-ink pt-4">Contact</h2>
        <p>For privacy questions, write to us at:<br>
        <strong class="text-ink"><?= h($site['brand']['full_name']) ?></strong><br>
        <?= h($site['contact']['address']) ?><br>
        <a href="mailto:<?= h($site['contact']['email']) ?>" class="text-brand"><?= h($site['contact']['email']) ?></a></p>
      </div>
    </div>
  </main>

  <?php require __DIR__ . '/partials/pre-footer.php'; ?>
  <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
