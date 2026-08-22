<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin','editor','viewer');
$adminPage = 'orders';
$id = (int) ($_GET['id'] ?? 0);
$order = App\Database::fetchOne('SELECT * FROM orders WHERE id=?', [$id]);
if (!$order) { App\Flash::set('error', 'Order not found.'); redirect('/admin/orders/index.php'); }

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    $action = $_POST['action'] ?? '';
    if ($action === 'transition' && App\Auth::hasRole('admin','editor')) {
        $new = $_POST['new_status'] ?? '';
        $valid = ['placed','confirmed','paid','processing','shipped','delivered','completed','cancelled'];
        if (in_array($new, $valid, true)) {
            try {
                App\Order::transition($id, $new, (int) App\Auth::user()['id']);
                App\Flash::set('success', 'Order moved to ' . $new . '.');
            } catch (Throwable $e) {
                App\Flash::set('error', $e->getMessage());
            }
        }
    } elseif ($action === 'update_info' && App\Auth::hasRole('admin','editor')) {
        App\Database::update('orders', [
            'name'    => trim((string) ($_POST['name']    ?? $order['name'])),
            'phone'   => trim((string) ($_POST['phone']   ?? $order['phone'])),
            'address' => trim((string) ($_POST['address'] ?? $order['address'])),
            'thana'   => trim((string) ($_POST['thana']   ?? $order['thana'])),
            'email'   => trim((string) ($_POST['email']   ?? $order['email'])),
            'notes'   => (string) ($_POST['notes'] ?? $order['notes']),
        ], 'id=:id', ['id' => $id]);
        App\Content::log((int) App\Auth::user()['id'], 'order.update', 'order', $id);
        App\Flash::set('success', 'Order info updated.');
    }
    redirect('/admin/orders/view.php?id=' . $id);
}

