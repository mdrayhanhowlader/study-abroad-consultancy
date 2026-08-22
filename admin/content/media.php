<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin','editor','viewer');
$adminPage = 'media';
$pageTitle = 'Media library';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    try {
        if (!empty($_FILES['files'])) {
            $cfg = require __DIR__ . '/../../config/config.php';
            $count = 0;
            $files = $_FILES['files'];
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
                $one = [
                    'name'     => $files['name'][$i],
                    'type'     => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i],
                ];
                $isVideo = str_starts_with($mime = $one['type'], 'video/');
                $allow   = $isVideo ? App\Upload::ALLOWED_VIDEO : App\Upload::ALLOWED_IMAGE;
                $max     = $isVideo ? $cfg['uploads']['max_video_bytes'] : $cfg['uploads']['max_image_bytes'];
                $r = App\Upload::handle($one, 'media', $allow, $max, 1920);
                $u = App\Auth::user();
                App\Database::update('media', ['uploaded_by' => $u['id']], 'id=:id', ['id' => $r['id']]);
                App\Content::log((int) $u['id'], 'media.upload', 'media', (int) $r['id']);
                $count++;
            }
            App\Flash::set('success', "$count file(s) uploaded.");
        }
    } catch (Throwable $e) {
        App\Flash::set('error', $e->getMessage());
    }
    redirect('/admin/content/media.php');
}
if (isset($_GET['delete'])) {
    App\Csrf::verifyRequest();
    $id = (int) $_GET['delete'];
    $m = App\Database::fetchOne('SELECT * FROM media WHERE id=?', [$id]);
    if ($m) {
        $abs = rtrim((require __DIR__ . '/../../config/config.php')['uploads']['base_path'], '/') . '/' . $m['path'];
        if (is_file($abs)) @unlink($abs);
        App\Database::delete('media', 'id=:id', ['id' => $id]);
        App\Content::log((int) App\Auth::user()['id'], 'media.delete', 'media', $id);
        App\Flash::set('success', 'Media deleted.');
    }
    redirect('/admin/content/media.php');
}

$rows = App\Database::fetchAll('SELECT * FROM media ORDER BY id DESC LIMIT 200');
$canWrite = App\Auth::hasRole('admin','editor');

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <section class="bg-white rounded-xl border border-gray-200 p-5">
      <h2 class="font-semibold text-ink mb-4">Upload</h2>
      <form method="post" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-end">
        <?= App\Csrf::field() ?>
        <label class="flex-1">
          <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Images (jpg/png/webp/svg) or video (mp4/webm) — multiple OK</span>
          <input type="file" name="files[]" multiple accept="image/*,video/mp4,video/webm" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2 text-sm">
        </label>
        <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">Upload</button>
      </form>
      <p class="text-xs text-gray-500 mt-2">Images auto-resized to 1920 px wide. Max 5 MB images, 100 MB videos.</p>
    </section>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="font-semibold text-ink">Library</h2>
        <span class="text-xs text-gray-500"><?= count($rows) ?> item(s)</span>
      </div>
      <?php if (!$rows): ?>
        <div class="px-5 py-8 text-center text-gray-500 text-sm">No media yet. Upload your first file above.</div>
      <?php else: ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 p-4">
          <?php foreach ($rows as $m): ?>
            <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
              <div class="aspect-square bg-gray-100 grid place-items-center">
                <?php if ($m['type'] === 'image'): ?>
                  <img src="<?= h(App\Upload::url($m['path'])) ?>" alt="" class="w-full h-full object-cover">
                <?php else: ?>
                  <div class="text-center px-2">
                    <div class="text-2xl">🎬</div>
                    <div class="text-[10px] text-gray-500 mt-1 truncate"><?= h($m['original_name']) ?></div>
                  </div>
                <?php endif; ?>
              </div>
              <div class="p-2 text-xs text-gray-600 flex items-center justify-between">
                <span class="truncate" title="<?= h($m['original_name']) ?>"><?= h($m['original_name']) ?></span>
                <?php if ($canWrite): ?>
                  <a href="?delete=<?= (int) $m['id'] ?>" onclick="return confirm('Delete this file?')" class="text-accent hover:underline ml-2 shrink-0">Delete</a>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>
</div>
<?php require __DIR__ . '/../_layout/footer.php'; ?>
