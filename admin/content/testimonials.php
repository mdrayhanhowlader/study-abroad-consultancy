<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin','editor','viewer');
$adminPage = 'testimonials';
$pageTitle = 'Testimonials';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    $id = (int) ($_POST['id'] ?? 0);
    $data = [
        'name'    => trim((string) ($_POST['name'] ?? '')),
        'country' => trim((string) ($_POST['country'] ?? '')) ?: null,
        'rating'  => max(1, min(5, (int) ($_POST['rating'] ?? 5))),
        'body'    => (string) ($_POST['body'] ?? ''),
        'sort'    => (int) ($_POST['sort'] ?? 0),
        'active'  => isset($_POST['active']) ? 1 : 0,
    ];
    if (!empty($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $cfg = require __DIR__ . '/../../config/config.php';
        $r = App\Upload::handle($_FILES['photo'], 'media', App\Upload::ALLOWED_IMAGE, $cfg['uploads']['max_image_bytes'], 600);
        $data['photo_id'] = $r['id'];
    }
    if ($id > 0) {
        App\Database::update('testimonials', $data, 'id=:id', ['id' => $id]);
        App\Content::log((int) App\Auth::user()['id'], 'testimonial.update', 'testimonial', $id);
    } else {
        $id = App\Database::insert('testimonials', $data);
        App\Content::log((int) App\Auth::user()['id'], 'testimonial.create', 'testimonial', $id);
    }
    App\Flash::set('success', 'Testimonial saved.');
    redirect('/admin/content/testimonials.php');
}
if (isset($_GET['delete'])) {
    App\Csrf::verifyRequest();
    App\Database::delete('testimonials', 'id=:id', ['id' => (int) $_GET['delete']]);
    App\Content::log((int) App\Auth::user()['id'], 'testimonial.delete', 'testimonial', (int) $_GET['delete']);
    redirect('/admin/content/testimonials.php');
}
$edit = isset($_GET['edit']) ? App\Database::fetchOne('SELECT * FROM testimonials WHERE id=?', [(int) $_GET['edit']]) : null;
$rows = App\Database::fetchAll('SELECT t.*, m.path AS photo_path FROM testimonials t LEFT JOIN media m ON m.id=t.photo_id ORDER BY t.sort, t.id');
$canWrite = App\Auth::hasRole('admin','editor');

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <section class="bg-white rounded-xl border border-gray-200 p-5">
      <h2 class="font-semibold text-ink mb-4"><?= $edit ? 'Edit testimonial' : 'Add testimonial' ?></h2>
      <form method="post" enctype="multipart/form-data" class="grid sm:grid-cols-2 gap-4">
        <?= App\Csrf::field() ?>
        <input type="hidden" name="id" value="<?= h((string)($edit['id'] ?? '')) ?>">
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Name</span>
          <input name="name" value="<?= h($edit['name'] ?? '') ?>" required class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Country / City</span>
          <input name="country" value="<?= h($edit['country'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Rating (1–5)</span>
          <input name="rating" type="number" min="1" max="5" value="<?= h((string)($edit['rating'] ?? 5)) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Sort</span>
          <input name="sort" type="number" value="<?= h((string)($edit['sort'] ?? 0)) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Quote</span>
          <textarea name="body" rows="3" required class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm"><?= h($edit['body'] ?? '') ?></textarea>
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Photo</span>
          <input type="file" name="photo" accept="image/*" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2 text-sm">
        </label>
        <label class="flex items-center gap-2 mt-7">
          <input name="active" type="checkbox" value="1" <?= !isset($edit) || $edit['active'] ? 'checked' : '' ?>>
          <span class="text-sm">Active</span>
        </label>
        <div class="sm:col-span-2 flex items-center gap-2">
          <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md"><?= $edit ? 'Update' : 'Create' ?></button>
          <?php if ($edit): ?><a href="testimonials.php" class="text-sm text-gray-500 hover:text-brand">Cancel</a><?php endif; ?>
        </div>
      </form>
    </section>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200"><h2 class="font-semibold text-ink">All testimonials</h2></div>
      <ul class="divide-y divide-gray-100">
        <?php if (!$rows): ?>
          <li class="px-5 py-6 text-center text-gray-500 text-sm">No testimonials yet.</li>
        <?php else: foreach ($rows as $r): ?>
          <li class="px-5 py-4 flex items-start gap-4">
            <div class="w-12 h-12 rounded-full bg-gray-100 grid place-items-center overflow-hidden shrink-0">
              <?php if ($r['photo_path']): ?><img src="<?= h(App\Upload::url($r['photo_path'])) ?>" alt="" class="w-full h-full object-cover"><?php else: ?><span class="text-gray-500 font-semibold"><?= strtoupper(substr($r['name'], 0, 1)) ?></span><?php endif; ?>
            </div>
            <div class="flex-1">
              <div class="font-medium text-ink"><?= h($r['name']) ?> <span class="text-xs text-gray-500">· <?= h($r['country'] ?? '') ?></span> <span class="text-accent text-xs"><?= str_repeat('★', (int) $r['rating']) ?></span></div>
              <div class="text-sm text-gray-600 mt-1 line-clamp-2"><?= h($r['body']) ?></div>
            </div>
            <div class="shrink-0 text-xs space-x-3">
              <a href="?edit=<?= (int) $r['id'] ?>" class="text-brand hover:underline">Edit</a>
              <?php if ($canWrite): ?>
                <a href="?delete=<?= (int) $r['id'] ?>&_csrf=<?= urlencode(App\Csrf::token()) ?>" onclick="return confirm('Delete?')" class="text-accent hover:underline">Delete</a>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; endif; ?>
      </ul>
    </section>
  </main>
</div>
<?php require __DIR__ . '/../_layout/footer.php'; ?>
