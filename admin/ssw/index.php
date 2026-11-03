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

    try {
        if ($action === 'create') {
            $jlptLevel = (string) ($_POST['jlpt_level'] ?? 'none');
            if (!in_array($jlptLevel, ['none', 'n5', 'n4', 'n3', 'n2', 'n1'], true)) {
                throw new InvalidArgumentException('Choose a valid JLPT level.');
            }
            $assignedTo = (int) ($_POST['assigned_to'] ?? 0);
            if ($assignedTo && !App\Database::fetchOne('SELECT id FROM users WHERE id=? AND status=\'active\'', [$assignedTo])) {
                throw new InvalidArgumentException('Choose an active team member.');
            }
            $email = trim((string) ($_POST['applicant_email'] ?? ''));
            if ($email !== '') $email = App\Validation::email(App\Validation::str($email, 1, 190));
            $id = App\SSW::create([
                'applicant_name' => App\Validation::str((string) ($_POST['applicant_name'] ?? ''), 2, 120),
                'applicant_phone' => App\Validation::phone(App\Validation::str((string) ($_POST['applicant_phone'] ?? ''), 7, 40)),
                'applicant_email' => $email ?: null,
                'jlpt_level' => $jlptLevel,
                'industry' => App\Validation::str((string) ($_POST['industry'] ?? ''), 0, 80) ?: null,
                'preferred_prefecture' => App\Validation::str((string) ($_POST['preferred_prefecture'] ?? ''), 0, 80) ?: null,
                'notes' => App\Validation::str((string) ($_POST['notes'] ?? ''), 0, 5000) ?: null,
                'assigned_to' => $assignedTo ?: null,
            ], (int) App\Auth::user()['id']);
            App\Flash::set('success', 'SSW application created and added to Initial Assessment.');
            redirect('/admin/ssw/view.php?id=' . $id);
        } elseif ($action === 'convert_lead') {
            $leadId = max(0, (int) ($_POST['lead_id'] ?? 0));
            if (!$leadId) throw new InvalidArgumentException('Choose an SSW lead to convert.');
            $id = App\SSW::createFromLead($leadId, (int) App\Auth::user()['id']);
            App\Flash::set('success', 'SSW lead converted to an application.');
            redirect('/admin/ssw/view.php?id=' . $id);
        } elseif ($action === 'advance' && $appId) {
            $newStage = (string) ($_POST['new_stage'] ?? '');
            $notes = App\Validation::str((string) ($_POST['stage_notes'] ?? ''), 0, 2000) ?: null;
            App\SSW::advanceStage($appId, $newStage, (int) App\Auth::user()['id'], $notes ?: null);
            App\Flash::set('success', 'Stage updated to ' . (App\SSW::STAGE_LABELS[$newStage] ?? $newStage) . '.');
        } elseif ($action === 'update' && $appId) {
            $data = [
                'applicant_name' => App\Validation::str((string) ($_POST['applicant_name'] ?? ''), 2, 120),
            ];
            $email = trim((string) ($_POST['applicant_email'] ?? ''));
            $data['applicant_email'] = $email === '' ? null : App\Validation::email(App\Validation::str($email, 1, 190));
            $data['applicant_phone'] = App\Validation::phone(App\Validation::str((string) ($_POST['applicant_phone'] ?? ''), 7, 40));
            foreach (['passport_number' => 40, 'industry' => 80, 'preferred_prefecture' => 80, 'employer_name' => 200, 'coa_reference' => 80, 'visa_reference' => 80] as $field => $max) {
                $data[$field] = App\Validation::str((string) ($_POST[$field] ?? ''), 0, $max) ?: null;
            }
            $jlptLevel = (string) ($_POST['jlpt_level'] ?? '');
            if (!in_array($jlptLevel, ['none', 'n5', 'n4', 'n3', 'n2', 'n1'], true)) {
                throw new InvalidArgumentException('Choose a valid JLPT level.');
            }
            $data['jlpt_level'] = $jlptLevel;
            foreach (['jlpt_test_date', 'skills_test_date', 'departure_date'] as $field) {
                $value = trim((string) ($_POST[$field] ?? ''));
                if ($value !== '') {
                    $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
                    $dateErrors = \DateTimeImmutable::getLastErrors();
                    if (!$date || $date->format('Y-m-d') !== $value || ($dateErrors !== false && ($dateErrors['warning_count'] || $dateErrors['error_count']))) {
                        throw new InvalidArgumentException('Enter valid dates for tests and departure.');
                    }
                    $data[$field] = $value;
                } else {
                    $data[$field] = null;
                }
            }
            $assignedTo = (int) ($_POST['assigned_to'] ?? 0);
            if ($assignedTo && !App\Database::fetchOne('SELECT id FROM users WHERE id=? AND status=\'active\'', [$assignedTo])) {
                throw new InvalidArgumentException('Choose an active team member.');
            }
            $data['assigned_to'] = $assignedTo ?: null;
            $data['notes'] = App\Validation::str((string) ($_POST['notes'] ?? ''), 0, 5000) ?: null;
            App\SSW::updateDetails($appId, $data, (int) App\Auth::user()['id']);
            App\Flash::set('success', 'Application details updated.');
        } else {
            throw new InvalidArgumentException('Unknown SSW action.');
        }
    } catch (Throwable $e) {
        App\Flash::set('error', $e->getMessage());
    }

    redirect('/admin/ssw/index.php' . ($stageFilter ? '?stage=' . urlencode($stageFilter) : ''));
}

