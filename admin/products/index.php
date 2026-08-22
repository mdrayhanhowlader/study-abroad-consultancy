<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin','editor','viewer');
$adminPage = 'products';
$pageTitle = 'Products';

$status = $_GET['status'] ?? '';
$filterType = $_GET['type'] ?? '';
$where = []; $params = [];
if ($status === 'active')  { $where[] = 'active=1'; }
if ($status === 'inactive'){ $where[] = 'active=0'; }
if (in_array($filterType, ['physical','digital','course'], true)) { $where[] = 'type=?'; $params[] = $filterType; }
$sql = 'SELECT p.*, m.path AS cover_path FROM products p LEFT JOIN media m ON m.id=p.cover_id';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY p.sort, p.id DESC';
$rows = App\Database::fetchAll($sql, $params);

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <section class="flex flex-wrap items-center gap-2">
      <a href="create.php" class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2 rounded-md">+ New product</a>
      <div class="ml-auto flex items-center gap-2 text-sm">
        <a href="index.php" class="px-3 py-1.5 rounded-md <?= !$status && !$filterType ? 'bg-brand text-white' : 'bg-white border border-gray-200 text-gray-600' ?>">All</a>
        <a href="?type=physical" class="px-3 py-1.5 rounded-md <?= $filterType === 'physical' ? 'bg-brand text-white' : 'bg-white border border-gray-200 text-gray-600' ?>">Physical</a>
        <a href="?type=digital"  class="px-3 py-1.5 rounded-md <?= $filterType === 'digital'  ? 'bg-brand text-white' : 'bg-white border border-gray-200 text-gray-600' ?>">Digital</a>
        <a href="?type=course"   class="px-3 py-1.5 rounded-md <?= $filterType === 'course'   ? 'bg-brand text-white' : 'bg-white border border-gray-200 text-gray-600' ?>">Course</a>
      </div>
    </section>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr><th class="text-left px-5 py-2.5">Product</th><th class="text-left px-3 py-2.5">Type</th><th class="text-right px-3 py-2.5">Price</th><th class="text-right px-3 py-2.5">Stock</th><th class="text-right px-3 py-2.5">Sold</th><th class="text-left px-3 py-2.5">Active</th><th class="text-right px-5 py-2.5">Actions</th></tr>
          </thead>
          <tbody>
            <?php if (!$rows): ?>
              <tr><td colspan="7" class="px-5 py-6 text-center text-gray-500 text-sm">No products yet. <a href="create.php" class="text-brand hover:underline">Add your first one →</a></td></tr>
            <?php else: foreach ($rows as $r): ?>
              <tr class="border-t border-gray-100">
                <td class="px-5 py-2.5">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded bg-gray-100 grid place-items-center overflow-hidden shrink-0">
                      <?php if ($r['cover_path']): ?><img src="<?= h(App\Upload::url($r['cover_path'])) ?>" alt="" class="w-full h-full object-cover"><?php else: ?><span class="text-gray-400 text-xs">📦</span><?php endif; ?>
                    </div>
                    <div>
                      <a href="edit.php?id=<?= (int) $r['id'] ?>" class="font-medium text-ink hover:text-brand"><?= h($r['title']) ?></a>
                      <div class="text-xs text-gray-500">/<?= h($r['slug']) ?></div>
                    </div>
                  </div>
                </td>
                <td class="px-3 py-2.5"><span class="text-xs px-2 py-0.5 rounded-full bg-navy/10 text-navy"><?= h($r['type']) ?></span></td>
                <td class="px-3 py-2.5 text-right font-semibold"><?= format_bdt((float) $r['price_bdt']) ?></td>
                <td class="px-3 py-2.5 text-right"><?= (int) $r['stock'] ?></td>
                <td class="px-3 py-2.5 text-right"><?= (int) $r['sold'] ?></td>
                <td class="px-3 py-2.5"><?= $r['active'] ? '<span class="text-brand">Yes</span>' : '<span class="text-gray-400">No</span>' ?></td>
                <td class="px-5 py-2.5 text-right text-xs space-x-3">
                  <a href="edit.php?id=<?= (int) $r['id'] ?>" class="text-brand hover:underline">Edit</a>
                  <?php if (App\Auth::hasRole('admin','editor')): ?>
                    <a href="delete.php?id=<?= (int) $r['id'] ?>&_csrf=<?= urlencode(App\Csrf::token()) ?>" onclick="return confirm('Delete this product?')" class="text-accent hover:underline">Delete</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</div>
<?php require __DIR__ . '/../_layout/footer.php'; ?>
