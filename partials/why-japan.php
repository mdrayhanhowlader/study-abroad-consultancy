<?php /** @var array $site */ ?>
<section class="section bg-navy text-white relative overflow-hidden" style="background:#1A2B6D">
  <div class="bg-orb" style="width:500px;height:500px;background:#006837;top:-100px;right:-100px;opacity:0.18"></div>
  <div class="bg-orb" style="width:300px;height:300px;background:#B8923B;bottom:-80px;left:20%;opacity:0.10"></div>

  <div class="mx-auto max-w-7xl px-4 lg:px-6 relative">
    <div class="max-w-2xl">
      <div class="eyebrow" data-num="03" style="color:#B8923B"><?= h(t('why.eyebrow')) ?></div>
      <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-tight text-white"><?= h(t('why.headline')) ?></h2>
    </div>

    <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-4 gap-px bg-white/10">
      <?php for ($i = 1; $i <= 4; $i++): ?>
        <article class="bg-navy p-8 lg:p-10 relative" style="background:#1A2B6D">
          <div class="font-display italic text-7xl leading-none text-accent opacity-50 mb-4" style="color:#E31E24"><?= str_pad((string)$i, 2, '0', STR_PAD_LEFT) ?></div>
          <h3 class="font-display text-2xl font-bold text-white"><?= h(t('why.reason_' . $i . '_title')) ?></h3>
          <p class="mt-3 text-sm text-white/75 leading-relaxed"><?= h(t('why.reason_' . $i . '_desc')) ?></p>
        </article>
      <?php endfor; ?>
    </div>
  </div>
</section>
