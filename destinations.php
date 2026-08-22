<?php
require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';
$site = App\DataAdapter::site();
$countries = App\DataAdapter::countries();
$page_title = t('destinations_page.headline') . ' — ' . $site['brand']['full_name'];
$page_desc  = t('destinations_page.body');
$canonical  = $site['brand']['domain'] . '/destinations.php';
?>
<!doctype html>
<html lang="<?= h(current_lang()) ?>" dir="<?= h(\App\I18n::dir(current_lang())) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= h($page_title) ?></title>
  <meta name="description" content="<?= h($page_desc) ?>">
  <link rel="canonical" href="<?= h($canonical) ?>">
  <meta property="og:title" content="<?= h($page_title) ?>">
  <meta property="og:description" content="<?= h($page_desc) ?>">
  <meta property="og:url" content="<?= h($canonical) ?>">
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
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

  <main>
    <section class="relative overflow-hidden" style="background: linear-gradient(180deg, #1E242B 0%, #1A2B6D 100%);">
      <div class="bg-orb" style="width:500px;height:500px;background:#006837;top:-100px;right:-100px;opacity:0.18"></div>
      <div class="mx-auto max-w-7xl px-4 lg:px-6 py-20 lg:py-32 relative">
        <div class="max-w-3xl">
          <div class="eyebrow" data-num="07" style="color:#B8923B"><?= h(t('destinations_page.eyebrow')) ?></div>
          <h1 class="mt-5 font-display text-5xl sm:text-6xl lg:text-7xl font-bold leading-[0.95] text-white tracking-tight"><?= h(t('destinations_page.headline')) ?></h1>
          <p class="mt-6 text-white/80 text-lg max-w-2xl"><?= h(t('destinations_page.body')) ?></p>
        </div>
      </div>
    </section>

    <section class="section bg-paper">
      <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-px bg-hair" style="background:#E5E1D9">
          <?php foreach ($countries as $i => $c):
            $num = str_pad((string)($i+1), 2, '0', STR_PAD_LEFT);
          ?>
            <a href="country/<?= h(strtolower($c['code'])) ?>" class="group block bg-white aspect-[4/5] relative overflow-hidden" style="background:#FAF8F4">
              <div class="absolute inset-0">
                <img src="https://images.unsplash.com/<?= h($c['img1']) ?>?w=800&auto=format&fit=crop&q=70" alt="<?= h($c['name']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
              </div>
              <div class="absolute inset-0 bg-gradient-to-t from-ink/85 via-ink/30 to-transparent"></div>
              <div class="absolute top-5 right-5 w-12 h-12 rounded-full overflow-hidden ring-2 ring-white shadow bg-white grid place-items-center">
                <span class="flag <?= h($c['flag']) ?>"></span>
              </div>
              <?php if (!empty($c['featured'])): ?>
                <span class="absolute top-5 left-5 bg-accent text-white text-[10px] font-bold tracking-widest uppercase px-3 py-1"><?= h(t('destinations_page.featured_tag')) ?></span>
              <?php else: ?>
                <span class="absolute top-5 left-5 bg-white/95 text-ink-2 text-[10px] font-bold tracking-widest uppercase px-3 py-1"><?= h(t('destinations_page.coming_tag')) ?></span>
              <?php endif; ?>
              <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                <div class="font-display italic text-sm text-gold mb-1" style="color:#B8923B"><?= $num ?>.</div>
                <h3 class="font-display text-2xl font-bold leading-tight"><?= h($c['name']) ?></h3>
                <p class="text-xs text-white/80 mt-1.5 line-clamp-2"><?= h($c['description'] ?? '') ?></p>
                <div class="mt-4 inline-flex items-center gap-1.5 text-[10px] uppercase tracking-widest opacity-90">
                  <?= h(t('destinations_page.explore_btn')) ?>
                  <span class="transition group-hover:translate-x-1">→</span>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section bg-paper-2" style="background:#F4F1EA">
      <div class="mx-auto max-w-4xl px-4 lg:px-6 text-center">
        <h2 class="font-display text-3xl sm:text-4xl font-bold text-ink">Not sure where to go?</h2>
        <p class="mt-4 text-ink-2">Book a free 30-minute assessment and our counsellor will match you to the right country and programme.</p>
        <a href="<?= h(url('contact.php')) ?>" class="mt-8 btn btn-primary inline-flex">Book a free assessment →</a>
      </div>
    </section>

    <?php require __DIR__ . '/partials/pre-footer.php'; ?>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
