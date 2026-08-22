<?php /** @var array $services */ ?>
<section class="bg-ink py-3 overflow-hidden border-y border-white/5" style="background:#1E242B">
  <div class="marquee marquee-slow flex items-center text-white/90 font-display text-sm sm:text-base font-medium tracking-[0.16em] uppercase" aria-label="<?= h(t('marquee.aria')) ?>">
    <?php for ($loop = 0; $loop < 2; $loop++): ?>
      <?php foreach ($services as $s): ?>
        <span class="flex items-center gap-12 px-6">
          <span><?= h(strtoupper($s['title'])) ?></span>
          <span class="text-gold" style="color:#B8923B">✦</span>
        </span>
      <?php endforeach; ?>
    <?php endfor; ?>
  </div>
</section>
