<?php
require __DIR__ . '/../partials/_icons.php';
require __DIR__ . '/../app/bootstrap.php';
$site = App\DataAdapter::site();
$slug = preg_replace('/[^a-z0-9-]/', '', (string) ($_GET['slug'] ?? ''));
$country = $slug ? App\Database::fetchOne('SELECT * FROM countries WHERE LOWER(code)=? OR LOWER(name)=? LIMIT 1', [strtoupper($slug), ucwords(str_replace('-', ' ', $slug))]) : null;
if (!$country) { http_response_code(404); }

$page_title = ($country ? 'Study in ' . $country['name'] : 'Destination not found') . ' — ' . $site['brand']['full_name'];
$page_desc  = $country['description'] ?? '';
$canonical  = $site['brand']['domain'] . '/country/' . urlencode($slug);

$facts = [
    'jp' => [
        'capital' => 'Tokyo', 'language' => 'Japanese', 'currency' => 'Yen (¥)', 'tuition_range' => '¥800,000–¥1,500,000 / year',
        'visa' => 'Student Visa · SSW · TITP', 'work' => 'Up to 28 hrs/week on Student Visa',
        'universities' => '780+ universities', 'scholarships' => 'MEXT · JASSO · University-specific',
    ],
    'au' => ['capital' => 'Canberra', 'language' => 'English', 'currency' => 'AUD ($)', 'tuition_range' => 'AUD 20,000–45,000 / year', 'visa' => 'Subclass 500', 'work' => 'Up to 48 hrs/fortnight', 'universities' => '40+ universities', 'scholarships' => 'Australia Awards · RTP'],
    'ca' => ['capital' => 'Ottawa', 'language' => 'English / French', 'currency' => 'CAD ($)', 'tuition_range' => 'CAD 15,000–35,000 / year', 'visa' => 'Study Permit + PAL', 'work' => 'Up to 20 hrs/week', 'universities' => '90+ universities', 'scholarships' => 'Vanier · CGS'],
    'gb' => ['capital' => 'London', 'language' => 'English', 'currency' => 'GBP (£)', 'tuition_range' => '£10,000–25,000 / year', 'visa' => 'Student Route', 'work' => 'Up to 20 hrs/week', 'universities' => '130+ universities', 'scholarships' => 'Chevening · Commonwealth'],
    'us' => ['capital' => 'Washington, D.C.', 'language' => 'English', 'currency' => 'USD ($)', 'tuition_range' => '$15,000–50,000 / year', 'visa' => 'F-1 Student', 'work' => 'On-campus up to 20 hrs/week', 'universities' => '4,000+ universities', 'scholarships' => 'Fulbright · University-specific'],
    'eu' => ['capital' => 'Brussels (EU)', 'language' => 'Varies', 'currency' => 'EUR (€)', 'tuition_range' => '€2,000–20,000 / year', 'visa' => 'Schengen Type D', 'work' => 'Up to 20 hrs/week', 'universities' => 'Erasmus network', 'scholarships' => 'Erasmus+ · DAAD'],
    'my' => ['capital' => 'Kuala Lumpur', 'language' => 'Malay / English', 'currency' => 'MYR (RM)', 'tuition_range' => 'RM 20,000–60,000 / year', 'visa' => 'Student Pass', 'work' => 'Up to 20 hrs/week', 'universities' => '20+ universities', 'scholarships' => 'MIS · University-specific'],
    'kr' => ['capital' => 'Seoul', 'language' => 'Korean / English', 'currency' => 'KRW (₩)', 'tuition_range' => '₩3,000,000–7,000,000 / year', 'visa' => 'D-2 Student', 'work' => 'Up to 25 hrs/week (with permit)', 'universities' => '190+ universities', 'scholarships' => 'KGSP · University-specific'],
];
$code = $country ? strtolower($country['code']) : '';
$info = $facts[$code] ?? null;
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
  <link rel="stylesheet" href="<?= h(url('assets/css/style.css')) ?>">
  <?php require __DIR__ . '/../partials/favicon.php'; ?>
</head>
<body class="font-sans text-ink antialiased bg-paper">
  <?php require __DIR__ . '/../partials/topbar.php'; ?>
  <?php require __DIR__ . '/../partials/header.php'; ?>

  <main>
    <section class="relative overflow-hidden" style="background: linear-gradient(180deg, #1E242B 0%, #1A2B6D 100%);">
      <div class="bg-orb" style="width:500px;height:500px;background:#006837;top:-100px;right:-100px;opacity:0.18"></div>
      <div class="mx-auto max-w-7xl px-4 lg:px-6 py-20 lg:py-32 relative">
        <div class="max-w-3xl">
          <a href="<?= h(url('index.php#countries')) ?>" class="text-xs tracking-widest uppercase text-white/70 hover:text-gold">← All destinations</a>
          <?php if (!$country): ?>
            <h1 class="mt-4 font-display text-4xl font-bold text-white">Destination not found</h1>
          <?php else: ?>
            <div class="mt-6 flex items-center gap-3">
                <span class="flag <?= h($country['flag_css'] ?: 'flag-' . strtolower($country['code'])) ?> w-10 h-10 rounded-full ring-2 ring-white/20" style="background:#fff"></span>
              <div class="eyebrow" data-num="·" style="color:#B8923B">Study in</div>
            </div>
            <h1 class="mt-4 font-display text-5xl sm:text-6xl lg:text-7xl font-bold text-white leading-[0.95] tracking-tight"><?= h($country['name']) ?></h1>
            <p class="mt-6 text-white/80 text-lg max-w-2xl"><?= h($country['description'] ?? '') ?></p>
            <div class="mt-8 flex flex-wrap gap-3">
              <a href="<?= h(url('contact.php')) ?>" class="btn btn-primary">Book a free assessment →</a>
              <a href="<?= h(url('books.php')) ?>" class="btn btn-ghost text-white border border-white/30">Study materials</a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <?php if ($country && $info): ?>
    <section class="section bg-paper">
      <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="eyebrow" data-num="01">Quick facts</div>
        <h2 class="mt-4 font-display text-3xl sm:text-4xl font-bold text-ink">At a glance</h2>
        <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-px bg-hair" style="background:#E5E1D9">
          <?php foreach (['capital' => 'Capital', 'language' => 'Language', 'currency' => 'Currency', 'visa' => 'Student visa', 'work' => 'Work rights', 'tuition_range' => 'Tuition (per year)', 'universities' => 'Universities', 'scholarships' => 'Top scholarships'] as $key => $label): ?>
            <div class="bg-paper p-6" style="background:#FAF8F4">
              <div class="text-[10px] tracking-widest text-ink-3 uppercase"><?= h($label) ?></div>
              <div class="mt-2 text-ink font-semibold"><?= h($info[$key] ?? '—') ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section bg-paper-2" style="background:#F4F1EA">
      <div class="mx-auto max-w-3xl px-4 lg:px-6 text-center">
        <h2 class="font-display text-3xl sm:text-4xl font-bold text-ink">Ready to start your <?= h($country['name']) ?> journey?</h2>
        <p class="mt-4 text-ink-2">Book a free 30-minute assessment with one of our counsellors.</p>
        <a href="<?= h(url('contact.php')) ?>" class="mt-8 btn btn-primary inline-flex"><?= h(t('nav.book_appointment')) ?> →</a>
      </div>
    </section>
    <?php endif; ?>

    <?php require __DIR__ . '/../partials/pre-footer.php'; ?>
  </main>

  <?php require __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
