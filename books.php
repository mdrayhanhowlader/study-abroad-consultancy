<?php
require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';

$site         = App\DataAdapter::site();
$products     = App\Product::all();
$typeFilter   = $_GET['type'] ?? '';
if (in_array($typeFilter, ['physical','digital','course'], true)) {
    $products = array_values(array_filter($products, fn($p) => $p['type'] === $typeFilter));
}

$page_title = t('books.eyebrow') . ' — ' . $site['brand']['full_name'];
$page_desc  = t('books.body');
$canonical  = $site['brand']['domain'] . '/books.php';

$org_jsonld = [
    '@context' => 'https://schema.org',
    '@type'    => 'CollectionPage',
    'name'     => $page_title,
    'description' => $page_desc,
    'url'      => $canonical,
    'isPartOf' => ['@type' => 'WebSite', 'name' => $site['brand']['full_name'], 'url' => $site['brand']['domain']],
    'provider' => ['@type' => 'Organization', 'name' => $site['brand']['full_name']],
];

$bkashCfg = require __DIR__ . '/config/config.php';
$bkashInstructions = App\Settings::get('bkash_instructions', $bkashCfg['bkash']['instructions']);
$bkashMerchant    = App\Settings::get('bkash_merchant_number', $bkashCfg['bkash']['merchant_number']);
?>
<!doctype html>
<html lang="<?= h(current_lang()) ?>" dir="<?= h(\App\I18n::dir(current_lang())) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= h($page_title) ?></title>
  <meta name="description" content="<?= h($page_desc) ?>">
  <meta name="theme-color" content="#006837">
  <link rel="canonical" href="<?= h($canonical) ?>">

  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= h($page_title) ?>">
  <meta property="og:description" content="<?= h($page_desc) ?>">
  <meta property="og:url" content="<?= h($canonical) ?>">
  <meta property="og:image" content="<?= h($site['brand']['og_image']) ?>">

  <script type="application/ld+json"><?= json_encode($org_jsonld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>

  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <script>
    tailwind.config = {
      theme: { extend: {
        colors: {
          paper:    '#FAF8F4', 'paper-2': '#F4F1EA',
          ink:      { DEFAULT: '#1E242B', 2: '#4A5159', 3: '#8A857C' },
          hair:     { DEFAULT: '#E5E1D9', 2: '#D7D2C7' },
          brand:    { DEFAULT: '#006837', dark: '#004d28', light: '#cce8d6' },
          navy:     { DEFAULT: '#1A2B6D', dark: '#101f4f', light: '#d6dcec' },
          accent:   { DEFAULT: '#E31E24', dark: '#a8131a', light: '#f7c8ca' },
          gold:     '#B8923B',
        },
        fontFamily: { display: ['"Playfair Display"','Georgia','serif'], sans: ['Inter','ui-sans-serif','system-ui','sans-serif'] }
      }}
    }
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,500;1,600;1,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/flags.css">
  <link rel="stylesheet" href="assets/css/style.css">

  <!-- PWA -->
  <link rel="manifest" href="manifest.json">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Manami Education">
  <?php require __DIR__ . '/partials/favicon.php'; ?>

  <script defer src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js"></script>
  <script defer src="assets/js/main.js"></script>
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => navigator.serviceWorker.register('./sw.js'));
    }
  </script>
