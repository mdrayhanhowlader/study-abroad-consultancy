<?php /** @var array $site */
$logoId  = $site['brand']['logo_image_id'] ?? null;
$logoUrl = $site['brand']['logo_url'] ?? ($logoId ? App\Content::mediaUrl((int) $logoId) : '');
$brandN  = htmlspecialchars($site['brand']['name'] ?? 'Manami');
$brandT  = htmlspecialchars($site['brand']['tagline'] ?? 'EDUCATION');
$brandS  = htmlspecialchars($site['brand']['slogan'] ?? '');
$variant = $variant ?? 'default';
$homeHref = url('index.php');
?>
<?php if (!empty($logoUrl)): ?>
  <a href="<?= h($homeHref) ?>" class="flex min-w-0 items-center shrink-0" aria-label="<?= $brandN ?> <?= $brandT ?> — Home">
    <img src="<?= h($logoUrl) ?>" alt="<?= $brandN ?> <?= $brandT ?>" class="block h-11 w-auto max-w-[180px] object-contain sm:h-12 sm:max-w-[210px] xl:h-14 xl:max-w-[230px]">
  </a>
<?php else: ?>
  <a href="<?= h($homeHref) ?>" class="flex items-center gap-2.5 shrink-0" aria-label="<?= $brandN ?> <?= $brandT ?> — Home">
    <span class="relative w-9 h-9 xl:w-10 xl:h-10 grid place-items-center rounded-xl bg-navy text-white shrink-0 shadow-md" aria-hidden="true">
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3zm0 8.18L5.18 9 12 5.82 18.82 9 12 11.18zM5 13.18v3.93L11 20v-3.93L5 13.18zm14 0L13 16.07V20l6-2.89v-3.93z"/></svg>
    </span>
    <span class="leading-tight">
      <span class="block font-display text-lg xl:text-xl font-extrabold tracking-tight">
        <span class="text-navy">M</span><span class="text-brand"><?= substr($brandN, 1) ?></span>
      </span>
      <span class="hidden sm:block text-[10px] font-semibold tracking-[0.3em] text-accent uppercase"><?= $brandT ?></span>
    </span>
  </a>
<?php endif; ?>
