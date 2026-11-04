<?php
require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';
$site      = App\DataAdapter::site();
$services  = App\DataAdapter::services();
$page_title = t('services.headline') . ' — ' . $site['brand']['full_name'];
$page_desc  = t('services.body');
$canonical  = $site['brand']['domain'] . '/services.php';

$japanSvgs = [
  'student-visa' => 'M4 4h12a3 3 0 0 1 3 3v15a1 1 0 0 1-1 1H7a3 3 0 0 1-3-3V4zm12 2H6v13a1 1 0 0 0 1 1h11V5zM8 7h7v2H8V7zm0 4h7v2H8v-2z',
  'ssw'          => 'M5 4a3 3 0 1 1 4 2.8V11a3 3 0 0 0 3 3h3a5 5 0 0 1 0 10h-2a1 1 0 1 1 0-2h2a3 3 0 0 0 0-6h-3a5 5 0 0 1-5-5V6.8A3 3 0 0 1 5 4z',
  'titp'         => 'M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1L15 22v-1.5L13 19v-5.5l8 2.5z',
  'japanese'     => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm-1 17.9A8 8 0 0 1 4.1 13H7c.1 2.3.6 4.5 1.4 6.4.5 1 .9 1.7 1.2 2.1.4.5 1 .6 1.4.4z',
  'ielts'        => 'M12 6.5a4 4 0 1 1 0 8 4 4 0 0 1 0-8zM4 4h16v2H4zM4 18h16v2H4z',
  'career-counselling' => 'M12 2a8 8 0 1 1 0 16 8 8 0 0 1 0-16zm0 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8z',
];
$details = [
  'student-visa' => [
    'id' => 'student-visa',
    'title' => 'Student Visa Support',
    'subtitle' => 'Study at a Japanese language school or university with full visa support.',
    'duration' => '4–6 months',
    'eligibility' => 'High school diploma + JLPT N5 (or commitment to start in Japan)',
    'documents' => 'Passport, academic transcripts, JLPT cert, financial proof, photos, CV',
    'process' => ['Free 30-min assessment', 'Shortlist Japanese language schools', 'School admission + COE', 'Visa application & interview prep', 'Flight booking & arrival'],
  ],
  'ssw' => [
    'id' => 'ssw',
    'title' => 'Skilled Worker Programme (SSW)',
    'subtitle' => 'Work in Japan with a Specified Skilled Worker visa in high-demand industries.',
    'duration' => '3–5 months',
    'eligibility' => 'JLPT N4 or industry skills test, age 18+',
    'documents' => 'Passport, JLPT N4 / skills test, work experience, employer offer',
    'process' => ['Skills assessment', 'JLPT N4 preparation (in-house)', 'Employer matching', 'Visa application', 'Pre-departure orientation'],
  ],
  'titp' => [
    'id' => 'titp',
    'title' => 'Technical Intern Training (TITP)',
    'subtitle' => 'Train and work in Japan for 1–5 years through the TITP programme.',
    'duration' => '2–4 months',
    'eligibility' => 'Age 18+, basic Japanese, willing to train for 1–5 years',
    'documents' => 'Passport, no criminal record, medical fitness',
    'process' => ['Sending organisation match', 'Host company placement', 'JLPT N4/N3 prep', 'Visa filing', 'Arrival & onboarding in Japan'],
  ],
];

$allSections = [
  'student-visa' => ['title' => 'Student Visa Support', 'subtitle' => 'Study at a Japanese language school or university with full visa support.'],
  'ssw'          => ['title' => 'Skilled Worker Programme (SSW)', 'subtitle' => 'Work in Japan with a Specified Skilled Worker visa in high-demand industries.'],
  'titp'         => ['title' => 'Technical Intern Training (TITP)', 'subtitle' => 'Train and work in Japan for 1–5 years through the TITP programme.'],
  'japanese'     => ['title' => 'Japanese Language Courses (JLPT N5–N1)', 'subtitle' => 'From zero to JLPT N1 with native-trained teachers.'],
  'ielts'        => ['title' => 'IELTS Preparation', 'subtitle' => 'A 6-week intensive covering all four modules with mock tests and feedback.'],
];

