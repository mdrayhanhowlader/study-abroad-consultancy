<?php /** @var array $site */ ?>
<?php $appScopePath = rtrim(url(''), '/') . '/'; ?>
<script>
(() => {
  const appScope = new URL(<?= json_encode($appScopePath, JSON_UNESCAPED_SLASHES) ?>, location.origin).href;
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.getRegistrations().then((registrations) =>
      Promise.all(registrations
        .filter((registration) => registration.scope.startsWith(appScope))
        .map((registration) => registration.unregister()))
    );
  }
  if ('caches' in window) {
    caches.keys().then((keys) =>
      Promise.all(keys
        .filter((key) => key.startsWith('manami-'))
        .map((key) => caches.delete(key)))
    );
  }
})();
</script>
<div class="bg-ink text-white text-xs relative z-30" style="background:#1E242B">
  <div class="mx-auto max-w-7xl px-4 lg:px-6 flex items-center justify-between h-9">
    <div class="hidden sm:flex items-center gap-5">
      <a href="tel:<?= h($site['contact']['phone_tel']) ?>" class="topbar-link flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.6 10.8c1.4 2.8 3.8 5.2 6.6 6.6l2.2-2.2c.3-.3.7-.4 1.1-.3 1.2.4 2.5.6 3.8.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.4.2 2.6.6 3.8.1.4 0 .8-.3 1.1l-2.2 1.9z"/></svg>
        <span><?= h($site['contact']['phone']) ?></span>
      </a>
      <a href="mailto:<?= h($site['contact']['email']) ?>" class="topbar-link hidden md:flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2zm0 4 8 5 8-5V6l-8 5-8-5v2z"/></svg>
        <span><?= h($site['contact']['email']) ?></span>
      </a>
    </div>
    <div class="flex items-center gap-3 ml-auto">
      <a href="<?= h(url('login.php')) ?>" class="topbar-link hidden md:inline-flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 12l2 2 4-4M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        <span>Track order</span>
      </a>
      <span class="hidden md:inline-block w-px h-4 bg-white/20"></span>
      <span class="hidden md:inline text-white/50 text-[11px] tracking-widest uppercase"><?= h(t('topbar.follow_us')) ?></span>
      <?php foreach ($site['social'] as $s): ?>
        <a href="<?= h($s['href']) ?>" aria-label="<?= h($s['label']) ?>" class="topbar-link inline-flex">
          <?= icon($s['icon'], 'w-3.5 h-3.5') ?>
        </a>
      <?php endforeach; ?>
      <span class="hidden md:inline-block w-px h-4 bg-white/20 mx-1"></span>
      <?php $variant = 'top'; require __DIR__ . '/lang-switcher.php'; ?>
    </div>
  </div>
</div>
