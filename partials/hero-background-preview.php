<section class="hero-background-preview relative isolate flex items-center overflow-hidden text-white">
  <img class="hero-background-preview__image" src="<?= h($heroImage) ?>" alt="" aria-hidden="true" fetchpriority="high" decoding="async">
  <div class="hero-background-preview__overlay absolute inset-0" aria-hidden="true"></div>
  <div class="relative z-10 mx-auto w-full max-w-7xl px-5 py-16 sm:px-8 sm:py-20 lg:px-10 lg:py-24">
    <div class="max-w-3xl">
      <div class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-white shadow-lg shadow-black/10 backdrop-blur-md sm:text-[11px]">
        <span class="h-1.5 w-1.5 rounded-full bg-[#a7e4ba]" aria-hidden="true"></span>
        Japan · Study · Work
      </div>
      <p class="mt-6 text-xs font-semibold uppercase tracking-[0.24em] text-white/80">
        <?= h(App\Homepage::text('hero.eyebrow', t('hero.eyebrow'))) ?>
      </p>
      <h1 class="mt-4 max-w-3xl font-display text-4xl font-bold leading-[1.04] tracking-tight sm:text-6xl lg:text-7xl">
        <?= h(App\Homepage::text('hero.headline', t('hero.headline'))) ?>
      </h1>
      <p class="mt-6 max-w-2xl text-base leading-relaxed text-white/85 sm:text-lg">
        <?= h(App\Homepage::text('hero.body', t('hero.body'))) ?>
      </p>
      <p class="mt-4 font-display text-lg italic text-white sm:text-xl">
        — <?= h(App\Homepage::text('hero.slogan', t('hero.slogan'))) ?>
      </p>
      <div class="mt-8 flex flex-wrap gap-3">
        <a href="#services" class="btn border-white bg-white text-ink hover:bg-white/90">
          <?= h(App\Homepage::text('hero.cta_primary', t('hero.cta_primary'))) ?><span aria-hidden="true">→</span>
        </a>
        <a href="#contact" class="btn border-white/70 bg-black/15 text-white backdrop-blur-sm hover:bg-white/15">
          <?= h(App\Homepage::text('hero.cta_secondary', t('hero.cta_secondary'))) ?>
        </a>
      </div>
      <div class="mt-7 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-white/20 pt-5 text-[10px] font-semibold uppercase tracking-[0.16em] text-white/75 sm:text-[11px]" aria-label="Our Japan pathways">
        <span>Student Visa</span>
        <span>Skilled Worker · SSW</span>
        <span>Technical Training · TITP</span>
      </div>
    </div>
  </div>
</section>
