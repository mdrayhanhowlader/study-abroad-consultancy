<?php /** @var array $posts */ ?>
<section id="blog" class="section bg-paper-2" style="background:#F4F1EA">
  <div class="mx-auto max-w-7xl px-4 lg:px-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12">
      <div>
        <div class="eyebrow" data-num="09"><?= h(t('blog.eyebrow')) ?></div>
        <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-tight text-ink"><?= h(t('blog.headline')) ?></h2>
      </div>
    </div>

    <div class="grid md:grid-cols-3 gap-px bg-hair" style="background:#E5E1D9">
      <?php if (!$posts): ?>
        <div class="md:col-span-3 bg-paper-2 text-center text-ink-3 text-sm py-12"><?= h(t('blog.empty')) ?></div>
      <?php else: foreach ($posts as $i => $p):
        $num = str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT);
      ?>
        <a href="<?= h($p['href']) ?>" class="group bg-paper-2 block hover:bg-white transition reveal" style="background:#F4F1EA">
          <div class="aspect-[16/10] bg-paper relative overflow-hidden" style="background:#FAF8F4">
            <div class="absolute inset-0 grid place-items-center text-ink-3/30">
              <svg class="w-20 h-20" viewBox="0 0 24 24" fill="currentColor"><path d="M4 3h12a3 3 0 0 1 3 3v15a1 1 0 0 1-1 1H7a3 3 0 0 1-3-3V3zm12 2H6v13a1 1 0 0 0 1 1h11V5zM8 7h7v2H8V7zm0 4h7v2H8v-2z"/></svg>
            </div>
            <div class="absolute top-4 left-4 bg-white px-3 py-2 text-center">
              <div class="text-[9px] tracking-widest text-accent uppercase font-semibold" style="color:#E31E24"><?= h($p['mo']) ?></div>
              <div class="font-display text-xl font-bold text-ink leading-none"><?= h($p['day']) ?></div>
            </div>
            <div class="absolute top-4 right-4 bg-ink/85 text-white text-[9px] font-bold tracking-widest uppercase px-2.5 py-1" style="background:rgba(30,36,43,0.85)"><?= h($p['tag']) ?></div>
          </div>
          <div class="p-6 lg:p-8">
            <div class="font-display italic text-xs text-gold" style="color:#B8923B"><?= $num ?>.</div>
            <h3 class="mt-3 font-display text-xl font-semibold text-ink leading-snug group-hover:text-brand transition"><?= h($p['title']) ?></h3>
            <span class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-brand uppercase tracking-widest">
              <?= h(t('blog.read_more')) ?>
              <span class="transition group-hover:translate-x-1">→</span>
            </span>
          </div>
        </a>
      <?php endforeach; endif; ?>
    </div>
  </div>
</section>
