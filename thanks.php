<?php
require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';
$site = App\DataAdapter::site();

$code = preg_replace('/[^A-Z0-9\-]/i', '', (string) ($_GET['code'] ?? ''));
$order = $code ? App\Order::findByCode($code) : null;

$page_title = ($order ? 'Order ' . $order['code'] : 'Thank you') . ' — ' . $site['brand']['full_name'];
$page_desc  = t('thanks.verify_body');
$canonical  = $site['brand']['domain'] . '/thanks.php';
?>
<!doctype html>
<html lang="<?= h(current_lang()) ?>" dir="<?= h(\App\I18n::dir(current_lang())) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= h($page_title) ?></title>
  <meta name="description" content="<?= h($page_desc) ?>">
  <meta name="robots" content="noindex, follow">
  <link rel="canonical" href="<?= h($canonical) ?>">
  <script src="https://cdn.tailwindcss.com"></script>
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
  <link rel="stylesheet" href="assets/css/style.css">
  <?php require __DIR__ . '/partials/favicon.php'; ?>
</head>
<body class="font-sans text-ink antialiased bg-paper min-h-screen">

  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <?php require __DIR__ . '/partials/header.php'; ?>

  <main class="py-16 lg:py-24">
    <div class="mx-auto max-w-2xl px-4 lg:px-6">
      <div class="bg-white border border-hair p-8 sm:p-12 text-center" style="border-color:#E5E1D9">
        <div class="w-16 h-16 mx-auto rounded-full bg-brand/10 grid place-items-center" style="background:rgba(0,104,55,0.1)">
          <svg class="w-9 h-9 text-brand" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
        </div>
        <div class="eyebrow justify-center mt-6" data-num="✓"><?= h(t('thanks.confirmed')) ?></div>
        <h1 class="font-display text-3xl sm:text-4xl font-bold text-ink mt-3 leading-tight"><?= h(t('thanks.title')) ?></h1>

        <?php if ($order): ?>
          <p class="mt-3 text-ink-3 text-sm tracking-widest uppercase"><?= h(t('thanks.code_label')) ?></p>
          <div class="mt-2 inline-block bg-brand/5 border border-brand/20 px-5 py-2 font-mono text-xl font-bold text-brand" style="background:rgba(0,104,55,0.05);border-color:rgba(0,104,55,0.2)"><?= h($order['code']) ?></div>

          <div class="mt-8 text-left bg-paper-2 p-6 text-sm space-y-3" style="background:#F4F1EA">
            <div class="flex justify-between gap-4"><span class="text-ink-3"><?= h(t('thanks.name_label')) ?></span><span class="font-semibold"><?= h($order['name']) ?></span></div>
            <div class="flex justify-between gap-4"><span class="text-ink-3"><?= h(t('thanks.phone_label')) ?></span><span class="font-semibold"><?= h($order['phone']) ?></span></div>
            <div class="flex justify-between gap-4"><span class="text-ink-3"><?= h(t('thanks.address_label')) ?></span><span class="font-semibold text-right"><?= h($order['address']) ?>, <?= h($order['thana']) ?></span></div>
            <div class="flex justify-between gap-4"><span class="text-ink-3"><?= h(t('thanks.bkash_label')) ?></span><span class="font-mono font-semibold"><?= h($order['bkash_txn'] ?? '—') ?></span></div>
            <div class="flex justify-between gap-4 hairline pt-3 mt-3" style="border-color:#E5E1D9"><span class="font-semibold text-ink"><?= h(t('thanks.total_label')) ?></span><span class="font-display font-bold text-brand text-lg"><?= format_bdt((float) $order['total']) ?></span></div>
          </div>

          <p class="mt-7 text-sm text-ink-2">
            <?= h(t('thanks.verify_body')) ?>
            <a href="tel:<?= h($site['contact']['phone_tel']) ?>" class="text-brand font-semibold"><?= h($site['contact']['phone']) ?></a>.
          </p>
        <?php else: ?>
          <p class="mt-3 text-ink-2"><?= h(t('thanks.generic_body')) ?></p>
        <?php endif; ?>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
          <a href="index.php" class="btn btn-primary"><?= h(t('thanks.back_home')) ?> →</a>
          <a href="books.php" class="btn btn-secondary"><?= h(t('thanks.continue_shopping')) ?></a>
        </div>
      </div>
    </div>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>

  <?php if ($order && App\Pixel::isEnabled()): ?>
    <script>
      fbq('track', 'Purchase', {
        value: <?= (float) $order['total'] ?>,
        currency: 'BDT',
        order_id: '<?= h($order['code']) ?>'
      });
    </script>
    <?php App\Pixel::logEvent('Purchase', ['order_id' => (int) $order['id'], 'value' => (float) $order['total'], 'currency' => 'BDT']); ?>
  <?php endif; ?>
</body>
</html>
