<?php /** @var array $stats */ ?>
<section class="section-sm bg-paper hairline-top hairline-bottom" style="border-color:#E5E1D9">
  <div class="mx-auto max-w-7xl px-4 lg:px-6">
    <div class="text-center mb-12">
      <div class="eyebrow" data-num="06"><?= h(t('stats.eyebrow')) ?></div>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-hair" style="background:#E5E1D9">
      <?php foreach ($stats as $i => $s):
        $num = str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT);
      ?>
        <div class="bg-paper p-8 lg:p-10 text-center lg:text-left reveal">
          <div class="font-display italic text-xs text-gold" style="color:#B8923B"><?= $num ?>.</div>
          <div class="mt-3 stat-num" data-counter data-target="<?= h($s['value']) ?>" data-suffix="<?= h($s['suffix']) ?>">0<?= h($s['suffix']) ?></div>
          <div class="mt-5 text-xs tracking-widest text-ink-2 uppercase"><?= h(t('stats.label_' . ($i + 1))) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
