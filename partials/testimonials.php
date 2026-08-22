<?php /** @var array $testimonials */ /** @var array $site */ ?>
<section id="testimonials" class="section bg-paper">
  <div class="mx-auto max-w-5xl px-4 lg:px-6">
    <div class="text-center max-w-2xl mx-auto">
      <div class="eyebrow justify-center" data-num="07"><?= h(t('testimonials.eyebrow')) ?></div>
      <h2 class="mt-5 font-display text-4xl sm:text-5xl font-bold leading-tight text-ink"><?= h(t('testimonials.headline')) ?></h2>
      <p class="mt-4 text-ink-2"><?= h(t('testimonials.body')) ?></p>
    </div>

    <?php if (!$testimonials): ?>
      <div class="mt-12 text-center text-ink-3 text-sm"><?= h(t('blog.empty')) ?></div>
    <?php else: ?>
      <div class="mt-14 relative" x-data="{ idx: 0, count: <?= count($testimonials) ?> }" x-init="setInterval(() => idx = (idx + 1) % count, 7000)">
        <div class="overflow-hidden">
          <div class="flex transition-transform duration-700 ease-out" :style="`transform: translateX(-${idx * 100}%)`">
            <?php foreach ($testimonials as $t): ?>
              <div class="w-full shrink-0 px-2">
                <figure class="bg-white border border-hair p-10 sm:p-14 text-center relative" style="border-color:#E5E1D9">
                  <div class="quote-mark">”</div>
                  <svg class="w-10 h-10 text-gold mx-auto" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" style="color:#B8923B"><path d="M7 7h4v4H8c0 2 .5 3 1.5 4L8 16c-2-1.5-3-3.5-3-6V7zm9 0h4v4h-3c0 2 .5 3 1.5 4L17 16c-2-1.5-3-3.5-3-6V7z"/></svg>
                  <blockquote class="mt-6 font-display text-2xl sm:text-3xl leading-snug text-ink italic">
                    "<?= h($t['quote']) ?>"
                  </blockquote>
                  <div class="mt-6 flex items-center justify-center gap-1 star" style="color:#B8923B">
                    <?php for ($i=0; $i<$t['rating']; $i++): ?><span><?= icon('star', 'w-4 h-4') ?></span><?php endfor; ?>
                  </div>
                  <figcaption class="mt-5">
                    <div class="font-semibold text-ink"><?= h($t['name']) ?></div>
                    <div class="text-xs text-ink-3 tracking-widest uppercase mt-1"><?= h($t['role']) ?></div>
                  </figcaption>
                </figure>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="mt-6 flex items-center justify-center gap-2">
          <template x-for="i in count" :key="i">
            <button @click="idx = i - 1" :class="idx === i - 1 ? 'bg-ink w-8' : 'bg-ink-2/30 w-2.5'" class="h-2.5 rounded-full transition-all" style="background:#1E242B" :style="idx === i - 1 ? 'background:#1E242B;width:2rem' : 'background:#D7D2C7;width:0.625rem'"></button>
          </template>
        </div>
      </div>
    <?php endif; ?>

    <div class="text-center mt-10">
      <a href="#contact" class="btn btn-primary"><?= h(t('testimonials.cta')) ?> →</a>
    </div>
  </div>
</section>
