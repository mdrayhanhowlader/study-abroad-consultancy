<?php /** @var array $site */ ?>
<footer class="bg-paper-2 text-ink hairline-top">
  <div class="mx-auto max-w-7xl px-4 lg:px-6 pt-16 lg:pt-20 pb-10">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-12 gap-10 lg:gap-12">
      <div class="col-span-2 lg:col-span-4">
        <?php require __DIR__ . '/_logo.php'; ?>
        <p class="mt-5 text-sm text-ink-2 leading-relaxed"><?= h(t('footer.description')) ?></p>
        <p class="mt-4 text-sm">
          <span class="text-ink-3">E-mail:</span>
          <a href="mailto:<?= h($site['contact']['email']) ?>" class="hover:text-brand ml-1 break-all"><?= h($site['contact']['email']) ?></a>
        </p>
        <div class="flex items-center gap-2 mt-5">
          <?php foreach ($site['social'] as $s): ?>
            <a href="<?= h($s['href']) ?>" aria-label="<?= h($s['label']) ?>" class="w-9 h-9 grid place-items-center border border-hair hover:border-ink hover:text-brand transition">
              <?= icon($s['icon'], 'w-4 h-4') ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="lg:col-span-2">
        <h4 class="text-[10px] font-semibold tracking-[0.25em] uppercase text-ink-3">Pages</h4>
        <ul class="mt-5 space-y-2.5 text-sm text-ink-2">
          <li><a href="<?= h(url('about.php')) ?>" class="hover:text-brand"><?= h(t('footer.about_manami')) ?></a></li>
          <li><a href="<?= h(url('services.php')) ?>" class="hover:text-brand"><?= h(t('footer.study_in_japan')) ?></a></li>
          <li><a href="<?= h(url('blog.php')) ?>" class="hover:text-brand"><?= h(t('nav.blog')) ?></a></li>
          <li><a href="<?= h(url('events.php')) ?>" class="hover:text-brand"><?= h(t('nav.events')) ?></a></li>
          <li><a href="<?= h(url('faq.php')) ?>" class="hover:text-brand"><?= h(t('nav.faq')) ?></a></li>
          <li><a href="<?= h(url('contact.php')) ?>" class="hover:text-brand"><?= h(t('nav.contact')) ?></a></li>
        </ul>
      </div>

      <div class="lg:col-span-2">
        <h4 class="text-[10px] font-semibold tracking-[0.25em] uppercase text-ink-3"><?= h(t('footer.study_in_japan')) ?></h4>
        <ul class="mt-5 space-y-2.5 text-sm text-ink-2">
          <li><a href="<?= h(url('services.php#student-visa')) ?>" class="hover:text-brand"><?= h(t('footer.japan_student')) ?></a></li>
          <li><a href="<?= h(url('services.php#ssw')) ?>" class="hover:text-brand"><?= h(t('footer.japan_ssw')) ?></a></li>
          <li><a href="<?= h(url('services.php#titp')) ?>" class="hover:text-brand"><?= h(t('footer.japan_titp')) ?></a></li>
          <li><a href="<?= h(url('services.php#japanese')) ?>" class="hover:text-brand"><?= h(t('footer.japan_jlpt')) ?></a></li>
          <li><a href="<?= h(url('books.php')) ?>" class="hover:text-brand"><?= h(t('footer.books_courses')) ?></a></li>
        </ul>
      </div>

      <div class="lg:col-span-2">
        <h4 class="text-[10px] font-semibold tracking-[0.25em] uppercase text-ink-3"><?= h(t('footer.future_destinations')) ?></h4>
        <ul class="mt-5 space-y-2.5 text-sm text-ink-2">
          <li><a href="<?= h(url('country/japan')) ?>" class="hover:text-brand"><?= h(t('footer.dest_jp')) ?></a></li>
          <li><a href="<?= h(url('country/australia')) ?>" class="hover:text-brand"><?= h(t('footer.dest_au')) ?></a></li>
          <li><a href="<?= h(url('country/malaysia')) ?>" class="hover:text-brand"><?= h(t('footer.dest_my')) ?></a></li>
          <li><a href="<?= h(url('country/south-korea')) ?>" class="hover:text-brand"><?= h(t('footer.dest_kr')) ?></a></li>
        </ul>
      </div>

      <div class="col-span-2 md:col-span-1 lg:col-span-2">
        <h4 class="text-[10px] font-semibold tracking-[0.25em] uppercase text-ink-3"><?= h(t('footer.office_address')) ?></h4>
        <a href="<?= h($site['contact']['address_link']) ?>" target="_blank" rel="noopener" class="mt-5 block text-sm text-ink-2 hover:text-brand leading-relaxed">
          <?= h($site['contact']['address']) ?>
        </a>
        <div class="mt-5 space-y-3 text-sm">
          <div>
            <div class="text-[10px] uppercase tracking-widest text-ink-3 mb-1"><?= h(t('footer.call_us')) ?></div>
            <a href="tel:<?= h($site['contact']['phone_tel']) ?>" class="hover:text-brand"><?= h($site['contact']['phone']) ?></a>
          </div>
          <div>
            <div class="text-[10px] uppercase tracking-widest text-ink-3 mb-1"><?= h(t('footer.email_us')) ?></div>
            <a href="mailto:<?= h($site['contact']['email']) ?>" class="hover:text-brand break-all"><?= h($site['contact']['email']) ?></a>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-14 pt-6 border-t hairline flex flex-col md:flex-row items-center justify-between gap-3 text-xs text-ink-3">
      <div class="flex items-center gap-4 flex-wrap justify-center md:justify-start">
        <span>© <?= date('Y') ?> <?= h($site['brand']['full_name']) ?>. <?= h(t('common.all_rights_reserved')) ?></span>
        <a href="<?= h(url('privacy.php')) ?>" class="hover:text-ink">Privacy</a>
        <a href="<?= h(url('terms.php')) ?>" class="hover:text-ink">Terms</a>
        <a href="<?= h(url('login.php')) ?>" class="hover:text-ink">Track order</a>
        <a href="<?= h(url('admin/login.php')) ?>" class="hover:text-ink opacity-60">Admin</a>
      </div>
      <div class="font-display italic"><?= h($site['brand']['slogan']) ?></div>
    </div>
  </div>
