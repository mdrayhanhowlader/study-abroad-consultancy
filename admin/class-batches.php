<?php
require_once __DIR__ . '/../app/bootstrap.php';
App\Auth::requireRole('admin', 'editor', 'viewer');
$adminPage = 'class_batches';
$pageTitle = 'Class Batches & Holidays';
$canWrite = App\Auth::hasRole('admin', 'editor');

$validDate = static function (string $value): ?string {
    $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    $errors = \DateTimeImmutable::getLastErrors();
    return $date && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
        && $date->format('Y-m-d') === $value ? $value : null;
};

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    if (!$canWrite) {
        http_response_code(403);
        exit('You do not have permission to change class batches.');
    }

    try {
        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'save_batch') {
            $id = max(0, (int) ($_POST['id'] ?? 0));
            $code = strtoupper(trim((string) ($_POST['batch_code'] ?? '')));
            if (!preg_match('/^[A-Z0-9][A-Z0-9_-]{1,39}$/', $code)) {
                throw new InvalidArgumentException('Batch ID must be 2–40 characters using letters, numbers, - or _.');
            }
            $startDate = $validDate(trim((string) ($_POST['start_date'] ?? '')));
            if ($startDate === null) throw new InvalidArgumentException('Enter a valid batch start date.');
            $durationMonths = filter_var($_POST['duration_months'] ?? null, FILTER_VALIDATE_INT);
            if ($durationMonths === false || $durationMonths < 1 || $durationMonths > 12) {
                throw new InvalidArgumentException('Course duration must be from 1 to 12 months.');
            }
            $weekdays = $_POST['class_weekdays'] ?? [];
            if (!is_array($weekdays)) throw new InvalidArgumentException('Choose at least one class day.');
            $weekdayIds = array_values(array_unique(array_filter(
                array_map('intval', $weekdays),
                static fn(int $day): bool => isset(App\ClassBatch::WEEKDAYS[$day])
            )));
            sort($weekdayIds);
            if (!$weekdayIds) throw new InvalidArgumentException('Choose at least one class day.');

            App\ClassBatch::save([
                'batch_code' => $code,
                'course_name' => App\Validation::str((string) ($_POST['course_name'] ?? ''), 2, 120),
                'start_date' => $startDate,
                'duration_months' => $durationMonths,
                'class_weekdays' => implode(',', $weekdayIds),
                'class_time' => App\Validation::str((string) ($_POST['class_time'] ?? ''), 0, 80) ?: null,
                'class_location' => App\Validation::str((string) ($_POST['class_location'] ?? ''), 0, 120) ?: null,
                'active' => isset($_POST['active']) ? 1 : 0,
            ], $id);
            App\Content::log((int) App\Auth::user()['id'], $id ? 'class_batch.update' : 'class_batch.create', 'class_batch', $id ?: null);
            App\Flash::set('success', $id ? 'Class batch updated.' : 'Class batch created.');
        } elseif ($action === 'delete_batch') {
            $id = max(0, (int) ($_POST['id'] ?? 0));
            if (!$id) throw new InvalidArgumentException('Choose a valid class batch.');
            App\ClassBatch::delete($id);
            App\Content::log((int) App\Auth::user()['id'], 'class_batch.delete', 'class_batch', $id);
            App\Flash::set('success', 'Class batch deleted.');
        } elseif ($action === 'add_holiday') {
            $date = $validDate(trim((string) ($_POST['holiday_date'] ?? '')));
            if ($date === null) throw new InvalidArgumentException('Enter a valid holiday date.');
            $title = App\Validation::str((string) ($_POST['holiday_title'] ?? ''), 2, 120);
            $id = App\ClassBatch::saveHoliday($date, $title);
            App\Content::log((int) App\Auth::user()['id'], 'class_holiday.create', 'class_holiday', $id);
            App\Flash::set('success', 'Holiday added to class calendars.');
        } elseif ($action === 'delete_holiday') {
            $id = max(0, (int) ($_POST['id'] ?? 0));
            if (!$id) throw new InvalidArgumentException('Choose a valid holiday.');
            App\ClassBatch::deleteHoliday($id);
            App\Content::log((int) App\Auth::user()['id'], 'class_holiday.delete', 'class_holiday', $id);
            App\Flash::set('success', 'Holiday removed from class calendars.');
        } else {
            throw new InvalidArgumentException('Unknown class schedule action.');
        }
    } catch (Throwable $e) {
        App\Flash::set('error', $e->getMessage());
    }
    redirect('/admin/class-batches.php');
}

