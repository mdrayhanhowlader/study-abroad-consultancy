<?php
require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';
$site = App\DataAdapter::site();

$events = [
    ['date' => '2026-08-15', 'title' => 'Manami Study Japan Expo 2026', 'city' => 'Dhaka — Dhanmondi', 'tag' => 'Expo', 'desc' => 'Our biggest Japan-focused event of the year. Free entry, meet Japanese language school representatives, employers, and past students.'],
    ['date' => '2026-09-12', 'title' => 'JLPT N4 Mock Test Day',          'city' => 'Dhaka — Dhanmondi', 'tag' => 'Workshop', 'desc' => 'Free full-length JLPT N4 mock test with native Japanese examiners. Limited seats.'],
    ['date' => '2026-10-05', 'title' => 'SSW Open Day',                    'city' => 'Chattogram', 'tag' => 'Info session', 'desc' => 'Everything about the Specified Skilled Worker visa — eligibility, employers, timeline, and application.'],
    ['date' => '2026-11-20', 'title' => 'TITP Information Seminar',        'city' => 'Dhaka — Farmgate', 'tag' => 'Seminar', 'desc' => 'Technical Intern Training Program explained in detail. Q&A with past TITP participants.'],
    ['date' => '2026-12-04', 'title' => 'Student Visa Filing Workshop',    'city' => 'Online', 'tag' => 'Workshop', 'desc' => 'Hands-on workshop for shortlisted students. Document preparation, COE process, embassy interview.'],
];

usort($events, fn($a, $b) => strcmp($a['date'], $b['date']));
$page_title = t('nav.events') . ' — ' . $site['brand']['full_name'];
$page_desc  = 'Upcoming Manami Education events, expos, workshops, and info sessions for students heading to Japan.';
$canonical  = $site['brand']['domain'] . '/events.php';
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
          <div class="eyebrow" data-num="07" style="color:#B8923B">Events</div>
          <h1 class="mt-5 font-display text-5xl sm:text-6xl lg:text-7xl font-bold leading-[0.95] text-white tracking-tight">Upcoming events &amp; expos</h1>
          <p class="mt-6 text-white/80 text-lg max-w-2xl">Workshops, expos, and info sessions across Bangladesh to help you prepare for Japan.</p>
        </div>
      </div>
    </section>

    <section class="section bg-paper">
      <div class="mx-auto max-w-5xl px-4 lg:px-6 space-y-px bg-hair" style="background:#E5E1D9">
        <?php foreach ($events as $i => $e):
          $num = str_pad((string)($i+1), 2, '0', STR_PAD_LEFT);
          $ts = strtotime($e['date']);
          $day = date('d', $ts);
          $mo  = date('M', $ts);
          $year= date('Y', $ts);
          $weekday = date('D', $ts);
        ?>
          <article class="bg-paper p-6 sm:p-8 grid sm:grid-cols-12 gap-6 items-center reveal" style="background:#FAF8F4">
            <div class="sm:col-span-2 text-center sm:text-left">
              <div class="text-[10px] tracking-widest text-accent uppercase font-semibold" style="color:#E31E24"><?= h($mo) ?> · <?= h($weekday) ?></div>
              <div class="font-display text-5xl font-bold text-ink leading-none"><?= h($day) ?></div>
              <div class="text-xs text-ink-3 mt-1"><?= h($year) ?></div>
            </div>
            <div class="sm:col-span-7">
              <span class="text-[10px] tracking-widest text-ink-3 uppercase"><?= h($num) ?> · <?= h($e['tag']) ?></span>
              <h3 class="font-display text-2xl font-bold text-ink leading-tight mt-1"><?= h($e['title']) ?></h3>
              <p class="mt-2 text-ink-2 text-sm"><?= h($e['desc']) ?></p>
              <div class="mt-2 text-xs text-ink-3 flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                  <?= h($e['city']) ?>
                </span>
              </div>
            </div>
            <div class="sm:col-span-3 sm:text-right">
              <a href="<?= h(url('contact.php')) ?>" class="btn-arrow">Register →</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section bg-ink text-white" style="background:#1E242B">
      <div class="mx-auto max-w-4xl px-4 lg:px-6 text-center">
        <h2 class="font-display text-3xl sm:text-4xl font-bold text-white">Don't see an event near you?</h2>
        <p class="mt-4 text-white/70">Tell us your city — we'll host a session when there's enough demand.</p>
        <a href="<?= h(url('contact.php')) ?>" class="mt-8 btn btn-primary inline-flex">Request an event →</a>
      </div>
    </section>

    <?php require __DIR__ . '/partials/pre-footer.php'; ?>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
