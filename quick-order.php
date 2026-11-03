<?php
declare(strict_types=1);

header('Location: books.php', true, 301);
exit;

require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';

$site = App\DataAdapter::site();

$page_title = t('quick_order.eyebrow') . ' — ' . $site['brand']['full_name'];
$page_desc  = t('quick_order.body');
$canonical  = $site['brand']['domain'] . '/quick-order.php';

$success = false;
$error   = '';
$orderCode = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();

    $name    = trim((string) ($_POST['name'] ?? ''));
    $phone   = trim((string) ($_POST['phone'] ?? ''));
    $email   = trim((string) ($_POST['email'] ?? ''));
    $item    = trim((string) ($_POST['item'] ?? ''));
    $qty     = (int) ($_POST['quantity'] ?? 1);
    $address = trim((string) ($_POST['address'] ?? ''));
    $thana   = trim((string) ($_POST['thana'] ?? ''));
    $payment = in_array(($_POST['payment_method'] ?? ''), ['bkash', 'cod'], true) ? $_POST['payment_method'] : 'bkash';
    $bkashTxn = trim((string) ($_POST['bkash_txn'] ?? ''));
    $notes   = trim((string) ($_POST['notes'] ?? ''));

    if ($name && $phone && $item && $address && $thana && $qty > 0) {
        $code = order_code();

        $detailLines = [
            'Requested item: ' . $item,
            'Quantity: ' . $qty,
        ];
        if ($notes !== '') {
            $detailLines[] = 'Customer notes: ' . $notes;
        }
        $detailLines[] = 'Payment method: ' . strtoupper($payment);
        if ($payment === 'bkash' && $bkashTxn !== '') {
            $detailLines[] = 'bKash txn: ' . $bkashTxn;
        }
        $orderNotes = implode("\n", $detailLines);

        try {
            $orderId = App\Database::insert('orders', [
                'code'           => $code,
                'name'           => $name,
                'address'        => $address,
                'thana'          => $thana,
                'phone'          => $phone,
                'email'          => $email ?: null,
                'payment_method' => $payment,
                'bkash_txn'      => $bkashTxn !== '' ? $bkashTxn : null,
                'subtotal'       => 0,
                'shipping'       => 0,
                'total'          => 0,
                'status'         => 'placed',
                'notes'          => $orderNotes,
                'ip'             => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent'     => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
            ]);

            App\Content::log(null, 'order.placed', 'order', $orderId, ['code' => $code, 'source' => 'quick_order']);

            $mailBody  = '<p>New quick order request from the website.</p>';
            $mailBody .= '<p><strong>Order code:</strong> ' . h($code) . '<br>';
            $mailBody .= '<strong>Name:</strong> ' . h($name) . '<br>';
            $mailBody .= '<strong>Phone:</strong> ' . h($phone) . '<br>';
            $mailBody .= '<strong>Email:</strong> ' . h($email) . '<br>';
            $mailBody .= '<strong>Address:</strong> ' . h($address) . ', ' . h($thana) . '</p>';
            $mailBody .= '<p><strong>Item:</strong> ' . h($item) . '<br><strong>Quantity:</strong> ' . (int) $qty . '</p>';
            $mailBody .= '<p><strong>Payment:</strong> ' . h(strtoupper($payment)) . '</p>';
            if ($payment === 'bkash' && $bkashTxn !== '') {
                $mailBody .= '<p><strong>bKash TXN:</strong> ' . h($bkashTxn) . '</p>';
            }
            if ($notes !== '') {
                $mailBody .= '<p><strong>Notes:</strong><br>' . nl2br(h($notes)) . '</p>';
            }
            App\Mail::send($site['contact']['email'], $site['brand']['full_name'], 'Quick order: ' . $code, App\Mail::wrap('Quick order request', $mailBody));

            $success = true;
            $orderCode = $code;
        } catch (Throwable $e) {
            error_log('Quick order error: ' . $e->getMessage());
            $error = 'Something went wrong. Please try again or call us directly.';
        }
    } else {
        $error = t('quick_order.error_required');
    }
}
?>
<!doctype html>
<html lang="<?= h(current_lang()) ?>" dir="<?= h(\App\I18n::dir(current_lang())) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= h($page_title) ?></title>
  <meta name="description" content="<?= h($page_desc) ?>">
  <link rel="canonical" href="<?= h($canonical) ?>">

  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= h($page_title) ?>">
  <meta property="og:description" content="<?= h($page_desc) ?>">
  <meta property="og:url" content="<?= h($canonical) ?>">
  <meta property="og:image" content="<?= h($site['brand']['og_image']) ?>">

  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
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
        }
      }
    }
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,500;1,600;1,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">

  <!-- PWA -->
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="#006837">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Manami Education">
  <?php require __DIR__ . '/partials/favicon.php'; ?>

  <script defer src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js"></script>
