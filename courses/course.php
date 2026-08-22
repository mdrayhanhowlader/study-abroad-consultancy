<?php
require __DIR__ . '/../partials/_icons.php';
require __DIR__ . '/../app/bootstrap.php';
$site = App\DataAdapter::site();
$slug = preg_replace('/[^a-z0-9-]/', '', (string) ($_GET['slug'] ?? ''));
$product = $slug ? App\Product::bySlug($slug) : null;
if (!$product || $product['type'] !== 'course') { http_response_code(404); }
$page_title = ($product ? $product['title'] : 'Course not found') . ' — ' . $site['brand']['full_name'];
$page_desc  = $product['summary'] ?? '';
$canonical  = $site['brand']['domain'] . '/courses/' . urlencode($slug);
$cover      = $product && $product['cover_id'] ? App\Content::mediaUrl((int) $product['cover_id']) : null;

// Synthetic course curriculum (in real use, would come from DB)
$curriculum = [
    ['week' => 'Week 1',  'title' => 'Foundations & orientation', 'topics' => ['Welcome & programme overview', 'Pre-flight checklist', 'Tools and resources', 'Setting up your Japan study desk']],
    ['week' => 'Week 2',  'title' => 'Japan essentials',         'topics' => ['Living costs & banking', 'SIM & connectivity', 'Accommodation 101', 'Bangladeshi community in Japan']],
    ['week' => 'Week 3',  'title' => 'Banking, jobs & lifestyle', 'topics' => ['Opening a bank account', 'Part-time work rules', 'JLPT roadmap', 'Cultural do\'s and don\'ts']],
    ['week' => 'Week 4',  'title' => '1:1 mentorship & Q&A',      'topics' => ['Personalised Q&A with Japan-based mentor', 'Emergency contacts', 'Next steps after landing']],
];
$instructor = [
    'name'  => 'Marina Tanaka',
    'role'  => 'Tokyo-based Mentor · 8 years in Japan',
    'photo' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&auto=format&fit=crop&q=70',
];
?>
<!doctype html>
<html lang="<?= h(current_lang()) ?>" dir="<?= h(\App\I18n::dir(current_lang())) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= h($page_title) ?></title>
  <meta name="description" content="<?= h($page_desc) ?>">
  <link rel="canonical" href="<?= h($canonical) ?>">
  <meta property="og:type" content="product">
  <meta property="og:title" content="<?= h($page_title) ?>">
  <meta property="og:description" content="<?= h($page_desc) ?>">
  <meta property="og:url" content="<?= h($canonical) ?>">
  <?php if ($cover): ?><meta property="og:image" content="<?= h($cover) ?>"><?php endif; ?>
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <script>
    tailwind.config = { theme: { extend: {
      colors: { paper:'#FAF8F4','paper-2':'#F4F1EA',ink:{DEFAULT:'#1E242B',2:'#4A5159',3:'#8A857C'},hair:{DEFAULT:'#E5E1D9',2:'#D7D2C7'},brand:{DEFAULT:'#006837',dark:'#004d28',light:'#cce8d6'},navy:{DEFAULT:'#1A2B6D',dark:'#101f4f',light:'#d6dcec'},accent:{DEFAULT:'#E31E24',dark:'#a8131a',light:'#f7c8ca'},gold:'#B8923B' },
      fontFamily: { display:['"Playfair Display"','Georgia','serif'], sans:['Inter','ui-sans-serif','system-ui','sans-serif'] }
    }}}
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,500;1,600;1,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= h(url('assets/css/style.css')) ?>">
  <?php require __DIR__ . '/../partials/favicon.php'; ?>
  <script defer src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js"></script>
