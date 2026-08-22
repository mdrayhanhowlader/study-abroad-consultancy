<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin','editor','viewer');
$adminPage = 'orders';
$pageTitle = 'Orders';

$status = $_GET['status'] ?? '';
$validStatuses = ['placed','confirmed','paid','processing','shipped','delivered','completed','cancelled'];
$where = []; $params = [];
if (in_array($status, $validStatuses, true)) { $where[] = 'status=?'; $params[] = $status; }
$sql = 'SELECT * FROM orders';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY id DESC LIMIT 200';
$rows = App\Database::fetchAll($sql, $params);
$statusColors = [
    'placed' => 'bg-accent/10 text-accent',
    'confirmed' => 'bg-yellow-100 text-yellow-800',
    'paid' => 'bg-brand/10 text-brand',
    'processing' => 'bg-navy/10 text-navy',
    'shipped' => 'bg-purple-100 text-purple-700',
    'delivered' => 'bg-brand/10 text-brand',
    'completed' => 'bg-brand/20 text-brand-dark',
    'cancelled' => 'bg-gray-200 text-gray-600',
];

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <section class="flex flex-wrap gap-2 text-sm">
      <a href="index.php" class="px-3 py-1.5 rounded-md <?= !$status ? 'bg-brand text-white' : 'bg-white border border-gray-200 text-gray-600' ?>">All</a>
      <?php foreach ($validStatuses as $s): ?>
        <a href="?status=<?= h($s) ?>" class="px-3 py-1.5 rounded-md <?= $status === $s ? 'bg-brand text-white' : 'bg-white border border-gray-200 text-gray-600' ?>"><?= h(ucfirst($s)) ?></a>
      <?php endforeach; ?>
    </section>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr><th class="text-left px-5 py-2.5">Code</th><th class="text-left px-3 py-2.5">Customer</th><th class="text-left px-3 py-2.5">Phone</th><th class="text-left px-3 py-2.5">Thana</th><th class="text-right px-3 py-2.5">Total</th><th class="text-left px-3 py-2.5">bKash</th><th class="text-left px-3 py-2.5">Status</th><th class="text-right px-5 py-2.5">When</th></tr>
          </thead>
          <tbody>
            <?php if (!$rows): ?>
              <tr><td colspan="8" class="px-5 py-6 text-center text-gray-500 text-sm">No orders match this filter.</td></tr>
            <?php else: foreach ($rows as $o): ?>
              <tr class="border-t border-gray-100 hover:bg-gray-50">
                <td class="px-5 py-2.5 font-mono text-xs"><a href="view.php?id=<?= (int) $o['id'] ?>" class="text-brand hover:underline"><?= h($o['code']) ?></a></td>
                <td class="px-3 py-2.5"><?= h($o['name']) ?><div class="text-xs text-gray-500"><?= h($o['address']) ?></div></td>
                <td class="px-3 py-2.5 text-xs"><?= h($o['phone']) ?></td>
                <td class="px-3 py-2.5 text-xs"><?= h($o['thana']) ?></td>
                <td class="px-3 py-2.5 text-right font-semibold"><?= format_bdt((float) $o['total']) ?></td>
                <td class="px-3 py-2.5 text-xs font-mono"><?= h($o['bkash_txn'] ?? '—') ?></td>
                <td class="px-3 py-2.5"><span class="text-xs px-2 py-0.5 rounded-full <?= $statusColors[$o['status']] ?? 'bg-gray-100' ?>"><?= h($o['status']) ?></span></td>
                <td class="px-5 py-2.5 text-right text-xs text-gray-500"><?= h($o['created_at']) ?></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</div>
<?php require __DIR__ . '/../_layout/footer.php'; ?>
