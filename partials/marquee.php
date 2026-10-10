<?php /** @var array $services */ ?>
<section class="marquee-band relative overflow-hidden py-4" aria-label="<?= h(t('marquee.aria')) ?>">
  <div class="marquee marquee-slow flex items-center font-display text-xs sm:text-sm font-semibold tracking-[0.14em] uppercase" aria-hidden="true">
    <?php for ($loop = 0; $loop < 2; $loop++): ?>
      <?php foreach ($services as $s): ?>
        <span class="marquee-item flex items-center gap-4 px-5">
          <span><?= h($s['title']) ?></span>
          <span class="marquee-star" aria-hidden="true">✦</span>
        </span>
      <?php endforeach; ?>
    <?php endfor; ?>
  </div>
</section>
