<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin', 'editor', 'viewer');
$adminPage = 'homepage_features';
$pageTitle = 'Homepage Features';
$canWrite = App\Auth::hasRole('admin', 'editor');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    if (!$canWrite) {
        http_response_code(403);
        exit('You do not have permission to change homepage features.');
    }

    try {
        $name = App\Validation::str((string) ($_POST['chairman_name'] ?? ''), 0, 120);
        $title = App\Validation::str((string) ($_POST['chairman_title'] ?? ''), 0, 160);
        $label = App\Validation::str((string) ($_POST['chairman_label'] ?? ''), 0, 120);
        $bio = App\Validation::str((string) ($_POST['chairman_bio'] ?? ''), 0, 3000);
        $quote = App\Validation::str((string) ($_POST['chairman_quote'] ?? ''), 0, 500);
        $videoUrl = trim((string) ($_POST['homepage_video_url'] ?? ''));
        $videoTitle = App\Validation::str((string) ($_POST['homepage_video_title'] ?? ''), 0, 160);
        $videoCaption = App\Validation::str((string) ($_POST['homepage_video_caption'] ?? ''), 0, 1000);
        if ($videoUrl !== '' && App\FacebookVideo::embedUrl($videoUrl) === null) {
            throw new InvalidArgumentException('Enter a valid Facebook video permalink (facebook.com video/reel or fb.watch link).');
        }

        $photoId = trim((string) App\Settings::get('chairman_photo_id', ''));
        $photo = $_FILES['chairman_photo'] ?? null;
        if ($photo && ($photo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $cfg = require __DIR__ . '/../../config/config.php';
            $uploaded = App\Upload::handle($photo, 'chairman', App\Upload::ALLOWED_IMAGE, $cfg['uploads']['max_image_bytes'], 1400);
            $photoId = (string) $uploaded['id'];
            App\Database::update(
                'media',
                ['uploaded_by' => (int) App\Auth::user()['id']],
                'id=:id',
                ['id' => (int) $uploaded['id']]
            );
            App\Content::log((int) App\Auth::user()['id'], 'homepage.chairman_photo.upload', 'media', (int) $uploaded['id']);
        } elseif (isset($_POST['remove_chairman_photo'])) {
            $photoId = '';
        }

        App\Settings::set('chairman_name', $name);
        App\Settings::set('chairman_title', $title);
        App\Settings::set('chairman_label', $label);
        App\Settings::set('chairman_bio', $bio);
        App\Settings::set('chairman_quote', $quote);
        App\Settings::set('chairman_photo_id', $photoId);
        App\Settings::set('chairman_visible', isset($_POST['chairman_visible']) ? '1' : '0');
        App\Settings::set('homepage_video_url', $videoUrl);
        App\Settings::set('homepage_video_title', $videoTitle);
        App\Settings::set('homepage_video_caption', $videoCaption);
        App\Content::log((int) App\Auth::user()['id'], 'homepage.features.update', 'homepage');
        App\Flash::set('success', 'Homepage features saved.');
    } catch (Throwable $e) {
        App\Flash::set('error', $e->getMessage());
    }
    redirect('/admin/content/homepage-features.php');
}

