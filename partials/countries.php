<?php /** @var array $countries */
$featured = array_values(array_filter($countries, fn($c) => !empty($c['featured'])));
?>
<section id="countries" class="section bg-paper-2" style="background:#F4F1EA">
  <div class="mx-auto max-w-7xl px-4 lg:px-6">
    <div class="max-w-3xl">
      <div class="eyebrow" data-num="05"><?= h(App\Homepage::text('countries.eyebrow', t('countries.eyebrow'))) ?></div>
      <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-tight text-ink"><?= h(App\Homepage::text('countries.headline', t('countries.headline'))) ?></h2>
      <p class="mt-4 text-ink-2 leading-relaxed max-w-2xl"><?= h(App\Homepage::text('countries.body', t('countries.body'))) ?></p>
      <a href="#contact" class="mt-6 inline-flex btn-arrow"><?= h(App\Homepage::text('countries.cta', t('countries.cta'))) ?></a>
    </div>

    <div id="destinationSlider" class="destination-slider-track mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-px bg-hair" style="background:#E5E1D9">
      <?php foreach ($featured as $i => $c):
        $num = str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT);
      ?>
        <a href="<?= h($c['href']) ?>" class="group relative block bg-white aspect-[3/4] overflow-hidden">
          <div class="tile-image absolute inset-0">
            <img src="<?= h($c['image_url'] ?: 'https://images.unsplash.com/' . $c['img1'] . '?w=600&auto=format&fit=crop&q=70') ?>" alt="<?= h($c['name']) ?>" loading="lazy">
          </div>
          <div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-ink/30 to-transparent"></div>
          <div class="absolute top-4 right-4 w-10 h-10 rounded-full overflow-hidden ring-2 ring-white shadow bg-white grid place-items-center">
            <span class="flag <?= h($c['flag']) ?>"></span>
          </div>
          <?php if (!empty($c['featured'])): ?>
            <span class="absolute top-4 left-4 bg-accent text-white text-[9px] font-bold tracking-widest uppercase px-2.5 py-1 rounded-sm"><?= h(App\Homepage::text('countries.featured_badge', t('countries.featured_badge'))) ?></span>
          <?php else: ?>
            <span class="absolute top-4 left-4 bg-white/95 text-ink-2 text-[9px] font-bold tracking-widest uppercase px-2.5 py-1 rounded-sm"><?= h(App\Homepage::text('countries.coming_soon_badge', t('countries.coming_soon_badge'))) ?></span>
          <?php endif; ?>
          <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
            <div class="font-display italic text-xs text-gold mb-1" style="color:#B8923B"><?= $num ?>.</div>
            <h3 class="font-display text-2xl font-bold leading-tight"><?= h($c['name']) ?></h3>
            <p class="text-xs text-white/75 mt-1.5 line-clamp-2"><?= h($c['description']) ?></p>
            <div class="mt-3 inline-flex items-center gap-1.5 text-[10px] uppercase tracking-widest opacity-90">
              <?= h(t('common.explore')) ?>
              <span class="transition group-hover:translate-x-1">→</span>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
