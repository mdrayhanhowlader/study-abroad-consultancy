<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin', 'editor');
$adminPage = 'leads';
$pageTitle = 'Leads';

$statusFilter = $_GET['status'] ?? '';
$sourceFilter = $_GET['source'] ?? '';
$validStatuses = ['new', 'contacted', 'qualified', 'converted', 'lost'];
$validSources  = ['contact_form', 'cta_form', 'book_page', 'course_page', 'facebook_ad', 'organic'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    App\Csrf::verifyRequest();
    $action = $_POST['action'] ?? '';
    $leadId = (int) ($_POST['lead_id'] ?? 0);

    if ($action === 'status' && $leadId) {
        $newStatus = $_POST['status'] ?? '';
        if (in_array($newStatus, $validStatuses, true)) {
            App\Lead::updateStatus($leadId, $newStatus);
            App\Flash::set('success', 'Lead status updated.');
        }
    } elseif ($action === 'assign' && $leadId) {
        $userId = (int) ($_POST['assigned_to'] ?? 0);
        if ($userId > 0) {
            App\Lead::assign($leadId, $userId);
            App\Flash::set('success', 'Lead assigned.');
        }
    }

    $redirect = 'index.php';
    if ($statusFilter) $redirect .= '?status=' . urlencode($statusFilter);
    if ($sourceFilter) $redirect .= ($statusFilter ? '&' : '?') . 'source=' . urlencode($sourceFilter);
    redirect($redirect);
}

$leads = App\Lead::all(
    in_array($statusFilter, $validStatuses, true) ? $statusFilter : null,
    in_array($sourceFilter, $validSources, true) ? $sourceFilter : null
);
$counts = App\Lead::countByStatus();
$users  = App\Database::fetchAll('SELECT id, name, role FROM users WHERE status=\'active\' ORDER BY name');

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <section class="grid grid-cols-2 lg:grid-cols-5 gap-4">
      <a href="index.php" class="bg-white rounded-xl p-5 border border-gray-200 block <?= !$statusFilter ? 'ring-2 ring-brand' : '' ?>">
        <div class="text-xs uppercase tracking-wider text-gray-500">All Leads</div>
        <div class="mt-1 text-3xl font-bold text-ink"><?= array_sum($counts) ?></div>
      </a>
      <?php foreach (['new' => 'text-accent', 'contacted' => 'text-yellow-600', 'qualified' => 'text-blue-600', 'converted' => 'text-brand', 'lost' => 'text-gray-500'] as $s => $color): ?>
        <a href="?status=<?= h($s) ?>" class="bg-white rounded-xl p-5 border border-gray-200 block <?= $statusFilter === $s ? 'ring-2 ring-brand' : '' ?>">
          <div class="text-xs uppercase tracking-wider text-gray-500"><?= h(ucfirst($s)) ?></div>
          <div class="mt-1 text-3xl font-bold <?= h($color) ?>"><?= (int) $counts[$s] ?></div>
        </a>
      <?php endforeach; ?>
    </section>

    <section class="flex flex-wrap gap-2 text-sm">
      <a href="index.php" class="px-3 py-1.5 rounded-md <?= !$statusFilter && !$sourceFilter ? 'bg-brand text-white' : 'bg-white border border-gray-200 text-gray-600' ?>">All</a>
      <?php foreach ($validStatuses as $s): ?>
        <a href="?status=<?= h($s) ?>" class="px-3 py-1.5 rounded-md <?= $statusFilter === $s ? 'bg-brand text-white' : 'bg-white border border-gray-200 text-gray-600' ?>"><?= h(ucfirst($s)) ?></a>
      <?php endforeach; ?>
      <span class="ml-auto text-gray-400">|</span>
      <?php foreach ($validSources as $src): ?>
        <a href="?source=<?= h($src) ?>" class="px-3 py-1.5 rounded-md <?= $sourceFilter === $src ? 'bg-brand text-white' : 'bg-white border border-gray-200 text-gray-600' ?>"><?= h(str_replace('_', ' ', $src)) ?></a>
      <?php endforeach; ?>
    </section>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr><th class="text-left px-5 py-2.5">Name</th><th class="text-left px-3 py-2.5">Phone</th><th class="text-left px-3 py-2.5">Email</th><th class="text-left px-3 py-2.5">Destination</th><th class="text-left px-3 py-2.5">Source</th><th class="text-left px-3 py-2.5">Status</th><th class="text-left px-3 py-2.5">Assigned</th><th class="text-right px-5 py-2.5">When</th><th class="text-right px-5 py-2.5">Actions</th></tr>
          </thead>
          <tbody>
            <?php if (!$leads): ?>
              <tr><td colspan="9" class="px-5 py-6 text-center text-gray-500 text-sm">No leads yet.</td></tr>
            <?php else: foreach ($leads as $l): ?>
              <tr class="border-t border-gray-100 hover:bg-gray-50">
                <td class="px-5 py-2.5"><a href="view.php?id=<?= (int) $l['id'] ?>" class="font-medium text-brand hover:underline"><?= h($l['name']) ?></a></td>
                <td class="px-3 py-2.5 text-xs"><?= h($l['phone']) ?></td>
                <td class="px-3 py-2.5 text-xs"><?= h($l['email'] ?? '—') ?></td>
                <td class="px-3 py-2.5 text-xs"><?= h($l['destination'] ?? '—') ?></td>
                <td class="px-3 py-2.5 text-xs"><?= h(str_replace('_', ' ', $l['source'])) ?></td>
                <td class="px-3 py-2.5">
                  <form method="post" class="inline">
                    <?= App\Csrf::field() ?>
                    <input type="hidden" name="action" value="status">
                    <input type="hidden" name="lead_id" value="<?= (int) $l['id'] ?>">
                    <select name="status" onchange="this.form.submit()" class="text-xs border border-gray-200 rounded px-2 py-1 bg-white">
                      <?php foreach ($validStatuses as $s): ?>
                        <option value="<?= h($s) ?>" <?= $l['status'] === $s ? 'selected' : '' ?>><?= h(ucfirst($s)) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </form>
                </td>
                <td class="px-3 py-2.5 text-xs"><?= h($l['assigned_name'] ?? '—') ?></td>
                <td class="px-5 py-2.5 text-right text-xs text-gray-500"><?= h($l['created_at']) ?></td>
                <td class="px-5 py-2.5 text-right text-xs">
                  <a href="view.php?id=<?= (int) $l['id'] ?>" class="text-brand hover:underline">View</a>
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