</head>
<body class="font-sans text-ink antialiased bg-paper">
  <?php require __DIR__ . '/../partials/topbar.php'; ?>
  <?php require __DIR__ . '/../partials/header.php'; ?>

  <main>
    <section class="relative overflow-hidden" style="background: linear-gradient(180deg, #1E242B 0%, #1A2B6D 100%);">
      <div class="bg-orb" style="width:500px;height:500px;background:#006837;top:-100px;right:-100px;opacity:0.18"></div>
      <div class="mx-auto max-w-7xl px-4 lg:px-6 py-16 lg:py-24 relative">
        <a href="<?= h(url('books.php')) ?>?type=course" class="text-xs tracking-widest uppercase text-white/70 hover:text-gold">← All courses</a>
        <?php if (!$product): ?>
          <h1 class="mt-4 font-display text-4xl font-bold text-white">Course not found</h1>
        <?php else: ?>
          <div class="mt-6 grid lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-7">
              <div class="eyebrow" data-num="·" style="color:#B8923B">Live Course</div>
              <h1 class="mt-4 font-display text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-[1.05] tracking-tight"><?= h($product['title']) ?></h1>
              <p class="mt-5 text-white/80 text-lg max-w-2xl"><?= h($product['summary'] ?? '') ?></p>
              <div class="mt-6 flex flex-wrap items-center gap-3">
                <a href="#enrol" class="btn btn-primary">Enrol now — <?= format_bdt((float) $product['price_bdt']) ?> →</a>
                <a href="#curriculum" class="btn btn-ghost text-white border border-white/30">View curriculum</a>
              </div>
            </div>
            <div class="lg:col-span-5">
              <div class="aspect-[4/3] bg-white/5 backdrop-blur border border-white/10 rounded-sm overflow-hidden">
                <?php if ($cover): ?>
                  <img src="<?= h($cover) ?>" alt="<?= h($product['title']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                  <div class="w-full h-full grid place-items-center text-white/40">
                    <svg class="w-20 h-20" viewBox="0 0 24 24" fill="currentColor"><path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1L15 22v-1.5L13 19v-5.5l8 2.5z"/></svg>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <?php if ($product): ?>
    <section id="curriculum" class="section bg-paper">
      <div class="mx-auto max-w-7xl px-4 lg:px-6 grid lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2">
          <div class="eyebrow" data-num="01">Curriculum</div>
          <h2 class="mt-4 font-display text-3xl sm:text-4xl font-bold text-ink leading-tight">What you'll learn</h2>
          <p class="mt-3 text-ink-2"><?= h($product['description'] ?? '4 weeks of live online orientation led by Japan-based mentors.') ?></p>

          <ol class="mt-10 space-y-px bg-hair" style="background:#E5E1D9">
            <?php foreach ($curriculum as $i => $week):
              $num = str_pad((string)($i+1), 2, '0', STR_PAD_LEFT);
            ?>
              <li class="bg-paper p-6 sm:p-7 grid sm:grid-cols-12 gap-4" style="background:#FAF8F4">
                <div class="sm:col-span-2">
                  <div class="text-[10px] tracking-widest text-accent uppercase font-semibold" style="color:#E31E24"><?= h($week['week']) ?></div>
                  <div class="font-display text-3xl font-bold text-ink"><?= $num ?></div>
                </div>
                <div class="sm:col-span-10">
                  <h3 class="font-display text-xl font-bold text-ink"><?= h($week['title']) ?></h3>
                  <ul class="mt-3 grid sm:grid-cols-2 gap-2">
                    <?php foreach ($week['topics'] as $topic): ?>
                      <li class="flex items-start gap-2 text-sm text-ink-2">
                        <span class="text-brand mt-1" style="color:#006837">✓</span>
                        <span><?= h($topic) ?></span>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </li>
            <?php endforeach; ?>
          </ol>
        </div>

        <aside class="lg:col-span-1">
          <div id="enrol" class="bg-white border border-hair p-7 sticky top-24" style="border-color:#E5E1D9">
            <div class="text-[10px] tracking-widest text-ink-3 uppercase">Course fee</div>
            <div class="font-display text-4xl font-bold text-ink mt-1"><?= format_bdt((float) $product['price_bdt']) ?></div>
            <div class="mt-1 text-xs text-ink-3">One-time payment · Lifetime access</div>

            <ul class="mt-5 space-y-2 text-sm text-ink-2 hairline pt-5" style="border-color:#E5E1D9">
              <li class="flex items-start gap-2"><span class="text-brand" style="color:#006837">✓</span> 4 weeks of live online sessions</li>
              <li class="flex items-start gap-2"><span class="text-brand" style="color:#006837">✓</span> 1:1 mentorship with a Japan-based mentor</li>
              <li class="flex items-start gap-2"><span class="text-brand" style="color:#006837">✓</span> All materials and recordings</li>
              <li class="flex items-start gap-2"><span class="text-brand" style="color:#006837">✓</span> Certificate of completion</li>
            </ul>

            <button type="button" data-add-to-cart="<?= (int) $product['id'] ?>" class="mt-6 w-full btn btn-primary justify-center">Add to cart →</button>
            <a href="<?= h(url('books.php#checkout')) ?>" class="mt-2 w-full btn btn-secondary justify-center">Or go to checkout</a>
            <p class="mt-4 text-[10px] text-ink-3 text-center">Payment via bKash · Manual verification · 7-day refund</p>
          </div>
        </aside>
      </div>
    </section>

    <section class="section bg-paper-2" style="background:#F4F1EA">
      <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="eyebrow" data-num="02">Your instructor</div>
        <h2 class="mt-4 font-display text-3xl sm:text-4xl font-bold text-ink">Meet your mentor</h2>
        <div class="mt-10 bg-white border border-hair p-7 sm:p-10 grid sm:grid-cols-12 gap-6 items-center" style="border-color:#E5E1D9">
          <div class="sm:col-span-3">
            <div class="aspect-square overflow-hidden rounded-sm">
              <img src="<?= h($instructor['photo']) ?>" alt="<?= h($instructor['name']) ?>" class="w-full h-full object-cover">
            </div>
          </div>
          <div class="sm:col-span-9">
            <div class="font-display italic text-xs text-gold" style="color:#B8923B">Instructor</div>
            <h3 class="font-display text-2xl font-bold text-ink mt-1"><?= h($instructor['name']) ?></h3>
            <p class="text-ink-3 text-xs tracking-widest uppercase mt-1"><?= h($instructor['role']) ?></p>
            <p class="mt-4 text-ink-2 leading-relaxed">Marina has supported 200+ Bangladeshi students through their first 90 days in Japan — from opening bank accounts to landing part-time work and navigating JLPT prep.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="section bg-ink text-white" style="background:#1E242B">
      <div class="mx-auto max-w-4xl px-4 lg:px-6 text-center">
        <h2 class="font-display text-3xl sm:text-4xl font-bold text-white">Ready to enrol?</h2>
        <p class="mt-4 text-white/70">Pay via bKash — we'll confirm your seat within 24 hours.</p>
        <a href="#enrol" class="mt-8 btn btn-primary inline-flex">Enrol now — <?= format_bdt((float) $product['price_bdt']) ?> →</a>
      </div>
    </section>
    <?php endif; ?>

    <?php require __DIR__ . '/../partials/pre-footer.php'; ?>
  </main>

  <?php require __DIR__ . '/../partials/footer.php'; ?>

  <script>
  (function () {
    document.querySelectorAll('[data-add-to-cart]').forEach(btn => {
      btn.addEventListener('click', () => {
        fetch('../api/cart.php?action=add&id=' + btn.dataset.addToCart + '&qty=1').then(() => {
          window.location.href = '../books.php#checkout';
        });
      });
    });
  })();
  </script>
</body>
</html>
