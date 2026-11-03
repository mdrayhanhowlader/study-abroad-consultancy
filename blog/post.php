<?php
require __DIR__ . '/../partials/_icons.php';
require __DIR__ . '/../app/bootstrap.php';
$site = App\DataAdapter::site();
$slug = preg_replace('/[^a-z0-9-]/', '', (string) ($_GET['slug'] ?? ''));
$post = $slug ? App\Database::fetchOne('SELECT * FROM posts WHERE slug=? AND status=\'published\' LIMIT 1', [$slug]) : null;
if (!$post) { http_response_code(404); }
$page_title = ($post ? $post['title'] : 'Post not found') . ' — ' . $site['brand']['full_name'];
$page_desc  = $post['excerpt'] ?? '';
$canonical  = $site['brand']['domain'] . '/blog/' . urlencode($slug);
$cover      = $post && $post['cover_id'] ? App\Content::mediaUrl((int) $post['cover_id']) : null;
$ts         = $post && $post['published_at'] ? strtotime($post['published_at']) : time();
$related    = $post ? App\Database::fetchAll(
    'SELECT id, slug, title, type, published_at FROM posts WHERE status=\'published\' AND id <> ? AND type=? ORDER BY published_at DESC LIMIT 3',
    [$post['id'], $post['type']]
) : [];
?>
<!doctype html>
<html lang="<?= h(current_lang()) ?>" dir="<?= h(\App\I18n::dir(current_lang())) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= h($page_title) ?></title>
  <meta name="description" content="<?= h($page_desc) ?>">
  <link rel="canonical" href="<?= h($canonical) ?>">
  <meta property="og:type" content="article">
  <meta property="og:title" content="<?= h($page_title) ?>">
  <meta property="og:description" content="<?= h($page_desc) ?>">
  <meta property="og:url" content="<?= h($canonical) ?>">
  <?php if ($cover): ?><meta property="og:image" content="<?= h($cover) ?>"><?php endif; ?>
  <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
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

  <main class="section">
    <div class="mx-auto max-w-3xl px-4 lg:px-6">
      <a href="<?= h(url('index.php#blog')) ?>" class="text-xs tracking-widest uppercase text-ink-3 hover:text-brand">← All articles</a>
      <?php if (!$post): ?>
        <h1 class="mt-4 font-display text-4xl font-bold text-ink">Post not found</h1>
      <?php else: ?>
        <div class="mt-6 flex items-center gap-3 text-[10px] tracking-widest text-ink-3 uppercase">
          <span class="px-2.5 py-1 bg-ink text-white"><?= h(ucfirst($post['type'])) ?></span>
          <span><?= date('F j, Y', $ts) ?></span>
          <span>·</span>
          <span>By Manami Education</span>
        </div>
        <h1 class="mt-4 font-display text-4xl sm:text-5xl font-bold text-ink leading-[1.05] tracking-tight"><?= h($post['title']) ?></h1>
        <p class="mt-5 font-display italic text-xl text-brand"><?= h($post['excerpt'] ?? '') ?></p>

        <?php if ($cover): ?>
          <div class="mt-10 aspect-[16/9] bg-paper-2 overflow-hidden" style="background:#F4F1EA">
            <img src="<?= h($cover) ?>" alt="<?= h($post['title']) ?>" class="w-full h-full object-cover">
          </div>
        <?php endif; ?>

        <article class="prose prose-sm max-w-none mt-10 text-ink-2 leading-relaxed">
          <?= $post['body'] ?? '<p>(No content yet.)</p>' ?>
        </article>

        <div class="mt-12 hairline pt-6" style="border-color:#E5E1D9">
          <div class="text-[10px] tracking-widest text-ink-3 uppercase">Share</div>
          <div class="mt-3 flex items-center gap-2">
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($canonical) ?>" target="_blank" rel="noopener" class="w-9 h-9 grid place-items-center border border-hair hover:border-ink" style="border-color:#E5E1D9"><?= icon('facebook', 'w-4 h-4') ?></a>
            <a href="https://twitter.com/intent/tweet?url=<?= urlencode($canonical) ?>&text=<?= urlencode($post['title']) ?>" target="_blank" rel="noopener" class="w-9 h-9 grid place-items-center border border-hair hover:border-ink" style="border-color:#E5E1D9"><?= icon('x', 'w-4 h-4') ?></a>
            <a href="https://wa.me/?text=<?= urlencode($post['title'] . ' ' . $canonical) ?>" target="_blank" rel="noopener" class="w-9 h-9 grid place-items-center border border-hair hover:border-ink" style="border-color:#E5E1D9">💬</a>
          </div>
        </div>

        <?php if ($related): ?>
          <div class="mt-14">
            <div class="eyebrow" data-num="·">More <?= h($post['type']) ?></div>
            <h2 class="mt-3 font-display text-2xl font-bold text-ink">Related reads</h2>
            <div class="mt-6 grid sm:grid-cols-3 gap-px bg-hair" style="background:#E5E1D9">
              <?php foreach ($related as $r):
                $rts = strtotime($r['published_at']);
              ?>
                <a href="<?= h(url('blog/' . $r['slug'])) ?>" class="group bg-paper-2 p-5 hover:bg-white transition" style="background:#F4F1EA">
                  <div class="text-[10px] tracking-widest text-ink-3 uppercase"><?= date('M d, Y', $rts) ?></div>
                  <h3 class="mt-2 font-display text-lg font-semibold text-ink leading-snug group-hover:text-brand"><?= h($r['title']) ?></h3>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </main>

  <?php require __DIR__ . '/../partials/pre-footer.php'; ?>
  <?php require __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