</footer>

<!-- Mobile Bottom Navigation (App-like) -->
<nav class="mobile-bottom-nav" aria-label="Mobile navigation">
  <a href="<?= h(url('index.php')) ?>" class="<?= basename($_SERVER['SCRIPT_NAME'] ?? '') === 'index.php' ? 'active' : '' ?>">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
    <span><?= h(t('mobile_nav.home')) ?></span>
  </a>
  <a href="<?= h(url('services.php')) ?>" class="<?= strpos($_SERVER['SCRIPT_NAME'] ?? '', 'services') !== false || strpos($_SERVER['SCRIPT_NAME'] ?? '', 'country') !== false ? 'active' : '' ?>">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
    <span><?= h(t('mobile_nav.study_abroad')) ?></span>
  </a>
  <a href="<?= h(url('books.php')) ?>" class="<?= strpos($_SERVER['SCRIPT_NAME'] ?? '', 'books') !== false || strpos($_SERVER['SCRIPT_NAME'] ?? '', 'courses') !== false ? 'active' : '' ?>">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
    <span><?= h(t('mobile_nav.shop')) ?></span>
  </a>
  <a href="<?= h(url('contact.php')) ?>" class="<?= strpos($_SERVER['SCRIPT_NAME'] ?? '', 'contact') !== false ? 'active' : '' ?>">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
    <span><?= h(t('mobile_nav.contact')) ?></span>
  </a>
</nav>