$editBatch = isset($_GET['edit']) ? App\Database::fetchOne('SELECT * FROM class_batches WHERE id=?', [(int) $_GET['edit']]) : null;
$batches = App\ClassBatch::all();
$holidays = App\ClassBatch::holidays();
$selectedDays = array_map('intval', explode(',', (string) ($editBatch['class_weekdays'] ?? '')));

require __DIR__ . '/_layout/head.php';
require __DIR__ . '/_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/_layout/topbar.php'; ?>
  <main class="flex-1 space-y-6 bg-gray-50 p-4 lg:p-8">
    <?= App\Flash::render() ?>
    <section class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900">
      Add official government holidays here so they are excluded from class dates. Holiday dates are maintained manually; verify the latest government announcements and add each applicable date.
    </section>

    <?php if ($canWrite): ?>
    <section class="rounded-xl border border-gray-200 bg-white p-5">
      <h2 class="mb-4 font-semibold text-ink"><?= $editBatch ? 'Edit class batch' : 'Create class batch' ?></h2>
      <form method="post" class="grid gap-4 sm:grid-cols-2">
        <?= App\Csrf::field() ?>
        <input type="hidden" name="action" value="save_batch">
        <input type="hidden" name="id" value="<?= (int) ($editBatch['id'] ?? 0) ?>">
        <label class="block"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">Batch ID</span>
          <input name="batch_code" required maxlength="40" pattern="[A-Za-z0-9][A-Za-z0-9_-]{1,39}" value="<?= h($editBatch['batch_code'] ?? '') ?>" placeholder="JP-N5-2027-01" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">Course / level</span>
          <input name="course_name" required maxlength="120" value="<?= h($editBatch['course_name'] ?? '') ?>" placeholder="Japanese Language — JLPT N5" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">First class date</span>
          <input name="start_date" type="date" required value="<?= h($editBatch['start_date'] ?? '') ?>" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">Duration (months)</span>
          <input name="duration_months" type="number" min="1" max="12" required value="<?= h((string) ($editBatch['duration_months'] ?? 4)) ?>" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm">
        </label>
        <fieldset class="sm:col-span-2">
          <legend class="text-xs font-semibold uppercase tracking-wide text-gray-700">Class days each week</legend>
          <div class="mt-2 flex flex-wrap gap-3">
            <?php foreach (App\ClassBatch::WEEKDAYS as $day => $label): ?>
              <label class="inline-flex items-center gap-1.5 text-sm text-gray-700"><input type="checkbox" name="class_weekdays[]" value="<?= $day ?>" <?= in_array($day, $selectedDays, true) ? 'checked' : '' ?>><?= h($label) ?></label>
            <?php endforeach; ?>
          </div>
        </fieldset>
        <label class="block"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">Class time (optional)</span>
          <input name="class_time" maxlength="80" value="<?= h($editBatch['class_time'] ?? '') ?>" placeholder="6:00 PM – 8:00 PM" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">Location / format (optional)</span>
          <input name="class_location" maxlength="120" value="<?= h($editBatch['class_location'] ?? '') ?>" placeholder="Dhanmondi campus / Online" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm">
        </label>
        <label class="flex items-center gap-2 sm:col-span-2">
          <input type="checkbox" name="active" value="1" <?= !$editBatch || $editBatch['active'] ? 'checked' : '' ?>>
          <span class="text-sm">Visible to students on the class schedule page</span>
        </label>
        <div class="flex items-center gap-3 sm:col-span-2">
          <button class="rounded-md bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-dark"><?= $editBatch ? 'Update batch' : 'Create batch' ?></button>
          <?php if ($editBatch): ?><a href="class-batches.php" class="text-sm text-gray-500 hover:text-brand">Cancel</a><?php endif; ?>
        </div>
      </form>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
      <h2 class="mb-4 font-semibold text-ink">Add a holiday</h2>
      <form method="post" class="grid gap-3 sm:grid-cols-[1fr_2fr_auto] sm:items-end">
        <?= App\Csrf::field() ?>
        <input type="hidden" name="action" value="add_holiday">
        <label class="block"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">Date</span>
          <input name="holiday_date" type="date" required class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">Holiday name</span>
          <input name="holiday_title" required maxlength="120" placeholder="Eid-ul-Fitr holiday" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm">
        </label>
        <button class="rounded-md border border-gray-300 px-4 py-2.5 text-sm font-semibold text-ink hover:border-brand hover:text-brand">Add holiday</button>
      </form>
    </section>
    <?php endif; ?>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white">
      <div class="border-b border-gray-200 px-5 py-4"><h2 class="font-semibold text-ink">Class batches</h2></div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="px-5 py-3 text-left">Batch</th><th class="px-3 py-3 text-left">Course</th><th class="px-3 py-3 text-left">Dates</th><th class="px-3 py-3 text-left">Class days</th><th class="px-3 py-3 text-left">Visibility</th><th class="px-5 py-3 text-right">Actions</th></tr></thead>
          <tbody>
            <?php if (!$batches): ?>
              <tr><td colspan="6" class="px-5 py-7 text-center text-sm text-gray-500">No class batches created yet.</td></tr>
            <?php else: foreach ($batches as $batch):
              $endDate = App\ClassBatch::endDate($batch['start_date'], (int) $batch['duration_months']);
              $days = array_filter(array_map('intval', explode(',', $batch['class_weekdays'])));
            ?>
              <tr class="border-t border-gray-100">
                <td class="px-5 py-3"><div class="font-mono font-semibold text-ink"><?= h($batch['batch_code']) ?></div><div class="mt-1 text-[11px] text-gray-500"><?= (int) $batch['duration_months'] ?> months</div></td>
                <td class="px-3 py-3"><?= h($batch['course_name']) ?></td>
                <td class="px-3 py-3 text-xs"><?= h(date('d M Y', strtotime($batch['start_date']))) ?> – <?= h($endDate->format('d M Y')) ?></td>
                <td class="px-3 py-3 text-xs"><?= h(implode(', ', array_intersect_key(App\ClassBatch::WEEKDAYS, array_flip($days)))) ?><?php if ($batch['class_time']): ?><div class="mt-1 text-gray-500"><?= h($batch['class_time']) ?></div><?php endif; ?></td>
                <td class="px-3 py-3"><span class="rounded-full px-2 py-1 text-xs <?= $batch['active'] ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' ?>"><?= $batch['active'] ? 'Public' : 'Hidden' ?></span></td>
                <td class="px-5 py-3 text-right text-xs">
                  <?php if ($canWrite): ?>
                    <a href="?edit=<?= (int) $batch['id'] ?>" class="text-brand hover:underline">Edit</a>
                    <form method="post" class="ml-3 inline" onsubmit="return confirm('Delete this class batch?')">
                      <?= App\Csrf::field() ?><input type="hidden" name="action" value="delete_batch"><input type="hidden" name="id" value="<?= (int) $batch['id'] ?>">
                      <button class="text-accent hover:underline">Delete</button>
                    </form>
                  <?php endif; ?>
                  <?php if ($batch['active']): ?><a href="<?= h(url('batch-schedule.php?batch=' . urlencode($batch['batch_code']))) ?>" target="_blank" rel="noopener" class="ml-3 text-gray-500 hover:text-brand">Preview</a><?php endif; ?>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white">
      <div class="border-b border-gray-200 px-5 py-4"><h2 class="font-semibold text-ink">Government & campus holidays</h2></div>
      <ul class="divide-y divide-gray-100">
        <?php if (!$holidays): ?>
          <li class="px-5 py-6 text-center text-sm text-gray-500">No holidays added yet.</li>
        <?php else: foreach ($holidays as $holiday): ?>
          <li class="flex items-center gap-4 px-5 py-3">
            <time class="w-28 shrink-0 text-sm font-medium text-ink"><?= h(date('d M Y', strtotime($holiday['holiday_date']))) ?></time>
            <span class="flex-1 text-sm text-gray-600"><?= h($holiday['title']) ?></span>
            <?php if ($canWrite): ?>
              <form method="post" onsubmit="return confirm('Remove this holiday from all class calendars?')">
                <?= App\Csrf::field() ?><input type="hidden" name="action" value="delete_holiday"><input type="hidden" name="id" value="<?= (int) $holiday['id'] ?>">
                <button class="text-xs text-accent hover:underline">Remove</button>
              </form>
            <?php endif; ?>
          </li>
        <?php endforeach; endif; ?>
      </ul>
    </section>
  </main>
</div>
<?php require __DIR__ . '/_layout/footer.php'; ?>
