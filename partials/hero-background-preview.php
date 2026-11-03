<?php
$heroImage = App\Homepage::text('visual.hero_image') ?: 'https://images.unsplash.com/photo-1542051841857-5f90071e7989?w=1800&auto=format&fit=crop&q=80';
?>
<section class="hero-background-preview relative isolate flex items-center overflow-hidden text-white"
         style="--hero-background-image: url('<?= h($heroImage) ?>')">
  <div class="hero-background-preview__overlay absolute inset-0 -z-10" aria-hidden="true"></div>
  <div class="mx-auto w-full max-w-7xl px-5 py-20 sm:px-8 sm:py-28 lg:px-10 lg:py-36">
    <div class="max-w-3xl">
      <div class="inline-flex items-center gap-3 rounded-full border border-white/25 bg-white/10 py-2 pl-2 pr-5 shadow-lg shadow-black/10 backdrop-blur-md">
        <span class="grid h-8 w-8 place-items-center rounded-full bg-white text-brand" aria-hidden="true">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 3v2m0 14v2M3 12h2m14 0h2M5.64 5.64l1.42 1.42m9.88 9.88 1.42 1.42m0-12.72-1.42 1.42m-9.88 9.88-1.42 1.42"/>
            <circle cx="12" cy="12" r="5"/>
          </svg>
        </span>
        <span class="text-[10px] font-semibold uppercase tracking-[0.2em] text-white sm:text-[11px]">
          Your future starts in Japan
        </span>
      </div>
      <p class="mt-8 text-xs font-semibold uppercase tracking-[0.24em] text-white/80">
        <?= h(App\Homepage::text('hero.eyebrow', t('hero.eyebrow'))) ?>
      </p>
      <h2 class="mt-5 max-w-3xl font-display text-4xl font-bold leading-[1.04] tracking-tight sm:text-6xl lg:text-7xl">
        <?= h(App\Homepage::text('hero.headline', t('hero.headline'))) ?>
      </h2>
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
    </div>
  </div>
</section>
