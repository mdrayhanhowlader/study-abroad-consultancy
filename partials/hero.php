<?php /** @var array $site */ $eventIso = $site['event']['date_iso']; $eventTitle = $site['event']['title']; ?>
<section id="home" class="relative overflow-hidden" style="background: linear-gradient(180deg, #FAF8F4 0%, #F4F1EA 100%);">
  <div class="bg-orb" style="width:480px;height:480px;background:#006837;top:-120px;right:-120px;opacity:0.06"></div>
  <div class="bg-orb" style="width:380px;height:380px;background:#087642;bottom:-120px;left:-80px;opacity:0.07"></div>
  <div class="bg-orb" style="width:300px;height:300px;background:#B8923B;top:40%;left:30%;opacity:0.05"></div>

  <div class="hero-inner mx-auto max-w-7xl px-4 lg:px-6 pt-14 pb-20 lg:pt-24 lg:pb-32 grid lg:grid-cols-12 gap-12 lg:gap-16 items-center relative">
    <div class="hero-copy lg:col-span-7 reveal">
      <div class="hero-eyebrow eyebrow" data-num="01">      <?= h(App\Homepage::text('hero.eyebrow', t('hero.eyebrow'))) ?></div>
      <h1 class="hero-title mt-6 font-display text-[44px] sm:text-6xl lg:text-[88px] font-bold leading-[0.95] tracking-[-0.02em] text-ink">
        <?= h(App\Homepage::text('hero.headline', t('hero.headline'))) ?>
      </h1>
      <p class="hero-description mt-7 text-lg lg:text-xl text-ink-2 leading-relaxed max-w-xl">
        <?= h(App\Homepage::text('hero.body', t('hero.body'))) ?>
      </p>
      <p class="hero-slogan mt-4 font-display italic text-lg text-brand">— <?= h(App\Homepage::text('hero.slogan', t('hero.slogan'))) ?></p>

      <div class="hero-actions mt-10 flex flex-wrap items-center gap-3">
        <a href="#services" class="btn btn-primary">
          <?= h(App\Homepage::text('hero.cta_primary', t('hero.cta_primary'))) ?>
          <span class="opacity-80">→</span>
        </a>
        <a href="#contact" class="btn btn-secondary">
          <?= h(App\Homepage::text('hero.cta_secondary', t('hero.cta_secondary'))) ?>
        </a>
      </div>

      <div class="hero-countdown mt-8 inline-flex flex-wrap items-center gap-4 bg-white/90 backdrop-blur-md border border-hair rounded-2xl pl-5 pr-6 py-3 shadow-xl"
           style="border-color:#E5E1D9"
           x-data="countdown('<?= h($eventIso) ?>T10:00:00')"
           x-init="start()">
        <div class="hero-countdown-details flex items-center gap-3 pr-4 border-r border-hair" style="border-color:#E5E1D9">
          <div class="w-10 h-10 rounded-xl bg-brand/10 text-brand grid place-items-center shrink-0">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </div>
          <div>
            <div class="text-[10px] font-bold tracking-[0.2em] text-accent uppercase"><?= h(App\Homepage::text('hero.countdown_eyebrow', t('hero.countdown_eyebrow'))) ?></div>
            <div class="text-xs font-semibold text-ink"><?= h(App\Homepage::text('hero.countdown_label', t('hero.countdown_label'))) ?></div>
            <div class="text-[10px] text-ink-3 font-display italic"><?= h($eventTitle) ?></div>
          </div>
        </div>
        <div class="hero-countdown-units flex items-center gap-2">
          <template x-for="u in units" :key="u.k">
            <div class="text-center min-w-[44px] bg-paper-2 rounded-lg py-1.5 px-1" style="background:#F4F1EA">
              <div class="font-display text-xl font-bold text-brand tabular-nums" x-text="u.v"></div>
              <div class="text-[8px] tracking-[0.15em] text-ink-3 uppercase mt-0.5" x-text="u.k"></div>
            </div>
          </template>
        </div>
      </div>
    </div>

    <div class="hero-art-wrapper lg:col-span-5 reveal reveal-d2">
      <div class="globe-stage hero-globe">
        <div class="globe-orbit globe-orbit--3">
          <div class="globe-pin">
            <svg class="globe-plane globe-plane--brand" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1L15 22v-1.5L13 19v-5.5l8 2.5z"/></svg>
          </div>
        </div>
        <div class="globe-orbit globe-orbit--2">
          <div class="globe-pin">
            <svg class="globe-plane globe-plane--gold" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1L15 22v-1.5L13 19v-5.5l8 2.5z"/></svg>
          </div>
        </div>
        <div class="globe-orbit">
          <div class="globe-pin">
            <svg class="globe-plane" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1L15 22v-1.5L13 19v-5.5l8 2.5z"/></svg>
          </div>
        </div>

        <div class="globe-core">
          <img src="<?= h(App\Homepage::text('visual.hero_image') ?: 'https://images.unsplash.com/photo-1542051841857-5f90071e7989?w=900&auto=format&fit=crop&q=70') ?>"
               alt="<?= h(App\Homepage::text('hero.badge_image_alt', t('hero.badge_image_alt'))) ?>" loading="eager">
        </div>

        <div class="hero-stat-badge absolute -bottom-4 -right-4 lg:-right-8 bg-white border border-hair shadow-xl rounded-sm px-4 py-3 flex items-center gap-3 reveal reveal-d3">
          <div class="w-9 h-9 rounded-full bg-brand/10 grid place-items-center text-brand"><?= icon('check', 'w-5 h-5') ?></div>
          <div>
            <div class="text-[10px] uppercase tracking-widest text-ink-3"><?= h(App\Homepage::text('hero.stat_label', t('hero.stat_label'))) ?></div>
            <div class="text-sm font-bold text-ink font-display"><?= h(App\Homepage::text('hero.stat_value', t('hero.stat_value'))) ?></div>
          </div>
        </div>

        <div class="hero-route-badge absolute -top-3 left-0 lg:-left-8 bg-navy text-white px-3 py-2 rounded-sm text-[10px] font-bold tracking-widest shadow-lg reveal reveal-d3"
             aria-label="<?= h(App\Homepage::text('hero.badge_eyebrow', t('hero.badge_eyebrow'))) ?>">
          <span aria-hidden="true">✈</span>
          <span class="hero-route-window" aria-hidden="true">
            <span class="hero-route-track">
              <?php $routeCities = App\Homepage::destinations(); foreach (array_merge($routeCities, [$routeCities[0]]) as $city): ?>
                <span><?= h('DHAKA → ' . $city) ?></span>
              <?php endforeach; ?>
            </span>
          </span>
        </div>
      </div>

      <div class="mt-8 flex items-center justify-center gap-6 text-xs text-ink-3">
        <div class="flex items-center gap-1.5">
          <span class="w-1.5 h-1.5 rounded-full bg-brand"></span>
          <?= h(App\Homepage::text('about.point_4', t('about.point_4'))) ?>
        </div>
        <div class="flex items-center gap-1.5">
          <span class="w-1.5 h-1.5 rounded-full bg-gold" style="background:#B8923B"></span>
          <?= h(t('stats.label_1')) ?>
        </div>
      </div>
    </div>
  </div>
</section>
