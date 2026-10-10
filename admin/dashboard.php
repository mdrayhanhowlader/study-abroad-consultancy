<?php
require_once __DIR__ . '/../app/bootstrap.php';
App\Auth::require();
$adminPage = 'dashboard';
$pageTitle = 'Dashboard';

$counts = [
    'orders_placed' => (int) App\Database::fetchValue("SELECT COUNT(*) FROM orders WHERE status='placed'"),
    'orders_paid'   => (int) App\Database::fetchValue("SELECT COUNT(*) FROM orders WHERE status='paid'"),
    'orders_total'  => (int) App\Database::fetchValue('SELECT COUNT(*) FROM orders'),
    'revenue_paid'  => (float) App\Database::fetchValue("SELECT COALESCE(SUM(total),0) FROM orders WHERE status IN ('paid','processing','shipped','delivered','completed')"),
    'products'      => (int) App\Database::fetchValue('SELECT COUNT(*) FROM products WHERE active=1'),
    'posts'         => (int) App\Database::fetchValue("SELECT COUNT(*) FROM posts WHERE status='published'"),
    'users'         => (int) App\Database::fetchValue('SELECT COUNT(*) FROM users WHERE status=\'active\''),
    'leads_new'     => (int) App\Database::fetchValue("SELECT COUNT(*) FROM leads WHERE status='new'"),
    'leads_total'   => (int) App\Database::fetchValue('SELECT COUNT(*) FROM leads'),
    'ssw_total'     => (int) App\Database::fetchValue('SELECT COUNT(*) FROM ssw_applications'),
    'ssw_active'    => (int) App\Database::fetchValue("SELECT COUNT(*) FROM ssw_applications WHERE current_stage NOT IN ('deployed','onboarded')"),
];

$recentOrders = App\Database::fetchAll('SELECT id, code, name, total, status, created_at FROM orders ORDER BY id DESC LIMIT 8');
$recentPosts  = App\Database::fetchAll("SELECT id, type, title, status, published_at FROM posts ORDER BY id DESC LIMIT 6");
$recentLog    = App\Database::fetchAll('SELECT al.*, u.name AS user_name FROM activity_log al LEFT JOIN users u ON u.id=al.user_id ORDER BY al.id DESC LIMIT 10');

