<?php /** @var array $testimonials */ /** @var array $site */ ?>
<section id="testimonials" class="testimonials-section relative overflow-hidden bg-[#f7f5f0] py-20 sm:py-24 lg:py-28">
  <div class="pointer-events-none absolute -right-32 top-0 h-96 w-96 rounded-full bg-gold/10 blur-3xl" aria-hidden="true"></div>
  <div class="relative mx-auto max-w-7xl px-4 lg:px-6">
    <div class="mb-12 flex flex-col gap-6 lg:mb-14 lg:flex-row lg:items-end lg:justify-between">
      <div class="max-w-2xl">
        <div class="eyebrow" data-num="07"><?= h(App\Homepage::text('testimonials.eyebrow', t('testimonials.eyebrow'))) ?></div>
        <h2 class="mt-5 font-display text-4xl font-bold leading-tight text-ink sm:text-5xl"><?= h(App\Homepage::text('testimonials.headline', t('testimonials.headline'))) ?></h2>
        <p class="mt-4 max-w-xl leading-7 text-ink-2"><?= h(App\Homepage::text('testimonials.body', t('testimonials.body'))) ?></p>
      </div>
      <a href="<?= h($site['contact']['address_link']) ?>" target="_blank" rel="noopener noreferrer" class="inline-flex w-fit items-center gap-3 rounded-full border border-hair bg-white px-5 py-3 text-sm font-semibold text-ink shadow-sm transition hover:-translate-y-0.5 hover:border-gold/60 hover:shadow-md">
        <span class="grid h-9 w-9 place-items-center rounded-full bg-[#f7f5f0] text-[#4285f4] font-bold">G</span>
        <span>Read our Google reviews</span>
        <span class="text-ink-3" aria-hidden="true">↗</span>
      </a>
    </div>

    <?php if (!$testimonials): ?>
      <div class="rounded-3xl border border-hair bg-white px-6 py-14 text-center text-sm text-ink-3"><?= h(t('blog.empty')) ?></div>
    <?php else: ?>
      <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        <?php foreach ($testimonials as $index => $review):
          $name = trim((string) ($review['name'] ?? ''));
          $initial = $name !== '' ? mb_strtoupper(mb_substr($name, 0, 1)) : 'S';
          $rating = max(0, min(5, (int) ($review['rating'] ?? 0)));
          $isGoogle = ($review['source'] ?? '') === 'google';
        ?>
          <article class="group flex min-h-64 flex-col rounded-2xl border border-[#e8e4dc] bg-white p-6 shadow-[0_16px_44px_-36px_rgba(30,36,43,0.5)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_54px_-34px_rgba(30,36,43,0.35)] sm:p-7">
            <div class="flex items-start justify-between gap-4">
              <div class="flex gap-1 text-[#c49b43]" aria-label="<?= $rating ?> out of 5 stars">
                <?php for ($star = 1; $star <= 5; $star++): ?>
                  <svg class="h-4 w-4 <?= $star <= $rating ? '' : 'text-gray-200' ?>" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="m10 1.7 2.45 4.96 5.47.8-3.96 3.86.94 5.45L10 14.2l-4.9 2.58.94-5.45L2.08 7.46l5.47-.8L10 1.7Z"/></svg>
                <?php endfor; ?>
              </div>
              <?php if ($isGoogle): ?>
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#f7f5f0] font-bold text-[#4285f4]" aria-label="Google review">G</span>
              <?php else: ?>
                <span class="rounded-full bg-brand/5 px-3 py-2 text-[9px] font-bold uppercase tracking-[0.14em] text-brand">Student</span>
              <?php endif; ?>
            </div>

            <blockquote class="mt-6 flex-1 text-[15px] leading-7 text-ink-2">
              <span class="mb-1 block font-display text-4xl leading-none text-gold/70" aria-hidden="true">“</span>
              <?= h($review['quote'] ?? '') ?>
            </blockquote>

            <div class="mt-6 flex items-center gap-3 border-t border-[#eeeae3] pt-5">
              <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#f2efe8] font-display text-lg font-semibold text-brand"><?= h($initial) ?></span>
              <div class="min-w-0">
                <div class="truncate text-sm font-semibold text-ink"><?= h($name ?: 'Student') ?></div>
                <?php if ($isGoogle): ?>
                  <a href="<?= h($site['contact']['address_link']) ?>" target="_blank" rel="noopener noreferrer" class="mt-1 inline-flex items-center gap-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-ink-3 transition hover:text-brand">
                    <?= h($review['role'] ?: 'Google review') ?> <span aria-hidden="true">↗</span>
                  </a>
                <?php else: ?>
                  <div class="mt-1 truncate text-[10px] font-semibold uppercase tracking-[0.12em] text-ink-3"><?= h($review['role'] ?: 'Student') ?></div>
                <?php endif; ?>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="mt-10 text-center">
      <a href="#contact" class="btn btn-primary"><?= h(App\Homepage::text('testimonials.cta', t('testimonials.cta'))) ?> <span aria-hidden="true">→</span></a>
    </div>
  </div>
</section>
