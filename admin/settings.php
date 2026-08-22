<?php
require_once __DIR__ . '/../app/bootstrap.php';
App\Auth::require();
$adminPage = 'settings';
$pageTitle = 'Settings';

$tab = $_GET['tab'] ?? 'brand';
$u   = App\Auth::user();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'password') {
            $cur = (string) ($_POST['current'] ?? '');
            $new = (string) ($_POST['new'] ?? '');
            $cnf = (string) ($_POST['confirm'] ?? '');
            $row = App\Database::fetchOne('SELECT password_hash FROM users WHERE id=?', [$u['id']]);
            if (!$row || !password_verify($cur, $row['password_hash'])) {
                throw new RuntimeException('Current password is incorrect.');
            }
            if (strlen($new) < 8) throw new RuntimeException('New password must be at least 8 characters.');
            if ($new !== $cnf) throw new RuntimeException('New passwords do not match.');
            App\Auth::setPassword((int) $u['id'], $new);
            $_SESSION['user']['must_change_pw'] = 0;
            App\Flash::set('success', 'Password updated.');
            redirect('/admin/settings.php?tab=password');
        }

        if (!App\Auth::hasRole('admin')) {
            throw new RuntimeException('Only administrators can change site settings.');
        }

        if ($action === 'brand') {
            foreach (['brand_name','brand_tagline','brand_slogan'] as $k) {
                if (isset($_POST[$k])) App\Settings::set($k, trim((string) $_POST[$k]));
            }
            foreach (['logo_image_id','favicon_image_id','apple_touch_icon_image_id','og_image_id'] as $k) {
                if (isset($_POST[$k])) {
                    $v = trim((string) $_POST[$k]);
                    App\Settings::set($k, $v);
                }
            }
            $cfg = require __DIR__ . '/../config/config.php';
            foreach (['logo','favicon','apple_touch_icon','og_image'] as $k) {
                if (!empty($_FILES[$k]) && $_FILES[$k]['error'] === UPLOAD_ERR_OK) {
                    $r = App\Upload::handle($_FILES[$k], 'brand', App\Upload::ALLOWED_IMAGE, $cfg['uploads']['max_image_bytes'], 1920);
                    $map = ['logo' => 'logo_image_id', 'favicon' => 'favicon_image_id', 'apple_touch_icon' => 'apple_touch_icon_image_id', 'og_image' => 'og_image_id'];
                    App\Settings::set($map[$k], (string) $r['id']);
                }
            }
            App\Cache::clear();
            App\Flash::set('success', 'Brand updated.');
        } elseif ($action === 'contact') {
            foreach (['contact_phone','contact_phone_tel','contact_email','contact_address','contact_address_link'] as $k) {
                if (isset($_POST[$k])) App\Settings::set($k, trim((string) $_POST[$k]));
            }
            App\Flash::set('success', 'Contact details updated.');
        } elseif ($action === 'social') {
            $items = [];
            for ($i = 0; $i < 6; $i++) {
                $l = trim((string) ($_POST["social_label_$i"] ?? ''));
                $h = trim((string) ($_POST["social_href_$i"]  ?? ''));
                $c = trim((string) ($_POST["social_icon_$i"]  ?? ''));
                if ($l && $h) $items[] = ['label' => $l, 'href' => $h, 'icon' => $c ?: 'globe'];
            }
            App\Settings::set('social_json', json_encode($items, JSON_UNESCAPED_UNICODE));
            App\Flash::set('success', 'Social links updated.');
        } elseif ($action === 'stats') {
            $items = [];
            for ($i = 0; $i < 6; $i++) {
                $l = trim((string) ($_POST["stat_label_$i"] ?? ''));
                $v = trim((string) ($_POST["stat_value_$i"] ?? ''));
                if ($l && $v !== '') $items[] = ['label' => $l, 'value' => is_numeric($v) ? (int) $v : $v];
            }
            App\Settings::set('stats_json', json_encode($items, JSON_UNESCAPED_UNICODE));
            App\Flash::set('success', 'Stats updated.');
        } elseif ($action === 'bkash') {
            App\Settings::set('bkash_merchant_number', trim((string) ($_POST['bkash_merchant_number'] ?? '')));
            App\Settings::set('bkash_instructions', trim((string) ($_POST['bkash_instructions'] ?? '')));
            $mode = ($_POST['bkash_mode'] ?? 'manual') === 'api' ? 'api' : 'manual';
            $configFile = __DIR__ . '/../config/config.php';
            $cfgRaw = file_get_contents($configFile);
            $cfgRaw = preg_replace("/'mode'\s*=>\s*'[^']*'/", "'mode' => '$mode'", $cfgRaw, 1);
            file_put_contents($configFile, $cfgRaw);
            App\Flash::set('success', 'bKash settings updated. Mode is now: ' . $mode . '.');
        } elseif ($action === 'nav') {
            $raw = trim((string) ($_POST['nav_json'] ?? ''));
            if ($raw === '') {
                App\Settings::set('nav_json', '[]');
            } else {
                $d = json_decode($raw, true);
                if (!is_array($d)) throw new RuntimeException('Invalid JSON for navigation menu.');
                App\Settings::set('nav_json', json_encode($d, JSON_UNESCAPED_UNICODE));
            }
            App\Cache::clear();
            App\Flash::set('success', 'Navigation menu updated.');
        } elseif ($action === 'pixel') {
            App\Settings::set('pixel_id', trim((string) ($_POST['pixel_id'] ?? '')));
            App\Settings::set('pixel_enabled', !empty($_POST['pixel_enabled']) ? '1' : '0');
            App\Cache::clear();
            App\Flash::set('success', 'Pixel settings updated.');
        } elseif ($action === 'payment') {
            foreach (['sslcommerz_store_id', 'sslcommerz_store_pass', 'sslcommerz_sandbox'] as $k) {
                if (isset($_POST[$k])) App\Settings::set($k, trim((string) $_POST[$k]));
            }
            App\Flash::set('success', 'Payment settings updated.');
        }
    } catch (Throwable $e) {
        App\Flash::set('error', $e->getMessage());
    }
    redirect('settings.php?tab=' . urlencode($tab));
}

