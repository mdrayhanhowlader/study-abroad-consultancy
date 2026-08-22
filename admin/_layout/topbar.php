<?php $u = App\Auth::user(); ?>
<header class="bg-white border-b border-gray-200 h-16 flex items-center px-4 lg:px-8 sticky top-0 z-30">
  <button @click="sideOpen = !sideOpen" class="lg:hidden p-2 -ml-2 text-gray-700" aria-label="Open menu">
    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
  </button>

  <h1 class="text-lg font-semibold text-ink ml-2 lg:ml-0"><?= h($pageTitle ?? 'Dashboard') ?></h1>

  <div class="ml-auto flex items-center gap-3">
    <?php if (!empty($u['must_change_pw'])): ?>
      <a href="<?= h($adminBase) ?>settings.php?tab=password" class="hidden md:inline-flex items-center gap-2 text-xs bg-accent/10 text-accent border border-accent/30 px-3 py-1.5 rounded-full">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01M10.3 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.41 0z"/></svg>
        Change default password
      </a>
    <?php endif; ?>
    <div class="text-right hidden sm:block">
      <div class="text-sm font-semibold text-ink"><?= h($u['name']) ?></div>
      <div class="text-xs text-gray-500"><?= h(ucfirst($u['role'])) ?></div>
    </div>
    <div class="w-9 h-9 rounded-full bg-navy text-white grid place-items-center font-semibold text-sm"><?= strtoupper(substr($u['name'], 0, 1)) ?></div>
  </div>
</header>
