<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin', 'editor');
$adminPage = 'ssw';
$pageTitle = 'SSW Application Detail';

$appId = (int) ($_GET['id'] ?? 0);
$app   = App\SSW::find($appId);
if (!$app) { http_response_code(404); echo 'Application not found.'; exit; }

$users    = App\Database::fetchAll('SELECT id, name, role FROM users WHERE status=\'active\' ORDER BY name');
$history  = App\SSW::history($appId);
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