</head>
<body class="font-sans text-ink antialiased bg-paper">

  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <?php require __DIR__ . '/partials/header.php'; ?>

  <main>
    <section class="relative overflow-hidden" style="background: linear-gradient(180deg, #1E242B 0%, #1A2B6D 100%);">
      <div class="bg-orb" style="width:500px;height:500px;background:#006837;top:-100px;right:-100px;opacity:0.2"></div>
      <div class="mx-auto max-w-7xl px-4 lg:px-6 py-20 lg:py-32 relative">
        <div class="max-w-3xl">
          <div class="eyebrow" data-num="06" style="color:#B8923B"><?= h(t('books.eyebrow')) ?></div>
          <h1 class="mt-5 font-display text-5xl sm:text-6xl lg:text-7xl font-bold leading-[0.95] text-white tracking-tight"><?= h(t('books.headline')) ?></h1>
          <p class="mt-6 text-white/80 text-lg max-w-2xl"><?= h(t('books.body')) ?></p>
          <div class="mt-8 flex flex-wrap gap-3">
            <a href="#products" class="btn btn-primary"><?= h(t('books.cta_browse')) ?> →</a>
            <a href="index.php#contact" class="btn btn-ghost text-white border border-white/30 hover:bg-white/10"><?= h(t('books.cta_talk')) ?></a>
          </div>
        </div>
      </div>
    </section>

    <section id="products" class="section bg-paper">
      <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-12">
          <div>
            <div class="eyebrow" data-num="07"><?= h(t('books.all_products')) ?></div>
            <p class="mt-3 text-sm text-ink-3"><?= h(t('books.all_products_sub')) ?></p>
          </div>
          <div class="flex items-center gap-1 text-sm flex-wrap">
            <a href="books.php" class="px-3 py-1.5 rounded-sm border <?= !$typeFilter ? 'bg-ink text-white border-ink' : 'bg-white border-hair text-ink-2 hover:border-ink' ?>" style="<?= !$typeFilter ? '' : 'border-color:#E5E1D9' ?>"><?= h(t('books.filter_all')) ?></a>
            <a href="?type=physical" class="px-3 py-1.5 rounded-sm border <?= $typeFilter==='physical' ? 'bg-ink text-white border-ink' : 'bg-white border-hair text-ink-2 hover:border-ink' ?>" style="border-color:#E5E1D9"><?= h(t('books.filter_physical')) ?></a>
            <a href="?type=digital"  class="px-3 py-1.5 rounded-sm border <?= $typeFilter==='digital'  ? 'bg-ink text-white border-ink' : 'bg-white border-hair text-ink-2 hover:border-ink' ?>" style="border-color:#E5E1D9"><?= h(t('books.filter_digital')) ?></a>
            <a href="?type=course"   class="px-3 py-1.5 rounded-sm border <?= $typeFilter==='course'   ? 'bg-ink text-white border-ink' : 'bg-white border-hair text-ink-2 hover:border-ink' ?>" style="border-color:#E5E1D9"><?= h(t('books.filter_course')) ?></a>
          </div>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-px bg-hair" style="background:#E5E1D9">
          <?php if (!$products): ?>
            <div class="sm:col-span-2 lg:col-span-3 bg-paper text-center text-ink-3 text-sm py-12"><?= h(t('books.no_products')) ?></div>
          <?php else: foreach ($products as $i => $p):
            $cover = $p['cover_id'] ? App\Content::mediaUrl((int) $p['cover_id']) : null;
            $typeKey = 'type_' . $p['type'];
          ?>
            <article class="bg-paper p-6 flex flex-col group hover:bg-white transition reveal">
                <a href="<?= h(url('books/' . $p['slug'])) ?>" class="block aspect-[4/3] bg-paper-2 mb-5 relative overflow-hidden" style="background:#F4F1EA">
                <?php if ($cover): ?>
                  <img src="<?= h($cover) ?>" alt="<?= h($p['title']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                <?php else: ?>
                  <div class="absolute inset-0 grid place-items-center text-ink-3/30">
                    <svg class="w-20 h-20" viewBox="0 0 24 24" fill="currentColor"><path d="M4 3h12a3 3 0 0 1 3 3v15a1 1 0 0 1-1 1H7a3 3 0 0 1-3-3V3zm12 2H6v13a1 1 0 0 0 1 1h11V5zM8 7h7v2H8V7zm0 4h7v2H8v-2z"/></svg>
                  </div>
                <?php endif; ?>
                <span class="absolute top-3 left-3 text-[9px] font-bold tracking-widest uppercase px-2.5 py-1 bg-white text-ink"><?= h(t('books.' . $typeKey)) ?></span>
              </a>
              <div class="flex items-center gap-2">
                <span class="font-display italic text-xs text-gold" style="color:#B8923B"><?= str_pad((string)($i+1), 2, '0', STR_PAD_LEFT) ?>.</span>
                <h3 class="font-display text-xl font-semibold text-ink leading-snug flex-1"><a href="<?= h(url('books/' . $p['slug'])) ?>" class="hover:text-brand"><?= h($p['title']) ?></a></h3>
              </div>
              <p class="mt-2 text-sm text-ink-2 flex-1"><?= h($p['summary'] ?? '') ?></p>
              <div class="mt-5 flex items-end justify-between">
                <div>
                  <div class="font-display text-2xl font-bold text-ink"><?= format_bdt((float) $p['price_bdt']) ?></div>
                  <?php if ($p['type'] === 'physical'): ?><div class="text-[10px] text-ink-3 mt-1"><?= h(t('common.in_stock', ['n' => (int) $p['stock']])) ?></div><?php endif; ?>
                </div>
                <button type="button" data-add-to-cart="<?= (int) $p['id'] ?>" class="btn btn-primary text-xs"><?= h(t('common.add_to_cart')) ?> →</button>
              </div>
            </article>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </section>

    <section id="checkout" class="section bg-paper-2" style="background:#F4F1EA">
      <div class="mx-auto max-w-5xl px-4 lg:px-6">
        <div class="text-center max-w-2xl mx-auto mb-10">
          <div class="eyebrow justify-center" data-num="08"><?= h(t('books.checkout_eyebrow')) ?></div>
          <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-tight text-ink"><?= h(t('books.checkout_headline')) ?></h2>
          <p class="mt-4 text-ink-2"><?= h(t('books.checkout_body')) ?></p>
        </div>

        <div class="grid lg:grid-cols-5 gap-8">
          <div class="lg:col-span-3">
            <form id="orderForm" method="post" enctype="multipart/form-data" action="api/order.php" x-data="{ paymentMethod: 'bkash' }" class="bg-white border border-hair p-7 sm:p-10 space-y-5" style="border-color:#E5E1D9">
              <?= App\Csrf::field() ?>
              <div class="grid sm:grid-cols-2 gap-4">
                <label class="block">
                  <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('books.full_name')) ?> *</span>
                  <input name="name" required class="input mt-2">
                </label>
                <label class="block">
                  <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('books.phone')) ?> *</span>
                  <input name="phone" required type="tel" class="input mt-2">
                </label>
              </div>
              <label class="block">
                <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('books.address')) ?> *</span>
                <input name="address" required placeholder="<?= h(t('books.address_ph')) ?>" class="input mt-2">
              </label>
              <label class="block">
                <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('books.thana')) ?> *</span>
                <input name="thana" required placeholder="<?= h(t('books.thana_ph')) ?>" class="input mt-2">
              </label>
              <label class="block">
                <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('books.email_label')) ?></span>
                <input name="email" type="email" class="input mt-2">
              </label>

              <div class="hairline pt-5 mt-5" style="border-color:#E5E1D9">
                <h3 class="font-display text-lg font-bold text-ink"><?= h(t('books.payment_method')) ?></h3>

                <div class="grid sm:grid-cols-2 gap-3 mt-3">
                  <label class="relative flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition hover:border-brand has-[:checked]:border-brand has-[:checked]:bg-brand/5" style="border-color:#E5E1D9">
                    <input type="radio" name="payment_method" value="bkash" checked class="mt-1 accent-brand" @change="paymentMethod = 'bkash'">
                    <div class="flex-1">
                      <div class="font-semibold text-ink text-sm">bKash</div>
                      <div class="text-xs text-ink-2 mt-0.5">Pay now via bKash</div>
                    </div>
                    <span class="text-xl">📱</span>
                  </label>
                  <label class="relative flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition hover:border-brand has-[:checked]:border-brand has-[:checked]:bg-brand/5" style="border-color:#E5E1D9">
                    <input type="radio" name="payment_method" value="cod" class="mt-1 accent-brand" @change="paymentMethod = 'cod'">
                    <div class="flex-1">
                      <div class="font-semibold text-ink text-sm">Cash on Delivery</div>
                      <div class="text-xs text-ink-2 mt-0.5">Pay when you receive</div>
                    </div>
                    <span class="text-xl">💵</span>
                  </label>
                </div>

                <div x-show="paymentMethod === 'bkash'" x-transition class="mt-5">
                  <div class="bg-gold/5 border border-gold/30 rounded-xl p-4 text-sm text-ink" style="background:rgba(184,146,59,0.05);border-color:rgba(184,146,59,0.3)">
                    <div class="font-semibold text-ink mb-1"><?= h(t('books.bkash_send')) ?> <span id="checkoutTotal" class="text-accent font-display">৳0</span> <?= h(t('books.bkash_to')) ?></div>
                    <div class="font-mono text-lg"><?= h($bkashMerchant) ?></div>
                    <p class="mt-2 text-xs text-ink-2"><?= h($bkashInstructions) ?></p>
                  </div>
                  <div class="grid sm:grid-cols-2 gap-4 mt-4">
                    <label class="block">
                      <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('books.bkash_txn')) ?> *</span>
                      <input name="bkash_txn" :required="paymentMethod === 'bkash'" placeholder="<?= h(t('books.bkash_txn_ph')) ?>" class="input mt-2 font-mono">
                    </label>
                    <label class="block">
                      <span class="text-[10px] font-semibold text-ink-2 uppercase tracking-widest"><?= h(t('books.bkash_screenshot')) ?></span>
                      <input name="bkash_screenshot" type="file" accept="image/*" class="input mt-2 text-sm">
                    </label>
                  </div>
                </div>

                <div x-show="paymentMethod === 'cod'" x-transition class="mt-5 bg-navy/5 border border-navy/20 rounded-xl p-4 text-sm text-ink" style="background:rgba(26,43,109,0.05);border-color:rgba(26,43,109,0.2)">
                  <p>You have selected <strong>Cash on Delivery</strong>. Our team will call you to confirm the order before shipping. Please keep the exact amount ready.</p>
                </div>
              </div>

              <button type="submit" id="placeOrderBtn" disabled class="w-full btn btn-primary justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="placeOrderLabel"><?= h(t('books.place_order')) ?></span>
              </button>
              <p class="text-xs text-ink-3 text-center" id="cartEmptyMsg" style="display:none"><?= h(t('common.add_to_continue')) ?></p>
            </form>
          </div>

          <aside class="lg:col-span-2">
            <div class="bg-white border border-hair p-7 sticky top-24" style="border-color:#E5E1D9">
              <h3 class="font-display text-xl font-bold text-ink"><?= h(t('books.your_cart')) ?></h3>
              <p class="text-[10px] tracking-widest text-ink-3 uppercase mt-1"><?= h(t('books.free_shipping')) ?></p>
              <div id="cartItems" class="mt-5 divide-y divide-hair" style="--tw-divide-opacity:1">
                <div class="text-sm text-ink-3 py-4 text-center"><?= h(t('common.cart_empty')) ?></div>
              </div>
              <dl class="mt-5 space-y-2 text-sm hairline pt-4" style="border-color:#E5E1D9">
                <div class="flex justify-between"><dt class="text-ink-3"><?= h(t('common.subtotal')) ?></dt><dd id="cartSubtotal" class="font-semibold">৳0</dd></div>
                <div class="flex justify-between"><dt class="text-ink-3"><?= h(t('common.shipping')) ?></dt><dd id="cartShipping" class="font-semibold">৳0</dd></div>
                <div class="flex justify-between text-base pt-2 hairline mt-2" style="border-color:#E5E1D9"><dt class="font-bold text-ink"><?= h(t('common.total')) ?></dt><dd id="cartTotal" class="font-bold text-brand">৳0</dd></div>
              </dl>
              <button type="button" id="clearCartBtn" class="mt-3 text-xs text-ink-3 hover:text-accent" style="display:none"><?= h(t('common.clear_cart')) ?></button>
            </div>
          </aside>
        </div>
      </div>
    </section>

    <?php require __DIR__ . '/partials/pre-footer.php'; ?>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>

  <script>
  (function () {
    const itemsEl  = document.getElementById('cartItems');
    const subtotalEl = document.getElementById('cartSubtotal');
    const shippingEl = document.getElementById('cartShipping');
    const totalEl   = document.getElementById('cartTotal');
    const checkoutTotalEl = document.getElementById('checkoutTotal');
    const placeBtn  = document.getElementById('placeOrderBtn');
    const placeLabel = document.getElementById('placeOrderLabel');
    const emptyMsg  = document.getElementById('cartEmptyMsg');
    const clearBtn  = document.getElementById('clearCartBtn');

    const L = {
        add:        <?= json_encode(t('common.add_to_cart')) ?>,
        added:      <?= json_encode(t('common.added')) ?>,
        clear:      <?= json_encode(t('common.clear_cart')) ?>,
        placing:    <?= json_encode(t('books.placing_order')) ?>,
        place:      <?= json_encode(t('books.place_order')) ?>,
        empty:      <?= json_encode(t('common.cart_empty')) ?>,
        continue:   <?= json_encode(t('common.add_to_continue')) ?>,
        emptyStock: <?= json_encode(t('blog.empty')) ?>,
    };

    const bdt = (n) => '৳' + Number(n).toLocaleString('en-IN');
    function escapeHtml(s) { return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

    function render(cart) {
      if (!cart.items || cart.items.length === 0) {
        itemsEl.innerHTML = '<div class="text-sm text-ink-3 py-4 text-center">' + L.empty + '</div>';
        placeBtn.disabled = true;
        emptyMsg.style.display = 'block';
        emptyMsg.textContent = L.continue;
        clearBtn.style.display = 'none';
      } else {
        itemsEl.innerHTML = cart.items.map(i => `
          <div class="py-3 flex items-start gap-3">
            <div class="flex-1">
              <div class="text-sm font-semibold text-ink">${escapeHtml(i.title)}</div>
              <div class="text-xs text-ink-3">${bdt(i.unit_price)} × ${i.qty}</div>
            </div>
            <div class="text-sm font-bold text-brand">${bdt(i.line_total)}</div>
            <button type="button" data-remove="${i.id}" class="text-ink-3 hover:text-accent" aria-label="Remove">✕</button>
          </div>`).join('');
        placeBtn.disabled = false;
        emptyMsg.style.display = 'none';
        clearBtn.style.display = 'inline';
        itemsEl.querySelectorAll('[data-remove]').forEach(b => {
          b.addEventListener('click', () => fetch('api/cart.php?action=remove&id=' + b.dataset.remove).then(refresh));
        });
      }
      subtotalEl.textContent = bdt(cart.subtotal);
      shippingEl.textContent = bdt(cart.shipping);
      totalEl.textContent    = bdt(cart.total);
      checkoutTotalEl.textContent = bdt(cart.total);
    }

    function refresh() {
      fetch('api/cart.php').then(r => r.json()).then(render);
    }

    const checkoutSection = document.getElementById('checkout');
    const cartBox = document.querySelector('aside .bg-white.border');

    function showToast(message) {
      let toast = document.getElementById('cartToast');
      if (!toast) {
        toast = document.createElement('div');
        toast.id = 'cartToast';
        toast.className = 'fixed top-24 right-5 z-50 bg-ink text-white text-sm font-medium px-4 py-3 rounded-xl shadow-2xl flex items-center gap-2 transition-all duration-300 translate-x-full opacity-0';
        toast.innerHTML = '<span class="text-brand">✓</span> <span id="cartToastMsg"></span>';
        document.body.appendChild(toast);
      }
      document.getElementById('cartToastMsg').textContent = message;
      toast.classList.remove('translate-x-full', 'opacity-0');
      setTimeout(() => toast.classList.add('translate-x-full', 'opacity-0'), 2500);
    }

    function highlightCart() {
      if (cartBox) {
        cartBox.classList.add('ring-2', 'ring-brand', 'ring-offset-2');
        setTimeout(() => cartBox.classList.remove('ring-2', 'ring-brand', 'ring-offset-2'), 1200);
      }
    }

    document.querySelectorAll('[data-add-to-cart]').forEach(btn => {
      btn.addEventListener('click', () => {
        fetch('api/cart.php?action=add&id=' + btn.dataset.addToCart + '&qty=1')
          .then(r => r.json())
          .then(cart => {
            render(cart);
            showToast(L.added);
            highlightCart();
            if (checkoutSection) {
              checkoutSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
          });
        if (typeof fbq === 'function') fbq('track', 'AddToCart');
        const orig = btn.innerHTML;
        btn.innerHTML = '<span class="flex items-center gap-1">Added <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></span>';
        btn.disabled = true;
        setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; }, 1500);
      });
    });

    if (clearBtn) clearBtn.addEventListener('click', () => fetch('api/cart.php?action=clear').then(refresh));

    document.getElementById('orderForm').addEventListener('submit', function (e) {
      if (placeBtn.disabled) { e.preventDefault(); return; }
      placeBtn.disabled = true;
      placeLabel.textContent = L.placing;
    });

    refresh();
  })();
  </script>

  <?php if (App\Pixel::isEnabled()): ?>
    <script>fbq('track', 'ViewContent', {content_type: 'product_list'});</script>
  <?php endif; ?>
</body>
</html>