$items      = App\Order::items($id);
$payments   = App\Database::fetchAll('SELECT * FROM payment_transactions WHERE order_id=? ORDER BY id DESC', [$id]);
$history    = App\Database::fetchAll('SELECT al.*, u.name AS user_name FROM activity_log al LEFT JOIN users u ON u.id=al.user_id WHERE al.entity=\'order\' AND al.entity_id=? ORDER BY al.id DESC', [$id]);
$pageTitle  = 'Order ' . $order['code'];
$screenshot = $order['bkash_screenshot'] ? App\Upload::url($order['bkash_screenshot']) : null;

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <div class="flex items-center gap-3 flex-wrap">
      <a href="index.php" class="text-sm text-gray-500 hover:text-brand">← All orders</a>
      <span class="text-sm text-gray-400">·</span>
      <span class="text-sm font-mono text-ink"><?= h($order['code']) ?></span>
      <span class="text-xs px-2 py-0.5 rounded-full bg-navy/10 text-navy uppercase"><?= h($order['status']) ?></span>
      <span class="ml-auto text-sm text-gray-500">Placed: <?= h($order['created_at']) ?></span>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
      <section class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="font-semibold text-ink mb-4">Items</h2>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
              <tr><th class="text-left px-3 py-2.5">Item</th><th class="text-right px-3 py-2.5">Unit</th><th class="text-right px-3 py-2.5">Qty</th><th class="text-right px-5 py-2.5">Line</th></tr>
            </thead>
            <tbody>
              <?php foreach ($items as $it): ?>
                <tr class="border-t border-gray-100">
                  <td class="px-3 py-2.5"><?= h($it['title']) ?></td>
                  <td class="px-3 py-2.5 text-right"><?= format_bdt((float) $it['unit_price']) ?></td>
                  <td class="px-3 py-2.5 text-right"><?= (int) $it['qty'] ?></td>
                  <td class="px-5 py-2.5 text-right font-semibold"><?= format_bdt((float) $it['line_total']) ?></td>
                </tr>
              <?php endforeach; ?>
              <tr class="border-t border-gray-200"><td colspan="3" class="px-3 py-2.5 text-right text-gray-500">Subtotal</td><td class="px-5 py-2.5 text-right"><?= format_bdt((float) $order['subtotal']) ?></td></tr>
              <tr><td colspan="3" class="px-3 py-2.5 text-right text-gray-500">Shipping</td><td class="px-5 py-2.5 text-right"><?= format_bdt((float) $order['shipping']) ?></td></tr>
              <tr class="border-t border-gray-200"><td colspan="3" class="px-3 py-2.5 text-right font-semibold">Total</td><td class="px-5 py-2.5 text-right text-lg font-bold text-brand"><?= format_bdt((float) $order['total']) ?></td></tr>
            </tbody>
          </table>
        </div>

        <h2 class="font-semibold text-ink mt-8 mb-4">Customer</h2>
        <form method="post" class="grid sm:grid-cols-2 gap-3">
          <?= App\Csrf::field() ?>
          <input type="hidden" name="action" value="update_info">
          <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Name</span>
            <input name="name" value="<?= h($order['name']) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
          </label>
          <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Phone</span>
            <input name="phone" value="<?= h($order['phone']) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
          </label>
          <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Address</span>
            <input name="address" value="<?= h($order['address']) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
          </label>
          <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Thana</span>
            <input name="thana" value="<?= h($order['thana']) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
          </label>
          <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Email</span>
            <input type="email" name="email" value="<?= h($order['email'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
          </label>
          <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Internal notes</span>
            <textarea name="notes" rows="3" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm"><?= h($order['notes'] ?? '') ?></textarea>
          </label>
          <div class="sm:col-span-2"><button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">Save customer info</button></div>
        </form>
      </section>

      <aside class="space-y-4">
        <?php if ($payments): ?>
          <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="font-semibold text-ink mb-3">Payment Transactions</h2>
            <div class="space-y-3">
              <?php foreach ($payments as $pt): ?>
                <div class="border border-gray-100 rounded-lg p-3 text-sm">
                  <div class="flex items-center justify-between">
                    <span class="text-xs px-2 py-0.5 rounded-full bg-navy/10 text-navy"><?= h($pt['method']) ?></span>
                    <span class="text-xs px-2 py-0.5 rounded-full <?= $pt['status'] === 'verified' ? 'bg-brand/10 text-brand' : ($pt['status'] === 'failed' ? 'bg-accent/10 text-accent' : 'bg-yellow-100 text-yellow-700') ?>"><?= h($pt['status']) ?></span>
                  </div>
                  <div class="mt-2 font-semibold"><?= format_bdt((float) $pt['amount']) ?> <span class="text-xs text-gray-400"><?= h($pt['currency']) ?></span></div>
                  <?php if ($pt['txn_reference']): ?><div class="text-xs font-mono text-gray-500 mt-1">Ref: <?= h($pt['txn_reference']) ?></div><?php endif; ?>
                  <?php if ($pt['verified_at']): ?><div class="text-xs text-gray-400 mt-0.5">Verified: <?= h($pt['verified_at']) ?></div><?php endif; ?>
                  <?php if ($pt['notes']): ?><div class="text-xs text-gray-500 mt-1"><?= h($pt['notes']) ?></div><?php endif; ?>
                  <?php if ($pt['screenshot_path']): ?>
                    <a href="<?= h(App\Upload::url($pt['screenshot_path'])) ?>" target="_blank" class="block mt-2">
                      <img src="<?= h(App\Upload::url($pt['screenshot_path'])) ?>" alt="Payment screenshot" class="rounded border border-gray-200 w-full">
                    </a>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
          <h2 class="font-semibold text-ink mb-3">bKash payment</h2>
          <div class="text-sm space-y-2">
            <div><span class="text-gray-500 text-xs uppercase tracking-wide">Transaction ID</span><div class="font-mono"><?= h($order['bkash_txn'] ?? '—') ?></div></div>
            <div><span class="text-gray-500 text-xs uppercase tracking-wide">Status</span><div><span class="text-xs px-2 py-0.5 rounded-full bg-navy/10 text-navy uppercase"><?= h($order['status']) ?></span></div></div>
            <?php if ($screenshot): ?>
              <div><span class="text-gray-500 text-xs uppercase tracking-wide">Screenshot</span>
                <a href="<?= h($screenshot) ?>" target="_blank"><img src="<?= h($screenshot) ?>" alt="bKash screenshot" class="mt-1 rounded border border-gray-200 w-full"></a>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <?php if (App\Auth::hasRole('admin','editor')): ?>
          <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="font-semibold text-ink mb-3">Move to status</h2>
            <form method="post" class="space-y-2">
              <?= App\Csrf::field() ?>
              <input type="hidden" name="action" value="transition">
              <select name="new_status" class="w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm bg-white">
                <?php foreach (['placed','confirmed','paid','processing','shipped','delivered','completed','cancelled'] as $s): ?>
                  <option value="<?= h($s) ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= h(ucfirst($s)) ?></option>
                <?php endforeach; ?>
              </select>
              <button class="w-full bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">Update status</button>
              <p class="text-xs text-gray-500 mt-1">Customer will be emailed automatically when an email is on file.</p>
            </form>
          </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
          <h2 class="font-semibold text-ink mb-3">History</h2>
          <ul class="text-sm space-y-2">
            <?php if (!$history): ?>
              <li class="text-gray-500 text-xs">No activity yet.</li>
            <?php else: foreach ($history as $h): ?>
              <li class="border-l-2 border-brand/30 pl-3">
                <div class="text-xs text-ink"><?= h($h['action']) ?></div>
                <div class="text-xs text-gray-500"><?= h($h['user_name'] ?? 'system') ?> · <?= h($h['created_at']) ?></div>
                <?php if ($h['meta']): ?>
                  <div class="text-xs text-gray-500 font-mono mt-0.5 break-all"><?= h($h['meta']) ?></div>
                <?php endif; ?>
              </li>
            <?php endforeach; endif; ?>
          </ul>
        </div>
      </aside>
    </div>
  </main>
</div>
<?php require __DIR__ . '/../_layout/footer.php'; ?>
