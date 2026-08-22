<?php
require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';
$site = App\DataAdapter::site();
$faqs = App\DataAdapter::faqs();
$page_title = t('nav.faq') . ' — ' . $site['brand']['full_name'];
$page_desc  = 'Answers to the most frequently asked questions about studying in Japan, SSW, TITP, and Manami Education services.';
$canonical  = $site['brand']['domain'] . '/faq.php';
?>
<!doctype html>
<html lang="<?= h(current_lang()) ?>" dir="<?= h(\App\I18n::dir(current_lang())) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= h($page_title) ?></title>
  <meta name="description" content="<?= h($page_desc) ?>">
  <link rel="canonical" href="<?= h($canonical) ?>">
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
          <div class="eyebrow" data-num="09" style="color:#B8923B"><?= h(t('nav.faq')) ?></div>
          <h1 class="mt-5 font-display text-5xl sm:text-6xl lg:text-7xl font-bold leading-[0.95] text-white tracking-tight"><?= h(t('faq.headline')) ?></h1>
          <p class="mt-6 text-white/80 text-lg max-w-2xl"><?= h(t('faq.body')) ?><a href="<?= h(url('contact.php')) ?>" class="text-white underline ml-1"><?= h(t('faq.body_link')) ?></a>.</p>
        </div>
      </div>
    </section>

    <section class="section bg-paper">
      <div class="mx-auto max-w-4xl px-4 lg:px-6">
        <div class="space-y-px bg-hair" style="background:#E5E1D9" x-data="{ open: 0 }">
          <?php if (!$faqs): ?>
            <div class="bg-paper text-center text-ink-3 text-sm py-8"><?= h(t('faq.empty')) ?></div>
          <?php else: foreach ($faqs as $i => $f): ?>
            <div class="bg-paper" style="background:#FAF8F4">
              <button @click="open = open === <?= $i ?> ? -1 : <?= $i ?>"
                      class="w-full flex items-center justify-between gap-4 text-left px-6 py-5 hover:bg-white transition">
                <span class="flex items-center gap-4">
                  <span class="font-display italic text-sm text-gold shrink-0" style="color:#B8923B"><?= str_pad((string)($i+1), 2, '0', STR_PAD_LEFT) ?>.</span>
                  <span class="font-semibold text-ink"><?= h($f['q']) ?></span>
                </span>
                <span :class="open === <?= $i ?> && 'rotate-180 text-brand'" class="text-ink-3 transition-transform shrink-0"><?= icon('chevron', 'w-5 h-5') ?></span>
              </button>
              <div x-show="open === <?= $i ?>" x-collapse class="px-6 pb-6 pl-16 text-ink-2 leading-relaxed">
                <?= h($f['a']) ?>
              </div>
            </div>
          <?php endforeach; endif; ?>
        </div>

        <div class="mt-12 text-center bg-paper-2 p-10" style="background:#F4F1EA">
          <h3 class="font-display text-2xl font-bold text-ink">Still have questions?</h3>
          <p class="mt-2 text-ink-2 text-sm">Book a free 30-minute assessment and get personalised answers.</p>
          <a href="<?= h(url('contact.php')) ?>" class="mt-6 btn btn-primary inline-flex">Contact us →</a>
        </div>
      </div>
    </section>

    <?php require __DIR__ . '/partials/pre-footer.php'; ?>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
