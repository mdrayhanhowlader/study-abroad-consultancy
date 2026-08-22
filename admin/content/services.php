<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin','editor','viewer');
$adminPage = 'services';
$pageTitle = 'Services';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    $id = (int) ($_POST['id'] ?? 0);
    $title = trim((string) ($_POST['title'] ?? ''));
    $slug  = trim((string) ($_POST['slug'] ?? '')) ?: App\Validation::slug($title);
    if (!App\Database::fetchOne('SELECT id FROM services WHERE slug=? AND id<>?', [$slug, $id ?: 0])) {
        $data = [
            'slug'     => $slug,
            'title'    => $title,
            'icon'     => trim((string) ($_POST['icon'] ?? 'globe')) ?: 'globe',
            'summary'  => trim((string) ($_POST['summary'] ?? '')) ?: null,
            'body'     => (string) ($_POST['body'] ?? ''),
            'cta_text' => trim((string) ($_POST['cta_text'] ?? '')) ?: null,
            'cta_href' => trim((string) ($_POST['cta_href'] ?? '')) ?: null,
            'sort'     => (int) ($_POST['sort'] ?? 0),
            'active'   => isset($_POST['active']) ? 1 : 0,
        ];
        if ($id > 0) {
            App\Database::update('services', $data, 'id=:id', ['id' => $id]);
            App\Content::log((int) App\Auth::user()['id'], 'service.update', 'service', $id);
        } else {
            $id = App\Database::insert('services', $data);
            App\Content::log((int) App\Auth::user()['id'], 'service.create', 'service', $id);
        }
        App\Flash::set('success', 'Service saved.');
        redirect('/admin/content/services.php');
    } else {
        App\Flash::set('error', 'Slug already used.');
    }
}
if (isset($_GET['delete'])) {
    App\Csrf::verifyRequest();
    App\Database::delete('services', 'id=:id', ['id' => (int) $_GET['delete']]);
    App\Content::log((int) App\Auth::user()['id'], 'service.delete', 'service', (int) $_GET['delete']);
    redirect('/admin/content/services.php');
}
$edit = isset($_GET['edit']) ? App\Database::fetchOne('SELECT * FROM services WHERE id=?', [(int) $_GET['edit']]) : null;
$rows = App\Database::fetchAll('SELECT * FROM services ORDER BY sort, id');
$canWrite = App\Auth::hasRole('admin','editor');

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <section class="bg-white rounded-xl border border-gray-200 p-5">
      <h2 class="font-semibold text-ink mb-4"><?= $edit ? 'Edit service' : 'Add service' ?></h2>
      <form method="post" class="grid sm:grid-cols-2 gap-4">
        <?= App\Csrf::field() ?>
        <input type="hidden" name="id" value="<?= h((string)($edit['id'] ?? '')) ?>">
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Title</span>
          <input name="title" value="<?= h($edit['title'] ?? '') ?>" required class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Slug</span>
          <input name="slug" value="<?= h($edit['slug'] ?? '') ?>" placeholder="auto" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Icon (from _icons.php)</span>
          <input name="icon" value="<?= h($edit['icon'] ?? 'globe') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Summary</span>
          <input name="summary" value="<?= h($edit['summary'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Body (HTML)</span>
          <textarea name="body" rows="6" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm font-mono"><?= h($edit['body'] ?? '') ?></textarea>
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">CTA text</span>
          <input name="cta_text" value="<?= h($edit['cta_text'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">CTA link</span>
          <input name="cta_href" value="<?= h($edit['cta_href'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Sort</span>
          <input name="sort" type="number" value="<?= h((string)($edit['sort'] ?? 0)) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="flex items-center gap-2 mt-7">
          <input name="active" type="checkbox" value="1" <?= !isset($edit) || $edit['active'] ? 'checked' : '' ?>>
          <span class="text-sm">Active</span>
        </label>
        <div class="sm:col-span-2 flex items-center gap-2">
          <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md"><?= $edit ? 'Update' : 'Create' ?></button>
          <?php if ($edit): ?><a href="services.php" class="text-sm text-gray-500 hover:text-brand">Cancel</a><?php endif; ?>
        </div>
      </form>
    </section>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200"><h2 class="font-semibold text-ink">All services</h2></div>
      <ul class="divide-y divide-gray-100">
        <?php if (!$rows): ?>
          <li class="px-5 py-6 text-center text-gray-500 text-sm">No services yet.</li>
        <?php else: foreach ($rows as $r): ?>
          <li class="px-5 py-4 flex items-start gap-4">
            <div class="w-10 h-10 rounded-md bg-brand/10 text-brand grid place-items-center shrink-0 font-bold text-sm"><?= h(strtoupper(substr($r['title'], 0, 1))) ?></div>
            <div class="flex-1">
              <div class="font-medium text-ink"><?= h($r['title']) ?> <span class="text-xs text-gray-500">/<?= h($r['slug']) ?></span></div>
              <div class="text-sm text-gray-600 mt-0.5"><?= h($r['summary'] ?? '') ?></div>
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
