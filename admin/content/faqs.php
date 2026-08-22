<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin','editor','viewer');
$adminPage = 'faqs';
$pageTitle = 'FAQs';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    $id = (int) ($_POST['id'] ?? 0);
    $data = [
        'question' => trim((string) ($_POST['question'] ?? '')),
        'answer'   => (string) ($_POST['answer'] ?? ''),
        'sort'     => (int) ($_POST['sort'] ?? 0),
        'active'   => isset($_POST['active']) ? 1 : 0,
    ];
    if ($id > 0) {
        App\Database::update('faqs', $data, 'id=:id', ['id' => $id]);
        App\Content::log((int) App\Auth::user()['id'], 'faq.update', 'faq', $id);
    } else {
        $id = App\Database::insert('faqs', $data);
        App\Content::log((int) App\Auth::user()['id'], 'faq.create', 'faq', $id);
    }
    App\Flash::set('success', 'FAQ saved.');
    redirect('/admin/content/faqs.php');
}
if (isset($_GET['delete'])) {
    App\Csrf::verifyRequest();
    App\Database::delete('faqs', 'id=:id', ['id' => (int) $_GET['delete']]);
    App\Content::log((int) App\Auth::user()['id'], 'faq.delete', 'faq', (int) $_GET['delete']);
    redirect('/admin/content/faqs.php');
}
$edit = isset($_GET['edit']) ? App\Database::fetchOne('SELECT * FROM faqs WHERE id=?', [(int) $_GET['edit']]) : null;
$rows = App\Database::fetchAll('SELECT * FROM faqs ORDER BY sort, id');
$canWrite = App\Auth::hasRole('admin','editor');

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <section class="bg-white rounded-xl border border-gray-200 p-5">
      <h2 class="font-semibold text-ink mb-4"><?= $edit ? 'Edit FAQ' : 'Add FAQ' ?></h2>
      <form method="post" class="space-y-4">
        <?= App\Csrf::field() ?>
        <input type="hidden" name="id" value="<?= h((string)($edit['id'] ?? '')) ?>">
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Question</span>
          <input name="question" value="<?= h($edit['question'] ?? '') ?>" required class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Answer</span>
          <textarea name="answer" rows="4" required class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm"><?= h($edit['answer'] ?? '') ?></textarea>
        </label>
        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Sort</span>
            <input name="sort" type="number" value="<?= h((string)($edit['sort'] ?? 0)) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
          </label>
          <label class="flex items-center gap-2 mt-7">
            <input name="active" type="checkbox" value="1" <?= !isset($edit) || $edit['active'] ? 'checked' : '' ?>>
            <span class="text-sm">Active</span>
          </label>
        </div>
        <div class="flex items-center gap-2">
          <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md"><?= $edit ? 'Update' : 'Create' ?></button>
          <?php if ($edit): ?><a href="faqs.php" class="text-sm text-gray-500 hover:text-brand">Cancel</a><?php endif; ?>
        </div>
      </form>
    </section>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200"><h2 class="font-semibold text-ink">All FAQs</h2></div>
      <ul class="divide-y divide-gray-100">
        <?php if (!$rows): ?>
          <li class="px-5 py-6 text-center text-gray-500 text-sm">No FAQs yet.</li>
        <?php else: foreach ($rows as $r): ?>
          <li class="px-5 py-4 flex items-start gap-4">
            <div class="flex-1">
              <div class="font-medium text-ink"><?= h($r['question']) ?></div>
              <div class="text-sm text-gray-600 mt-1 line-clamp-2"><?= h($r['answer']) ?></div>
              <div class="text-xs text-gray-500 mt-1">Sort: <?= (int) $r['sort'] ?> · <?= $r['active'] ? '<span class=text-brand>Active</span>' : '<span class=text-gray-400>Inactive</span>' ?></div>
            </div>
            <div class="shrink-0 text-right text-xs space-x-3">
              <a href="?edit=<?= (int) $r['id'] ?>" class="text-brand hover:underline">Edit</a>
              <?php if ($canWrite): ?>
                <a href="?delete=<?= (int) $r['id'] ?>&_csrf=<?= urlencode(App\Csrf::token()) ?>" onclick="return confirm('Delete this FAQ?')" class="text-accent hover:underline">Delete</a>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; endif; ?>
      </ul>
    </section>
  </main>
</div>
<?php require __DIR__ . '/../_layout/footer.php'; ?>