$defaultHero = ['title' => t('services.headline'), 'subtitle' => t('services.body')];
$currentHash = ltrim((string) ($_SERVER['REQUEST_URI'] ?? ''), '/');
$currentHash = explode('#', $currentHash);
$currentHash = $currentHash[1] ?? '';
$activeSection = $allSections[$currentHash] ?? null;
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
</head>
<body class="font-sans text-ink antialiased bg-paper">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <?php require __DIR__ . '/partials/header.php'; ?>

  <main x-data="serviceHero()" x-init="init()">
    <section class="relative overflow-hidden" style="background: linear-gradient(180deg, #1E242B 0%, #1A2B6D 100%);">
      <div class="bg-orb" style="width:500px;height:500px;background:#006837;top:-100px;right:-100px;opacity:0.18"></div>
      <div class="mx-auto max-w-7xl px-4 lg:px-6 py-20 lg:py-32 relative">
        <div class="max-w-3xl">
          <div class="eyebrow" data-num="04" style="color:#B8923B"><?= h(t('services.eyebrow')) ?></div>
          <h1 class="mt-5 font-display text-4xl sm:text-5xl lg:text-6xl font-bold leading-[1.05] text-white tracking-tight" x-text="hero.title"><?= h(t('services.headline')) ?></h1>
          <p class="mt-6 text-white/80 text-lg max-w-2xl" x-text="hero.subtitle"><?= h(t('services.body')) ?></p>
          <a href="#" @click.prevent="scrollToSection()" x-show="hero.hash" x-cloak class="mt-6 inline-flex btn btn-primary">View details →</a>
        </div>
      </div>
    </section>

    <section class="section bg-paper">
      <div class="mx-auto max-w-7xl px-4 lg:px-6 space-y-24">

        <?php foreach ($details as $key => $d):
          $svc = null;
          foreach ($services as $s) if ($s['slug'] === $key) { $svc = $s; break; }
          if (!$svc) continue;
          $svg = $japanSvgs[$key] ?? 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z';
        ?>
        <article id="<?= h($d['id']) ?>" class="grid lg:grid-cols-2 gap-12 items-center scroll-mt-24">
          <div class="<?= array_search($key, array_keys($details)) % 2 === 1 ? 'lg:order-2' : '' ?> reveal">
            <div class="eyebrow" data-num="·"><?= h(t('nav.study_in_japan')) ?></div>
            <h2 class="mt-4 font-display text-4xl sm:text-5xl font-bold text-ink leading-[1.05] tracking-tight"><?= h($svc['title']) ?></h2>
            <p class="mt-4 text-ink-2 text-lg leading-relaxed"><?= h($svc['desc']) ?></p>
            <?php if (!empty($svc['body'])): ?>
              <div class="mt-4 prose prose-sm text-ink-2"><?= nl2br(h($svc['body'])) ?></div>
            <?php endif; ?>
            <div class="mt-8 space-y-3">
              <div class="hairline pt-5" style="border-color:#E5E1D9">
                <div class="text-[10px] tracking-widest text-ink-3 uppercase">Typical timeline</div>
                <div class="font-display text-2xl font-bold text-ink mt-1"><?= h($d['duration']) ?></div>
              </div>
              <div class="hairline pt-5" style="border-color:#E5E1D9">
                <div class="text-[10px] tracking-widest text-ink-3 uppercase">Eligibility</div>
                <div class="text-ink mt-1"><?= h($d['eligibility']) ?></div>
              </div>
              <div class="hairline pt-5" style="border-color:#E5E1D9">
                <div class="text-[10px] tracking-widest text-ink-3 uppercase">Key documents</div>
                <div class="text-ink mt-1 text-sm"><?= h($d['documents']) ?></div>
              </div>
            </div>
            <a href="<?= h(url('contact.php')) ?>" class="mt-8 btn btn-primary inline-flex"><?= h($svc['cta_text'] ?: t('services.cta_get_started')) ?> →</a>
          </div>
          <div class="<?= array_search($key, array_keys($details)) % 2 === 1 ? 'lg:order-1' : '' ?> reveal reveal-d2">
            <div class="aspect-[4/5] bg-paper-2 rounded-sm grid place-items-center text-brand" style="background:#F4F1EA">
              <svg class="w-32 h-32" viewBox="0 0 24 24" fill="currentColor"><path d="<?= h($svg) ?>"/></svg>
            </div>
            <ol class="mt-8 space-y-3">
              <?php foreach ($d['process'] as $i => $step): ?>
                <li class="flex items-start gap-3">
                  <span class="w-7 h-7 rounded-full bg-brand text-white grid place-items-center text-xs font-semibold shrink-0" style="background:#006837"><?= $i + 1 ?></span>
                  <span class="text-ink leading-relaxed pt-0.5"><?= h($step) ?></span>
                </li>
              <?php endforeach; ?>
            </ol>
          </div>
        </article>
        <?php endforeach; ?>

        <article id="japanese" class="grid lg:grid-cols-2 gap-12 items-center scroll-mt-24">
          <div class="reveal">
            <div class="eyebrow" data-num="·">Language</div>
            <h2 class="mt-4 font-display text-4xl sm:text-5xl font-bold text-ink leading-[1.05] tracking-tight">Japanese Language Courses (JLPT N5–N1)</h2>
            <p class="mt-4 text-ink-2 text-lg leading-relaxed">From zero to JLPT N1 with native-trained teachers. Levels N5 → N1, weekend and weekday batches, in-person (Dhanmondi) and online.</p>
            <div class="mt-8 grid grid-cols-5 gap-2">
              <?php foreach (['N5','N4','N3','N2','N1'] as $lvl): ?>
                <div class="text-center py-3 bg-paper-2" style="background:#F4F1EA">
                  <div class="text-[10px] tracking-widest text-ink-3 uppercase">Level</div>
                  <div class="font-display text-2xl font-bold text-ink"><?= h($lvl) ?></div>
                </div>
              <?php endforeach; ?>
            </div>
            <a href="<?= h(url('contact.php')) ?>" class="mt-8 btn btn-primary inline-flex">Enrol now →</a>
          </div>
          <div class="reveal reveal-d2">
            <div class="aspect-[4/5] rounded-sm overflow-hidden">
              <img src="https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=700&auto=format&fit=crop&q=70" alt="Japanese language class" class="w-full h-full object-cover" loading="lazy">
            </div>
          </div>
        </article>

        <article id="ielts" class="grid lg:grid-cols-2 gap-12 items-center scroll-mt-24">
          <div class="lg:order-2 reveal">
            <div class="eyebrow" data-num="·">Language</div>
            <h2 class="mt-4 font-display text-4xl sm:text-5xl font-bold text-ink leading-[1.05] tracking-tight">IELTS Preparation</h2>
            <p class="mt-4 text-ink-2 text-lg leading-relaxed">A 6-week intensive covering all four modules with mock tests, band-tracking, and 1:1 feedback from ex-examiners.</p>
            <a href="<?= h(url('contact.php')) ?>" class="mt-8 btn btn-primary inline-flex">Enrol now →</a>
          </div>
          <div class="lg:order-1 reveal reveal-d2">
            <div class="aspect-[4/5] rounded-sm overflow-hidden">
              <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=700&auto=format&fit=crop&q=70" alt="IELTS preparation" class="w-full h-full object-cover" loading="lazy">
            </div>
          </div>
        </article>

      </div>
    </section>

    <section class="section bg-ink text-white" style="background:#1E242B">
      <div class="mx-auto max-w-4xl px-4 lg:px-6 text-center">
        <h2 class="font-display text-3xl sm:text-4xl font-bold text-white">Not sure which pathway fits you?</h2>
        <p class="mt-4 text-white/70">Book a free 30-minute assessment with a registered counsellor.</p>
        <a href="<?= h(url('contact.php')) ?>" class="mt-8 btn btn-primary inline-flex"><?= h(t('nav.book_appointment')) ?> →</a>
      </div>
    </section>

    <?php require __DIR__ . '/partials/pre-footer.php'; ?>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('serviceHero', () => ({
        default: {
          title: <?= json_encode(t('services.headline')) ?>,
          subtitle: <?= json_encode(t('services.body')) ?>,
          hash: null
        },
        sections: {
          'student-visa': { title: 'Student Visa Support', subtitle: 'Study at a Japanese language school or university with full visa support.', hash: 'student-visa' },
          'ssw': { title: 'Skilled Worker Programme (SSW)', subtitle: 'Work in Japan with a Specified Skilled Worker visa in high-demand industries.', hash: 'ssw' },
          'titp': { title: 'Technical Intern Training (TITP)', subtitle: 'Train and work in Japan for 1–5 years through the TITP programme.', hash: 'titp' },
          'japanese': { title: 'Japanese Language Courses (JLPT N5–N1)', subtitle: 'From zero to JLPT N1 with native-trained teachers.', hash: 'japanese' },
          'ielts': { title: 'IELTS Preparation', subtitle: 'A 6-week intensive covering all four modules with mock tests and feedback.', hash: 'ielts' }
        },
        hero: { title: <?= json_encode(t('services.headline')) ?>, subtitle: <?= json_encode(t('services.body')) ?>, hash: null },
        init() {
          this.updateFromHash();
          window.addEventListener('hashchange', () => this.updateFromHash());
        },
        updateFromHash() {
          const hash = window.location.hash.replace('#', '');
          if (this.sections[hash]) {
            this.hero = { ...this.sections[hash] };
            document.title = this.sections[hash].title + ' — Manami Education';
            this.$nextTick(() => {
              const el = document.getElementById(hash);
              if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
          } else {
            this.hero = { ...this.default };
          }
        },
        scrollToSection() {
          if (this.hero.hash) {
            const el = document.getElementById(this.hero.hash);
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }
      }));
    });
  </script>
</body>
</html>
