<?php
$chairman = [
    'name' => trim((string) App\Settings::get('chairman_name', '')),
    'title' => trim((string) App\Settings::get('chairman_title', '')),
    'bio' => trim((string) App\Settings::get('chairman_bio', '')),
    'quote' => trim((string) App\Settings::get('chairman_quote', '')),
    'label' => trim((string) App\Settings::get('chairman_label', '')),
    'photo' => App\Content::mediaUrl((int) App\Settings::get('chairman_photo_id')),
];
$chairmanVisible = App\Settings::get('chairman_visible', '1') !== '0';
$chairmanName = $chairman['name'] ?: 'Our Chairman';
?>
<?php if ($chairmanVisible): ?>
<section class="chairman-section relative isolate overflow-hidden bg-white py-16 text-ink sm:py-20 lg:py-24">
  <div class="absolute -left-36 -top-32 -z-10 h-96 w-96 rounded-full bg-brand/5 blur-3xl" aria-hidden="true"></div>
  <div class="absolute -bottom-48 right-0 -z-10 h-[30rem] w-[30rem] rounded-full bg-gold/10 blur-3xl" aria-hidden="true"></div>
  <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 sm:px-8 lg:grid-cols-[0.85fr_1.15fr] lg:gap-20 lg:px-10">
    <div class="relative mx-auto w-full max-w-md lg:mx-0">
      <div class="absolute -inset-3 rotate-3 rounded-[2rem] border border-gold/20 bg-paper-2/70" aria-hidden="true"></div>
      <div class="relative aspect-[4/5] overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-paper-2 via-white to-[#efe9dc] shadow-2xl shadow-ink/10 ring-1 ring-ink/10">
        <?php if ($chairman['photo']): ?>
          <img src="<?= h($chairman['photo']) ?>" alt="<?= h($chairmanName) ?>" class="h-full w-full object-cover" loading="lazy" decoding="async">
        <?php else: ?>
          <div class="absolute inset-0 grid place-items-center" aria-label="Chairman portrait placeholder">
            <svg viewBox="0 0 300 380" class="h-[82%] w-[82%] opacity-80" role="img" aria-hidden="true">
              <defs>
                <linearGradient id="chairmanPortrait" x1="0" x2="1" y1="0" y2="1">
                  <stop offset="0" stop-color="#d8ba72"/>
                  <stop offset="1" stop-color="#8d7135"/>
                </linearGradient>
              </defs>
              <circle cx="150" cy="111" r="63" fill="url(#chairmanPortrait)" opacity=".92"/>
              <path d="M37 353c7-88 51-139 113-139s106 51 113 139H37Z" fill="url(#chairmanPortrait)" opacity=".82"/>
              <path d="M92 223c15 22 35 33 58 33s43-11 58-33" fill="none" stroke="#fff" stroke-opacity=".42" stroke-width="3"/>
            </svg>
          </div>
        <?php endif; ?>
        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-ink/90 via-ink/55 to-transparent px-6 pb-6 pt-24 text-white">
          <div class="font-display text-2xl font-bold"><?= h($chairmanName) ?></div>
          <?php if ($chairman['title'] !== ''): ?>
            <div class="mt-1 text-sm text-white/80"><?= h($chairman['title']) ?></div>
          <?php elseif (!$chairman['photo']): ?>
            <div class="mt-1 text-xs uppercase tracking-[0.18em] text-white/75">Official portrait to be added</div>
          <?php endif; ?>
        </div>
      </div>
      <div class="absolute -bottom-5 -right-3 grid h-16 w-16 place-items-center rounded-2xl border border-gold/25 bg-white text-brand shadow-xl sm:-right-5" aria-hidden="true">
        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3 4 7v5c0 5 3.4 8 8 9 4.6-1 8-4 8-9V7l-8-4Z"/><path d="m9 12 2 2 4-4"/></svg>
      </div>
    </div>

    <div class="relative py-2 lg:py-8">
      <div class="inline-flex items-center gap-2 rounded-full border border-brand/10 bg-paper-2 px-4 py-2 text-[10px] font-bold uppercase tracking-[0.2em] text-brand">
        <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
        <?= h($chairman['label'] ?: 'Leadership & vision') ?>
      </div>
      <?php if ($chairman['quote'] !== ''): ?>
        <h2 class="mt-7 font-display text-3xl font-semibold leading-tight text-ink sm:text-4xl lg:text-5xl">
          “<?= h($chairman['quote']) ?>”
        </h2>
      <?php else: ?>
        <h2 class="mt-7 font-display text-3xl font-semibold leading-tight text-ink sm:text-4xl lg:text-5xl">
          A message from <?= h($chairmanName) ?>
        </h2>
      <?php endif; ?>
      <?php if ($chairman['bio'] !== ''): ?>
        <p class="mt-6 max-w-2xl whitespace-pre-line text-base leading-8 text-ink-2 sm:text-lg"><?= h($chairman['bio']) ?></p>
      <?php else: ?>
        <p class="mt-6 max-w-2xl text-base leading-8 text-ink-2 sm:text-lg">
          Guidance and support for students preparing to study and build a future in Japan.
        </p>
      <?php endif; ?>
      <div class="mt-8 flex items-center gap-3">
        <span class="h-px w-10 bg-gold"></span>
        <div>
          <div class="font-display font-bold"><?= h($chairmanName) ?></div>
          <?php if ($chairman['title'] !== ''): ?><div class="mt-0.5 text-xs uppercase tracking-wider text-ink-3"><?= h($chairman['title']) ?></div><?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>