$social = App\Settings::getJson('social_json', []);
$stats  = App\Settings::getJson('stats_json', []);

require __DIR__ . '/_layout/head.php';
require __DIR__ . '/_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50">
    <?= App\Flash::render() ?>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="border-b border-gray-200 px-2 overflow-x-auto">
        <nav class="flex gap-1">
          <?php
            $tabs = [
              'brand'    => 'Brand',
              'nav'      => 'Navigation',
              'contact'  => 'Contact',
              'social'   => 'Social',
              'stats'    => 'Stats',
              'bkash'    => 'bKash',
              'payment'  => 'Payment',
              'pixel'    => 'Pixel',
              'password' => 'Password',
            ];
            if (!App\Auth::hasRole('admin')) {
                $tabs = ['password' => 'Password'];
            }
            foreach ($tabs as $k => $label):
          ?>
            <a href="?tab=<?= h($k) ?>" class="px-4 py-3 text-sm font-medium <?= $tab === $k ? 'text-brand border-b-2 border-brand' : 'text-gray-500 hover:text-brand' ?>"><?= h($label) ?></a>
          <?php endforeach; ?>
        </nav>
      </div>

      <div class="p-6">
        <?php if ($tab === 'brand' && App\Auth::hasRole('admin')):
            $brandImages = [
              ['key' => 'logo_image_id', 'label' => 'Logo', 'hint' => 'Shown in header & footer. PNG/SVG with transparent background recommended.'],
              ['key' => 'favicon_image_id', 'label' => 'Favicon', 'hint' => 'Browser tab icon, ideally 32×32 or 64×64 px.'],
              ['key' => 'apple_touch_icon_image_id', 'label' => 'Apple touch icon', 'hint' => 'iOS home-screen icon, ideally 192×192 or 512×512 px.'],
              ['key' => 'og_image_id', 'label' => 'OG / social image', 'hint' => 'Shown when sharing links. 1200×630 px recommended.'],
            ];
        ?>
          <form method="post" enctype="multipart/form-data" class="space-y-5 max-w-3xl">
            <?= App\Csrf::field() ?>
            <input type="hidden" name="action" value="brand">
            <div class="grid sm:grid-cols-2 gap-4">
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Brand name</span>
                <input name="brand_name" value="<?= h(App\Settings::get('brand_name', 'Manami')) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none">
              </label>
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Tagline</span>
                <input name="brand_tagline" value="<?= h(App\Settings::get('brand_tagline', 'EDUCATION')) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none">
              </label>
            </div>
            <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Slogan</span>
              <input name="brand_slogan" value="<?= h(App\Settings::get('brand_slogan', 'Manami Leads, You Succeed...')) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none">
            </label>

            <div class="border-t border-gray-200 pt-5">
              <h3 class="text-sm font-semibold text-gray-800 mb-3">Brand images</h3>
              <div class="grid sm:grid-cols-2 gap-4">
                <?php foreach ($brandImages as $img):
                  $id = (int) App\Settings::get($img['key']);
                  $url = $id ? App\Content::mediaUrl($id) : '';
                  $fileKey = str_replace('_image_id', '', $img['key']);
                  if ($fileKey === 'apple_touch_icon_image_id') $fileKey = 'apple_touch_icon';
                  if ($fileKey === 'logo_image_id') $fileKey = 'logo';
                ?>
                  <label class="block">
                    <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide"><?= h($img['label']) ?></span>
                    <input type="file" name="<?= h($fileKey) ?>" accept="image/*" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <p class="text-[11px] text-gray-500 mt-1"><?= h($img['hint']) ?></p>
                    <?php if ($url): ?>
                      <div class="mt-2 flex items-center gap-2">
                        <img src="<?= h($url) ?>" alt="" class="h-10 w-auto border border-gray-200 rounded">
                        <a href="<?= h($url) ?>" target="_blank" class="text-xs text-brand hover:underline">View current</a>
                      </div>
                    <?php endif; ?>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
            <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">Save</button>
          </form>

        <?php elseif ($tab === 'nav' && App\Auth::hasRole('admin')):
            $navJson = App\Settings::get('nav_json');
            $navPretty = $navJson ? json_encode(json_decode($navJson, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : json_encode(App\DataAdapter::site()['nav'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        ?>
          <form method="post" class="space-y-4 max-w-4xl">
            <?= App\Csrf::field() ?>
            <input type="hidden" name="action" value="nav">
            <p class="text-sm text-gray-500">Edit the navigation menu as JSON. Top-level items may have <code class="text-xs">children</code> for dropdowns. Inside children, set <code class="text-xs">"class":"section"</code> to create a column heading for mega-menus.</p>
            <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Navigation JSON</span>
              <textarea name="nav_json" rows="18" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-3 text-sm font-mono focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none"><?= h($navPretty) ?></textarea>
            </label>
            <div class="text-xs text-gray-500">
              <p>Example item: <code class="text-xs">{"key":"home","href":"index.php"}</code></p>
              <p>Example dropdown: <code class="text-xs">{"key":"study_abroad","children":[{"key":"student_visa","href":"services.php#student-visa"}]}</code></p>
            </div>
            <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">Save navigation</button>
          </form>

        <?php elseif ($tab === 'contact' && App\Auth::hasRole('admin')): ?>
          <form method="post" class="space-y-4 max-w-2xl">
            <?= App\Csrf::field() ?>
            <input type="hidden" name="action" value="contact">
            <div class="grid sm:grid-cols-2 gap-4">
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Phone (display)</span>
                <input name="contact_phone" value="<?= h(App\Settings::get('contact_phone', '')) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none">
              </label>
              <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Phone (tel: link, digits only)</span>
                <input name="contact_phone_tel" value="<?= h(App\Settings::get('contact_phone_tel', '')) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none">
              </label>
            </div>
            <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Email</span>
              <input type="email" name="contact_email" value="<?= h(App\Settings::get('contact_email', '')) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none">
            </label>
            <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Address</span>
              <textarea name="contact_address" rows="2" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none"><?= h(App\Settings::get('contact_address', '')) ?></textarea>
            </label>
            <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Maps link</span>
              <input name="contact_address_link" value="<?= h(App\Settings::get('contact_address_link', '')) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none">
            </label>
            <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">Save</button>
          </form>

        <?php elseif ($tab === 'social' && App\Auth::hasRole('admin')): ?>
          <form method="post" class="space-y-3 max-w-3xl">
            <?= App\Csrf::field() ?>
            <input type="hidden" name="action" value="social">
            <p class="text-sm text-gray-500">Add up to 6 social links. <code class="text-xs">icon</code> must match a key in <code>partials/_icons.php</code>.</p>
            <?php for ($i = 0; $i < 6; $i++):
              $s = $social[$i] ?? ['label' => '', 'href' => '', 'icon' => 'globe']; ?>
              <div class="grid grid-cols-12 gap-2">
                <input class="col-span-4 border border-gray-300 rounded-md px-3 py-2 text-sm" name="social_label_<?= $i ?>" value="<?= h($s['label']) ?>" placeholder="Label (e.g. Facebook)">
                <input class="col-span-6 border border-gray-300 rounded-md px-3 py-2 text-sm" name="social_href_<?= $i ?>"  value="<?= h($s['href'])  ?>" placeholder="https://…">
                <input class="col-span-2 border border-gray-300 rounded-md px-3 py-2 text-sm" name="social_icon_<?= $i ?>"  value="<?= h($s['icon'])  ?>" placeholder="icon">
              </div>
            <?php endfor; ?>
            <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">Save</button>
          </form>

        <?php elseif ($tab === 'stats' && App\Auth::hasRole('admin')): ?>
          <form method="post" class="space-y-3 max-w-2xl">
            <?= App\Csrf::field() ?>
            <input type="hidden" name="action" value="stats">
            <p class="text-sm text-gray-500">Stats shown in the "By the numbers" section on the landing page.</p>
            <?php for ($i = 0; $i < 6; $i++):
              $s = $stats[$i] ?? ['label' => '', 'value' => '']; ?>
              <div class="grid grid-cols-12 gap-2">
                <input class="col-span-7 border border-gray-300 rounded-md px-3 py-2 text-sm" name="stat_label_<?= $i ?>" value="<?= h($s['label']) ?>" placeholder="Label">
                <input class="col-span-5 border border-gray-300 rounded-md px-3 py-2 text-sm" name="stat_value_<?= $i ?>" value="<?= h((string)$s['value']) ?>" placeholder="Value (e.g. 1200 or 99%)">
              </div>
            <?php endfor; ?>
            <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">Save</button>
          </form>

        <?php elseif ($tab === 'bkash' && App\Auth::hasRole('admin')): ?>
          <?php $cfg = require __DIR__ . '/../config/config.php'; $mode = $cfg['bkash']['mode']; ?>
          <form method="post" class="space-y-4 max-w-2xl">
            <?= App\Csrf::field() ?>
            <input type="hidden" name="action" value="bkash">
            <div class="bg-navy/5 border border-navy/20 rounded-md p-4 text-sm text-navy">
              <strong>Current mode:</strong> <?= h($mode) ?><br>
              <span class="text-xs">Manual = customer enters bKash TXN ID + optional screenshot; you verify in dashboard. API = automated via bKash Tokenized API (requires credentials).</span>
            </div>
            <label class="block">
              <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Mode</span>
              <select name="bkash_mode" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm bg-white focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none">
                <option value="manual" <?= $mode === 'manual' ? 'selected' : '' ?>>Manual (TXN ID + screenshot)</option>
                <option value="api"    <?= $mode === 'api'    ? 'selected' : '' ?>>bKash Tokenized API</option>
              </select>
            </label>
            <label class="block">
              <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Merchant bKash number (display)</span>
              <input name="bkash_merchant_number" value="<?= h(App\Settings::get('bkash_merchant_number', '+8801700000000')) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none">
            </label>
            <label class="block">
              <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Instructions shown at checkout</span>
              <textarea name="bkash_instructions" rows="3" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none"><?= h(App\Settings::get('bkash_instructions', 'Send the exact total to the merchant bKash number, then enter the Transaction ID below.')) ?></textarea>
            </label>
            <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">Save</button>
          </form>

        <?php elseif ($tab === 'pixel' && App\Auth::hasRole('admin')): ?>
          <form method="post" class="space-y-4 max-w-2xl">
            <?= App\Csrf::field() ?>
            <input type="hidden" name="action" value="pixel">
            <div class="bg-navy/5 border border-navy/20 rounded-md p-4 text-sm text-navy">
              <strong>Facebook/Meta Pixel</strong><br>
              <span class="text-xs">Enter your Pixel ID from Meta Events Manager. The pixel will fire on all pages with PageView, plus specific events on product views, cart actions, checkout, purchases, and lead submissions.</span>
            </div>
            <label class="flex items-center gap-2">
              <input type="checkbox" name="pixel_enabled" value="1" <?= App\Settings::get('pixel_enabled') === '1' ? 'checked' : '' ?> class="rounded border-gray-300 text-brand focus:ring-brand">
              <span class="text-sm font-semibold text-gray-700">Enable Pixel</span>
            </label>
            <label class="block">
              <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Pixel ID</span>
              <input name="pixel_id" value="<?= h(App\Settings::get('pixel_id', '')) ?>" placeholder="e.g. 123456789012345" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none">
            </label>
            <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">Save</button>
          </form>

        <?php elseif ($tab === 'payment' && App\Auth::hasRole('admin')): ?>
          <form method="post" class="space-y-6 max-w-2xl">
            <?= App\Csrf::field() ?>
            <input type="hidden" name="action" value="payment">

            <div>
              <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">SSLCommerz (Bangladesh)</h3>
              <div class="bg-navy/5 border border-navy/20 rounded-md p-3 text-xs text-navy mb-3">
                Aggregates bKash, Nagad, Rocket, Visa, Mastercard, and bank transfers. Requires merchant account at <a href="https://sslcommerz.com" target="_blank" class="underline">sslcommerz.com</a>.
              </div>
              <div class="grid sm:grid-cols-2 gap-4">
                <label class="block"><span class="text-xs text-gray-600">Store ID</span>
                  <input name="sslcommerz_store_id" value="<?= h(App\Settings::get('sslcommerz_store_id', '')) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                </label>
                <label class="block"><span class="text-xs text-gray-600">Store Password</span>
                  <input name="sslcommerz_store_pass" type="password" value="<?= h(App\Settings::get('sslcommerz_store_pass', '')) ?>" class="mt-1.5 w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                </label>
              </div>
              <label class="flex items-center gap-2 mt-3">
                <input type="checkbox" name="sslcommerz_sandbox" value="1" <?= App\Settings::get('sslcommerz_sandbox') === '1' ? 'checked' : '' ?> class="rounded border-gray-300 text-brand focus:ring-brand">
                <span class="text-xs text-gray-600">Sandbox mode (testing)</span>
              </label>
            </div>

            <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">Save payment settings</button>
          </form>

        <?php else: ?>
          <?php $first = !empty($_GET['first']); ?>
          <?php if ($first): ?>
            <div class="bg-accent/10 border border-accent/30 text-accent rounded-md p-4 mb-5 text-sm">
              For your security, please change the default password before continuing.
            </div>
          <?php endif; ?>
          <form method="post" class="space-y-4 max-w-md">
            <?= App\Csrf::field() ?>
            <input type="hidden" name="action" value="password">
            <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Current password</span>
              <input type="password" name="current" required class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none">
            </label>
            <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">New password</span>
              <input type="password" name="new" required minlength="8" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none">
            </label>
            <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Confirm new password</span>
              <input type="password" name="confirm" required minlength="8" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:border-brand focus:ring-2 focus:ring-brand/20 outline-none">
            </label>
            <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">Update password</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>
<?php require __DIR__ . '/_layout/footer.php'; ?>
