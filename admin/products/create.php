<?php
$id = (int) ($_GET['id'] ?? 0);
$product = $id ? App\Database::fetchOne('SELECT * FROM products WHERE id=?', [$id]) : null;
$mode = $id ? 'edit' : 'create';
$pageTitle = $id ? 'Edit product' : 'New product';
$adminPage = 'products';
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin','editor');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    try {
    App\Csrf::verifyRequest();
    $title = trim((string) ($_POST['title'] ?? ''));
    $slug  = trim((string) ($_POST['slug'] ?? '')) ?: App\Validation::slug($title);
    if (!App\Database::fetchOne('SELECT id FROM products WHERE slug=? AND id<>?', [$slug, $id ?: 0])) {
        $data = [
            'slug'        => $slug,
            'type'        => in_array($_POST['type'] ?? 'physical', ['physical','digital','course'], true) ? $_POST['type'] : 'physical',
            'title'       => $title,
            'summary'     => trim((string) ($_POST['summary'] ?? '')) ?: null,
            'description' => (string) ($_POST['description'] ?? ''),
            'price_bdt'   => (float) ($_POST['price_bdt'] ?? 0),
            'stock'       => max(0, (int) ($_POST['stock'] ?? 0)),
            'sort'        => (int) ($_POST['sort'] ?? 0),
            'active'      => isset($_POST['active']) ? 1 : 0,
        ];
        if ($data['type'] === 'course' && !empty($_FILES['preview_pdf']['name'])) {
            throw new RuntimeException('Book PDF previews can only be added to book products.');
        }
        $data['preview_pdf_path'] = null;
        if (!empty($_FILES['preview_pdf']['name'])) {
            $data['preview_pdf_path'] = App\PrivateBookPreview::upload($_FILES['preview_pdf']);
        }
        if (!empty($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $cfg = require __DIR__ . '/../../config/config.php';
            $r = App\Upload::handle($_FILES['cover'], 'products', App\Upload::ALLOWED_IMAGE, $cfg['uploads']['max_image_bytes'], 1200);
            $data['cover_id'] = $r['id'];
        }
        if ($id) {
            App\Database::update('products', $data, 'id=:id', ['id' => $id]);
            App\Content::log((int) App\Auth::user()['id'], 'product.update', 'product', $id);
        } else {
            $id = App\Database::insert('products', $data);
            App\Content::log((int) App\Auth::user()['id'], 'product.create', 'product', $id);
        }
        App\Flash::set('success', 'Product saved.');
        redirect('/admin/products/edit.php?id=' . $id);
    } else {
        App\Flash::set('error', 'Slug already used.');
    }
    } catch (Throwable $e) {
        App\Flash::set('error', $e->getMessage());
    }
}

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50">
    <?= App\Flash::render() ?>

    <section class="bg-white rounded-xl border border-gray-200 p-5 max-w-3xl">
      <h2 class="font-semibold text-ink mb-4"><?= $id ? 'Edit product #' . $id : 'New product' ?></h2>
      <form method="post" enctype="multipart/form-data" class="grid sm:grid-cols-2 gap-4">
        <?= App\Csrf::field() ?>
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Title</span>
          <input name="title" value="<?= h($product['title'] ?? '') ?>" required class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Slug</span>
          <input name="slug" value="<?= h($product['slug'] ?? '') ?>" placeholder="auto" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Type</span>
          <select name="type" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm bg-white">
            <option value="physical" <?= ($product['type'] ?? '') === 'physical' ? 'selected' : '' ?>>Physical book</option>
            <option value="digital"  <?= ($product['type'] ?? '') === 'digital'  ? 'selected' : '' ?>>Digital download</option>
            <option value="course"   <?= ($product['type'] ?? '') === 'course'   ? 'selected' : '' ?>>Course</option>
          </select>
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Price (BDT)</span>
          <input name="price_bdt" type="number" step="0.01" value="<?= h((string)($product['price_bdt'] ?? 0)) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Stock</span>
          <input name="stock" type="number" value="<?= h((string)($product['stock'] ?? 0)) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Summary</span>
          <input name="summary" value="<?= h($product['summary'] ?? '') ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Description (HTML)</span>
          <textarea name="description" rows="8" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm font-mono"><?= h($product['description'] ?? '') ?></textarea>
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Cover image</span>
          <input type="file" name="cover" accept="image/*" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2 text-sm">
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Book sample PDF (max 25 MB)</span>
          <input type="file" name="preview_pdf" accept="application/pdf,.pdf" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2 text-sm">
          <span class="mt-1 block text-xs text-gray-500">Upload only a short excerpt, not the complete book.</span>
        </label>
        <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Sort</span>
          <input name="sort" type="number" value="<?= h((string)($product['sort'] ?? 0)) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
        </label>
        <label class="flex items-center gap-2 sm:col-span-2">
          <input name="active" type="checkbox" value="1" <?= !$id || $product['active'] ? 'checked' : '' ?>>
          <span class="text-sm">Active (visible on public site)</span>
        </label>
        <div class="sm:col-span-2 flex items-center gap-2">
          <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">Save</button>
          <a href="index.php" class="text-sm text-gray-500 hover:text-brand">Back to list</a>
        </div>
      </form>
    </section>
  </main>
</div>
<?php require __DIR__ . '/../_layout/footer.php'; ?>
