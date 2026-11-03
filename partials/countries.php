<?php /** @var array $countries */
$featured = array_values(array_filter($countries, fn($c) => !empty($c['featured'])));
?>
<section id="countries" class="destinations-section">
  <div class="mx-auto max-w-7xl px-4 lg:px-6">
    <div class="destinations-heading">
      <div class="max-w-3xl">
        <div class="eyebrow" data-num="05"><?= h(App\Homepage::text('countries.eyebrow', t('countries.eyebrow'))) ?></div>
        <h2 class="font-display"><?= h(App\Homepage::text('countries.headline', t('countries.headline'))) ?></h2>
        <p><?= h(App\Homepage::text('countries.body', t('countries.body'))) ?></p>
      </div>
      <a href="#contact" class="destinations-cta"><?= h(App\Homepage::text('countries.cta', t('countries.cta'))) ?><span aria-hidden="true">↗</span></a>
    </div>

    <div id="destinationSlider" class="destination-slider-track destinations-grid">
      <?php foreach ($featured as $i => $c):
        $num = str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT);
      ?>
        <a href="<?= h($c['href']) ?>" class="destination-card group">
          <div class="tile-image destination-image">
            <img src="<?= h($c['image_url'] ?: 'https://images.unsplash.com/' . $c['img1'] . '?w=600&auto=format&fit=crop&q=70') ?>" alt="<?= h($c['name']) ?>" loading="lazy">
          </div>
          <div class="destination-scrim"></div>
          <div class="destination-flag">
            <span class="flag <?= h($c['flag']) ?>"></span>
          </div>
          <?php if (!empty($c['featured'])): ?>
            <span class="destination-badge destination-badge--featured"><?= h(App\Homepage::text('countries.featured_badge', t('countries.featured_badge'))) ?></span>
          <?php else: ?>
            <span class="destination-badge"><?= h(App\Homepage::text('countries.coming_soon_badge', t('countries.coming_soon_badge'))) ?></span>
          <?php endif; ?>
          <div class="destination-copy">
            <span class="destination-index"><?= $num ?></span>
            <div class="destination-copy-row">
              <div>
                <h3 class="font-display"><?= h($c['name']) ?></h3>
                <p><?= h($c['description']) ?></p>
              </div>
              <span class="destination-explore" aria-label="<?= h(t('common.explore')) ?>">↗</span>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
