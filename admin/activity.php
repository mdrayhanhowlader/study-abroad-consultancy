<?php
require_once __DIR__ . '/../app/bootstrap.php';
App\Auth::require();
$adminPage = 'activity';
$pageTitle = 'Activity log';

$rows = App\Database::fetchAll(
    'SELECT al.*, u.name AS user_name, u.email AS user_email
     FROM activity_log al
     LEFT JOIN users u ON u.id = al.user_id
     ORDER BY al.id DESC
     LIMIT 200'
);

require __DIR__ . '/_layout/head.php';
require __DIR__ . '/_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50">
    <?= App\Flash::render() ?>
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200">
        <h2 class="font-semibold text-ink">Last 200 actions</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr><th class="text-left px-5 py-2.5">When</th><th class="text-left px-3 py-2.5">User</th><th class="text-left px-3 py-2.5">Action</th><th class="text-left px-3 py-2.5">Entity</th><th class="text-left px-3 py-2.5">Meta</th><th class="text-right px-5 py-2.5">IP</th></tr>
          </thead>
          <tbody>
            <?php if (!$rows): ?>
              <tr><td colspan="6" class="px-5 py-6 text-center text-gray-500 text-sm">No activity recorded yet.</td></tr>
            <?php else: foreach ($rows as $r): ?>
              <tr class="border-t border-gray-100 align-top">
                <td class="px-5 py-2.5 text-xs text-gray-500 whitespace-nowrap"><?= h($r['created_at']) ?></td>
                <td class="px-3 py-2.5"><?= h($r['user_name'] ?? '—') ?><div class="text-xs text-gray-500"><?= h($r['user_email'] ?? '') ?></div></td>
                <td class="px-3 py-2.5 font-medium"><?= h($r['action']) ?></td>
                <td class="px-3 py-2.5 text-xs text-gray-600"><?= h(($r['entity'] ?? '-') . ' #' . ($r['entity_id'] ?? '-')) ?></td>
                <td class="px-3 py-2.5 text-xs text-gray-500 font-mono"><?= h($r['meta'] ?? '') ?></td>
                <td class="px-5 py-2.5 text-right text-xs text-gray-500"><?= h($r['ip'] ?? '') ?></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
<?php require __DIR__ . '/_layout/footer.php'; ?>
