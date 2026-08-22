<?php
$role = App\Auth::user()['role'] ?? 'viewer';

/* Compute admin base path so links work from any admin subdirectory.
   We detect depth by counting slashes in the current script path. */
$adminBase = '';
$scriptPath = $_SERVER['SCRIPT_NAME'] ?? '/admin/dashboard.php';
$relPath = preg_replace('#^.*?/admin/#', '', $scriptPath) ?? '';
$depth = substr_count($relPath, '/');
if ($depth > 0) {
    $adminBase = str_repeat('../', $depth);
}

$nav = [
    ['key' => 'dashboard', 'label' => 'Dashboard',  'href' => $adminBase . 'dashboard.php',  'roles' => ['admin','editor','viewer'], 'icon' => 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z'],
    ['key' => 'orders',    'label' => 'Orders',     'href' => $adminBase . 'orders/index.php', 'roles' => ['admin','editor','viewer'], 'icon' => 'M3 6h2l1 12h12l1-12h2M9 10v4M15 10v4M5 6l1-3h12l1 3'],
    ['key' => 'leads',     'label' => 'Leads',      'href' => $adminBase . 'leads/index.php',  'roles' => ['admin','editor'], 'icon' => 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75'],
    ['key' => 'ssw',       'label' => 'SSW Pipeline','href' => $adminBase . 'ssw/index.php',    'roles' => ['admin','editor'], 'icon' => 'M22 12h-4l-3 9L9 3l-3 9H2'],
    ['key' => 'products',  'label' => 'Products',   'href' => $adminBase . 'products/index.php', 'roles' => ['admin','editor','viewer'], 'icon' => 'M4 4h16v4H4zM4 10h16v10H4zM8 14h4'],
    ['key' => 'services',  'label' => 'Services',   'href' => $adminBase . 'content/services.php',  'roles' => ['admin','editor','viewer'], 'icon' => 'M4 6h16M4 12h16M4 18h10'],
    ['key' => 'countries', 'label' => 'Countries',  'href' => $adminBase . 'content/countries.php', 'roles' => ['admin','editor','viewer'], 'icon' => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm-1 17.9A8 8 0 0 1 4.1 13H7c.1 2.3.6 4.5 1.4 6.4.5 1 .9 1.7 1.2 2.1.4.5 1 .6 1.4.4zM4.1 11H7c.1-2.3.6-4.5 1.4-6.4.5-1 .9-1.7 1.2-2.1.4-.5 1-.6 1.4-.4A8 8 0 0 0 4.1 11zM12 4c.5 0 1.4 1 2.2 3.1.4 1.2.8 2.5.9 3.9H8.9c.1-1.4.5-2.7.9-3.9C10.6 5 11.5 4 12 4zm0 16c-.5 0-1.4-1-2.2-3.1-.4-1.2-.8-2.5-.9-3.9h6.2c-.1 1.4-.5 2.7-.9 3.9C13.4 19 12.5 20 12 20z'],
    ['key' => 'faqs',      'label' => 'FAQs',       'href' => $adminBase . 'content/faqs.php',  'roles' => ['admin','editor','viewer'], 'icon' => 'M4 4h16v12H5l-1 4zM8 8h8M8 12h5'],
    ['key' => 'posts',     'label' => 'Blog & News','href' => $adminBase . 'content/posts.php', 'roles' => ['admin','editor','viewer'], 'icon' => 'M4 4h16v16H4zM8 8h8M8 12h8M8 16h5'],
    ['key' => 'banners',   'label' => 'Banners',    'href' => $adminBase . 'content/banners.php', 'roles' => ['admin','editor','viewer'], 'icon' => 'M3 5h18v14H3zM3 9h18M7 13l3 4 4-6 5 8'],
    ['key' => 'testimonials','label' => 'Testimonials','href' => $adminBase . 'content/testimonials.php', 'roles' => ['admin','editor','viewer'], 'icon' => 'M7 7h4v4H8c0 2 .5 3 1.5 4L8 16c-2-1.5-3-3.5-3-6V7zm9 0h4v4h-3c0 2 .5 3 1.5 4L17 16c-2-1.5-3-3.5-3-6V7z'],
    ['key' => 'media',     'label' => 'Media',      'href' => $adminBase . 'content/media.php',  'roles' => ['admin','editor','viewer'], 'icon' => 'M4 4h16v16H4zM4 16l5-5 4 4 3-3 4 4'],
    ['key' => 'users',     'label' => 'Users',      'href' => $adminBase . 'users/index.php',   'roles' => ['admin'], 'icon' => 'M16 11a4 4 0 1 0-4-4 4 4 0 0 0 4 4zM8 13a4 4 0 1 0-4-4 4 4 0 0 0 4 4zm0 2c-2.7 0-8 1.3-8 4v3h11v-3a5 5 0 0 1 3.5-4.7A14 14 0 0 0 8 15zm10 0a14 14 0 0 0-3 .3A6 6 0 0 1 19 20v2h5v-3c0-2.7-5.3-4-6-4z'],
    ['key' => 'activity',  'label' => 'Activity',   'href' => $adminBase . 'activity.php',      'roles' => ['admin','editor'], 'icon' => 'M12 8v4l3 3M3 12a9 9 0 1 0 18 0 9 9 0 0 0-18 0z'],
    ['key' => 'settings',  'label' => 'Settings',   'href' => $adminBase . 'settings.php',      'roles' => ['admin'], 'icon' => 'M12 8a4 4 0 1 0 4 4 4 4 0 0 0-4-4zm9 4a9 9 0 0 0-.3-2.3l2-1.6-2-3.4-2.4 1a9 9 0 0 0-4-2.3L14 1h-4l-.3 2.4a9 9 0 0 0-4 2.3l-2.4-1-2 3.4 2 1.6A9 9 0 0 0 3 12a9 9 0 0 0 .3 2.3l-2 1.6 2 3.4 2.4-1a9 9 0 0 0 4 2.3L10 23h4l.3-2.4a9 9 0 0 0 4-2.3l2.4 1 2-3.4-2-1.6A9 9 0 0 0 21 12z'],
];
$visible = array_filter($nav, fn($n) => in_array($role, $n['roles'], true));
?>
<aside :class="sideOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed lg:static lg:translate-x-0 z-40 w-64 h-screen bg-white border-r border-gray-200 flex flex-col transition-transform">
  <div class="px-5 h-16 flex items-center gap-2 border-b border-gray-200">
    <?php if (!empty($site['brand']['logo_url'])): ?>
      <img src="<?= h($site['brand']['logo_url']) ?>" alt="<?= h($site['brand']['full_name']) ?>" class="h-8 w-auto max-w-[140px] object-contain">
    <?php else: ?>
      <span class="w-9 h-9 rounded-md bg-navy text-white grid place-items-center font-bold">M</span>
      <div class="leading-tight">
        <div class="font-display text-lg font-bold text-navy"><?= h($site['brand']['name']) ?></div>
        <div class="text-[10px] tracking-[0.25em] text-accent font-semibold uppercase">Admin · <?= h($site['brand']['tagline']) ?></div>
      </div>
    <?php endif; ?>
  </div>

  <nav class="flex-1 overflow-y-auto py-3">
    <?php foreach ($visible as $n): ?>
      <a href="<?= h($n['href']) ?>"
         class="flex items-center gap-3 px-5 py-2.5 text-sm <?= ($adminPage === $n['key']) ? 'bg-brand/10 text-brand font-semibold border-l-4 border-brand' : 'text-gray-600 hover:bg-gray-50 hover:text-brand' ?>">
        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="<?= h($n['icon']) ?>"/></svg>
        <span><?= h($n['label']) ?></span>
      </a>
    <?php endforeach; ?>
  </nav>

  <div class="border-t border-gray-200 p-3">
    <a href="<?= $adminBase ?>../index.php" target="_blank" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-500 hover:text-brand">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3"/></svg>
      View public site
    </a>
    <a href="<?= $adminBase ?>logout.php" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-500 hover:text-accent">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
      Sign out
    </a>
  </div>
</aside>
<style>
  /* Mobile sidebar overlay */
  @media (max-width: 1023px) {
    aside.fixed { box-shadow: 4px 0 24px rgba(0,0,0,0.15); }
  }
  /* Touch-friendly nav items */
  aside a { min-height: 44px; -webkit-tap-highlight-color: transparent; }
  /* Smooth sidebar transitions */
  aside { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
</style>