</head>
<body class="font-sans text-ink antialiased bg-paper">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <?php require __DIR__ . '/partials/header.php'; ?>

  <main>
    <section class="relative overflow-hidden" style="background: linear-gradient(180deg, #1E242B 0%, #1A2B6D 100%);">
      <div class="bg-orb" style="width:500px;height:500px;background:#006837;top:-100px;right:-100px;opacity:0.18"></div>
      <div class="mx-auto max-w-7xl px-4 lg:px-6 py-20 lg:py-32 relative">
        <div class="max-w-3xl">
          <div class="eyebrow" data-num="09" style="color:#B8923B"><?= h(t('quick_order.eyebrow')) ?></div>
          <h1 class="mt-5 font-display text-5xl sm:text-6xl lg:text-7xl font-bold leading-[0.95] text-white tracking-tight"><?= h(t('quick_order.headline')) ?></h1>
          <p class="mt-6 text-white/80 text-lg max-w-2xl"><?= h(t('quick_order.body')) ?></p>
        </div>
      </div>
    </section>

    <section class="section bg-paper">
      <div class="mx-auto max-w-3xl px-4 lg:px-6">
        <?php if ($success): ?>
          <div class="bg-white border border-hair p-8 sm:p-12 text-center" style="border-color:#E5E1D9">
            <div class="w-16 h-16 mx-auto rounded-full bg-brand/10 grid place-items-center" style="background:rgba(0,104,55,0.1)">
              <svg class="w-9 h-9 text-brand" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
            </div>
            <h2 class="mt-6 font-display text-3xl font-bold text-ink"><?= h(t('quick_order.success_title')) ?></h2>
            <div class="mt-4 inline-block bg-brand/5 border border-brand/20 px-5 py-2 font-mono text-xl font-bold text-brand" style="background:rgba(0,104,55,0.05);border-color:rgba(0,104,55,0.2)"><?= h($orderCode) ?></div>
            <p class="mt-5 text-ink-2"><?= h(strtr(t('quick_order.success_body'), ['{code}' => $orderCode])) ?></p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
              <a href="index.php" class="btn btn-primary"><?= h(t('common.go_back')) ?> →</a>
              <a href="books.php" class="btn btn-secondary"><?= h(t('thanks.continue_shopping')) ?></a>
            </div>
          </div>
        <?php else: ?>
          <div class="bg-white border border-hair p-7 sm:p-10" style="border-color:#E5E1D9">
            <?php if ($error): ?>
              <div class="mb-6 bg-accent/5 border border-accent/30 p-4 text-sm" style="background:rgba(227,30,36,0.05);border-color:rgba(227,30,36,0.3);color:#E31E24"><?= h($error) ?></div>
            <?php endif; ?>

            <form method="post" x-data="{ paymentMethod: 'bkash' }" class="space-y-5">
              <?= App\Csrf::field() ?>

              <div class="grid sm:grid-cols-2 gap-4">
                <label class="block">
                  <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('quick_order.full_name')) ?> *</span>
                  <input name="name" required value="<?= h($_POST['name'] ?? '') ?>" class="input mt-2">
                </label>
                <label class="block">
                  <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('quick_order.phone')) ?> *</span>
                  <input name="phone" required type="tel" value="<?= h($_POST['phone'] ?? '') ?>" class="input mt-2">
                </label>
              </div>

              <label class="block">
                <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('quick_order.email')) ?></span>
                <input name="email" type="email" value="<?= h($_POST['email'] ?? '') ?>" class="input mt-2">
              </label>

              <div class="grid sm:grid-cols-3 gap-4">
                <label class="block sm:col-span-2">
                  <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('quick_order.item')) ?> *</span>
                  <input name="item" required placeholder="e.g. Manami Japan Handbook" value="<?= h($_POST['item'] ?? '') ?>" class="input mt-2">
                </label>
                <label class="block">
                  <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('quick_order.quantity')) ?> *</span>
                  <input name="quantity" required type="number" min="1" value="<?= h($_POST['quantity'] ?? '1') ?>" class="input mt-2">
                </label>
              </div>

              <div class="grid sm:grid-cols-2 gap-4">
                <label class="block">
                  <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('quick_order.address')) ?> *</span>
                  <input name="address" required placeholder="House, Road, Area" value="<?= h($_POST['address'] ?? '') ?>" class="input mt-2">
                </label>
                <label class="block">
                  <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('quick_order.thana')) ?> *</span>
                  <input name="thana" required placeholder="e.g. Farmgate" value="<?= h($_POST['thana'] ?? '') ?>" class="input mt-2">
                </label>
              </div>

              <div>
                <h3 class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest mb-3"><?= h(t('quick_order.payment_method')) ?></h3>
                <div class="grid sm:grid-cols-2 gap-3">
                  <label class="relative flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition hover:border-brand has-[:checked]:border-brand has-[:checked]:bg-brand/5" style="border-color:#E5E1D9">
                    <input type="radio" name="payment_method" value="bkash" checked class="mt-1 accent-brand" @change="paymentMethod = 'bkash'">
                    <div class="flex-1">
                      <div class="font-semibold text-ink text-sm">bKash</div>
                      <div class="text-xs text-ink-2 mt-0.5">Pay now via bKash</div>
                    </div>
                    <span class="text-xl">📱</span>
                  </label>
                  <label class="relative flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition hover:border-brand has-[:checked]:border-brand has-[:checked]:bg-brand/5" style="border-color:#E5E1D9">
                    <input type="radio" name="payment_method" value="cod" class="mt-1 accent-brand" @change="paymentMethod = 'cod'">
                    <div class="flex-1">
                      <div class="font-semibold text-ink text-sm">Cash on Delivery</div>
                      <div class="text-xs text-ink-2 mt-0.5">Pay when you receive</div>
                    </div>
                    <span class="text-xl">💵</span>
                  </label>
                </div>

                <div x-show="paymentMethod === 'bkash'" class="mt-4">
                  <label class="block">
                    <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('quick_order.bkash_txn')) ?></span>
                    <input name="bkash_txn" placeholder="<?= h(t('quick_order.bkash_txn_ph')) ?>" value="<?= h($_POST['bkash_txn'] ?? '') ?>" class="input mt-2 font-mono">
                  </label>
                </div>
              </div>

              <label class="block">
                <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('quick_order.notes')) ?></span>
                <textarea name="notes" rows="3" placeholder="Any extra details…" class="input mt-2"><?= h($_POST['notes'] ?? '') ?></textarea>
              </label>

              <button type="submit" class="w-full btn btn-primary justify-center"><?= h(t('quick_order.place_order')) ?> →</button>
              <p class="text-xs text-ink-3 text-center">* <?= h(t('common.required')) ?></p>
            </form>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <?php require __DIR__ . '/partials/pre-footer.php'; ?>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
