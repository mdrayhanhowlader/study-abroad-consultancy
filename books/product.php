<?php
require __DIR__ . '/../partials/_icons.php';
require __DIR__ . '/../app/bootstrap.php';

$site = App\DataAdapter::site();
$slug = preg_replace('/[^a-z0-9-]/', '', (string) ($_GET['slug'] ?? ''));
$product = $slug ? App\Product::bySlug($slug) : null;
if (!$product) { http_response_code(404); }

$page_title = ($product ? $product['title'] : t('product.not_found_title')) . ' — ' . $site['brand']['full_name'];
$page_desc  = $product['summary'] ?? t('product.not_found_title');
$canonical  = $site['brand']['domain'] . '/books/' . urlencode($slug) . '.php';
$cover      = $product && $product['cover_id'] ? App\Content::mediaUrl((int) $product['cover_id']) : null;
$typeLabel  = $product ? t('books.type_' . ($product['type'] ?? '')) : '';

$jsonld = $product ? [
    '@context' => 'https://schema.org',
    '@type'    => 'Product',
    'name'     => $product['title'],
    'description' => $product['summary'] ?? '',
    'image'    => $cover ?: $site['brand']['og_image'],
    'sku'      => 'ME-' . $product['id'],
    'brand'    => ['@type' => 'Brand', 'name' => $site['brand']['full_name']],
    'offers'   => [
        '@type'         => 'Offer',
        'price'         => number_format((float) $product['price_bdt'], 2, '.', ''),
        'priceCurrency' => 'BDT',
        'availability'  => $product['active'] && $product['stock'] > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        'url'           => $canonical,
        'seller'        => ['@type' => 'Organization', 'name' => $site['brand']['full_name']],
    ],
] : null;
?>
<!doctype html>
<html lang="<?= h(current_lang()) ?>" dir="<?= h(\App\I18n::dir(current_lang())) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= h($page_title) ?></title>
  <meta name="description" content="<?= h($page_desc) ?>">
  <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">
  <meta property="og:type" content="product">
  <meta property="og:title" content="<?= h($page_title) ?>">
  <meta property="og:description" content="<?= h($page_desc) ?>">
  <meta property="og:url" content="<?= h($canonical) ?>">
  <?php if ($cover): ?><meta property="og:image" content="<?= h($cover) ?>"><?php endif; ?>
  <?php if ($jsonld): ?><script type="application/ld+json"><?= json_encode($jsonld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script><?php endif; ?>

  <?php require __DIR__ . '/../partials/favicon.php'; ?>
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <script>
    tailwind.config = { theme: { extend: {
      colors: {
        paper:    '#FAF8F4', 'paper-2': '#F4F1EA',
        ink:      { DEFAULT: '#1E242B', 2: '#4A5159', 3: '#8A857C' },
        hair:     { DEFAULT: '#E5E1D9', 2: '#D7D2C7' },
        brand:    { DEFAULT: '#006837', dark: '#004d28', light: '#cce8d6' },
        navy:     { DEFAULT: '#1A2B6D', dark: '#101f4f', light: '#d6dcec' },
        accent:   { DEFAULT: '#E31E24', dark: '#a8131a', light: '#f7c8ca' },
        gold:     '#B8923B',
      },
      fontFamily: { display: ['"Playfair Display"','Georgia','serif'], sans: ['Inter','ui-sans-serif','system-ui','sans-serif'] }
    }}}
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,500;1,600;1,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= h(url('assets/css/style.css')) ?>">
  <script defer src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js"></script>
</head>
<body class="font-sans text-ink antialiased bg-paper">
  <?php require __DIR__ . '/../partials/topbar.php'; ?>
  <?php require __DIR__ . '/../partials/header.php'; ?>

  <main class="section">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
      <a href="<?= h(url('books.php')) ?>" class="text-xs tracking-widest uppercase text-ink-3 hover:text-brand"><?= h(t('product.back_to_catalogue')) ?></a>
    </div>

      <?php if (!$product): ?>
        <div class="mt-10 bg-white border border-hair p-12 text-center" style="border-color:#E5E1D9">
          <div class="text-5xl">🔍</div>
          <h1 class="font-display text-3xl font-bold text-ink mt-4"><?= h(t('product.not_found_title')) ?></h1>
          <p class="text-ink-2 mt-2"><?= h(t('product.not_found_body')) ?></p>
          <a href="<?= h(url('books.php')) ?>" class="mt-6 inline-flex btn btn-primary"><?= h(t('product.back')) ?></a>
        </div>
      <?php else: ?>
        <div class="mt-6 grid lg:grid-cols-2 gap-12 lg:gap-16">
          <div class="reveal">
            <div class="aspect-[4/3] bg-paper-2 overflow-hidden" style="background:#F4F1EA">
              <?php if ($cover): ?>
                <img src="<?= h($cover) ?>" alt="<?= h($product['title']) ?>" class="w-full h-full object-cover">
              <?php else: ?>
                <div class="w-full h-full grid place-items-center text-ink-3/30">
                  <svg class="w-32 h-32" viewBox="0 0 24 24" fill="currentColor"><path d="M4 3h12a3 3 0 0 1 3 3v15a1 1 0 0 1-1 1H7a3 3 0 0 1-3-3V3zm12 2H6v13a1 1 0 0 0 1 1h11V5zM8 7h7v2H8V7zm0 4h7v2H8v-2z"/></svg>
                </div>
              <?php endif; ?>
            </div>
          </div>
          <div class="reveal reveal-d2">
            <div class="eyebrow" data-num="·"><?= h($typeLabel) ?></div>
            <h1 class="mt-4 font-display text-4xl sm:text-5xl font-bold text-ink leading-[1.05] tracking-tight"><?= h($product['title']) ?></h1>
            <p class="mt-5 text-ink-2 leading-relaxed text-lg"><?= h($product['summary'] ?? '') ?></p>

            <?php if (!empty($product['description'])): ?>
              <div class="mt-6 prose prose-sm max-w-none text-ink-2"><?= $product['description'] ?></div>
            <?php endif; ?>

            <div class="mt-8 flex flex-wrap items-end gap-5 hairline pt-8" style="border-color:#E5E1D9">
              <div>
                <div class="font-display text-4xl font-bold text-ink"><?= format_bdt((float) $product['price_bdt']) ?></div>
                <?php if ($product['type'] === 'physical'): ?>
                  <div class="text-xs text-ink-3 mt-1"><?= h(t('common.in_stock', ['n' => (int) $product['stock']])) ?></div>
                <?php endif; ?>
              </div>
              <button type="button" data-add-to-cart="<?= (int) $product['id'] ?>" class="btn btn-primary">
                <?= h(t('common.add_to_cart')) ?> →
              </button>
              <a href="<?= h(url('books.php#checkout')) ?>" class="text-sm text-brand font-semibold hover:underline"><?= h(t('product.or_checkout')) ?></a>
            </div>

            <ul class="mt-8 space-y-2 text-sm text-ink-2">
              <li class="flex items-center gap-2"><span class="text-brand">✓</span> <?= h(t('product.promise_1')) ?></li>
              <li class="flex items-center gap-2"><span class="text-brand">✓</span> <?= h(t('product.promise_2')) ?></li>
              <li class="flex items-center gap-2"><span class="text-brand">✓</span> <?= h(t('product.promise_3')) ?></li>
              <li class="flex items-center gap-2"><span class="text-brand">✓</span> <?= h(t('product.promise_4')) ?></li>
            </ul>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <?php require __DIR__ . '/../partials/footer.php'; ?>

  <script>
  (function () {
    document.querySelectorAll('[data-add-to-cart]').forEach(btn => {
      btn.addEventListener('click', () => {
        fetch('../api/cart.php?action=add&id=' + btn.dataset.addToCart + '&qty=1').then(() => {
          window.location.href = '<?= h(url('books.php#checkout')) ?>';
        });
      });
    });
  })();
  </script>

  <?php if ($product && App\Pixel::isEnabled()): ?>
    <script>fbq('track', 'ViewContent', {content_ids: ['<?= h($product['slug']) ?>'], content_type: 'product', value: <?= (float) $product['price_bdt'] ?>, currency: 'BDT'});</script>
  <?php endif; ?>
</body>
</html>
