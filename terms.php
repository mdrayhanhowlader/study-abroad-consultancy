<?php
require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';
$site = App\DataAdapter::site();
$page_title = 'Terms of Service — ' . $site['brand']['full_name'];
$page_desc  = 'Terms of service for Manami Education.';
$canonical  = $site['brand']['domain'] . '/terms.php';
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
</head>
<body class="font-sans text-ink antialiased bg-paper">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <?php require __DIR__ . '/partials/header.php'; ?>

  <main class="section">
    <div class="mx-auto max-w-3xl px-4 lg:px-6">
      <div class="eyebrow" data-num="11">Legal</div>
      <h1 class="mt-4 font-display text-4xl sm:text-5xl font-bold text-ink leading-tight">Terms of Service</h1>
      <p class="mt-3 text-ink-3 text-sm">Last updated: <?= date('F Y') ?></p>

      <div class="mt-10 prose prose-sm max-w-none text-ink-2 space-y-5 leading-relaxed">
        <p>By accessing or using the Manami Education website and services, you agree to be bound by these Terms of Service.</p>

        <h2 class="font-display text-2xl font-bold text-ink pt-4">1. Services</h2>
        <p>Manami Education provides education consultancy services focused on Japan-bound pathways (Student Visa, SSW, TITP) and related products (books, digital packs, courses). Service specifics, timelines, and fees are confirmed in writing per engagement.</p>

        <h2 class="font-display text-2xl font-bold text-ink pt-4">2. Consultancy fees</h2>
        <p>Our consultancy fees and what's included are itemised in your engagement letter before any payment. We do not collect any fees from Japanese schools or employers — we work for the student.</p>

        <h2 class="font-display text-2xl font-bold text-ink pt-4">3. Refunds</h2>
        <p>Books and digital packs: refundable within 7 days of purchase if unused. Live courses: refundable up to 48 hours before the first session. Refund requests must be sent to <a href="mailto:<?= h($site['contact']['email']) ?>" class="text-brand"><?= h($site['contact']['email']) ?></a>.</p>

        <h2 class="font-display text-2xl font-bold text-ink pt-4">4. Visa outcomes</h2>
        <p>We provide end-to-end guidance and document preparation. We do not guarantee visa outcomes, as final decisions are made by Japanese immigration authorities. We will, however, support you through re-application if a visa is refused for reasons within our service scope.</p>

        <h2 class="font-display text-2xl font-bold text-ink pt-4">5. Student obligations</h2>
        <p>You agree to provide accurate, complete information and to notify us of any changes that may affect your application. Misrepresentation may result in visa refusal and is outside our service liability.</p>

        <h2 class="font-display text-2xl font-bold text-ink pt-4">6. Intellectual property</h2>
        <p>All content on this Site (text, graphics, logos, course materials) is the property of Manami Education or its licensors. You may not reproduce or distribute it without written permission.</p>

        <h2 class="font-display text-2xl font-bold text-ink pt-4">7. Limitation of liability</h2>
        <p>To the maximum extent permitted by law, Manami Education is not liable for indirect, incidental, or consequential damages arising from the use of our services.</p>

        <h2 class="font-display text-2xl font-bold text-ink pt-4">8. Governing law</h2>
        <p>These terms are governed by the laws of Bangladesh. Any disputes shall be subject to the exclusive jurisdiction of the courts of Dhaka.</p>

        <h2 class="font-display text-2xl font-bold text-ink pt-4">9. Contact</h2>
        <p><strong class="text-ink"><?= h($site['brand']['full_name']) ?></strong><br>
        <?= h($site['contact']['address']) ?><br>
        <a href="mailto:<?= h($site['contact']['email']) ?>" class="text-brand"><?= h($site['contact']['email']) ?></a></p>
      </div>
    </div>
  </main>

  <?php require __DIR__ . '/partials/pre-footer.php'; ?>
  <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
