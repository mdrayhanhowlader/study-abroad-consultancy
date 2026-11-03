<?php /** @var array $site */ ?>
<footer class="desktop-site-footer relative overflow-hidden">
  <div class="footer-inner mx-auto max-w-7xl px-4 lg:px-6 pt-10 lg:pt-14 pb-8">
    <div class="footer-contact-strip mb-12 flex flex-col gap-5 rounded-3xl p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8 lg:p-10">
      <div class="max-w-2xl">
        <span class="footer-contact-eyebrow"><?= h(t('footer.call_us')) ?></span>
        <h2 class="footer-contact-title mt-2 font-display text-2xl font-bold sm:text-3xl"><?= h(t('cta_form.headline')) ?></h2>
        <p class="footer-contact-copy mt-2 text-sm leading-relaxed"><?= h(t('success_section.body')) ?></p>
      </div>
      <div class="flex shrink-0 flex-wrap gap-3">
        <a href="<?= h(url('index.php#contact')) ?>" class="footer-contact-action footer-contact-action--primary inline-flex items-center justify-center rounded-full px-5 py-3 text-sm font-bold"><?= h(t('hero.cta_secondary')) ?> <span class="ml-2" aria-hidden="true">→</span></a>
        <a href="tel:<?= h($site['contact']['phone_tel']) ?>" class="footer-contact-action footer-contact-action--secondary inline-flex items-center justify-center rounded-full px-5 py-3 text-sm font-semibold"><?= h($site['contact']['phone']) ?></a>
      </div>
    </div>

    <div class="footer-main">
      <div class="footer-brand">
        <div class="inline-flex rounded-2xl bg-white px-4 py-2.5">
          <?php require __DIR__ . '/_logo.php'; ?>
        </div>
        <p class="footer-description"><?= h(App\Homepage::text('footer.description', t('footer.description'))) ?></p>
        <div class="footer-socials">
          <?php foreach ($site['social'] as $s): ?>
            <a href="<?= h($s['href']) ?>" aria-label="<?= h($s['label']) ?>" class="footer-social-link grid h-10 w-10 place-items-center rounded-full transition">
              <?= icon($s['icon'], 'w-4 h-4') ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="footer-contact-list">
        <a href="<?= h(url('batch-schedule.php')) ?>" class="footer-contact-item footer-schedule-link">
          <span class="footer-schedule-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="5" width="18" height="16" rx="2"/>
              <path d="M16 3v4M8 3v4M3 10h18"/>
              <path d="m8 15 2.2 2L16 12"/>
            </svg>
          </span>
          <span class="footer-schedule-copy">
            <span class="footer-contact-label">Student resources</span>
            <strong>Class schedules & holidays</strong>
            <span>Check class days and course dates by batch.</span>
            <span class="footer-schedule-action">View batch calendar <span aria-hidden="true">↗</span></span>
          </span>
          <span class="footer-schedule-arrow" aria-hidden="true">↗</span>
        </a>
        <a href="<?= h($site['contact']['address_link']) ?>" target="_blank" rel="noopener" class="footer-contact-item">
          <span class="footer-contact-label"><?= h(t('footer.office_address')) ?></span>
          <span class="footer-contact-value"><?= h($site['contact']['address']) ?></span>
        </a>
        <a href="tel:<?= h($site['contact']['phone_tel']) ?>" class="footer-contact-item">
          <span class="footer-contact-label"><?= h(t('footer.call_us')) ?></span>
          <span class="footer-contact-value"><?= h($site['contact']['phone']) ?></span>
        </a>
        <a href="mailto:<?= h($site['contact']['email']) ?>" class="footer-contact-item">
          <span class="footer-contact-label"><?= h(t('footer.email_us')) ?></span>
          <span class="footer-contact-value"><?= h($site['contact']['email']) ?></span>
        </a>
      </div>
    </div>

    <div class="footer-legal mt-12 flex flex-col items-center justify-between gap-3 border-t border-white/15 pt-6 text-xs text-white/50 md:flex-row">
      <span>© <?= date('Y') ?> <?= h($site['brand']['full_name']) ?>. <?= h(t('common.all_rights_reserved')) ?></span>
      <div class="font-display italic text-white/75"><?= h($site['brand']['slogan']) ?></div>
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
