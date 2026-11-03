<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin', 'editor');
$adminPage = 'ssw';
$pageTitle = 'SSW Application Detail';

$appId = (int) ($_GET['id'] ?? 0);
$app   = App\SSW::find($appId);
if (!$app) { http_response_code(404); echo 'Application not found.'; exit; }

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    try {
        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'advance') {
            $newStage = (string) ($_POST['new_stage'] ?? '');
            $notes = App\Validation::str((string) ($_POST['stage_notes'] ?? ''), 0, 2000) ?: null;
            App\SSW::advanceStage($appId, $newStage, (int) App\Auth::user()['id'], $notes);
            App\Flash::set('success', 'Application stage updated.');
        } elseif ($action === 'documents') {
            $documents = $_POST['documents'] ?? [];
            if (!is_array($documents)) throw new InvalidArgumentException('SSW document checklist data is invalid.');
            App\SSW::updateDocuments($appId, $documents, (int) App\Auth::user()['id']);
            App\Flash::set('success', 'Document checklist updated.');
        } elseif ($action === 'update') {
            $data = [
                'applicant_name' => App\Validation::str((string) ($_POST['applicant_name'] ?? ''), 2, 120),
                'applicant_phone' => App\Validation::phone(App\Validation::str((string) ($_POST['applicant_phone'] ?? ''), 7, 40)),
            ];
            $email = trim((string) ($_POST['applicant_email'] ?? ''));
            $data['applicant_email'] = $email === '' ? null : App\Validation::email(App\Validation::str($email, 1, 190));
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
    redirect('/admin/ssw/view.php?id=' . $appId);
}

