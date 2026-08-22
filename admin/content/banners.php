<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin','editor','viewer');
$adminPage = 'banners';
$pageTitle = 'Banners';

$edit = null;
if (isset($_GET['edit'])) {
    $edit = App\Database::fetchOne('SELECT * FROM banners WHERE id=?', [(int) $_GET['edit']]);
}
if (isset($_GET['delete'])) {
    App\Csrf::verifyRequest();
    App\Database::delete('banners', 'id=:id', ['id' => (int) $_GET['delete']]);
    App\Content::log((int) App\Auth::user()['id'], 'banner.delete', 'banner', (int) $_GET['delete']);
    App\Flash::set('success', 'Banner deleted.');
    redirect('/admin/content/banners.php');
}
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    $id = (int) ($_POST['id'] ?? 0);
    $data = [
        'slot'       => (string) ($_POST['slot'] ?? 'hero'),
        'heading'    => trim((string) ($_POST['heading'] ?? '')) ?: null,
        'subheading' => trim((string) ($_POST['subheading'] ?? '')) ?: null,
        'cta_text'   => trim((string) ($_POST['cta_text'] ?? '')) ?: null,
        'cta_href'   => trim((string) ($_POST['cta_href'] ?? '')) ?: null,
        'sort'       => (int) ($_POST['sort'] ?? 0),
        'active'     => isset($_POST['active']) ? 1 : 0,
    ];
    if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $cfg = require __DIR__ . '/../../config/config.php';
        $r = App\Upload::handle($_FILES['image'], 'banners', App\Upload::ALLOWED_IMAGE, $cfg['uploads']['max_image_bytes'], 1920);
        $data['image_id'] = $r['id'];
    }
    if ($id > 0) {
        App\Database::update('banners', $data, 'id=:id', ['id' => $id]);
        App\Content::log((int) App\Auth::user()['id'], 'banner.update', 'banner', $id);
    } else {
        $id = App\Database::insert('banners', $data);
        App\Content::log((int) App\Auth::user()['id'], 'banner.create', 'banner', $id);
    }
    App\Flash::set('success', 'Banner saved.');
    redirect('/admin/content/banners.php');
}

$rows = App\Database::fetchAll('SELECT b.*, m.path AS image_path FROM banners b LEFT JOIN media m ON m.id=b.image_id ORDER BY b.sort, b.id DESC');
$canWrite = App\Auth::hasRole('admin','editor');

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <section class="bg-white rounded-xl border border-gray-200 p-5">
      <h2 class="font-semibold text-ink mb-4"><?= $edit ? 'Edit banner #' . (int) $edit['id'] : 'Add banner' ?></h2>
      <form method="post" enctype="multipart/form-data" class="grid sm:grid-cols-2 gap-4">
        <?= App\Csrf::field() ?>
        <input type="hidden" name="id" value="<?= h((string)($edit['id'] ?? '')) ?>">
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Slot</span>
          <select name="slot" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm bg-white">
            <?php foreach (['hero','cta','prefooter','sidebar'] as $s): ?>
              <option value="<?= h($s) ?>" <?= ($edit['slot'] ?? 'hero') === $s ? 'selected' : '' ?>><?= h($s) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Sort</span>
          <input name="sort" type="number" value="<?= h((string)($edit['sort'] ?? 0)) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Heading</span>
          <input name="heading" value="<?= h($edit['heading'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Subheading</span>
          <input name="subheading" value="<?= h($edit['subheading'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">CTA text</span>
          <input name="cta_text" value="<?= h($edit['cta_text'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">CTA link</span>
          <input name="cta_href" value="<?= h($edit['cta_href'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Image (jpg/png/webp, 5 MB)</span>
          <input name="image" type="file" accept="image/*" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2 text-sm">
          <?php if (!empty($edit['image_path'])): ?>
            <div class="mt-2 text-xs text-gray-500">Current: <a href="<?= h(App\Upload::url($edit['image_path'])) ?>" target="_blank" class="text-brand hover:underline">view image</a></div>
          <?php endif; ?>
        </label>
        <label class="flex items-center gap-2 sm:col-span-2">
          <input name="active" type="checkbox" value="1" <?= !isset($edit) || $edit['active'] ? 'checked' : '' ?>>
          <span class="text-sm">Active (visible on public site)</span>
        </label>
        <div class="sm:col-span-2 flex items-center gap-2">
          <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md"><?= $edit ? 'Update' : 'Create' ?></button>
          <?php if ($edit): ?><a href="banners.php" class="text-sm text-gray-500 hover:text-brand">Cancel</a><?php endif; ?>
        </div>
      </form>
    </section>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200"><h2 class="font-semibold text-ink">All banners</h2></div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr><th class="text-left px-5 py-2.5">Slot</th><th class="text-left px-3 py-2.5">Heading</th><th class="text-left px-3 py-2.5">Image</th><th class="text-left px-3 py-2.5">Active</th><th class="text-right px-5 py-2.5">Actions</th></tr>
          </thead>
          <tbody>
            <?php if (!$rows): ?>
              <tr><td colspan="5" class="px-5 py-6 text-center text-gray-500 text-sm">No banners yet.</td></tr>
            <?php else: foreach ($rows as $r): ?>
              <tr class="border-t border-gray-100">
                <td class="px-5 py-2.5"><span class="text-xs px-2 py-0.5 rounded-full bg-navy/10 text-navy"><?= h($r['slot']) ?></span></td>
                <td class="px-3 py-2.5 font-medium"><?= h($r['heading'] ?? '—') ?></td>
                <td class="px-3 py-2.5"><?php if ($r['image_path']): ?><img src="<?= h(App\Upload::url($r['image_path'])) ?>" alt="" class="h-10 w-16 object-cover rounded"><?php else: ?>—<?php endif; ?></td>
                <td class="px-3 py-2.5"><?= $r['active'] ? '<span class="text-brand">Yes</span>' : '<span class="text-gray-400">No</span>' ?></td>
                <td class="px-5 py-2.5 text-right">
                  <a href="?edit=<?= (int) $r['id'] ?>" class="text-brand hover:underline text-xs">Edit</a>
                  <?php if ($canWrite): ?>
                    <a href="?delete=<?= (int) $r['id'] ?><?= h(App\Csrf::token() ? '&_csrf=' . urlencode(App\Csrf::token()) : '') ?>" onclick="return confirm('Delete this banner?')" class="text-accent hover:underline text-xs ml-3">Delete</a>
                  <?php endif; ?>
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