$applications = App\SSW::all(in_array($stageFilter, $validStages, true) ? $stageFilter : null);
$stageCounts  = App\SSW::countByStage();
$users        = App\Database::fetchAll('SELECT id, name, role FROM users WHERE status=\'active\' ORDER BY name');
$convertibleLeads = App\Database::fetchAll(
    'SELECT l.id, l.name, l.phone, l.created_at FROM leads l
     WHERE l.service_interest=\'ssw\' AND l.status != \'lost\'
     AND NOT EXISTS (SELECT 1 FROM ssw_applications s WHERE s.lead_id=l.id)
     ORDER BY l.id DESC LIMIT 100'
);

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <div class="grid gap-6 xl:grid-cols-2">
      <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="font-semibold text-ink">Create an SSW application</h2>
        <p class="mt-1 text-sm text-gray-500">Start a case directly at Initial Assessment, assign an owner, and track it through the pipeline.</p>
        <form method="post" class="mt-4 grid gap-3 sm:grid-cols-2">
          <?= App\Csrf::field() ?>
          <input type="hidden" name="action" value="create">
          <label class="text-xs font-semibold text-gray-600">Applicant name<input name="applicant_name" required maxlength="120" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></label>
          <label class="text-xs font-semibold text-gray-600">Phone<input name="applicant_phone" required maxlength="40" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></label>
          <label class="text-xs font-semibold text-gray-600">Email (optional)<input name="applicant_email" type="email" maxlength="190" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></label>
          <label class="text-xs font-semibold text-gray-600">JLPT level<select name="jlpt_level" class="mt-1 block w-full rounded-md border-gray-300 bg-white text-sm"><?php foreach (['none','n5','n4','n3','n2','n1'] as $level): ?><option value="<?= h($level) ?>"><?= h(strtoupper($level)) ?></option><?php endforeach; ?></select></label>
          <label class="text-xs font-semibold text-gray-600">Industry<input name="industry" maxlength="80" placeholder="Caregiving, food service..." class="mt-1 block w-full rounded-md border-gray-300 text-sm"></label>
          <label class="text-xs font-semibold text-gray-600">Preferred prefecture<input name="preferred_prefecture" maxlength="80" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></label>
          <label class="text-xs font-semibold text-gray-600 sm:col-span-2">Assign to<select name="assigned_to" class="mt-1 block w-full rounded-md border-gray-300 bg-white text-sm"><option value="">Unassigned</option><?php foreach ($users as $user): ?><option value="<?= (int) $user['id'] ?>"><?= h($user['name']) ?></option><?php endforeach; ?></select></label>
          <label class="text-xs font-semibold text-gray-600 sm:col-span-2">Initial notes<textarea name="notes" rows="2" maxlength="5000" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></textarea></label>
          <button class="rounded-md bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-dark sm:col-span-2">Create application</button>
        </form>
      </section>

      <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="font-semibold text-ink">Convert an SSW enquiry</h2>
        <p class="mt-1 text-sm text-gray-500">Convert an existing SSW lead without re-entering their details. The lead will be marked converted and linked to its application.</p>
        <?php if ($convertibleLeads): ?>
          <form method="post" class="mt-4 flex flex-col gap-3 sm:flex-row">
            <?= App\Csrf::field() ?>
            <input type="hidden" name="action" value="convert_lead">
            <select name="lead_id" required class="min-w-0 flex-1 rounded-md border-gray-300 bg-white text-sm">
              <?php foreach ($convertibleLeads as $lead): ?><option value="<?= (int) $lead['id'] ?>">#<?= (int) $lead['id'] ?> · <?= h($lead['name']) ?> · <?= h($lead['phone']) ?> · <?= h(date('d M Y', strtotime($lead['created_at']))) ?></option><?php endforeach; ?>
            </select>
            <button class="rounded-md border border-brand px-4 py-2 text-sm font-semibold text-brand hover:bg-brand hover:text-white">Convert to application</button>
          </form>
        <?php else: ?>
          <p class="mt-4 rounded-lg bg-gray-50 p-4 text-sm text-gray-500">No unconverted SSW leads available.</p>
        <?php endif; ?>
      </section>
    </div>

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
