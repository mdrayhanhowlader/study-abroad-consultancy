<?php /** @var array $site */ ?>
<?php require_once __DIR__ . '/pixel.php'; ?>
<header x-data="{ open: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 10"
        class="site-header sticky top-0 z-40 transition-all duration-300 bg-white/95 backdrop-blur-md border-b shadow-sm"
        :class="scrolled ? 'border-hair shadow-md' : 'border-transparent shadow-sm'"
        style="--hair:#E5E1D9">

  <div class="site-header-inner mx-auto max-w-7xl px-4 lg:px-6 h-16 xl:h-20 flex items-center justify-between gap-4">
    <!-- Logo -->
    <?php require __DIR__ . '/_logo.php'; ?>

    <!-- Desktop Nav -->
    <nav class="hidden xl:flex items-center gap-1 flex-1 justify-center">
      <?php foreach ($site['nav'] as $item): ?>
        <?php if (!empty($item['children'])): ?>
          <?php $isMega = $item['key'] === 'study_abroad'; ?>
          <div x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false" class="relative">
            <button class="nav-link" :class="open && 'active'">
              <?= h(t('nav.' . $item['key'])) ?>
              <svg class="w-3.5 h-3.5 opacity-60 transition-transform" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-transition.opacity.duration.150ms
                 class="absolute top-full pt-3 z-50 left-1/2 -translate-x-1/2 <?= $isMega ? 'w-[32rem]' : 'w-60' ?>"
                 x-cloak>
              <div class="bg-white border border-hair shadow-2xl rounded-2xl overflow-hidden" style="border-color:#E5E1D9">
                <?php if ($isMega): ?>
                  <div class="grid grid-cols-2 divide-x divide-hair" style="--tw-divide-opacity:1;border-color:#E5E1D9">
                    <?php
                      $sections = [];
                      $current = [];
                      foreach ($item['children'] as $c) {
                        if (($c['class'] ?? '') === 'section') {
                          if ($current) $sections[] = $current;
                          $current = ['title' => $c['key'], 'href' => $c['href'], 'items' => []];
                        } else {
                          $current['items'][] = $c;
                        }
                      }
                      if ($current) $sections[] = $current;
                    ?>
                    <?php foreach ($sections as $section): ?>
                      <div class="p-4">
                        <a href="<?= h(url($section['href'])) ?>" class="block px-3 pb-2 mb-3 text-[11px] font-bold tracking-widest text-ink-3 uppercase border-b border-hair hover:text-brand transition" style="border-color:#E5E1D9">
                          <?= h(t('nav.' . $section['title'])) ?>
                        </a>
                        <div class="space-y-1">
                          <?php foreach ($section['items'] as $c): ?>
                            <a href="<?= h(url($c['href'])) ?>" class="group flex items-center gap-3 px-3 py-2.5 text-sm text-ink hover:bg-paper-2 hover:text-brand rounded-xl transition">
                              <span class="w-8 h-8 rounded-lg bg-brand/10 text-brand grid place-items-center shrink-0 group-hover:bg-brand group-hover:text-white transition">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
                              </span>
                              <span class="leading-tight"><?= h(t('nav.' . $c['key'])) ?></span>
                            </a>
                          <?php endforeach; ?>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <div class="p-3 space-y-1">
                    <?php foreach ($item['children'] as $c): ?>
                      <a href="<?= h(url($c['href'])) ?>" class="group flex items-center gap-3 px-3 py-2.5 text-sm text-ink hover:bg-paper-2 hover:text-brand rounded-xl transition">
                        <span class="w-8 h-8 rounded-lg bg-brand/10 text-brand grid place-items-center shrink-0 group-hover:bg-brand group-hover:text-white transition">
                          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
                        </span>
                        <span class="leading-tight"><?= h(t('nav.' . $c['key'])) ?></span>
                      </a>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php else: ?>
          <a href="<?= h(url($item['href'])) ?>" class="nav-link"><?= h(t('nav.' . $item['key'])) ?></a>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>

    <!-- Right Actions -->
    <div class="flex items-center gap-2 shrink-0">
      <a href="<?= h(url('contact.php')) ?>" class="hidden md:inline-flex btn btn-primary text-sm px-4 py-2.5 shadow-lg shadow-brand/20">
        <?= h(t('nav.book_appointment')) ?>
      </a>
      <button @click="open = !open; document.body.style.overflow = open ? 'hidden' : ''"
              class="xl:hidden flex items-center justify-center w-10 h-10 rounded-xl bg-paper-2 text-ink hover:bg-brand hover:text-white transition-colors"
              :aria-label="open ? 'Close menu' : 'Open menu'" aria-controls="mobileMenu">
        <svg x-show="!open" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        <svg x-show="open" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
  </div>

  <!-- Mobile Drawer -->
  <div id="mobileMenu" x-show="open" x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0 -translate-y-2"
       x-transition:enter-end="opacity-100 translate-y-0"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="opacity-100 translate-y-0"
       x-transition:leave-end="opacity-0 -translate-y-2"
       class="xl:hidden fixed inset-x-0 top-16 bottom-0 z-30 bg-white overflow-y-auto"
       x-cloak @click.self="open=false; document.body.style.overflow=''">

    <!-- Quick Actions -->
    <div class="px-5 pt-5 pb-3">
      <div class="grid grid-cols-4 gap-2">
        <a href="tel:<?= h($site['contact']['phone_tel']) ?>" class="flex flex-col items-center gap-1 py-2 active:scale-95 transition-transform">
          <span class="w-11 h-11 rounded-xl bg-brand/10 grid place-items-center text-brand">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </span>
          <span class="text-[10px] font-medium text-ink-2">Call</span>
        </a>
        <a href="<?= h(url('books.php')) ?>" class="flex flex-col items-center gap-1 py-2 active:scale-95 transition-transform">
          <span class="w-11 h-11 rounded-xl bg-navy/10 grid place-items-center text-navy">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M4 3h12a3 3 0 0 1 3 3v15a1 1 0 0 1-1 1H7a3 3 0 0 1-3-3V3zm12 2H6v13a1 1 0 0 0 1 1h11V5zM8 7h7v2H8V7zm0 4h7v2H8v-2z"/></svg>
          </span>
          <span class="text-[10px] font-medium text-ink-2">Books</span>
        </a>
        <a href="<?= h(url('courses.php')) ?>" class="flex flex-col items-center gap-1 py-2 active:scale-95 transition-transform">
          <span class="w-11 h-11 rounded-xl bg-accent/10 grid place-items-center text-accent">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zM22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          </span>
          <span class="text-[10px] font-medium text-ink-2">Courses</span>
        </a>
        <a href="<?= h(url('faq.php')) ?>" class="flex flex-col items-center gap-1 py-2 active:scale-95 transition-transform">
          <span class="w-11 h-11 rounded-xl bg-gold/10 grid place-items-center" style="color:#B8923B">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01"/></svg>
          </span>
          <span class="text-[10px] font-medium text-ink-2">FAQ</span>
        </a>
      </div>
    </div>

    <div class="border-t border-hair mx-5" style="border-color:#E5E1D9"></div>

    <!-- Navigation Links -->
    <nav class="px-5 py-4 space-y-2">
      <?php foreach ($site['nav'] as $item): ?>
        <?php if (!empty($item['children'])): ?>
          <div x-data="{ o: false }" class="rounded-2xl overflow-hidden bg-paper-2/60" style="background:#F4F1EA99">
            <button @click="o = !o" class="w-full flex items-center justify-between px-4 py-3.5 text-sm font-semibold text-ink active:bg-paper-2 transition">
              <span><?= h(t('nav.' . $item['key'])) ?></span>
              <svg :class="o && 'rotate-180'" class="w-4 h-4 text-gray-400 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="o" x-transition class="pb-4 px-4 space-y-3" x-cloak>
              <?php
                $mobileSections = [];
                $cur = ['title' => null, 'href' => null, 'items' => []];
                foreach ($item['children'] as $c) {
                  if (($c['class'] ?? '') === 'section') {
                    if ($cur['items']) $mobileSections[] = $cur;
                    $cur = ['title' => $c['key'], 'href' => $c['href'], 'items' => []];
                  } else {
                    $cur['items'][] = $c;
                  }
                }
                if ($cur['items']) $mobileSections[] = $cur;
              ?>
              <?php foreach ($mobileSections as $section): ?>
                <div>
                  <?php if ($section['title']): ?>
                    <a href="<?= h(url($section['href'])) ?>" @click="open=false; document.body.style.overflow=''" class="block text-[10px] font-bold tracking-wider text-ink-3 uppercase mb-2 hover:text-brand transition">
                      <?= h(t('nav.' . $section['title'])) ?>
                    </a>
                  <?php endif; ?>
                  <div class="grid grid-cols-1 gap-1">
                    <?php foreach ($section['items'] as $c): ?>
                      <a href="<?= h(url($c['href'])) ?>" @click="open=false; document.body.style.overflow=''" class="flex items-center gap-3 py-2.5 pl-3 pr-2 text-sm text-ink-2 bg-white/60 hover:bg-white hover:text-brand rounded-xl transition">
                        <span class="w-6 h-6 rounded-lg bg-brand/10 text-brand grid place-items-center shrink-0">
                          <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
                        </span>
                        <?= h(t('nav.' . $c['key'])) ?>
                      </a>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php else: ?>
          <a href="<?= h(url($item['href'])) ?>" @click="open=false; document.body.style.overflow=''" class="flex items-center px-4 py-3.5 text-sm font-semibold text-ink bg-paper-2/60 hover:bg-paper-2 rounded-2xl transition" style="background:#F4F1EA99">
            <?= h(t('nav.' . $item['key'])) ?>
          </a>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>

    <!-- CTA Button -->
    <div class="px-5 pb-5">
      <a href="<?= h(url('contact.php')) ?>" @click="open=false; document.body.style.overflow=''" class="flex items-center justify-center gap-2 w-full btn btn-primary py-3.5 text-sm font-semibold shadow-lg shadow-brand/20">
        <?= h(t('nav.book_appointment')) ?> →
      </a>
    </div>

    <!-- Language Switcher -->
    <div class="px-5 pb-8 pt-2 border-t border-hair" style="border-color:#E5E1D9">
      <?php $variant = 'mobile'; require __DIR__ . '/lang-switcher.php'; ?>
    </div>
  </div>
</header>
<style>[x-cloak]{display:none!important}</style>
