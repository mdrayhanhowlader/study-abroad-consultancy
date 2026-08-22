<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin', 'editor');
$adminPage = 'leads';
$pageTitle = 'Lead Detail';

$leadId = (int) ($_GET['id'] ?? 0);
$lead   = App\Lead::find($leadId);
if (!$lead) { http_response_code(404); echo 'Lead not found.'; exit; }

$users = App\Database::fetchAll('SELECT id, name, role FROM users WHERE status=\'active\' ORDER BY name');
$validStatuses = ['new', 'contacted', 'qualified', 'converted', 'lost'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    App\Csrf::verifyRequest();
    $action = $_POST['action'] ?? '';

    if ($action === 'status') {
        $newStatus = $_POST['status'] ?? '';
        if (in_array($newStatus, $validStatuses, true)) {
            App\Lead::updateStatus($leadId, $newStatus);
            App\Flash::set('success', 'Status updated to ' . ucfirst($newStatus) . '.');
        }
    } elseif ($action === 'assign') {
        $userId = (int) ($_POST['assigned_to'] ?? 0);
        if ($userId > 0) App\Lead::assign($leadId, $userId);
        App\Flash::set('success', 'Lead assigned.');
    }

    $lead = App\Lead::find($leadId);
}

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <div class="flex items-center justify-between">
      <div>
        <a href="index.php" class="text-sm text-gray-500 hover:text-brand">&larr; Back to leads</a>
        <h1 class="text-xl font-bold text-ink mt-1"><?= h($lead['name']) ?></h1>
      </div>
      <span class="text-xs px-3 py-1 rounded-full bg-gray-100"><?= h(ucfirst($lead['status'])) ?></span>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
          <h2 class="font-semibold text-ink mb-4">Contact Information</h2>
          <dl class="grid sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500 text-xs uppercase">Phone</dt><dd class="font-medium mt-1"><?= h($lead['phone']) ?></dd></div>
            <div><dt class="text-gray-500 text-xs uppercase">Email</dt><dd class="font-medium mt-1"><?= h($lead['email'] ?? '—') ?></dd></div>
            <div><dt class="text-gray-500 text-xs uppercase">Destination</dt><dd class="font-medium mt-1"><?= h($lead['destination'] ?? '—') ?></dd></div>
            <div><dt class="text-gray-500 text-xs uppercase">Study Level</dt><dd class="font-medium mt-1"><?= h($lead['study_level'] ?? '—') ?></dd></div>
            <div><dt class="text-gray-500 text-xs uppercase">Service Interest</dt><dd class="font-medium mt-1"><?= h($lead['service_interest'] ?? '—') ?></dd></div>
            <div><dt class="text-gray-500 text-xs uppercase">Source</dt><dd class="font-medium mt-1"><?= h(str_replace('_', ' ', $lead['source'])) ?></dd></div>
          </dl>
          <?php if ($lead['message']): ?>
            <div class="mt-4 pt-4 border-t border-gray-100">
              <dt class="text-gray-500 text-xs uppercase">Message</dt>
              <dd class="mt-2 text-sm text-gray-700 whitespace-pre-wrap"><?= h($lead['message']) ?></dd>
            </div>
          <?php endif; ?>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
          <h2 class="font-semibold text-ink mb-4">Meta</h2>
          <dl class="grid sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500 text-xs uppercase">IP Address</dt><dd class="font-mono text-xs mt-1"><?= h($lead['ip'] ?? '—') ?></dd></div>
            <div><dt class="text-gray-500 text-xs uppercase">Created</dt><dd class="mt-1"><?= h($lead['created_at']) ?></dd></div>
            <div><dt class="text-gray-500 text-xs uppercase">FB CLID</dt><dd class="font-mono text-xs mt-1"><?= h($lead['fbclid'] ?? '—') ?></dd></div>
            <div><dt class="text-gray-500 text-xs uppercase">G CLID</dt><dd class="font-mono text-xs mt-1"><?= h($lead['gclid'] ?? '—') ?></dd></div>
          </dl>
        </div>
      </div>

      <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
          <h2 class="font-semibold text-ink mb-4">Update Status</h2>
          <form method="post">
            <?= App\Csrf::field() ?>
            <input type="hidden" name="action" value="status">
            <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white mb-3">
              <?php foreach ($validStatuses as $s): ?>
                <option value="<?= h($s) ?>" <?= $lead['status'] === $s ? 'selected' : '' ?>><?= h(ucfirst($s)) ?></option>
              <?php endforeach; ?>
            </select>
            <button class="w-full bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2 rounded-md">Update</button>
          </form>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
          <h2 class="font-semibold text-ink mb-4">Assign To</h2>
          <form method="post">
            <?= App\Csrf::field() ?>
            <input type="hidden" name="action" value="assign">
            <select name="assigned_to" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white mb-3">
              <option value="">— Unassigned —</option>
              <?php foreach ($users as $u): ?>
                <option value="<?= (int) $u['id'] ?>" <?= (int) $lead['assigned_to'] === (int) $u['id'] ? 'selected' : '' ?>><?= h($u['name']) ?> (<?= h($u['role']) ?>)</option>
              <?php endforeach; ?>
            </select>
            <button class="w-full bg-navy hover:bg-navy-dark text-white text-sm font-semibold px-4 py-2 rounded-md">Assign</button>
          </form>
        </div>
      </div>
    </div>
  </main>
</div>
<?php require __DIR__ . '/../_layout/footer.php'; ?>
