<?php /** @var array $site */ ?>
<section id="office-location" class="office-location">
  <div class="mx-auto max-w-7xl px-4 lg:px-6">
    <div class="office-location-card">
      <div class="office-location-copy">
        <div class="eyebrow" data-num=""><?= h(t('footer.office_address')) ?></div>
        <h2 class="font-display"><?= h($site['brand']['full_name']) ?></h2>
        <p class="office-location-address">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/>
            <circle cx="12" cy="10" r="2.5"/>
          </svg>
          <span><?= h($site['contact']['address']) ?></span>
        </p>
        <a href="<?= h($site['contact']['address_link']) ?>" target="_blank" rel="noopener noreferrer" class="office-location-link">
          <?= h(t('common.open_in_maps')) ?> <span aria-hidden="true">↗</span>
        </a>
      </div>
      <div class="office-map-frame">
        <iframe
          src="<?= h($site['contact']['map_embed_url']) ?>"
          title="<?= h($site['brand']['full_name'] . ' office location on Google Maps') ?>"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          allowfullscreen
        ></iframe>
      </div>
    </div>
  </div>
</section>
