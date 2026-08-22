<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin','editor','viewer');
$adminPage = 'countries';
$pageTitle = 'Countries';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    $id = (int) ($_POST['id'] ?? 0);
    $data = [
        'code'        => strtoupper(trim((string) ($_POST['code'] ?? ''))),
        'name'        => trim((string) ($_POST['name'] ?? '')),
        'flag_css'    => trim((string) ($_POST['flag_css'] ?? '')) ?: null,
        'description' => trim((string) ($_POST['description'] ?? '')) ?: null,
        'sort'        => (int) ($_POST['sort'] ?? 0),
        'active'      => isset($_POST['active']) ? 1 : 0,
        'featured'    => isset($_POST['featured']) ? 1 : 0,
    ];
    if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $cfg = require __DIR__ . '/../../config/config.php';
        $r = App\Upload::handle($_FILES['image'], 'media', App\Upload::ALLOWED_IMAGE, $cfg['uploads']['max_image_bytes'], 1920);
        $data['image_id'] = $r['id'];
    }
    if ($id > 0) {
        App\Database::update('countries', $data, 'id=:id', ['id' => $id]);
        App\Content::log((int) App\Auth::user()['id'], 'country.update', 'country', $id);
    } else {
        $id = App\Database::insert('countries', $data);
        App\Content::log((int) App\Auth::user()['id'], 'country.create', 'country', $id);
    }
    App\Flash::set('success', 'Country saved.');
    redirect('/admin/content/countries.php');
}
if (isset($_GET['delete'])) {
    App\Csrf::verifyRequest();
    App\Database::delete('countries', 'id=:id', ['id' => (int) $_GET['delete']]);
    App\Content::log((int) App\Auth::user()['id'], 'country.delete', 'country', (int) $_GET['delete']);
    redirect('/admin/content/countries.php');
}
$edit = isset($_GET['edit']) ? App\Database::fetchOne('SELECT * FROM countries WHERE id=?', [(int) $_GET['edit']]) : null;
$rows = App\Database::fetchAll('SELECT c.*, m.path AS image_path FROM countries c LEFT JOIN media m ON m.id=c.image_id ORDER BY c.sort, c.id');
$canWrite = App\Auth::hasRole('admin','editor');

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <section class="bg-white rounded-xl border border-gray-200 p-5">
      <h2 class="font-semibold text-ink mb-4"><?= $edit ? 'Edit country' : 'Add country' ?></h2>
      <form method="post" enctype="multipart/form-data" class="grid sm:grid-cols-2 gap-4">
        <?= App\Csrf::field() ?>
        <input type="hidden" name="id" value="<?= h((string)($edit['id'] ?? '')) ?>">
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Code (e.g. JP)</span>
          <input name="code" value="<?= h($edit['code'] ?? '') ?>" required maxlength="8" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Name</span>
          <input name="name" value="<?= h($edit['name'] ?? '') ?>" required class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Flag CSS class (e.g. flag-jp)</span>
          <input name="flag_css" value="<?= h($edit['flag_css'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Description</span>
          <input name="description" value="<?= h($edit['description'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Image</span>
          <input type="file" name="image" accept="image/*" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Sort</span>
          <input name="sort" type="number" value="<?= h((string)($edit['sort'] ?? 0)) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="flex items-center gap-2">
          <input name="active" type="checkbox" value="1" <?= !isset($edit) || $edit['active'] ? 'checked' : '' ?>>
          <span class="text-sm">Active</span>
        </label>
        <label class="flex items-center gap-2">
          <input name="featured" type="checkbox" value="1" <?= !isset($edit) || !empty($edit['featured']) ? 'checked' : '' ?>>
          <span class="text-sm">Featured (showcase on landing)</span>
        </label>
        <div class="sm:col-span-2 flex items-center gap-2">
          <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md"><?= $edit ? 'Update' : 'Create' ?></button>
          <?php if ($edit): ?><a href="countries.php" class="text-sm text-gray-500 hover:text-brand">Cancel</a><?php endif; ?>
        </div>
      </form>
    </section>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200"><h2 class="font-semibold text-ink">All countries</h2></div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr><th class="text-left px-5 py-2.5">Code</th><th class="text-left px-3 py-2.5">Name</th><th class="text-left px-3 py-2.5">Description</th><th class="text-left px-3 py-2.5">Flags</th><th class="text-right px-5 py-2.5">Actions</th></tr>
          </thead>
          <tbody>
            <?php if (!$rows): ?>
              <tr><td colspan="5" class="px-5 py-6 text-center text-gray-500 text-sm">No countries yet.</td></tr>
            <?php else: foreach ($rows as $r): ?>
              <tr class="border-t border-gray-100">
                <td class="px-5 py-2.5 font-mono text-xs"><?= h($r['code']) ?></td>
                <td class="px-3 py-2.5 font-medium"><?= h($r['name']) ?></td>
                <td class="px-3 py-2.5 text-gray-600"><?= h($r['description'] ?? '') ?></td>
                <td class="px-3 py-2.5 text-xs"><?= $r['featured'] ? '<span class="px-2 py-0.5 rounded-full bg-accent/10 text-accent">Featured</span> ' : '' ?><?= $r['active'] ? '<span class="px-2 py-0.5 rounded-full bg-brand/10 text-brand">Active</span>' : '<span class="text-gray-400">Inactive</span>' ?></td>
                <td class="px-5 py-2.5 text-right text-xs space-x-3">
                  <a href="?edit=<?= (int) $r['id'] ?>" class="text-brand hover:underline">Edit</a>
                  <?php if ($canWrite): ?>
                    <a href="?delete=<?= (int) $r['id'] ?>&_csrf=<?= urlencode(App\Csrf::token()) ?>" onclick="return confirm('Delete?')" class="text-accent hover:underline">Delete</a>
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
