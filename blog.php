<?php
require __DIR__ . '/partials/_icons.php';
require __DIR__ . '/app/bootstrap.php';
$site = App\DataAdapter::site();

$filter = $_GET['type'] ?? '';
if (!in_array($filter, ['blog', 'news'], true)) $filter = '';

$sql = 'SELECT * FROM posts WHERE status=\'published\' AND published_at <= NOW()';
$params = [];
if ($filter) { $sql .= ' AND type=?'; $params[] = $filter; }
$sql .= ' ORDER BY published_at DESC LIMIT 100';
$posts = App\Database::fetchAll($sql, $params);

$page_title = t('blog_page.headline') . ' — ' . $site['brand']['full_name'];
$page_desc  = t('blog_page.body');
$canonical  = $site['brand']['domain'] . '/blog.php';
?>
<!doctype html>
<html lang="<?= h(current_lang()) ?>" dir="<?= h(\App\I18n::dir(current_lang())) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= h($page_title) ?></title>
  <meta name="description" content="<?= h($page_desc) ?>">
  <link rel="canonical" href="<?= h($canonical) ?>">
  <meta property="og:title" content="<?= h($page_title) ?>">
  <meta property="og:description" content="<?= h($page_desc) ?>">
  <meta property="og:url" content="<?= h($canonical) ?>">
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <script>
    tailwind.config = { theme: { extend: {
      colors: { paper:'#FAF8F4','paper-2':'#F4F1EA',ink:{DEFAULT:'#1E242B',2:'#4A5159',3:'#8A857C'},hair:{DEFAULT:'#E5E1D9',2:'#D7D2C7'},brand:{DEFAULT:'#006837',dark:'#004d28',light:'#cce8d6'},navy:{DEFAULT:'#1A2B6D',dark:'#101f4f',light:'#d6dcec'},accent:{DEFAULT:'#E31E24',dark:'#a8131a',light:'#f7c8ca'},gold:'#B8923B' },
      fontFamily: { display:['"Playfair Display"','Georgia','serif'], sans:['Inter','ui-sans-serif','system-ui','sans-serif'] }
    }}}
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,500;1,600;1,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <?php require __DIR__ . '/partials/favicon.php'; ?>
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
      <div class="bg-orb" style="width:500px;height:500px;background:#006837;top:-100px;right:-100px;opacity:0.18"></div>
      <div class="mx-auto max-w-7xl px-4 lg:px-6 py-20 lg:py-32 relative">
        <div class="max-w-3xl">
          <div class="eyebrow" data-num="06" style="color:#B8923B"><?= h(t('blog_page.eyebrow')) ?></div>
          <h1 class="mt-5 font-display text-5xl sm:text-6xl lg:text-7xl font-bold leading-[0.95] text-white tracking-tight"><?= h(t('blog_page.headline')) ?></h1>
          <p class="mt-6 text-white/80 text-lg max-w-2xl"><?= h(t('blog_page.body')) ?></p>
        </div>
      </div>
    </section>

    <section class="section bg-paper">
      <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="flex flex-wrap items-end justify-between gap-4 mb-12">
          <div class="flex items-center gap-1 text-sm flex-wrap">
            <a href="blog.php" class="px-3 py-1.5 rounded-sm border <?= !$filter ? 'bg-ink text-white border-ink' : 'bg-white border-hair text-ink-2 hover:border-ink' ?>" style="<?= !$filter ? '' : 'border-color:#E5E1D9' ?>"><?= h(t('blog_page.filter_all')) ?></a>
            <a href="?type=blog" class="px-3 py-1.5 rounded-sm border <?= $filter==='blog' ? 'bg-ink text-white border-ink' : 'bg-white border-hair text-ink-2 hover:border-ink' ?>" style="border-color:#E5E1D9"><?= h(t('blog_page.filter_blog')) ?></a>
            <a href="?type=news" class="px-3 py-1.5 rounded-sm border <?= $filter==='news' ? 'bg-ink text-white border-ink' : 'bg-white border-hair text-ink-2 hover:border-ink' ?>" style="border-color:#E5E1D9"><?= h(t('blog_page.filter_news')) ?></a>
          </div>
        </div>

        <?php if (!$posts): ?>
          <div class="text-center py-16 bg-white border border-hair" style="border-color:#E5E1D9">
            <p class="text-ink-2"><?= h(t('blog_page.no_posts')) ?></p>
          </div>
        <?php else: ?>
          <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-px bg-hair" style="background:#E5E1D9">
            <?php foreach ($posts as $i => $p):
              $ts = strtotime($p['published_at']);
              $cover = $p['cover_id'] ? App\Content::mediaUrl((int) $p['cover_id']) : null;
              $num = str_pad((string)($i+1), 2, '0', STR_PAD_LEFT);
            ?>
              <article class="bg-paper group flex flex-col reveal" style="background:#FAF8F4">
                <a href="blog/<?= h($p['slug']) ?>" class="block aspect-[16/10] bg-paper-2 relative overflow-hidden" style="background:#F4F1EA">
                  <?php if ($cover): ?>
                    <img src="<?= h($cover) ?>" alt="<?= h($p['title']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                  <?php else: ?>
                    <div class="absolute inset-0 grid place-items-center text-brand/30">
                      <svg class="w-20 h-20" viewBox="0 0 24 24" fill="currentColor"><path d="M4 3h12a3 3 0 0 1 3 3v15a1 1 0 0 1-1 1H7a3 3 0 0 1-3-3V3zm12 2H6v13a1 1 0 0 0 1 1h11V5zM8 7h7v2H8V7zm0 4h7v2H8v-2z"/></svg>
                    </div>
                  <?php endif; ?>
                  <div class="absolute top-3 left-3 bg-white px-3 py-2 text-center">
                    <div class="text-[9px] tracking-widest text-accent uppercase font-semibold" style="color:#E31E24"><?= h(date('M', $ts)) ?></div>
                    <div class="font-display text-xl font-bold text-ink leading-none"><?= h(date('d', $ts)) ?></div>
                  </div>
                  <div class="absolute top-3 right-3 bg-ink/85 text-white text-[9px] font-bold tracking-widest uppercase px-2.5 py-1" style="background:rgba(30,36,43,0.85)"><?= h($p['type']) ?></div>
                </a>
                <div class="p-6 flex-1 flex flex-col">
                  <div class="font-display italic text-xs text-gold" style="color:#B8923B"><?= $num ?>.</div>
                  <h3 class="mt-2 font-display text-xl font-semibold text-ink leading-snug flex-1">
                    <a href="blog/<?= h($p['slug']) ?>" class="hover:text-brand"><?= h($p['title']) ?></a>
                  </h3>
                  <?php if (!empty($p['excerpt'])): ?>
                    <p class="mt-2 text-sm text-ink-2 line-clamp-2"><?= h($p['excerpt']) ?></p>
                  <?php endif; ?>
                  <div class="mt-4 text-[10px] text-ink-3 tracking-widest uppercase"><?= h(date('F j, Y', $ts)) ?> · <?= h(t('blog_page.by')) ?></div>
                  <a href="blog/<?= h($p['slug']) ?>" class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-brand uppercase tracking-widest">
                    <?= h(t('blog_page.read_more')) ?>
                    <span class="transition group-hover:translate-x-1">→</span>
                  </a>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <?php require __DIR__ . '/partials/pre-footer.php'; ?>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
