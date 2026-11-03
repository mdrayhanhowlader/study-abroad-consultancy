<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin', 'editor', 'viewer');
$adminPage = 'success_stories';
$pageTitle = 'Student Success Stories';
$canWrite = App\Auth::hasRole('admin', 'editor');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!$canWrite) {
        http_response_code(403);
        exit('You do not have permission to change student stories.');
    }
    try {
        App\Csrf::verifyRequest();
        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'name'        => App\Validation::str((string) ($_POST['name'] ?? ''), 2, 120),
            'destination' => App\Validation::str((string) ($_POST['destination'] ?? ''), 2, 80),
            'story_year'  => max(2000, min(2150, (int) ($_POST['story_year'] ?? date('Y')))),
            'program'     => App\Validation::str((string) ($_POST['program'] ?? ''), 2, 160),
            'quote'       => App\Validation::str((string) ($_POST['quote'] ?? ''), 2, 5000),
            'sort'        => (int) ($_POST['sort'] ?? 0),
            'active'      => isset($_POST['active']) ? 1 : 0,
        ];
        $existing = null;
        if ($id) {
            $existing = App\Database::fetchOne('SELECT id, photo_id FROM success_stories WHERE id=?', [$id]);
            if (!$existing) {
                throw new RuntimeException('Student story not found.');
            }
        }
        $photoSelected = isset($_FILES['photo']) && ($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        if (!$id && !$photoSelected) {
            throw new RuntimeException('Upload a student photo before adding the story.');
        }
        if ($photoSelected) {
            $cfg = require __DIR__ . '/../../config/config.php';
            $upload = App\Upload::handle($_FILES['photo'], 'student-stories', App\Upload::ALLOWED_IMAGE, $cfg['uploads']['max_image_bytes'], 1000);
            $data['photo_id'] = $upload['id'];
        } elseif ($id) {
            $data['photo_id'] = $existing['photo_id'] ?? null;
        } else {
            $data['photo_id'] = null;
        }
        if ($id) {
            App\Database::update('success_stories', $data, 'id=:id', ['id' => $id]);
            App\Content::log((int) App\Auth::user()['id'], 'success_story.update', 'success_story', $id);
        } else {
            $id = App\Database::insert('success_stories', $data);
            App\Content::log((int) App\Auth::user()['id'], 'success_story.create', 'success_story', $id);
        }
        App\Flash::set('success', 'Student story saved and published settings updated.');
        redirect('/admin/content/success-stories.php');
    } catch (Throwable $e) {
        App\Flash::set('error', $e->getMessage());
    }
}

if (isset($_GET['delete'])) {
    if (!$canWrite) {
        http_response_code(403);
        exit('You do not have permission to delete student stories.');
    }
    App\Csrf::verifyRequest();
    $deleteId = (int) $_GET['delete'];
    App\Database::delete('success_stories', 'id=:id', ['id' => $deleteId]);
    App\Content::log((int) App\Auth::user()['id'], 'success_story.delete', 'success_story', $deleteId);
    App\Flash::set('success', 'Student story deleted.');
    redirect('/admin/content/success-stories.php');
}

