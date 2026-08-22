<?php /** @var array $site */
$storiesData = require __DIR__ . '/../data/success_stories.php';
$section = $storiesData['section'];
$stories = $storiesData['stories'];
?>
<section class="py-20 lg:py-28 bg-paper-2 overflow-hidden relative" style="background:#F4F1EA">
  <div class="mx-auto max-w-7xl px-4 lg:px-6 relative">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-12 lg:mb-16">
      <span class="inline-block text-xs font-bold tracking-[0.25em] uppercase text-brand mb-3"><?= h($section['eyebrow']) ?></span>
      <h2 class="font-display text-3xl lg:text-5xl font-bold text-ink leading-tight"><?= h($section['headline']) ?></h2>
      <p class="mt-4 text-ink-2 leading-relaxed"><?= h($section['body']) ?></p>

      <!-- Animated counter -->
      <div class="mt-10 inline-flex flex-col items-center" x-data="{ count: 0, target: <?= (int)$section['total_count'] ?>, animate() { let interval = setInterval(() => { if (this.count < this.target) this.count += Math.ceil((this.target - this.count) / 20); else { this.count = this.target; clearInterval(interval); } }, 40); } }" x-init="let obs = new IntersectionObserver((entries) => { entries.forEach(e => { if (e.isIntersecting) { animate(); obs.disconnect(); } }); }, { threshold: 0.5 }); obs.observe($el);">
        <span class="text-5xl lg:text-7xl font-display font-extrabold text-brand tabular-nums" x-text="count.toLocaleString() + '+'"></span>
        <span class="text-sm font-semibold tracking-wider uppercase text-ink-3 mt-2"><?= h($section['total_label']) ?></span>
      </div>
    </div>

    <!-- Animated student photo wall -->
    <?php
      $carousel = count($stories) > 6;
      $displayStories = $carousel ? array_merge($stories, $stories) : $stories;
    ?>
    <div class="relative" x-data="{ open: false, active: null, items: <?= json_encode($stories) ?> }">
      <div class="relative overflow-hidden">
        <div class="flex gap-4 <?= $carousel ? 'student-carousel-track hover:pause' : 'flex-wrap justify-center' ?>">
          <?php foreach ($displayStories as $i => $story):
            $realIndex = $i % count($stories);
          ?>
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg cursor-pointer hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 shrink-0 <?= $carousel ? 'w-40 sm:w-44 lg:w-52' : 'w-[calc(50%-0.5rem)] sm:w-[calc(33.333%-0.75rem)] lg:w-[calc(20%-0.8rem)]' ?>"
                 @click="active = <?= $realIndex ?>; open = true">
              <div class="<?= $carousel ? 'aspect-[3/4]' : 'aspect-square' ?> overflow-hidden">
                <img src="<?= h($story['photo']) ?>" alt="<?= h($story['name']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
              </div>
              <div class="p-3">
                <div class="text-ink font-display font-bold text-sm leading-tight"><?= h($story['name']) ?></div>
                <div class="text-brand text-xs font-semibold mt-0.5"><?= h($story['destination']) ?></div>
                <div class="text-ink-3 text-[11px] mt-0.5 truncate"><?= h($story['program']) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
          <?php if (!$carousel): ?>
            <div class="w-[calc(50%-0.5rem)] sm:w-[calc(33.333%-0.75rem)] lg:w-[calc(20%-0.8rem)] aspect-square rounded-2xl bg-brand text-white grid place-items-center text-center p-4 shadow-lg">
              <div>
                <div class="text-2xl font-display font-bold"><?= count($stories) ?>+</div>
                <div class="text-[10px] uppercase tracking-wider mt-1">Success stories</div>
              </div>
            </div>
            <div class="w-[calc(50%-0.5rem)] sm:w-[calc(33.333%-0.75rem)] lg:w-[calc(20%-0.8rem)] aspect-square rounded-2xl bg-navy text-white grid place-items-center text-center p-4 shadow-lg">
              <div>
                <div class="text-2xl font-display font-bold">99%</div>
                <div class="text-[10px] uppercase tracking-wider mt-1">Visa success</div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Modal -->
      <div x-show="open" x-transition.opacity.duration.300ms class="fixed inset-0 z-50 bg-ink/80 backdrop-blur-sm p-4 flex items-center justify-center" x-cloak @click.self="open = false">
        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full overflow-hidden">
          <template x-if="active !== null">
            <div class="grid md:grid-cols-5 relative">
              <div class="md:col-span-2 aspect-square md:aspect-auto">
                <img :src="items[active].photo" :alt="items[active].name" class="w-full h-full object-cover">
              </div>
              <div class="md:col-span-3 p-8 flex flex-col justify-center">
                <button @click="open = false" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-ink/10 hover:bg-brand hover:text-white grid place-items-center text-ink transition">✕</button>
                <span class="text-xs font-bold tracking-widest uppercase text-brand" x-text="items[active].destination"></span>
                <h3 class="mt-2 font-display text-2xl font-bold text-ink" x-text="items[active].name"></h3>
                <p class="text-sm text-ink-2 mt-1" x-text="items[active].program + ' — ' + items[active].year"></p>
                <svg class="w-8 h-8 text-brand/30 mt-4 mb-2" viewBox="0 0 24 24" fill="currentColor"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                <p class="text-ink leading-relaxed italic" x-text="items[active].quote"></p>
                <div class="mt-6 flex gap-2">
                  <button @click="active = (active - 1 + items.length) % items.length" class="px-4 py-2 border border-hair rounded-lg text-sm hover:bg-paper-2 transition" style="border-color:#E5E1D9">Previous</button>
                  <button @click="active = (active + 1) % items.length" class="px-4 py-2 bg-brand text-white rounded-lg text-sm hover:bg-brand-dark transition">Next</button>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</section>
<style>
[x-cloak]{display:none!important}
.student-carousel-track {
  animation: studentScroll 40s linear infinite;
  width: max-content;
}
.student-carousel-track:hover,
.student-carousel-track.hover\:pause:hover {
  animation-play-state: paused;
}
@keyframes studentScroll {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}
</style>
