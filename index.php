<?php
require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';

$site         = App\DataAdapter::site();
$countries    = App\DataAdapter::countries();
$services     = App\DataAdapter::services();
$stats        = App\DataAdapter::stats();
$testimonials = App\DataAdapter::testimonials();
$faqs         = App\DataAdapter::faqs();
$posts        = App\DataAdapter::posts(3);

$page_title = $site['brand']['full_name'] . ' — ' . t('hero.eyebrow');
$page_desc  = t('hero.body') . ' ' . t('hero.slogan');

$canonical = $site['brand']['domain'] . '/';
$og_image  = $site['brand']['og_image'];

$org_jsonld = [
    '@context'      => 'https://schema.org',
    '@type'         => ['Organization', 'LocalBusiness'],
    'name'          => $site['brand']['full_name'],
    'alternateName' => $site['brand']['name'] . ' ' . $site['brand']['tagline'],
    'url'           => $site['brand']['domain'],
    'logo'          => $site['brand']['domain'] . '/assets/img/logo.png',
    'description'   => $page_desc,
    'email'         => $site['contact']['email'],
    'telephone'     => $site['contact']['phone'],
    'address'       => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => '20/A, B, Block No-2, Gate No-3, Monipuripara, Farmgate',
        'addressLocality' => 'Dhaka',
        'postalCode'      => '1215',
        'addressCountry'  => 'BD',
    ],
    'areaServed'    => ['BD', 'JP'],
    'sameAs'        => array_column($site['social'], 'href'),
];
?>
<!doctype html>
<html lang="<?= h(current_lang()) ?>" dir="<?= h(\App\I18n::dir(current_lang())) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= h($page_title) ?></title>
  <meta name="description" content="<?= h($page_desc) ?>">
  <meta name="theme-color" content="#006837">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= h($canonical) ?>">
  <link rel="alternate" hreflang="en-GB" href="<?= h($canonical) ?>?lang=en-GB">
  <link rel="alternate" hreflang="bn"    href="<?= h($canonical) ?>?lang=bn">
  <link rel="alternate" hreflang="ja"    href="<?= h($canonical) ?>?lang=ja">
  <link rel="alternate" hreflang="hi"    href="<?= h($canonical) ?>?lang=hi">
  <link rel="alternate" hreflang="x-default" href="<?= h($canonical) ?>">

  <meta property="og:type" content="website">
  <meta property="og:site_name" content="<?= h($site['brand']['full_name']) ?>">
  <meta property="og:title" content="<?= h($page_title) ?>">
  <meta property="og:description" content="<?= h($page_desc) ?>">
  <meta property="og:url" content="<?= h($canonical) ?>">
  <meta property="og:image" content="<?= h($og_image) ?>">
  <meta property="og:locale" content="<?= h(str_replace('-', '_', current_lang())) ?>">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= h($page_title) ?>">
  <meta name="twitter:description" content="<?= h($page_desc) ?>">
  <meta name="twitter:image" content="<?= h($og_image) ?>">

  <!-- PWA -->
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="#006837">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Manami Education">
  <?php require __DIR__ . '/partials/favicon.php'; ?>

  <script type="application/ld+json"><?= json_encode($org_jsonld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>

  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            paper:    '#FAF8F4',
            'paper-2':'#F4F1EA',
            ink:      { DEFAULT: '#1E242B', 2: '#4A5159', 3: '#8A857C' },
            hair:     { DEFAULT: '#E5E1D9', 2: '#D7D2C7' },
            brand:    { DEFAULT: '#006837', dark: '#004d28', light: '#cce8d6' },
            navy:     { DEFAULT: '#1A2B6D', dark: '#101f4f', light: '#d6dcec' },
            accent:   { DEFAULT: '#E31E24', dark: '#a8131a', light: '#f7c8ca' },
            gold:     '#B8923B',
          },
          fontFamily: {
            display: ['"Playfair Display"', 'Georgia', 'serif'],
            sans:    ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
          },
        }
      }
    }
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,500;1,600;1,700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/flags.css">
  <link rel="stylesheet" href="assets/css/style.css">

  <script defer src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js"></script>
  <script defer src="assets/js/main.js"></script>
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
    <?php require __DIR__ . '/partials/hero.php'; ?>
    <?php require __DIR__ . '/partials/marquee.php'; ?>
    <?php require __DIR__ . '/partials/about.php'; ?>
    <?php require __DIR__ . '/partials/why-japan.php'; ?>
    <?php require __DIR__ . '/partials/services.php'; ?>
    <?php require __DIR__ . '/partials/countries.php'; ?>
    <?php require __DIR__ . '/partials/stats.php'; ?>
    <?php require __DIR__ . '/partials/success-stories.php'; ?>
    <?php require __DIR__ . '/partials/testimonials.php'; ?>
    <?php require __DIR__ . '/partials/cta-form.php'; ?>
    <?php require __DIR__ . '/partials/blog.php'; ?>
    <?php require __DIR__ . '/partials/faq.php'; ?>
    <?php require __DIR__ . '/partials/pre-footer.php'; ?>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>

  <a href="<?= h(url('contact.php')) ?>" class="hidden md:inline-flex fixed bottom-5 right-5 bg-ink text-white font-semibold text-sm px-5 py-3 rounded-sm shadow-2xl z-50">
    <?= h(t('floating_cta.get_appointment')) ?>
  </a>
</body>
</html>
