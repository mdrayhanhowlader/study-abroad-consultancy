<?php /** @var array $site */ $phone = $site['contact']['phone']; $phoneTel = $site['contact']['phone_tel']; ?>
<section class="relative overflow-hidden">
  <div class="bg-ink text-white py-16 lg:py-24 relative" style="background:#1E242B">
    <div class="absolute inset-0 opacity-[0.06] bg-[radial-gradient(circle_at_30%_50%,#B8923B,transparent_50%)]"></div>
    <div class="mx-auto max-w-7xl px-4 lg:px-6 flex flex-col lg:flex-row items-center justify-between gap-8 relative">
      <div>
        <div class="eyebrow" data-num="11" style="color:#B8923B"><?= h(t('nav.contact')) ?></div>
        <h2 class="mt-4 font-display text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight text-white"><?= h(t('pre_footer.headline')) ?></h2>
        <p class="mt-3 text-white/70 max-w-2xl"><?= h(t('pre_footer.body')) ?></p>
      </div>
      <a href="tel:<?= h($phoneTel) ?>" class="inline-flex items-center gap-3 bg-white text-ink font-bold text-base px-7 py-4 rounded-sm shadow-xl hover:bg-gold transition" style="--tw-bg-opacity:1">
        <?= icon('phone', 'w-5 h-5') ?>
        <?= h(t('pre_footer.cta')) ?>
      </a>
    </div>
  </div>
</section>
