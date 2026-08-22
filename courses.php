<?php
require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';
$site = App\DataAdapter::site();

$products = App\Product::all();
$courses  = array_values(array_filter($products, fn($p) => $p['type'] === 'course'));
$page_title = t('courses.headline') . ' — ' . $site['brand']['full_name'];
$page_desc  = t('courses.body');
$canonical  = $site['brand']['domain'] . '/courses.php';

$jsonld = $courses ? [
    '@context' => 'https://schema.org',
    '@type'    => 'ItemList',
    'name'     => t('courses.headline'),
    'itemListElement' => array_map(fn($c, $i) => [
        '@type'    => 'ListItem',
        'position' => $i + 1,
        'item'     => [
            '@type'       => 'Course',
            'name'        => $c['title'],
            'description' => $c['summary'] ?? '',
            'offers'      => [
                '@type'         => 'Offer',
                'price'         => number_format((float) $c['price_bdt'], 2, '.', ''),
                'priceCurrency' => 'BDT',
            ],
            'url' => $site['brand']['domain'] . '/courses/' . urlencode($c['slug']),
        ],
    ], $courses, array_keys($courses)),
] : null;
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
  <?php if ($jsonld): ?><script type="application/ld+json"><?= json_encode($jsonld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script><?php endif; ?>
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
      <div class="bg-orb" style="width:300px;height:300px;background:#B8923B;bottom:-80px;left:20%;opacity:0.12"></div>
      <div class="mx-auto max-w-7xl px-4 lg:px-6 py-20 lg:py-32 relative">
        <div class="max-w-3xl">
          <div class="eyebrow" data-num="05" style="color:#B8923B"><?= h(t('courses.eyebrow')) ?></div>
          <h1 class="mt-5 font-display text-5xl sm:text-6xl lg:text-7xl font-bold leading-[0.95] text-white tracking-tight"><?= h(t('courses.headline')) ?></h1>
          <p class="mt-6 text-white/80 text-lg max-w-2xl"><?= h(t('courses.body')) ?></p>
          <div class="mt-8 flex flex-wrap gap-3">
            <a href="#catalogue" class="btn btn-primary">Browse courses →</a>
            <a href="<?= h(url('contact.php')) ?>" class="btn btn-ghost text-white border border-white/30">Talk to a counsellor</a>
          </div>
        </div>
      </div>
    </section>

    <section id="catalogue" class="section bg-paper">
      <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <?php if (!$courses): ?>
          <div class="text-center py-16 bg-white border border-hair" style="border-color:#E5E1D9">
            <div class="text-5xl">📚</div>
            <p class="mt-4 text-ink-2"><?= h(t('courses.no_courses')) ?></p>
            <a href="<?= h(url('books.php')) ?>" class="mt-6 inline-flex btn btn-primary"><?= h(t('courses.browse_books')) ?> →</a>
          </div>
        <?php else: ?>
          <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-px bg-hair" style="background:#E5E1D9">
            <?php foreach ($courses as $i => $c):
              $cover = $c['cover_id'] ? App\Content::mediaUrl((int) $c['cover_id']) : null;
              $num = str_pad((string)($i+1), 2, '0', STR_PAD_LEFT);
              $weeks = 4; // Default course duration
            ?>
              <article class="bg-paper flex flex-col group" style="background:#FAF8F4">
                <a href="<?= h(url('courses/' . $c['slug'])) ?>" class="block aspect-[16/10] bg-paper-2 relative overflow-hidden" style="background:#F4F1EA">
                  <?php if ($cover): ?>
                    <img src="<?= h($cover) ?>" alt="<?= h($c['title']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                  <?php else: ?>
                    <div class="absolute inset-0 grid place-items-center text-brand/30">
                      <svg class="w-20 h-20" viewBox="0 0 24 24" fill="currentColor"><path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1L15 22v-1.5L13 19v-5.5l8 2.5z"/></svg>
                    </div>
                  <?php endif; ?>
                  <span class="absolute top-3 left-3 bg-accent text-white text-[9px] font-bold tracking-widest uppercase px-2.5 py-1">Live</span>
                </a>
                <div class="p-6 flex-1 flex flex-col">
                  <div class="font-display italic text-xs text-gold" style="color:#B8923B"><?= $num ?>.</div>
                  <h3 class="mt-2 font-display text-xl font-bold text-ink leading-snug">
                    <a href="<?= h(url('courses/' . $c['slug'])) ?>" class="hover:text-brand"><?= h($c['title']) ?></a>
                  </h3>
                  <p class="mt-2 text-sm text-ink-2 flex-1 line-clamp-3"><?= h($c['summary'] ?? '') ?></p>

                  <div class="mt-4 flex items-center gap-3 text-xs">
                    <span class="inline-flex items-center gap-1.5 text-ink-2">
                      <svg class="w-3.5 h-3.5 text-brand" viewBox="0 0 24 24" fill="currentColor"><path d="M12 8v4l3 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                      <?= h(t('courses.weeks_4')) ?>
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-ink-2">
                      <svg class="w-3.5 h-3.5 text-brand" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5h18v12H3zM7 17v3h10v-3"/></svg>
                      <?= h(t('courses.format_live')) ?>
                    </span>
                  </div>

                  <div class="mt-5 hairline pt-4 flex items-end justify-between" style="border-color:#E5E1D9">
                    <div>
                      <div class="text-[10px] tracking-widest text-ink-3 uppercase">Course fee</div>
                      <div class="font-display text-2xl font-bold text-ink"><?= format_bdt((float) $c['price_bdt']) ?></div>
                    </div>
                    <a href="<?= h(url('courses/' . $c['slug'])) ?>" class="btn-arrow">View →</a>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>

          <div class="mt-12 bg-paper-2 p-8 sm:p-10 text-center" style="background:#F4F1EA">
            <h3 class="font-display text-2xl font-bold text-ink">Not sure which course fits you?</h3>
            <p class="mt-2 text-ink-2 text-sm">Talk to a counsellor for a personalised recommendation.</p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
              <a href="<?= h(url('contact.php')) ?>" class="btn btn-primary">Book a free call →</a>
              <a href="<?= h(url('books.php')) ?>" class="btn btn-secondary"><?= h(t('courses.browse_books')) ?></a>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section class="section bg-ink text-white" style="background:#1E242B">
      <div class="mx-auto max-w-4xl px-4 lg:px-6 text-center">
        <h2 class="font-display text-3xl sm:text-4xl font-bold text-white">What's included in every course</h2>
        <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-px bg-white/10 text-left">
          <?php foreach ([
            ['Live sessions', 'Cohort-based, scheduled with a Japan-based instructor.'],
            ['1:1 mentorship', 'Personal Q&A with a mentor who has lived in Japan for 5+ years.'],
            ['All materials', 'Handouts, recordings, and curated reading list — yours forever.'],
            ['Certificate', 'Certificate of completion to add to your visa application file.'],
          ] as $i => $f): ?>
            <div class="bg-ink p-6" style="background:#1E242B">
              <div class="font-display italic text-3xl text-gold mb-3" style="color:#B8923B"><?= str_pad((string)($i+1), 2, '0', STR_PAD_LEFT) ?>.</div>
              <h3 class="font-display text-lg font-bold text-white"><?= h($f[0]) ?></h3>
              <p class="mt-2 text-sm text-white/70"><?= h($f[1]) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <?php require __DIR__ . '/partials/pre-footer.php'; ?>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
