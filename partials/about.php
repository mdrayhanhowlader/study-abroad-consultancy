<?php /** @var array $site */ ?>
<section id="about" class="section bg-paper relative overflow-hidden">
  <div class="mx-auto max-w-7xl px-4 lg:px-6 grid lg:grid-cols-2 gap-14 lg:gap-20 items-center">
    <!-- Image collage -->
    <div class="relative reveal order-2 lg:order-1">
      <div class="grid grid-cols-12 gap-4 items-end">
        <div class="col-span-7 space-y-4">
          <div class="aspect-[4/5] rounded-3xl overflow-hidden shadow-xl">
            <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=700&auto=format&fit=crop&q=70" alt="<?= h(t('about.headline')) ?>" class="w-full h-full object-cover" loading="lazy">
          </div>
        </div>
        <div class="col-span-5 space-y-4 pb-8">
          <div class="aspect-[3/4] rounded-3xl overflow-hidden shadow-lg">
            <img src="https://images.unsplash.com/photo-1531545514256-b1400bc00f31?w=600&auto=format&fit=crop&q=70" alt="" class="w-full h-full object-cover" loading="lazy">
          </div>
          <div class="aspect-square rounded-3xl bg-navy text-white grid place-items-center text-center p-5 shadow-xl" style="background:#1A2B6D">
            <div>
              <div class="font-display text-5xl font-bold">5+</div>
              <div class="mt-2 text-[10px] tracking-[0.2em] uppercase leading-relaxed"><?= h(t('about.years_label')) ?><br><?= h(t('about.years_sub')) ?></div>
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
      <div class="absolute -top-5 -left-5 w-24 h-24 border-2 border-gold/40 rounded-full -z-10" style="border-color:#B8923B66"></div>
      <div class="absolute top-1/2 -right-8 w-16 h-16 bg-brand/10 rounded-full -z-10"></div>
    </div>

    <!-- Content -->
    <div class="reveal reveal-d2 order-1 lg:order-2">
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand/10 text-brand text-xs font-semibold tracking-wider uppercase">
        <span class="w-1.5 h-1.5 rounded-full bg-brand"></span>
        <?= h(t('about.eyebrow')) ?>
      </div>
      <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-[1.05] text-ink tracking-tight">
        <?= h(t('about.headline')) ?>
      </h2>
      <p class="mt-3 font-display italic text-brand text-lg">— <?= h(t('about.slogan')) ?></p>
      <p class="mt-6 text-lg text-ink-2 leading-relaxed">
        <?= h(t('about.body')) ?>
      </p>

      <div class="mt-8 grid sm:grid-cols-2 gap-4">
        <?php foreach (['point_1','point_2','point_3','point_4'] as $key): ?>
          <div class="flex items-start gap-3 p-4 bg-white rounded-2xl border border-hair shadow-sm" style="border-color:#E5E1D9">
            <span class="w-6 h-6 rounded-full bg-brand/10 text-brand grid place-items-center shrink-0 mt-0.5">
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg>
            </span>
            <span class="text-sm text-ink leading-snug"><?= h(t('about.' . $key)) ?></span>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="mt-10 flex flex-wrap items-center gap-4">
        <a href="#services" class="btn btn-primary px-6 py-3"><?= h(t('about.cta_primary')) ?> →</a>
        <a href="#contact" class="text-sm font-semibold text-ink hover:text-brand transition"><?= h(t('about.cta_secondary')) ?></a>
      </div>
    </div>
  </div>
</section>
