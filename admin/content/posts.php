<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin','editor','viewer');
$adminPage = 'posts';
$pageTitle = 'Blog & News';

$edit = null;
if (isset($_GET['edit'])) {
    $edit = App\Database::fetchOne('SELECT * FROM posts WHERE id=?', [(int) $_GET['edit']]);
}
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    $id = (int) ($_POST['id'] ?? 0);
    $title = trim((string) ($_POST['title'] ?? ''));
    $slug  = trim((string) ($_POST['slug'] ?? '')) ?: App\Validation::slug($title);
    if (!App\Database::fetchOne('SELECT id FROM posts WHERE slug=? AND id<>?', [$slug, $id ?: 0])) {
        $data = [
            'slug'         => $slug,
            'type'         => ($_POST['type'] ?? 'blog') === 'news' ? 'news' : 'blog',
            'title'        => $title,
            'excerpt'      => trim((string) ($_POST['excerpt'] ?? '')) ?: null,
            'body'         => (string) ($_POST['body'] ?? ''),
            'status'       => ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft',
            'published_at' => ($_POST['status'] ?? 'draft') === 'published' ? ($_POST['published_at'] ?: now()) : null,
            'author_id'    => (int) App\Auth::user()['id'],
        ];
        if (!empty($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $cfg = require __DIR__ . '/../../config/config.php';
            $r = App\Upload::handle($_FILES['cover'], 'posts', App\Upload::ALLOWED_IMAGE, $cfg['uploads']['max_image_bytes'], 1920);
            $data['cover_id'] = $r['id'];
        }
        if ($id > 0) {
            App\Database::update('posts', $data, 'id=:id', ['id' => $id]);
            App\Content::log((int) App\Auth::user()['id'], 'post.update', 'post', $id);
        } else {
            $id = App\Database::insert('posts', $data);
            App\Content::log((int) App\Auth::user()['id'], 'post.create', 'post', $id);
        }
        App\Flash::set('success', 'Post saved.');
        redirect('/admin/content/posts.php');
    } else {
        App\Flash::set('error', 'Slug is already used. Pick a different one.');
    }
}
if (isset($_GET['delete'])) {
    App\Csrf::verifyRequest();
    App\Database::delete('posts', 'id=:id', ['id' => (int) $_GET['delete']]);
    App\Content::log((int) App\Auth::user()['id'], 'post.delete', 'post', (int) $_GET['delete']);
    App\Flash::set('success', 'Post deleted.');
    redirect('/admin/content/posts.php');
}

$rows = App\Database::fetchAll('SELECT * FROM posts ORDER BY id DESC');
$canWrite = App\Auth::hasRole('admin','editor');

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <section class="bg-white rounded-xl border border-gray-200 p-5">
      <h2 class="font-semibold text-ink mb-4"><?= $edit ? 'Edit post #' . (int) $edit['id'] : 'Add post' ?></h2>
      <form method="post" enctype="multipart/form-data" class="grid sm:grid-cols-2 gap-4">
        <?= App\Csrf::field() ?>
        <input type="hidden" name="id" value="<?= h((string)($edit['id'] ?? '')) ?>">
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Title</span>
          <input name="title" value="<?= h($edit['title'] ?? '') ?>" required class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Slug</span>
          <input name="slug" value="<?= h($edit['slug'] ?? '') ?>" placeholder="auto from title" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Type</span>
          <select name="type" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm bg-white">
            <option value="blog" <?= ($edit['type'] ?? 'blog') === 'blog' ? 'selected' : '' ?>>Blog</option>
            <option value="news" <?= ($edit['type'] ?? '') === 'news' ? 'selected' : '' ?>>News</option>
          </select>
        </label>
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Excerpt</span>
          <textarea name="excerpt" rows="2" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm"><?= h($edit['excerpt'] ?? '') ?></textarea>
        </label>
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Body (HTML)</span>
          <textarea name="body" rows="10" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm font-mono"><?= h($edit['body'] ?? '') ?></textarea>
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Cover image</span>
          <input type="file" name="cover" accept="image/*" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Status</span>
          <select name="status" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm bg-white">
            <option value="draft"     <?= ($edit['status'] ?? 'draft') === 'draft'     ? 'selected' : '' ?>>Draft</option>
            <option value="published" <?= ($edit['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
          </select>
        </label>
        <div class="sm:col-span-2 flex items-center gap-2">
          <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md"><?= $edit ? 'Update' : 'Create' ?></button>
          <?php if ($edit): ?><a href="posts.php" class="text-sm text-gray-500 hover:text-brand">Cancel</a><?php endif; ?>
        </div>
      </form>
    </section>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200"><h2 class="font-semibold text-ink">All posts</h2></div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr><th class="text-left px-5 py-2.5">Type</th><th class="text-left px-3 py-2.5">Title</th><th class="text-left px-3 py-2.5">Status</th><th class="text-left px-3 py-2.5">Published</th><th class="text-right px-5 py-2.5">Actions</th></tr>
          </thead>
          <tbody>
            <?php if (!$rows): ?>
              <tr><td colspan="5" class="px-5 py-6 text-center text-gray-500 text-sm">No posts yet.</td></tr>
            <?php else: foreach ($rows as $r): ?>
              <tr class="border-t border-gray-100">
                <td class="px-5 py-2.5"><span class="text-xs px-2 py-0.5 rounded-full bg-navy/10 text-navy"><?= h($r['type']) ?></span></td>
                <td class="px-3 py-2.5 font-medium"><?= h($r['title']) ?></td>
                <td class="px-3 py-2.5"><span class="text-xs px-2 py-0.5 rounded-full <?= $r['status'] === 'published' ? 'bg-brand/10 text-brand' : 'bg-gray-100 text-gray-600' ?>"><?= h($r['status']) ?></span></td>
                <td class="px-3 py-2.5 text-xs text-gray-500"><?= h($r['published_at'] ?? '—') ?></td>
                <td class="px-5 py-2.5 text-right">
                  <a href="?edit=<?= (int) $r['id'] ?>" class="text-brand hover:underline text-xs">Edit</a>
                  <?php if ($canWrite): ?>
                    <a href="?delete=<?= (int) $r['id'] ?>&_csrf=<?= urlencode(App\Csrf::token()) ?>" onclick="return confirm('Delete this post?')" class="text-accent hover:underline text-xs ml-3">Delete</a>
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
