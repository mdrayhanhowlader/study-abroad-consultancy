<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin', 'editor');
$adminPage = 'ssw';
$pageTitle = 'SSW Applications';

$stageFilter = $_GET['stage'] ?? '';
$validStages = App\SSW::STAGES;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    App\Csrf::verifyRequest();
    $action = $_POST['action'] ?? '';
    $appId  = (int) ($_POST['application_id'] ?? 0);

    if ($action === 'advance' && $appId) {
        $newStage = $_POST['new_stage'] ?? '';
        $notes    = trim((string) ($_POST['stage_notes'] ?? ''));
        try {
            App\SSW::advanceStage($appId, $newStage, (int) App\Auth::user()['id'], $notes ?: null);
            App\Flash::set('success', 'Stage updated to ' . (App\SSW::STAGE_LABELS[$newStage] ?? $newStage) . '.');
        } catch (Throwable $e) {
            App\Flash::set('error', $e->getMessage());
        }
    } elseif ($action === 'update' && $appId) {
        $data = [];
        foreach (['applicant_name', 'applicant_phone', 'applicant_email', 'passport_number', 'jlpt_level', 'industry', 'preferred_prefecture', 'employer_name', 'coa_reference', 'visa_reference', 'notes'] as $field) {
            if (isset($_POST[$field])) $data[$field] = trim((string) $_POST[$field]) ?: null;
        }
        foreach (['jlpt_test_date', 'skills_test_date', 'departure_date'] as $field) {
            if (isset($_POST[$field])) $data[$field] = trim((string) $_POST[$field]) ?: null;
        }
        if (isset($_POST['assigned_to'])) $data['assigned_to'] = (int) $_POST['assigned_to'] ?: null;
        if ($data) App\SSW::updateDetails($appId, $data);
        App\Flash::set('success', 'Application details updated.');
    }

    redirect('/admin/ssw/index.php' . ($stageFilter ? '?stage=' . urlencode($stageFilter) : ''));
}

$applications = App\SSW::all(in_array($stageFilter, $validStages, true) ? $stageFilter : null);
$stageCounts  = App\SSW::countByStage();
$users        = App\Database::fetchAll('SELECT id, name, role FROM users WHERE status=\'active\' ORDER BY name');

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <section class="flex flex-wrap gap-2 text-sm">
      <a href="index.php" class="px-3 py-1.5 rounded-md <?= !$stageFilter ? 'bg-brand text-white' : 'bg-white border border-gray-200 text-gray-600' ?>">All (<?= App\SSW::totalCount() ?>)</a>
      <?php foreach ($validStages as $s): ?>
        <?php if ($stageCounts[$s] > 0): ?>
          <a href="?stage=<?= h($s) ?>" class="px-3 py-1.5 rounded-md <?= $stageFilter === $s ? 'bg-brand text-white' : 'bg-white border border-gray-200 text-gray-600' ?>"><?= h(App\SSW::STAGE_LABELS[$s]) ?> (<?= $stageCounts[$s] ?>)</a>
        <?php endif; ?>
      <?php endforeach; ?>
    </section>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr><th class="text-left px-5 py-2.5">Applicant</th><th class="text-left px-3 py-2.5">Phone</th><th class="text-left px-3 py-2.5">JLPT</th><th class="text-left px-3 py-2.5">Industry</th><th class="text-left px-3 py-2.5">Stage</th><th class="text-left px-3 py-2.5">Assigned</th><th class="text-right px-5 py-2.5">When</th><th class="text-right px-5 py-2.5">Actions</th></tr>
          </thead>
          <tbody>
            <?php if (!$applications): ?>
              <tr><td colspan="8" class="px-5 py-6 text-center text-gray-500 text-sm">No SSW applications yet.</td></tr>
            <?php else: foreach ($applications as $a): ?>
              <tr class="border-t border-gray-100 hover:bg-gray-50">
                <td class="px-5 py-2.5"><a href="view.php?id=<?= (int) $a['id'] ?>" class="font-medium text-brand hover:underline"><?= h($a['applicant_name']) ?></a></td>
                <td class="px-3 py-2.5 text-xs"><?= h($a['applicant_phone']) ?></td>
                <td class="px-3 py-2.5 text-xs"><?= h(strtoupper($a['jlpt_level'])) ?></td>
                <td class="px-3 py-2.5 text-xs"><?= h($a['industry'] ?? '—') ?></td>
                <td class="px-3 py-2.5"><span class="text-xs px-2 py-0.5 rounded-full bg-navy/10 text-navy"><?= h(App\SSW::STAGE_LABELS[$a['current_stage']] ?? $a['current_stage']) ?></span></td>
                <td class="px-3 py-2.5 text-xs"><?= h($a['assigned_name'] ?? '—') ?></td>
                <td class="px-5 py-2.5 text-right text-xs text-gray-500"><?= h($a['created_at']) ?></td>
                <td class="px-5 py-2.5 text-right text-xs">
                  <a href="view.php?id=<?= (int) $a['id'] ?>" class="text-brand hover:underline">View</a>
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
