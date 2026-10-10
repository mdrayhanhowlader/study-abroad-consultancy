<?php /** @var array $site */ ?>
<section id="about" class="section bg-paper relative overflow-hidden">
  <div class="mx-auto max-w-7xl px-4 lg:px-6 grid lg:grid-cols-2 gap-14 lg:gap-20 items-center">
    <!-- Image collage -->
    <div class="relative reveal order-2 lg:order-1">
      <div class="grid grid-cols-12 gap-4 items-end">
        <div class="col-span-7 space-y-4">
          <div class="aspect-[4/5] rounded-3xl overflow-hidden shadow-xl">
            <img src="<?= h(App\Homepage::text('visual.about_image_primary') ?: 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=700&auto=format&fit=crop&q=70') ?>" alt="<?= h(App\Homepage::text('about.headline', t('about.headline'))) ?>" class="w-full h-full object-cover" loading="lazy">
          </div>
        </div>
        <div class="col-span-5 space-y-4 pb-8">
          <div class="aspect-[3/4] rounded-3xl overflow-hidden shadow-lg">
            <img src="<?= h(App\Homepage::text('visual.about_image_secondary') ?: 'https://images.unsplash.com/photo-1531545514256-b1400bc00f31?w=600&auto=format&fit=crop&q=70') ?>" alt="" class="w-full h-full object-cover" loading="lazy">
          </div>
          <div class="aspect-square rounded-3xl bg-navy text-white grid place-items-center text-center p-5 shadow-xl" style="background:#1A2B6D">
            <div>
              <div class="font-display text-5xl font-bold">5+</div>
              <div class="mt-2 text-[10px] tracking-[0.2em] uppercase leading-relaxed"><?= h(App\Homepage::text('about.years_label', t('about.years_label'))) ?><br><?= h(App\Homepage::text('about.years_sub', t('about.years_sub'))) ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Floating badge -->
      <div class="absolute -bottom-6 -right-2 lg:right-8 bg-white rounded-2xl shadow-xl border border-hair p-4 flex items-center gap-3 max-w-[220px]" style="border-color:#E5E1D9">
        <div class="w-12 h-12 rounded-xl bg-brand/10 grid place-items-center text-brand shrink-0">
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div>
          <div class="font-display text-xl font-bold text-ink">99%</div>
          <div class="text-[10px] text-ink-3 uppercase tracking-wider">Visa success rate</div>
        </div>
      </div>

      <!-- Decorative elements -->
      <div class="about-japan-focus absolute top-0 left-0 z-10 w-[calc(58.333%-0.5rem)] min-h-[116px] rounded-2xl border border-white/80 bg-white/95 p-3 shadow-lg">
        <div class="flex items-center gap-2">
          <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full border border-red-100 bg-white shadow-sm" aria-hidden="true">
            <svg class="h-4 w-6 rounded-sm" viewBox="0 0 28 20" fill="none">
              <rect width="28" height="20" rx="2" fill="white"/>
              <circle cx="14" cy="10" r="5" fill="#BC002D"/>
            </svg>
          </span>
          <div class="min-w-0">
            <div class="text-[8px] font-bold uppercase tracking-[0.16em] text-brand"><?= h(App\Homepage::text('about.focus_label', 'Japan-focused')) ?></div>
            <div class="font-display text-xs font-bold leading-tight text-ink"><?= h(App\Homepage::text('about.focus_title', 'Pathways to Japan')) ?></div>
          </div>
        </div>
        <div class="mt-3 flex flex-wrap gap-1" aria-label="Student Visa, SSW and TITP guidance">
          <span class="rounded-full bg-paper-2 px-2 py-1 text-[8px] font-semibold leading-none text-ink-2"><?= h(App\Homepage::text('about.focus_student_visa', 'Student Visa')) ?></span>
          <span class="rounded-full bg-paper-2 px-2 py-1 text-[8px] font-semibold leading-none text-ink-2"><?= h(App\Homepage::text('about.focus_ssw', 'SSW')) ?></span>
          <span class="rounded-full bg-paper-2 px-2 py-1 text-[8px] font-semibold leading-none text-ink-2"><?= h(App\Homepage::text('about.focus_titp', 'TITP')) ?></span>
        </div>
      </div>
      <div class="absolute top-1/2 -right-8 w-16 h-16 bg-brand/10 rounded-full -z-10"></div>
    </div>

    <!-- Content -->
    <div class="reveal reveal-d2 order-1 lg:order-2">
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand/10 text-brand text-xs font-semibold tracking-wider uppercase">
        <span class="w-1.5 h-1.5 rounded-full bg-brand"></span>
        <?= h(App\Homepage::text('about.eyebrow', t('about.eyebrow'))) ?>
      </div>
      <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-[1.05] text-ink tracking-tight">
        <?= h(App\Homepage::text('about.headline', t('about.headline'))) ?>
      </h2>
      <p class="mt-3 font-display italic text-brand text-lg">— <?= h(App\Homepage::text('about.slogan', t('about.slogan'))) ?></p>
      <p class="mt-6 text-lg text-ink-2 leading-relaxed">
        <?= h(App\Homepage::text('about.body', t('about.body'))) ?>
      </p>

      <div class="mt-8 grid sm:grid-cols-2 gap-4">
        <?php foreach (['point_1','point_2','point_3','point_4'] as $key): ?>
          <div class="flex items-start gap-3 p-4 bg-white rounded-2xl border border-hair shadow-sm" style="border-color:#E5E1D9">
            <span class="w-6 h-6 rounded-full bg-brand/10 text-brand grid place-items-center shrink-0 mt-0.5">
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg>
            </span>
            <span class="text-sm text-ink leading-snug"><?= h(App\Homepage::text('about.' . $key, t('about.' . $key))) ?></span>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="mt-10 flex flex-wrap items-center gap-4">
        <a href="#services" class="btn btn-primary px-6 py-3"><?= h(App\Homepage::text('about.cta_primary', t('about.cta_primary'))) ?> →</a>
        <a href="#contact" class="text-sm font-semibold text-ink hover:text-brand transition"><?= h(App\Homepage::text('about.cta_secondary', t('about.cta_secondary'))) ?></a>
      </div>
    </div>
  </div>
</section>