$users    = App\Database::fetchAll('SELECT id, name, role FROM users WHERE status=\'active\' ORDER BY name');
$history  = App\SSW::history($appId);
$documents = App\SSW::documents($appId);
$stages   = App\SSW::STAGES;
$labels   = App\SSW::STAGE_LABELS;
$currentIdx = array_search($app['current_stage'], $stages, true);

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <div class="flex items-center justify-between">
      <div>
        <a href="index.php" class="text-sm text-gray-500 hover:text-brand">&larr; Back to applications</a>
        <h1 class="text-xl font-bold text-ink mt-1"><?= h($app['applicant_name']) ?></h1>
      </div>
      <span class="text-xs px-3 py-1 rounded-full bg-navy/10 text-navy"><?= h($labels[$app['current_stage']] ?? $app['current_stage']) ?></span>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5">
      <h2 class="font-semibold text-ink mb-4">Pipeline Progress</h2>
      <div class="flex items-center gap-1 overflow-x-auto pb-2">
        <?php foreach ($stages as $idx => $s): ?>
          <div class="flex items-center shrink-0">
            <div class="text-center px-2 py-1 rounded <?= $idx <= $currentIdx ? 'bg-brand text-white' : 'bg-gray-100 text-gray-500' ?>">
              <div class="text-[10px] font-semibold"><?= h($labels[$s]) ?></div>
            </div>
            <?php if ($idx < count($stages) - 1): ?>
              <div class="w-4 h-0.5 <?= $idx < $currentIdx ? 'bg-brand' : 'bg-gray-200' ?>"></div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if (App\Auth::hasRole('admin', 'editor') && $currentIdx < count($stages) - 1): ?>
        <form method="post" class="mt-4 flex items-center gap-2">
          <?= App\Csrf::field() ?>
          <input type="hidden" name="action" value="advance">
          <input type="hidden" name="application_id" value="<?= $appId ?>">
          <select name="new_stage" class="border border-gray-300 rounded-md px-3 py-2 text-sm bg-white">
            <?php for ($i = $currentIdx + 1; $i < count($stages); $i++): ?>
              <option value="<?= h($stages[$i]) ?>"><?= h($labels[$stages[$i]]) ?></option>
            <?php endfor; ?>
          </select>
          <input name="stage_notes" placeholder="Notes (optional)" class="border border-gray-300 rounded-md px-3 py-2 text-sm flex-1">
          <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2 rounded-md">Advance →</button>
        </form>
      <?php endif; ?>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
          <h2 class="font-semibold text-ink mb-4">Application Details</h2>
          <form method="post">
            <?= App\Csrf::field() ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="application_id" value="<?= $appId ?>">
            <div class="grid sm:grid-cols-2 gap-4">
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Name</span>
                <input name="applicant_name" value="<?= h($app['applicant_name']) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
              </label>
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Phone</span>
                <input name="applicant_phone" value="<?= h($app['applicant_phone']) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
              </label>
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Email</span>
                <input name="applicant_email" value="<?= h($app['applicant_email'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
              </label>
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Passport</span>
                <input name="passport_number" value="<?= h($app['passport_number'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
              </label>
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">JLPT Level</span>
                <select name="jlpt_level" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white">
                  <?php foreach (['none','n5','n4','n3','n2','n1'] as $l): ?>
                    <option value="<?= h($l) ?>" <?= $app['jlpt_level'] === $l ? 'selected' : '' ?>><?= h(strtoupper($l)) ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Industry</span>
                <input name="industry" value="<?= h($app['industry'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
              </label>
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Preferred Prefecture</span>
                <input name="preferred_prefecture" value="<?= h($app['preferred_prefecture'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
              </label>
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Employer</span>
                <input name="employer_name" value="<?= h($app['employer_name'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
              </label>
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">JLPT Test Date</span>
                <input name="jlpt_test_date" type="date" value="<?= h($app['jlpt_test_date'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
              </label>
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Skills Test Date</span>
                <input name="skills_test_date" type="date" value="<?= h($app['skills_test_date'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
              </label>
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">COA Reference</span>
                <input name="coa_reference" value="<?= h($app['coa_reference'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
              </label>
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Visa Reference</span>
                <input name="visa_reference" value="<?= h($app['visa_reference'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
              </label>
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Departure Date</span>
                <input name="departure_date" type="date" value="<?= h($app['departure_date'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
              </label>
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Assigned To</span>
                <select name="assigned_to" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white">
                  <option value="">— Unassigned —</option>
                  <?php foreach ($users as $u): ?>
                    <option value="<?= (int) $u['id'] ?>" <?= (int) $app['assigned_to'] === (int) $u['id'] ? 'selected' : '' ?>><?= h($u['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
              <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Notes</span>
                <textarea name="notes" rows="3" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm"><?= h($app['notes'] ?? '') ?></textarea>
              </label>
            </div>
            <button class="mt-4 bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">Save details</button>
          </form>
        </div>
      </div>

      <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
          <div class="flex items-start justify-between gap-3">
            <div>
              <h2 class="font-semibold text-ink">Document checklist</h2>
              <p class="mt-1 text-xs leading-5 text-gray-500">Track receipt and verification; document files are not uploaded or stored here.</p>
            </div>
            <span class="shrink-0 rounded-full bg-brand/10 px-2.5 py-1 text-xs font-semibold text-brand">
              <?= count(array_filter($documents, static fn(array $document): bool => in_array($document['status'], ['received', 'verified'], true))) ?>/<?= count($documents) ?>
            </span>
          </div>
          <form method="post" class="mt-4 space-y-3">
            <?= App\Csrf::field() ?>
            <input type="hidden" name="action" value="documents">
            <input type="hidden" name="application_id" value="<?= $appId ?>">
            <?php foreach ($documents as $document): ?>
              <div class="border-t border-gray-100 pt-3">
                <label class="block text-xs font-medium text-gray-700" for="doc-<?= h($document['key']) ?>"><?= h($document['label']) ?></label>
                <div class="mt-1.5 grid grid-cols-[minmax(0,1fr)_minmax(0,1.3fr)] gap-2">
                  <select id="doc-<?= h($document['key']) ?>" name="documents[<?= h($document['key']) ?>][status]" class="w-full rounded-md border-gray-300 bg-white px-2 py-2 text-xs">
                    <?php foreach (App\SSW::DOCUMENT_STATUSES as $status => $statusLabel): ?>
                      <option value="<?= h($status) ?>" <?= $document['status'] === $status ? 'selected' : '' ?>><?= h($statusLabel) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <input name="documents[<?= h($document['key']) ?>][notes]" maxlength="500" value="<?= h($document['notes']) ?>" placeholder="Notes (optional)" class="w-full rounded-md border-gray-300 px-2 py-2 text-xs">
                </div>
              </div>
            <?php endforeach; ?>
            <button class="w-full rounded-md bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-dark">Save checklist</button>
          </form>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
          <h2 class="font-semibold text-ink mb-3">Stage History</h2>
          <ul class="text-sm space-y-2">
            <?php if (!$history): ?>
              <li class="text-gray-500 text-xs">No stage changes yet.</li>
            <?php else: foreach ($history as $h): ?>
              <li class="border-l-2 border-brand/30 pl-3">
                <div class="text-xs text-ink"><?= h($labels[$h['from_stage']] ?? $h['from_stage'] ?? 'start') ?> → <?= h($labels[$h['to_stage']] ?? $h['to_stage']) ?></div>
                <div class="text-xs text-gray-500"><?= h($h['user_name'] ?? 'system') ?> · <?= h($h['created_at']) ?></div>
                <?php if ($h['notes']): ?><div class="text-xs text-gray-500 mt-0.5"><?= h($h['notes']) ?></div><?php endif; ?>
              </li>
            <?php endforeach; endif; ?>
          </ul>
        </div>

        <?php if ($app['lead_id']): ?>
          <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="font-semibold text-ink mb-2">Related Lead</h2>
            <a href="../leads/view.php?id=<?= (int) $app['lead_id'] ?>" class="text-sm text-brand hover:underline">View lead #<?= (int) $app['lead_id'] ?> →</a>
          </div>
        <?php endif; ?>
        <?php if ($app['order_id']): ?>
          <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="font-semibold text-ink mb-2">Related Order</h2>
            <a href="../orders/view.php?id=<?= (int) $app['order_id'] ?>" class="text-sm text-brand hover:underline">View order #<?= (int) $app['order_id'] ?> →</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>
<?php require __DIR__ . '/../_layout/footer.php'; ?>
