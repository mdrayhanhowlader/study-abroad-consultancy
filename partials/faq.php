<?php /** @var array $faqs */ ?>
<section id="faq" class="section bg-paper">
  <div class="mx-auto max-w-4xl px-4 lg:px-6">
    <div class="text-center mb-12">
      <div class="eyebrow justify-center" data-num="10"><?= h(t('faq.eyebrow')) ?></div>
      <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-tight text-ink"><?= h(t('faq.headline')) ?></h2>
      <p class="mt-4 text-ink-2"><?= h(t('faq.body')) ?><a href="#contact" class="text-brand font-semibold underline underline-offset-4"><?= h(t('faq.body_link')) ?></a>.</p>
    </div>

    <div class="space-y-px bg-hair" style="background:#E5E1D9" x-data="{ open: 0 }">
      <?php if (!$faqs): ?>
        <div class="bg-paper text-center text-ink-3 text-sm py-8"><?= h(t('faq.empty')) ?></div>
      <?php else: foreach ($faqs as $i => $f): ?>
        <div class="bg-paper" style="background:#FAF8F4">
          <button @click="open = open === <?= $i ?> ? -1 : <?= $i ?>"
                  class="w-full flex items-center justify-between gap-4 text-left px-6 py-5 hover:bg-white transition">
            <span class="flex items-center gap-4">
              <span class="font-display italic text-sm text-gold shrink-0" style="color:#B8923B"><?= str_pad((string)($i+1), 2, '0', STR_PAD_LEFT) ?>.</span>
              <span class="font-semibold text-ink"><?= h($f['q']) ?></span>
            </span>
            <span :class="open === <?= $i ?> && 'rotate-180 text-brand'" class="text-ink-3 transition-transform"><?= icon('chevron', 'w-5 h-5') ?></span>
          </button>
          <div x-show="open === <?= $i ?>" class="px-6 pb-6 pl-16 text-ink-2 leading-relaxed">
            <?= h($f['a']) ?>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
</section>
