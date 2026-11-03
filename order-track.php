<?php
declare(strict_types=1);

require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';

$site = App\DataAdapter::site();
$page_title = t('order_tracking.title') . ' — ' . $site['brand']['full_name'];
$page_desc = t('order_tracking.description');
$order = null;
$items = [];
$searched = false;
$notFound = false;
$codeValue = preg_replace('/[^A-Z0-9\-]/i', '', (string) ($_POST['code'] ?? $_GET['code'] ?? ''));
$phoneValue = trim((string) ($_POST['phone'] ?? ''));

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    $searched = true;
    $order = App\Order::findForTracking($codeValue, $phoneValue);
    $notFound = !$order;
    if ($order) {
        $items = App\Database::fetchAll(
            'SELECT oi.*, p.preview_pdf_path FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=? ORDER BY oi.id',
            [(int) $order['id']]
        );
    }
}

$statusLabels = [
    'placed' => t('order_tracking.status_placed'),
    'confirmed' => t('order_tracking.status_confirmed'),
    'paid' => t('order_tracking.status_paid'),
    'processing' => t('order_tracking.status_processing'),
    'shipped' => t('order_tracking.status_shipped'),
    'delivered' => t('order_tracking.status_delivered'),
    'completed' => t('order_tracking.status_completed'),
    'cancelled' => t('order_tracking.status_cancelled'),
];
?>
<!doctype html>
<html lang="<?= h(current_lang()) ?>" dir="<?= h(\App\I18n::dir(current_lang())) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= h($page_title) ?></title>
  <meta name="description" content="<?= h($page_desc) ?>">
  <meta name="robots" content="noindex, follow">
  <link rel="stylesheet" href="<?= h(url('assets/css/style.css')) ?>">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: {
      colors: {
        paper: '#FAF8F4', 'paper-2': '#F4F1EA',
        ink: { DEFAULT: '#1E242B', 2: '#4A5159', 3: '#8A857C' },
        hair: { DEFAULT: '#E5E1D9', 2: '#D7D2C7' },
        brand: { DEFAULT: '#006837', dark: '#004d28', light: '#cce8d6' },
        navy: { DEFAULT: '#1A2B6D', dark: '#101f4f', light: '#d6dcec' },
        accent: { DEFAULT: '#E31E24', dark: '#a8131a', light: '#f7c8ca' },
        gold: '#B8923B'
      },
      fontFamily: { display: ['"Playfair Display"', 'Georgia', 'serif'], sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] }
    }}};
  </script>
  <?php require __DIR__ . '/partials/favicon.php'; ?>
  <script defer src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js"></script>
</head>
<body class="font-sans text-ink antialiased bg-paper min-h-screen">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <?php require __DIR__ . '/partials/header.php'; ?>

  <main class="py-16 lg:py-24">
    <div class="mx-auto max-w-3xl px-4 lg:px-6">
      <div class="text-center mb-10">
        <div class="eyebrow justify-center"><?= h(t('order_tracking.eyebrow')) ?></div>
        <h1 class="mt-4 font-display text-4xl sm:text-5xl font-bold text-ink"><?= h(t('order_tracking.title')) ?></h1>
        <p class="mt-4 text-ink-2"><?= h(t('order_tracking.description')) ?></p>
      </div>

      <form method="post" class="bg-white border border-hair rounded-2xl p-6 sm:p-8 shadow-sm" style="border-color:#E5E1D9">
        <?= App\Csrf::field() ?>
        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-xs font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('order_tracking.code')) ?></span>
            <input name="code" required value="<?= h($codeValue) ?>" placeholder="e.g. ME-ABC123" class="input mt-2">
          </label>
          <label class="block">
            <span class="text-xs font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('order_tracking.phone')) ?></span>
            <input name="phone" required type="tel" value="<?= h($phoneValue) ?>" placeholder="+880 1XXXXXXXXX" class="input mt-2">
          </label>
        </div>
        <button type="submit" class="mt-5 w-full btn btn-primary justify-center rounded-xl"><?= h(t('order_tracking.submit')) ?></button>
        <?php if ($notFound): ?>
          <p role="alert" class="mt-4 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700"><?= h(t('order_tracking.not_found')) ?></p>
        <?php endif; ?>
      </form>

      <?php if ($order): ?>
        <section class="mt-6 bg-white border border-hair rounded-2xl p-6 sm:p-8 shadow-sm" style="border-color:#E5E1D9" aria-live="polite">
          <div class="flex flex-wrap items-start justify-between gap-4 border-b border-hair pb-5" style="border-color:#E5E1D9">
            <div>
              <p class="text-xs uppercase tracking-widest text-ink-3">Order <?= h($order['code']) ?></p>
              <p class="mt-1 text-sm text-ink-2">Placed <?= h(date('M j, Y', strtotime($order['created_at']))) ?></p>
            </div>
            <span class="inline-flex rounded-full bg-brand/10 px-3 py-1.5 text-sm font-semibold text-brand"><?= h($statusLabels[$order['status']] ?? ucfirst($order['status'])) ?></span>
          </div>
          <div class="mt-5">
            <h2 class="font-semibold text-ink"><?= h(t('order_tracking.items')) ?></h2>
            <ul class="mt-3 divide-y divide-hair" style="--tw-divide-opacity:1">
              <?php foreach ($items as $item): ?>
                <li class="flex flex-wrap items-center justify-between gap-4 py-3 text-sm">
                  <span class="text-ink-2"><?= h($item['title']) ?> <span class="text-ink-3">× <?= (int) $item['qty'] ?></span></span>
                  <span class="font-semibold text-ink"><?= format_bdt((float) $item['line_total']) ?></span>
                  <?php if (!empty($item['preview_pdf_path']) && in_array($order['status'], ['paid', 'completed'], true)): ?>
                    <form method="post" action="<?= h(url('api/book-preview.php')) ?>" class="w-full">
                      <?= App\Csrf::field() ?>
                      <input type="hidden" name="order_code" value="<?= h($order['code']) ?>">
                      <input type="hidden" name="phone" value="<?= h($phoneValue) ?>">
                      <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                      <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand px-4 py-2 text-xs font-semibold text-white hover:bg-brand-dark">
                        <?= h(t('order_tracking.download_preview')) ?> <span aria-hidden="true">↓</span>
                      </button>
                    </form>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="mt-4 flex justify-between border-t border-hair pt-4 font-semibold" style="border-color:#E5E1D9">
            <span><?= h(t('order_tracking.total')) ?></span><span class="text-brand"><?= format_bdt((float) $order['total']) ?></span>
          </div>
          <?php if (in_array($order['status'], ['paid', 'completed'], true)): ?>
            <p class="mt-4 text-sm text-ink-2"><?= h(t('order_tracking.preview_paid_note')) ?></p>
          <?php else: ?>
            <p class="mt-4 text-sm text-ink-2"><?= h(t('order_tracking.preview_pending_note')) ?></p>
          <?php endif; ?>
          <p class="mt-5 text-sm text-ink-2"><?= h(t('order_tracking.help')) ?> <a href="tel:<?= h($site['contact']['phone_tel']) ?>" class="font-semibold text-brand"><?= h($site['contact']['phone']) ?></a>.</p>
        </section>
      <?php endif; ?>
    </div>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
