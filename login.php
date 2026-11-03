<?php
require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';
$site = App\DataAdapter::site();

$error = '';
$order = null;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    $code = trim((string) ($_POST['code'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    if ($code && $email) {
        $o = App\Database::fetchOne(
            'SELECT * FROM orders WHERE code=? AND (email=? OR phone=?)',
            [$code, $email, $email]
        );
        if ($o) {
            $order = $o;
        } else {
            $error = 'No order found with that code and email/phone. Please check and try again.';
        }
    } else {
        $error = 'Please enter both the order code and your email or phone.';
    }
}
$page_title = 'Track your order — ' . $site['brand']['full_name'];
$page_desc  = 'Track your Manami Education order with your order code and email.';
$canonical  = $site['brand']['domain'] . '/login.php';
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
  <?php require __DIR__ . '/partials/favicon.php'; ?>
  <script defer src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js"></script>
</head>
<body class="font-sans text-ink antialiased bg-paper min-h-screen">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <?php require __DIR__ . '/partials/header.php'; ?>

  <main class="py-16 lg:py-24">
    <div class="mx-auto max-w-2xl px-4 lg:px-6">
      <div class="bg-white border border-hair p-8 sm:p-12" style="border-color:#E5E1D9">
        <div class="eyebrow justify-center" data-num="·">Track order</div>
        <h1 class="mt-4 font-display text-3xl sm:text-4xl font-bold text-ink text-center">Track your order</h1>
        <p class="mt-3 text-ink-3 text-sm text-center">Enter the order code from your confirmation email and the email or phone you used at checkout.</p>

        <?php if ($error): ?>
          <div class="mt-6 bg-accent/5 border border-accent/30 p-4 text-sm" style="background:rgba(227,30,36,0.05);border-color:rgba(227,30,36,0.3);color:#E31E24"><?= h($error) ?></div>
        <?php endif; ?>

        <form method="post" class="mt-6 space-y-4">
          <?= App\Csrf::field() ?>
          <label class="block">
            <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest">Order code</span>
            <input name="code" required placeholder="MAN-20260621-XXXX" value="<?= h($_POST['code'] ?? '') ?>" class="input mt-2 font-mono">
          </label>
          <label class="block">
            <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest">Email or phone</span>
            <input name="email" required placeholder="jane@example.com or +880 17xx" value="<?= h($_POST['email'] ?? '') ?>" class="input mt-2">
          </label>
          <button class="w-full btn btn-primary justify-center">Track order →</button>
        </form>

        <?php if ($order):
          $items = App\Order::items((int) $order['id']);
          $status = $order['status'];
          $paymentMethod = $order['payment_method'] ?? 'bkash';
          $steps = ['placed','confirmed','paid','processing','shipped','delivered','completed'];
          $currentIdx = array_search($status, $steps);
          if ($currentIdx === false) $currentIdx = -1;
          $colorByStatus = [
            'placed'      => ['bg-accent/10 text-accent border-accent/30', 'Pending'],
            'confirmed'   => ['bg-yellow-100 text-yellow-800 border-yellow-300', 'Confirmed'],
            'paid'        => ['bg-brand/10 text-brand border-brand/30', 'Paid'],
            'processing'  => ['bg-navy/10 text-navy border-navy/30', 'Processing'],
            'shipped'     => ['bg-purple-100 text-purple-700 border-purple-300', 'Shipped'],
            'delivered'   => ['bg-brand/10 text-brand border-brand/30', 'Delivered'],
            'completed'   => ['bg-brand/20 text-brand-dark border-brand/30', 'Completed'],
            'cancelled'   => ['bg-gray-200 text-gray-600 border-gray-300', 'Cancelled'],
          ];
          $statusLabel = $colorByStatus[$status][1] ?? ucfirst($status);
          $isPending = in_array($status, ['placed','confirmed'], true);
        ?>
          <div class="mt-8 bg-paper-2 p-6 sm:p-8 rounded-2xl" style="background:#F4F1EA">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <div class="text-[10px] tracking-widest text-ink-3 uppercase">Order</div>
                <div class="font-mono font-bold text-ink mt-1 text-lg"><?= h($order['code']) ?></div>
                <div class="text-xs text-ink-3 mt-1">Placed on <?= h(date('d M Y, h:i A', strtotime($order['created_at']))) ?></div>
              </div>
              <div class="flex flex-col items-start sm:items-end gap-1">
                <span class="text-sm px-4 py-1.5 border rounded-full font-semibold <?= $colorByStatus[$status][0] ?? 'bg-gray-100 text-gray-600 border-gray-300' ?>"><?= h($statusLabel) ?></span>
                <span class="text-xs text-ink-3">Payment: <span class="font-medium text-ink"><?= h(strtoupper($paymentMethod)) ?></span></span>
              </div>
            </div>

            <?php if ($status === 'cancelled'): ?>
              <div class="mt-6 bg-gray-100 border border-gray-300 rounded-xl p-4 text-sm text-gray-700">
                This order has been cancelled. Please contact us if you need help.
              </div>
            <?php else: ?>
              <div class="mt-6 <?= $isPending ? 'bg-accent/5 border-accent/20' : 'bg-brand/5 border-brand/20' ?> rounded-xl p-4 text-sm" style="<?= $isPending ? 'background:rgba(227,30,36,0.05);border:1px solid rgba(227,30,36,0.2)' : 'background:rgba(0,104,55,0.05);border:1px solid rgba(0,104,55,0.2)' ?>">
                <div class="flex items-start gap-3">
                  <span class="text-xl"><?= $isPending ? '⏳' : '✅' ?></span>
                  <div>
                    <div class="font-semibold text-ink"><?= $isPending ? 'Your order is pending' : 'Your order is being processed' ?></div>
                    <p class="text-ink-2 mt-1">
                      <?php if ($isPending): ?>
                        We are verifying your order details. You will receive a confirmation call within 24 hours.
                      <?php else: ?>
                        Your order has been confirmed and is now being prepared for delivery.
                      <?php endif; ?>
                    </p>
                  </div>
                </div>
              </div>

              <ol class="mt-8 grid grid-cols-3 sm:grid-cols-7 gap-2">
                <?php foreach ($steps as $i => $step):
                  $done = $i <= $currentIdx;
                  $active = $i === $currentIdx;
                ?>
                  <li class="flex flex-col items-center text-center gap-2">
                    <span class="w-8 h-8 rounded-full grid place-items-center text-xs font-bold transition <?= $done ? 'bg-brand text-white' : ($active ? 'bg-white border-2 border-brand text-brand' : 'bg-white border border-hair text-ink-3') ?>" style="<?= !$done && !$active ? 'border-color:#E5E1D9' : '' ?>"><?= $done ? '✓' : ($i + 1) ?></span>
                    <span class="text-[10px] uppercase tracking-wider <?= $done || $active ? 'text-ink font-medium' : 'text-ink-3' ?>"><?= h(ucfirst($step)) ?></span>
                  </li>
                <?php endforeach; ?>
              </ol>
            <?php endif; ?>

            <div class="mt-8 hairline pt-6" style="border-color:#E5E1D9">
              <div class="text-[10px] tracking-widest text-ink-3 uppercase mb-3">Items</div>
              <ul class="space-y-3 text-sm">
                <?php foreach ($items as $it): ?>
                  <li class="flex justify-between gap-4">
                    <span class="text-ink"><?= h($it['title']) ?> <span class="text-ink-3">× <?= (int) $it['qty'] ?></span></span>
                    <span class="font-semibold"><?= format_bdt((float) $it['line_total']) ?></span>
                  </li>
                <?php endforeach; ?>
              </ul>
              <div class="mt-4 hairline pt-4 flex justify-between font-display text-lg" style="border-color:#E5E1D9">
                <span class="font-bold text-ink">Total</span>
                <span class="font-bold text-brand"><?= format_bdt((float) $order['total']) ?></span>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
