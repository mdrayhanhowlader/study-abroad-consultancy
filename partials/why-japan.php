<?php /** @var array $site */ ?>
<section class="why-japan-section section bg-navy text-white relative overflow-hidden" style="background:#1A2B6D">
  <div class="bg-orb" style="width:500px;height:500px;background:#006837;top:-100px;right:-100px;opacity:0.08"></div>
  <div class="bg-orb" style="width:300px;height:300px;background:#B8923B;bottom:-80px;left:20%;opacity:0.05"></div>

  <div class="mx-auto max-w-7xl px-4 lg:px-6 relative">
    <div class="why-japan-heading max-w-2xl">
      <div class="eyebrow" data-num="03" style="color:#B8923B"><?= h(App\Homepage::text('why.eyebrow', t('why.eyebrow'))) ?></div>
      <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-tight text-white"><?= h(App\Homepage::text('why.headline', t('why.headline'))) ?></h2>
    </div>

    <div id="whyJapanSlider" class="why-japan-grid why-japan-slider-track mt-14 grid sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5">
      <?php for ($i = 1; $i <= 4; $i++): ?>
        <article class="why-japan-card bg-navy p-7 lg:p-8 relative" style="background:#1A2B6D">
          <div class="why-japan-number"><?= str_pad((string)$i, 2, '0', STR_PAD_LEFT) ?></div>
          <div class="why-japan-rule" aria-hidden="true"></div>
          <h3 class="font-display text-xl lg:text-2xl font-bold text-white"><?= h(App\Homepage::text('why.reason_' . $i . '_title', t('why.reason_' . $i . '_title'))) ?></h3>
          <p class="mt-3 text-sm text-white/85 leading-relaxed"><?= h(App\Homepage::text('why.reason_' . $i . '_desc', t('why.reason_' . $i . '_desc'))) ?></p>
        </article>
      <?php endfor; ?>
    </div>
  </div>
</section>