require __DIR__ . '/_layout/head.php';
require __DIR__ . '/_layout/sidebar.php';
$a = ''; /* admin is root-level, no prefix needed */
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 space-y-6 bg-gray-50">
    <?= App\Flash::render() ?>

    <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl p-5 border border-gray-200">
        <div class="text-xs uppercase tracking-wider text-gray-500">Pending Orders</div>
        <div class="mt-1 text-3xl font-bold text-accent"><?= (int) $counts['orders_placed'] ?></div>
        <a href="orders/index.php?status=placed" class="text-xs text-brand hover:underline mt-2 inline-block">View →</a>
      </div>
      <div class="bg-white rounded-xl p-5 border border-gray-200">
        <div class="text-xs uppercase tracking-wider text-gray-500">Paid Orders</div>
        <div class="mt-1 text-3xl font-bold text-brand"><?= (int) $counts['orders_paid'] ?></div>
        <a href="orders/index.php?status=paid" class="text-xs text-brand hover:underline mt-2 inline-block">View →</a>
      </div>
      <div class="bg-white rounded-xl p-5 border border-gray-200">
        <div class="text-xs uppercase tracking-wider text-gray-500">Revenue (Paid)</div>
        <div class="mt-1 text-3xl font-bold text-navy"><?= format_bdt($counts['revenue_paid']) ?></div>
        <span class="text-xs text-gray-400 mt-2 inline-block">All-time</span>
      </div>
      <div class="bg-white rounded-xl p-5 border border-gray-200">
        <div class="text-xs uppercase tracking-wider text-gray-500">Total Orders</div>
        <div class="mt-1 text-3xl font-bold text-ink"><?= (int) $counts['orders_total'] ?></div>
        <a href="orders/index.php" class="text-xs text-brand hover:underline mt-2 inline-block">View →</a>
      </div>
    </section>

    <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl p-4 border border-gray-200"><div class="text-xs text-gray-500">Active Products</div><div class="text-2xl font-bold mt-1"><?= (int) $counts['products'] ?></div></div>
      <div class="bg-white rounded-xl p-4 border border-gray-200"><div class="text-xs text-gray-500">Published Posts</div><div class="text-2xl font-bold mt-1"><?= (int) $counts['posts'] ?></div></div>
      <div class="bg-white rounded-xl p-4 border border-gray-200"><div class="text-xs text-gray-500">Active Users</div><div class="text-2xl font-bold mt-1"><?= (int) $counts['users'] ?></div></div>
      <div class="bg-white rounded-xl p-4 border border-gray-200"><div class="text-xs text-gray-500">Quick Actions</div><a href="homepage.php" class="block mt-1 text-sm font-semibold text-brand hover:underline">Edit homepage content →</a><a href="content/posts.php" class="block mt-1 text-sm font-semibold text-brand hover:underline">+ New Blog / News →</a></div>
    </section>

    <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <a href="leads/index.php?status=new" class="bg-white rounded-xl p-5 border border-gray-200 block hover:shadow-md transition">
        <div class="text-xs uppercase tracking-wider text-gray-500">New Leads</div>
        <div class="mt-1 text-3xl font-bold text-accent"><?= (int) $counts['leads_new'] ?></div>
        <div class="text-xs text-gray-400 mt-1">Total: <?= (int) $counts['leads_total'] ?></div>
      </a>
      <a href="ssw/index.php" class="bg-white rounded-xl p-5 border border-gray-200 block hover:shadow-md transition">
        <div class="text-xs uppercase tracking-wider text-gray-500">SSW Applications</div>
        <div class="mt-1 text-3xl font-bold text-navy"><?= (int) $counts['ssw_total'] ?></div>
        <div class="text-xs text-gray-400 mt-1">Active: <?= (int) $counts['ssw_active'] ?></div>
      </a>
      <div class="bg-white rounded-xl p-5 border border-gray-200">
        <div class="text-xs uppercase tracking-wider text-gray-500">Revenue (Paid)</div>
        <div class="mt-1 text-3xl font-bold text-brand"><?= format_bdt($counts['revenue_paid']) ?></div>
        <span class="text-xs text-gray-400 mt-1 inline-block">All-time</span>
      </div>
      <div class="bg-white rounded-xl p-5 border border-gray-200">
        <div class="text-xs uppercase tracking-wider text-gray-500">Total Orders</div>
        <div class="mt-1 text-3xl font-bold text-ink"><?= (int) $counts['orders_total'] ?></div>
        <a href="orders/index.php" class="text-xs text-brand hover:underline mt-1 inline-block">View →</a>
      </div>
    </section>

    <section class="grid lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 class="font-semibold text-ink">Recent orders</h2>
          <a href="orders/index.php" class="text-xs text-brand hover:underline">All orders</a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
              <tr><th class="text-left px-5 py-2.5">Code</th><th class="text-left px-3 py-2.5">Customer</th><th class="text-right px-3 py-2.5">Total</th><th class="text-left px-3 py-2.5">Status</th><th class="text-right px-5 py-2.5">When</th></tr>
            </thead>
            <tbody>
              <?php if (!$recentOrders): ?>
                <tr><td colspan="5" class="px-5 py-6 text-center text-gray-500 text-sm">No orders yet.</td></tr>
              <?php else: foreach ($recentOrders as $o): ?>
                <tr class="border-t border-gray-100">
                  <td class="px-5 py-2.5 font-mono text-xs"><a href="orders/view.php?id=<?= (int) $o['id'] ?>" class="text-brand hover:underline"><?= h($o['code']) ?></a></td>
                  <td class="px-3 py-2.5"><?= h($o['name']) ?></td>
                  <td class="px-3 py-2.5 text-right"><?= format_bdt((float) $o['total']) ?></td>
                  <td class="px-3 py-2.5"><span class="text-xs px-2 py-0.5 rounded-full bg-gray-100"><?= h($o['status']) ?></span></td>
                  <td class="px-5 py-2.5 text-right text-xs text-gray-500"><?= h($o['created_at']) ?></td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 class="font-semibold text-ink">Activity log</h2>
          <a href="activity.php" class="text-xs text-brand hover:underline">All</a>
        </div>
        <ul class="divide-y divide-gray-100">
          <?php if (!$recentLog): ?>
            <li class="px-5 py-4 text-sm text-gray-500">No activity yet.</li>
          <?php else: foreach ($recentLog as $l): ?>
            <li class="px-5 py-3 text-sm">
              <div class="font-medium text-ink"><?= h($l['action']) ?> · <span class="text-gray-500"><?= h($l['entity'] ?? '-') ?> #<?= h((string)($l['entity_id'] ?? '')) ?></span></div>
              <div class="text-xs text-gray-500 mt-0.5"><?= h($l['user_name'] ?? 'system') ?> · <?= h($l['created_at']) ?></div>
            </li>
          <?php endforeach; endif; ?>
        </ul>
      </div>
    </section>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="font-semibold text-ink">Recent posts</h2>
        <a href="content/posts.php" class="text-xs text-brand hover:underline">Manage</a>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr><th class="text-left px-5 py-2.5">Type</th><th class="text-left px-3 py-2.5">Title</th><th class="text-left px-3 py-2.5">Status</th><th class="text-right px-5 py-2.5">Published</th></tr>
          </thead>
          <tbody>
            <?php if (!$recentPosts): ?>
              <tr><td colspan="4" class="px-5 py-6 text-center text-gray-500 text-sm">No posts yet.</td></tr>
            <?php else: foreach ($recentPosts as $p): ?>
              <tr class="border-t border-gray-100">
                <td class="px-5 py-2.5"><span class="text-xs px-2 py-0.5 rounded-full bg-navy/10 text-navy"><?= h($p['type']) ?></span></td>
                <td class="px-3 py-2.5"><a href="content/posts.php?edit=<?= (int) $p['id'] ?>" class="text-brand hover:underline"><?= h($p['title']) ?></a></td>
                <td class="px-3 py-2.5"><span class="text-xs px-2 py-0.5 rounded-full bg-gray-100"><?= h($p['status']) ?></span></td>
                <td class="px-5 py-2.5 text-right text-xs text-gray-500"><?= h($p['published_at'] ?? '—') ?></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </section>

  </main>
</div>
<?php require __DIR__ . '/_layout/footer.php'; ?>
