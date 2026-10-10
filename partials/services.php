<?php
$featured = array_values(array_filter($services, fn($s) => in_array($s['slug'], ['student-visa','ssw','titp'], true)));
$other    = array_values(array_filter($services, fn($s) => !in_array($s['slug'], ['student-visa','ssw','titp'], true)));
$japanSvgs = [
  'student-visa' => 'M4 4h12a3 3 0 0 1 3 3v15a1 1 0 0 1-1 1H7a3 3 0 0 1-3-3V4zm12 2H6v13a1 1 0 0 0 1 1h11V6zM8 7h7v2H8V7zm0 4h7v2H8v-2z',
  'ssw'          => 'M5 4a3 3 0 1 1 4 2.8V11a3 3 0 0 0 3 3h3a5 5 0 0 1 0 10h-2a1 1 0 1 1 0-2h2a3 3 0 0 0 0-6h-3a5 5 0 0 1-5-5V6.8A3 3 0 0 1 5 4z',
  'titp'         => 'M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1L15 22v-1.5L13 19v-5.5l8 2.5z',
];
$paths = ['student-visa', 'ssw', 'titp'];
$supportSteps = [
  [
    'title' => App\Homepage::text('services.support_school_title', 'School & Course Planning'),
    'description' => App\Homepage::text('services.support_school_desc', 'Explore study options that fit your goals and background.'),
    'icon' => 'M4 4h16v16H4zM8 8h8M8 12h8M8 16h5',
  ],
  [
    'title' => App\Homepage::text('services.support_application_title', 'Application Guidance'),
    'description' => App\Homepage::text('services.support_application_desc', 'Get help preparing forms and organising your documents.'),
    'icon' => 'M8 3h8l4 4v14H4V3h4zm1 9h6m-6 4h6m-6 4h4',
  ],
  [
    'title' => App\Homepage::text('services.support_departure_title', 'Pre-departure Support'),
    'description' => App\Homepage::text('services.support_departure_desc', 'Prepare for travel and settling into life in Japan.'),
    'icon' => 'M3 12 21 3l-6 18-3-7-9-2zm9 2 5-6',
  ],
];
?>
<section id="services" class="pathways-section section bg-paper">
  <div class="mx-auto max-w-7xl px-4 lg:px-6">
    <div class="pathways-heading max-w-2xl">
      <div class="eyebrow" data-num="04"><?= h(App\Homepage::text('services.eyebrow', t('services.eyebrow'))) ?></div>
      <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-tight text-ink"><?= h(App\Homepage::text('services.headline', t('services.headline'))) ?></h2>
      <p class="mt-4 text-ink-2 leading-relaxed"><?= h(App\Homepage::text('services.body', t('services.body'))) ?></p>
    </div>

    <div class="pathways-grid mt-12 grid md:grid-cols-3 gap-5 lg:gap-6">
      <?php foreach ($featured as $i => $f):
        $svg = $japanSvgs[$f['slug']] ?? 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z';
        $num = str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT);
      ?>
        <article class="pathway-card bg-white p-7 lg:p-8 flex flex-col group transition reveal">
          <div class="flex items-center justify-between gap-3">
            <span class="pathway-number"><?= $num ?></span>
            <span class="pathway-line" aria-hidden="true"></span>
          </div>
          <div class="pathway-icon mt-6 w-12 h-12 grid place-items-center text-brand transition">
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor"><path d="<?= h($svg) ?>"/></svg>
          </div>
          <h3 class="mt-5 font-display text-2xl font-bold text-ink leading-tight"><?= h($f['title']) ?></h3>
          <p class="mt-3 text-sm text-ink-2 leading-relaxed flex-1"><?= h($f['desc']) ?></p>
          <a href="<?= h($f['cta_href'] ?: '#contact') ?>" class="mt-6 btn-arrow">
            <?= h($f['cta_text'] ?: App\Homepage::text('services.cta_get_started', t('services.cta_get_started'))) ?>
          </a>
        </article>
      <?php endforeach; ?>
    </div>

    <?php if ($other): ?>
    <div class="mt-12 grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-px bg-hair" style="background:#E5E1D9">
      <?php foreach ($other as $s): ?>
        <div class="bg-paper p-5 text-center hover:bg-white transition group">
          <div class="w-10 h-10 mx-auto grid place-items-center text-ink group-hover:text-brand transition">
            <?= icon($s['icon'], 'w-6 h-6') ?>
          </div>
          <div class="mt-3 text-sm font-semibold text-ink"><?= h($s['title']) ?></div>
        </div>
      <?php endforeach; ?>
      <?php foreach ($supportSteps as $step): ?>
        <a href="#contact" class="bg-paper p-5 text-center hover:bg-white transition group">
          <div class="w-10 h-10 mx-auto grid place-items-center text-ink group-hover:text-brand transition" aria-hidden="true">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
              <path d="<?= h($step['icon']) ?>"/>
            </svg>
          </div>
          <div class="mt-3 text-sm font-semibold text-ink"><?= h($step['title']) ?></div>
          <p class="mt-2 text-xs leading-relaxed text-ink-3"><?= h($step['description']) ?></p>
        </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