$photoId = (int) App\Settings::get('chairman_photo_id');
$photoUrl = App\Content::mediaUrl($photoId);
$values = [
    'chairman_name' => App\Settings::get('chairman_name', ''),
    'chairman_title' => App\Settings::get('chairman_title', ''),
    'chairman_label' => App\Settings::get('chairman_label', 'A message from our Chairman'),
    'chairman_bio' => App\Settings::get('chairman_bio', ''),
    'chairman_quote' => App\Settings::get('chairman_quote', ''),
    'homepage_video_url' => App\Settings::get('homepage_video_url', ''),
    'homepage_video_title' => App\Settings::get('homepage_video_title', ''),
    'homepage_video_caption' => App\Settings::get('homepage_video_caption', ''),
];

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 space-y-6 bg-gray-50 p-4 lg:p-8">
    <?= App\Flash::render() ?>
    <section class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm leading-6 text-emerald-900">
      Manage the chairman feature shown below the homepage banner and the Facebook video displayed in Student Insights. The chairman section appears only when a name and photo are set and visibility is enabled.
    </section>

    <?php if ($canWrite): ?>
    <form method="post" enctype="multipart/form-data" class="space-y-6">
      <?= App\Csrf::field() ?>
      <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="mb-5">
          <h2 class="font-semibold text-ink">Chairman's message</h2>
          <p class="mt-1 text-sm text-gray-500">Use the chairman's approved name, portrait, title and introduction.</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
          <label class="block"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">Chairman name</span>
            <input name="chairman_name" value="<?= h($values['chairman_name']) ?>" maxlength="120" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm">
          </label>
          <label class="block"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">Role / title</span>
            <input name="chairman_title" value="<?= h($values['chairman_title']) ?>" maxlength="160" placeholder="Chairman, Manami Education" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm">
          </label>
          <label class="block sm:col-span-2"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">Section label</span>
            <input name="chairman_label" value="<?= h($values['chairman_label']) ?>" maxlength="120" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm">
          </label>
          <label class="block sm:col-span-2"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">Short introduction</span>
            <textarea name="chairman_bio" rows="4" maxlength="3000" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm"><?= h($values['chairman_bio']) ?></textarea>
          </label>
          <label class="block sm:col-span-2"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">Featured message / quote (optional)</span>
            <textarea name="chairman_quote" rows="2" maxlength="500" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm"><?= h($values['chairman_quote']) ?></textarea>
          </label>
          <label class="block sm:col-span-2"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">Portrait (JPG, PNG, WebP; max 5 MB)</span>
            <input type="file" name="chairman_photo" accept="image/jpeg,image/png,image/webp,image/gif" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2 text-sm">
            <?php if ($photoUrl): ?>
              <span class="mt-2 flex items-center gap-2 text-xs text-gray-500">
                Current portrait
                <label class="inline-flex items-center gap-1"><input type="checkbox" name="remove_chairman_photo" value="1"> Remove it</label>
              </span>
              <img src="<?= h($photoUrl) ?>" alt="Current chairman portrait" class="mt-2 h-32 w-24 rounded-lg object-cover">
            <?php endif; ?>
          </label>
          <label class="flex items-center gap-2 sm:col-span-2">
            <input type="checkbox" name="chairman_visible" value="1" <?= App\Settings::get('chairman_visible', '1') === '1' ? 'checked' : '' ?>>
            <span class="text-sm">Show chairman section on the homepage</span>
          </label>
        </div>
      </section>

      <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="mb-5">
          <h2 class="font-semibold text-ink">Student Insights video</h2>
          <p class="mt-1 text-sm text-gray-500">Paste a public Facebook video, Reel, Watch or fb.watch link. No video file upload is needed.</p>
        </div>
        <div class="space-y-4">
          <label class="block"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">Facebook video URL</span>
            <input type="url" name="homepage_video_url" value="<?= h($values['homepage_video_url']) ?>" placeholder="https://www.facebook.com/.../videos/..." class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm">
          </label>
          <label class="block"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">Video heading</span>
            <input name="homepage_video_title" value="<?= h($values['homepage_video_title']) ?>" maxlength="160" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm">
          </label>
          <label class="block"><span class="text-xs font-semibold uppercase tracking-wide text-gray-700">Video description (optional)</span>
            <textarea name="homepage_video_caption" rows="3" maxlength="1000" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm"><?= h($values['homepage_video_caption']) ?></textarea>
          </label>
        </div>
      </section>
      <button class="rounded-md bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-dark">Save homepage features</button>
    </form>
    <?php else: ?>
      <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="font-semibold text-ink">Chairman's message</h2>
        <p class="mt-2 text-sm text-gray-600"><?= h($values['chairman_name'] ?: 'Not configured') ?> · <?= h($values['chairman_title']) ?></p>
        <h2 class="mt-6 font-semibold text-ink">Student Insights video</h2>
        <p class="mt-2 break-all text-sm text-gray-600"><?= h($values['homepage_video_url'] ?: 'Not configured') ?></p>
      </section>
    <?php endif; ?>
  </main>
</div>
<?php require __DIR__ . '/../_layout/footer.php'; ?>
