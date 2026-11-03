<?php /** @var array $stats */ ?>
<section class="impact-section">
  <div class="mx-auto max-w-7xl px-4 lg:px-6">
    <div class="impact-panel">
      <div class="impact-heading">
        <span class="impact-kicker"><span></span><?= h(t('stats.eyebrow')) ?></span>
        <div class="impact-mark" aria-hidden="true">M<span>.</span></div>
      </div>
      <div class="impact-metrics">
        <?php foreach ($stats as $i => $s):
          $num = str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT);
        ?>
          <div class="impact-metric">
            <span class="impact-index"><?= $num ?></span>
            <div class="stat-num" data-counter data-target="<?= h($s['value']) ?>" data-suffix="<?= h($s['suffix']) ?>">0<?= h($s['suffix']) ?></div>
            <div class="impact-label"><?= h(t('stats.label_' . ($i + 1))) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
