<?php
require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';
$site = App\DataAdapter::site();
$team = [
    ['name' => 'Rashed Ahmed',   'role' => 'Founder & Japan Lead',           'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&auto=format&fit=crop&q=70'],
    ['name' => 'Ayesha Siddiqua','role' => 'Senior Counsellor — SSW & TITP',   'photo' => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=400&auto=format&fit=crop&q=70'],
    ['name' => 'Tanvir Hossain', 'role' => 'JLPT Instructor (N4–N2)',          'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&auto=format&fit=crop&q=70'],
    ['name' => 'Marina Tanaka',  'role' => 'Tokyo-based Mentor',                'photo' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&auto=format&fit=crop&q=70'],
];
$page_title = t('about.headline') . ' — ' . $site['brand']['full_name'];
$page_desc  = t('about.body');
$canonical  = $site['brand']['domain'] . '/about.php';
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
          <div class="eyebrow" data-num="01" style="color:#B8923B"><?= h(t('about.eyebrow')) ?></div>
          <h1 class="mt-5 font-display text-5xl sm:text-6xl lg:text-7xl font-bold leading-[0.95] text-white tracking-tight"><?= h(t('about.headline')) ?></h1>
          <p class="mt-6 font-display italic text-gold text-2xl" style="color:#B8923B">— <?= h(t('about.slogan')) ?></p>
          <p class="mt-5 text-white/80 text-lg max-w-2xl"><?= h(t('about.body')) ?></p>
        </div>
      </div>
    </section>

    <section class="section bg-paper">
      <div class="mx-auto max-w-7xl px-4 lg:px-6 grid lg:grid-cols-2 gap-12 items-center">
        <div class="grid grid-cols-2 gap-3 reveal">
          <div class="aspect-[4/5] rounded-sm overflow-hidden">
            <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=600&auto=format&fit=crop&q=70" alt="" class="w-full h-full object-cover" loading="lazy">
          </div>
          <div class="pt-12 space-y-3">
            <div class="aspect-[3/4] rounded-sm overflow-hidden">
              <img src="https://images.unsplash.com/photo-1531545514256-b1400bc00f31?w=600&auto=format&fit=crop&q=70" alt="" class="w-full h-full object-cover" loading="lazy">
            </div>
            <div class="aspect-square rounded-sm bg-navy text-white grid place-items-center text-center p-4" style="background:#1A2B6D">
              <div>
                <div class="font-display text-5xl font-bold">5+</div>
                <div class="mt-1 text-[9px] tracking-[0.25em] uppercase"><?= h(t('about.years_label')) ?><br><?= h(t('about.years_sub')) ?></div>
              </div>
            </div>
          </div>
        </div>
        <div class="reveal reveal-d2">
          <ul class="space-y-4">
            <?php foreach (['point_1','point_2','point_3','point_4','point_5'] as $i => $key): ?>
              <li class="flex items-start gap-4 pb-4 border-b border-hair" style="border-color:#E5E1D9">
                <span class="font-display italic text-gold text-lg shrink-0" style="color:#B8923B"><?= str_pad((string)($i+1), 2, '0', STR_PAD_LEFT) ?>.</span>
                <span class="text-ink leading-relaxed"><?= h(t('about.' . $key)) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </section>

    <section id="why-japan" class="section bg-paper-2" style="background:#F4F1EA">
      <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="max-w-2xl">
          <div class="eyebrow" data-num="02"><?= h(t('why.eyebrow')) ?></div>
          <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-tight text-ink"><?= h(t('why.headline')) ?></h2>
        </div>
        <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-4 gap-px bg-hair" style="background:#E5E1D9">
          <?php for ($i = 1; $i <= 4; $i++): ?>
            <article class="bg-white p-8 reveal">
              <div class="font-display italic text-5xl text-gold mb-3" style="color:#B8923B"><?= str_pad((string)$i, 2, '0', STR_PAD_LEFT) ?></div>
              <h3 class="font-display text-2xl font-bold text-ink leading-tight"><?= h(t('why.reason_' . $i . '_title')) ?></h3>
              <p class="mt-3 text-sm text-ink-2 leading-relaxed"><?= h(t('why.reason_' . $i . '_desc')) ?></p>
            </article>
          <?php endfor; ?>
        </div>
      </div>
    </section>

    <section class="section bg-paper">
      <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="text-center max-w-2xl mx-auto">
          <div class="eyebrow justify-center" data-num="03"><?= h(t('about.eyebrow')) ?></div>
          <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-tight text-ink">Our team</h2>
        </div>
        <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-4 gap-px bg-hair" style="background:#E5E1D9">
          <?php foreach ($team as $i => $m):
            $num = str_pad((string)($i+1), 2, '0', STR_PAD_LEFT);
          ?>
            <article class="bg-white p-6 text-center reveal">
              <div class="aspect-square bg-paper-2 mx-auto mb-5 overflow-hidden rounded-sm" style="background:#F4F1EA">
                <img src="<?= h($m['photo']) ?>" alt="<?= h($m['name']) ?>" class="w-full h-full object-cover" loading="lazy">
              </div>
              <div class="font-display italic text-xs text-gold" style="color:#B8923B"><?= $num ?>.</div>
              <div class="font-display text-xl font-bold text-ink mt-1"><?= h($m['name']) ?></div>
              <div class="text-xs text-ink-3 tracking-widest uppercase mt-1"><?= h($m['role']) ?></div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section bg-ink text-white" style="background:#1E242B">
      <div class="mx-auto max-w-4xl px-4 lg:px-6 text-center">
        <h2 class="font-display text-3xl sm:text-4xl font-bold text-white">Ready to start your Japan journey?</h2>
        <p class="mt-4 text-white/70">Book a free 30-minute assessment with a registered counsellor.</p>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
          <a href="<?= h(url('contact.php')) ?>" class="btn btn-primary"><?= h(t('nav.book_appointment')) ?> →</a>
          <a href="<?= h(url('books.php')) ?>" class="btn btn-ghost text-white border border-white/30"><?= h(t('nav.books_courses')) ?> →</a>
        </div>
      </div>
    </section>

    <section class="section bg-paper">
      <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="max-w-2xl">
          <div class="eyebrow" data-num="04"><?= h(t('about_extra.milestones_eyebrow')) ?></div>
          <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-tight text-ink"><?= h(t('about_extra.milestones_headline')) ?></h2>
        </div>
        <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-4 gap-px bg-hair" style="background:#E5E1D9">
          <?php for ($i = 1; $i <= 4; $i++):
            $yKey = "milestone_{$i}_y";
            $tKey = "milestone_{$i}_t";
            $dKey = "milestone_{$i}_d";
          ?>
            <div class="bg-paper p-7 reveal" style="background:#FAF8F4">
              <div class="font-display text-5xl font-bold text-brand leading-none" style="color:#006837"><?= h(t('about_extra.' . $yKey)) ?></div>
              <div class="mt-3 h-px w-10 bg-gold" style="background:#B8923B"></div>
              <h3 class="mt-3 font-display text-lg font-bold text-ink leading-snug"><?= h(t('about_extra.' . $tKey)) ?></h3>
              <p class="mt-2 text-sm text-ink-2 leading-relaxed"><?= h(t('about_extra.' . $dKey)) ?></p>
            </div>
          <?php endfor; ?>
        </div>
      </div>
    </section>

    <section class="section bg-paper-2" style="background:#F4F1EA">
      <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="text-center max-w-2xl mx-auto">
          <div class="eyebrow justify-center" data-num="05"><?= h(t('about_extra.partners_eyebrow')) ?></div>
          <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-tight text-ink"><?= h(t('about_extra.partners_headline')) ?></h2>
        </div>
        <div class="mt-14 grid grid-cols-2 md:grid-cols-4 gap-px bg-hair" style="background:#E5E1D9">
          <?php foreach ([
            ['name' => 'JETRO',                  'tag' => 'Japan External Trade Org'],
            ['name' => 'MEXT',                   'tag' => 'Ministry of Education'],
            ['name' => 'JASSO',                  'tag' => 'Student Services Org'],
            ['name' => 'JBIC',                   'tag' => 'Bank for Intl. Coop.'],
            ['name' => 'Dhaka Univ.',            'tag' => 'Academic partner'],
            ['name' => 'BRAC Univ.',             'tag' => 'Academic partner'],
            ['name' => 'AIUB',                   'tag' => 'Career fair partner'],
            ['name' => 'IUB',                    'tag' => 'Career fair partner'],
          ] as $p): ?>
            <div class="bg-paper p-6 text-center" style="background:#FAF8F4">
              <div class="font-display text-2xl font-bold text-ink"><?= h($p['name']) ?></div>
              <div class="mt-1 text-[10px] tracking-widest text-ink-3 uppercase"><?= h($p['tag']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section bg-paper">
      <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="text-center max-w-2xl mx-auto">
          <div class="eyebrow justify-center" data-num="06"><?= h(t('about_extra.stats_eyebrow')) ?></div>
          <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-tight text-ink"><?= h(t('about_extra.stats_headline')) ?></h2>
        </div>
        <div class="mt-14 grid grid-cols-2 lg:grid-cols-4 gap-px bg-hair" style="background:#E5E1D9">
          <?php
            $stats = [
                ['value' => '1,200+', 'label' => t('about_extra.value_students')],
                ['value' => '50+',    'label' => t('about_extra.value_partners')],
                ['value' => '20+',    'label' => t('about_extra.value_employers')],
                ['value' => '99%',    'label' => t('about_extra.value_success')],
            ];
            foreach ($stats as $i => $s):
              $num = str_pad((string)($i+1), 2, '0', STR_PAD_LEFT);
          ?>
            <div class="bg-paper p-8 text-center reveal" style="background:#FAF8F4">
              <div class="font-display italic text-xs text-gold" style="color:#B8923B"><?= $num ?>.</div>
              <div class="mt-3 stat-num"><?= h($s['value']) ?></div>
              <div class="mt-5 text-xs tracking-widest text-ink-2 uppercase"><?= h($s['label']) ?></div>
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
