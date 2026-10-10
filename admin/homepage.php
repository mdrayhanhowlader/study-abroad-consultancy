<?php
require_once __DIR__ . '/../app/bootstrap.php';
App\Auth::requireRole('admin', 'editor');
$adminPage = 'homepage';
$pageTitle = 'Homepage content';

$lang = (string) ($_GET['lang'] ?? App\I18n::current());
if (!App\I18n::isSupported($lang)) $lang = App\I18n::DEFAULT;
App\I18n::set($lang);
$groups = require __DIR__ . '/../data/homepage_editor.php';
$storyDefaults = require __DIR__ . '/../data/success_stories.php';
$stories = App\Homepage::stories($storyDefaults['stories']);
$storyFields = ['name' => 'Name', 'destination' => 'Destination', 'year' => 'Year', 'program' => 'Program', 'photo' => 'Photo URL', 'quote' => 'Quote'];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    $postedLang = (string) ($_POST['lang'] ?? '');
    if (!App\I18n::isSupported($postedLang)) {
        App\Flash::set('error', 'Unsupported language selected.');
        redirect('/admin/homepage.php');
    }

    $values = [];
    $current = App\Settings::getJson('homepage_content_' . $postedLang, []);
    foreach ($groups as $fields) {
        foreach ($fields as $fieldDef) {
            [$key, $label] = $fieldDef;
            $type = $fieldDef[2] ?? 'text';
            $value = trim((string) ($_POST['content'][$key] ?? ''));
            if (strlen($value) > 5000) {
                App\Flash::set('error', $label . ' is too long (maximum 5,000 characters).');
                redirect('/admin/homepage.php?lang=' . urlencode($postedLang));
            }
            if ($type === 'number' && $value !== '') {
                $numericValue = filter_var($value, FILTER_VALIDATE_INT);
                if ($numericValue === false || $numericValue < 0) {
                    App\Flash::set('error', $label . ' must be a non-negative whole number.');
                    redirect('/admin/homepage.php?lang=' . urlencode($postedLang));
                }
            }
            if ($type === 'url' && $value !== '') {
                $isUploadPath = preg_match('#^/?uploads/[A-Za-z0-9_./-]+$#', $value) === 1;
                $parts = parse_url($value);
                $validRemote = filter_var($value, FILTER_VALIDATE_URL)
                    && in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true);
                if (!$isUploadPath && !$validRemote) {
                    App\Flash::set('error', $label . ' must be an http(s) URL or an uploads path.');
                    redirect('/admin/homepage.php?lang=' . urlencode($postedLang));
                }
            }
            $values[$key] = $value;
        }
    }

    $postedStories = $_POST['stories'] ?? [];
    if (!is_array($postedStories)) {
        App\Flash::set('error', 'Student stories data is invalid.');
        redirect('/admin/homepage.php?lang=' . urlencode($postedLang));
    }
    $savedStories = [];
    foreach (array_values($stories) as $index => $fallbackStory) {
        $row = $postedStories[$index] ?? [];
        if (!is_array($row)) {
            App\Flash::set('error', 'Student story data is incomplete.');
            redirect('/admin/homepage.php?lang=' . urlencode($postedLang));
        }
        $story = [];
        foreach ($storyFields as $field => $label) {
            $value = trim((string) ($row[$field] ?? ''));
            if (strlen($value) > 5000) {
                App\Flash::set('error', 'Student story ' . ($index + 1) . ' ' . strtolower($label) . ' is too long.');
                redirect('/admin/homepage.php?lang=' . urlencode($postedLang));
            }
            if ($field === 'photo' && $value !== '') {
                $parts = parse_url($value);
                $isUploadPath = preg_match('#^/?uploads/[A-Za-z0-9_./-]+$#', $value) === 1;
                $validRemote = filter_var($value, FILTER_VALIDATE_URL)
                    && in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true);
                if (!$isUploadPath && !$validRemote) {
                    App\Flash::set('error', 'Student story ' . ($index + 1) . ' photo must be an http(s) URL or an uploads path.');
                    redirect('/admin/homepage.php?lang=' . urlencode($postedLang));
                }
            }
            $story[$field] = $value;
        }
        if ($story['name'] === '') {
            App\Flash::set('error', 'Every student story needs a name.');
            redirect('/admin/homepage.php?lang=' . urlencode($postedLang));
        }
        if ($story['photo'] === '') $story['photo'] = $fallbackStory['photo'];
        $savedStories[] = array_merge($fallbackStory, $story);
        foreach ($story as $field => $value) {
            $values['success.story.' . $index . '.' . $field] = $value;
        }
    }
    if (!$savedStories) {
        App\Flash::set('error', 'Keep at least one student success story.');
        redirect('/admin/homepage.php?lang=' . urlencode($postedLang));
    }

    $values['success.stories'] = $savedStories;
    $current = array_merge($current, $values);
    App\Settings::set('homepage_content_' . $postedLang, json_encode($current, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    App\Cache::clear();
    App\Content::log((int) App\Auth::user()['id'], 'homepage.content.update', 'homepage');
    App\Flash::set('success', 'Homepage content saved for ' . App\I18n::nativeName($postedLang) . '.');
    redirect('/admin/homepage.php?lang=' . urlencode($postedLang));
}

$content = App\Homepage::content();
require __DIR__ . '/_layout/head.php';
require __DIR__ . '/_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <section class="bg-white rounded-xl border border-gray-200 p-5">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="font-semibold text-ink">Edit homepage content</h2>
          <p class="mt-1 text-sm text-gray-500">Update copy, image links, hero destinations, and success stories. Manage destination tiles, service cards, articles, testimonials, and banners in their respective content pages. Layout and styling stay unchanged.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <?php foreach (App\I18n::SUPPORTED as $code): ?>
            <a href="?lang=<?= h($code) ?>" class="rounded-full border px-3 py-1.5 text-sm <?= $lang === $code ? 'border-brand bg-brand/10 font-semibold text-brand' : 'border-gray-200 text-gray-600 hover:border-brand' ?>">
              <?= h(App\I18n::flag($code) . ' ' . App\I18n::nativeName($code)) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <form method="post" class="space-y-6">
      <?= App\Csrf::field() ?>
      <input type="hidden" name="lang" value="<?= h($lang) ?>">

      <?php foreach ($groups as $group => $fields): ?>
        <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
          <h2 class="px-5 py-4 border-b border-gray-200 font-semibold text-ink"><?= h($group) ?></h2>
          <div class="grid md:grid-cols-2 gap-4 p-5">
            <?php foreach ($fields as $fieldDef): ?>
              <?php
                [$key, $label] = $fieldDef;
                $type = $fieldDef[2] ?? 'text';
                $fallback = $fieldDef[3] ?? null;
                $default = $fallback ?? t($key);
                $value = $content[$key] ?? $default;
                $wide = $type === 'textarea' || $type === 'url' || $key === 'visual.route_destinations';
              ?>
              <label class="block <?= $wide ? 'md:col-span-2' : '' ?>">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-700"><?= h($label) ?></span>
                <?php if ($type === 'textarea'): ?>
                  <textarea name="content[<?= h($key) ?>]" rows="3" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm"><?= h((string) $value) ?></textarea>
                <?php else: ?>
                  <input name="content[<?= h($key) ?>]" type="<?= $type === 'number' ? 'number' : 'text' ?>" <?= $type === 'number' ? 'min="0" step="1"' : '' ?> inputmode="<?= $type === 'url' ? 'url' : 'text' ?>" value="<?= h((string) $value) ?>" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm">
                <?php endif; ?>
                <?php if ($key === 'visual.route_destinations'): ?>
                  <span class="mt-1 block text-xs text-gray-500">City names only; the route animation keeps “Dhaka →” fixed.</span>
                <?php elseif ($type === 'url'): ?>
                  <span class="mt-1 block text-xs text-gray-500">Use an https image URL or a path under uploads/.</span>
                <?php endif; ?>
              </label>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>

      <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-200 px-5 py-4">
          <h2 class="font-semibold text-ink">Student success stories</h2>
          <p class="mt-1 text-sm text-gray-500">Edit the cards and modal details shown in the homepage story gallery.</p>
        </div>
        <div class="space-y-5 p-5">
          <?php foreach (array_values($stories) as $index => $story): ?>
            <div class="rounded-lg border border-gray-200 p-4">
              <h3 class="mb-3 text-sm font-semibold text-gray-700">Story <?= $index + 1 ?></h3>
              <div class="grid md:grid-cols-2 gap-4">
                <?php foreach ($storyFields as $field => $label):
                  $key = 'success.story.' . $index . '.' . $field;
                  $value = $content[$key] ?? ($story[$field] ?? '');
                  $isLong = $field === 'quote';
                  $isUrl = $field === 'photo';
                ?>
                  <label class="block <?= $isLong || $isUrl ? 'md:col-span-2' : '' ?>">
                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-700"><?= h($label) ?></span>
                    <?php if ($isLong): ?>
                      <textarea name="stories[<?= $index ?>][<?= h($field) ?>]" rows="2" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm"><?= h((string) $value) ?></textarea>
                    <?php else: ?>
                      <input name="stories[<?= $index ?>][<?= h($field) ?>]" type="text" inputmode="<?= $isUrl ? 'url' : 'text' ?>" value="<?= h((string) $value) ?>" class="mt-1.5 w-full rounded-md border border-gray-300 px-4 py-2.5 text-sm">
                    <?php endif; ?>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

      <div class="sticky bottom-3 z-10 flex justify-end">
        <button type="submit" class="rounded-md bg-brand px-6 py-3 text-sm font-semibold text-white shadow-lg hover:bg-brand-dark">Save homepage content</button>
      </div>
    </form>
  </main>
</div>
<?php require __DIR__ . '/_layout/footer.php'; ?>