$editId = (int) ($_GET['edit'] ?? (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' ? ($_POST['id'] ?? 0) : 0));
$edit = $editId > 0
    ? App\Database::fetchOne(
        'SELECT s.*, m.path AS photo_path FROM success_stories s LEFT JOIN media m ON m.id=s.photo_id WHERE s.id=?',
        [$editId]
    )
    : null;
$formValues = $edit ?? [];
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $formValues = array_merge($formValues, [
        'name'        => (string) ($_POST['name'] ?? ''),
        'destination' => (string) ($_POST['destination'] ?? ''),
        'story_year'  => (string) ($_POST['story_year'] ?? date('Y')),
        'program'     => (string) ($_POST['program'] ?? ''),
        'quote'       => (string) ($_POST['quote'] ?? ''),
        'sort'        => (string) ($_POST['sort'] ?? '0'),
        'active'      => isset($_POST['active']) ? 1 : 0,
    ]);
}
$rows = App\Database::fetchAll(
    'SELECT s.*, m.path AS photo_path FROM success_stories s LEFT JOIN media m ON m.id=s.photo_id ORDER BY s.sort, s.id'
);

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>
    <section class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">
      Add real students who travelled with Manami Education. Active stories appear in the homepage Student Success section after saving.
    </section>

    <?php if ($canWrite): ?>
    <section class="bg-white rounded-xl border border-gray-200 p-5 max-w-4xl">
      <h2 class="font-semibold text-ink mb-4"><?= $edit ? 'Edit student story' : 'Add student story' ?></h2>
      <form method="post" enctype="multipart/form-data" class="grid sm:grid-cols-2 gap-4">
        <?= App\Csrf::field() ?>
        <input type="hidden" name="id" value="<?= h((string) ($edit['id'] ?? '')) ?>">
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Student name</span>
          <input name="name" value="<?= h($formValues['name'] ?? '') ?>" required maxlength="120" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Destination</span>
          <input name="destination" value="<?= h($formValues['destination'] ?? '') ?>" required maxlength="80" placeholder="Japan" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Year</span>
          <input name="story_year" type="number" min="2000" max="2150" value="<?= h((string) ($formValues['story_year'] ?? date('Y'))) ?>" required class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Study / work program</span>
          <input name="program" value="<?= h($formValues['program'] ?? '') ?>" required maxlength="160" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Student photo</span>
          <input type="file" name="photo" accept="image/jpeg,image/png,image/webp,image/gif" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2 text-sm">
          <?php if (!empty($edit['photo_path'])): ?>
            <span class="mt-1 block text-xs text-gray-500">Current photo — leave blank to keep it. Use a photo you have permission to publish. Maximum 5 MB.</span>
            <img src="<?= h(App\Upload::url($edit['photo_path'])) ?>" alt="<?= h($edit['name']) ?>" class="mt-2 h-24 w-24 rounded-lg object-cover">
          <?php else: ?>
            <span class="mt-1 block text-xs text-gray-500">Use a photo you have permission to publish. Maximum 5 MB.</span>
          <?php endif; ?>
        </label>
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Student story / quote</span>
          <textarea name="quote" rows="3" required maxlength="5000" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm"><?= h($formValues['quote'] ?? '') ?></textarea>
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Display order</span>
          <input name="sort" type="number" value="<?= h((string) ($formValues['sort'] ?? 0)) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="flex items-center gap-2">
          <input name="active" type="checkbox" value="1" <?= !empty($formValues['active']) || (!$edit && ($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') ? 'checked' : '' ?>>
          <span class="text-sm">Show on homepage</span>
        </label>
        <div class="sm:col-span-2 flex items-center gap-2">
          <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md"><?= $edit ? 'Update story' : 'Add story' ?></button>
          <?php if ($edit): ?><a href="success-stories.php" class="text-sm text-gray-500 hover:text-brand">Cancel</a><?php endif; ?>
        </div>
      </form>
    </section>
    <?php endif; ?>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200"><h2 class="font-semibold text-ink">Homepage student stories</h2></div>
      <ul class="divide-y divide-gray-100">
        <?php if (!$rows): ?>
          <li class="px-5 py-6 text-center text-gray-500 text-sm">No student stories have been added yet.</li>
        <?php else: foreach ($rows as $row): ?>
          <li class="px-5 py-4 flex items-start gap-4">
            <div class="w-14 h-14 rounded-full bg-gray-100 grid place-items-center overflow-hidden shrink-0">
              <?php if ($row['photo_path']): ?><img src="<?= h(App\Upload::url($row['photo_path'])) ?>" alt="" class="w-full h-full object-cover"><?php else: ?><span class="text-gray-500 font-semibold"><?= h(mb_strtoupper(mb_substr($row['name'], 0, 1))) ?></span><?php endif; ?>
            </div>
            <div class="flex-1 min-w-0">
              <div class="font-medium text-ink"><?= h($row['name']) ?> <span class="text-xs text-gray-500">· <?= h($row['destination']) ?> · <?= (int) $row['story_year'] ?></span></div>
              <div class="text-sm text-gray-600 mt-1"><?= h($row['program']) ?></div>
              <div class="text-xs text-gray-500 mt-1 line-clamp-2"><?= h($row['quote']) ?></div>
              <div class="text-xs mt-1 <?= $row['active'] ? 'text-emerald-700' : 'text-gray-400' ?>"><?= $row['active'] ? 'Published on homepage' : 'Hidden' ?></div>
            </div>
            <?php if ($canWrite): ?>
            <div class="shrink-0 text-xs space-x-3">
              <a href="?edit=<?= (int) $row['id'] ?>" class="text-brand hover:underline">Edit</a>
              <a href="?delete=<?= (int) $row['id'] ?>&_csrf=<?= urlencode(App\Csrf::token()) ?>" onclick="return confirm('Delete this student story?')" class="text-accent hover:underline">Delete</a>
            </div>
            <?php endif; ?>
          </li>
        <?php endforeach; endif; ?>
      </ul>
    </section>
  </main>
</div>
<?php require __DIR__ . '/../_layout/footer.php'; ?>
