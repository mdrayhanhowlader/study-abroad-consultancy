<?php /** @var string $variant */ $variant = $variant ?? 'top'; ?>
<div class="lang-switcher relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
  <button type="button" @click="open = !open" class="<?= $variant === 'top' ? 'topbar-link lang-trigger' : 'btn btn-ghost' ?>" :aria-expanded="open" aria-haspopup="true">
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <circle cx="12" cy="12" r="9"/>
      <path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>
    </svg>
    <span class="font-medium"><?= h(\App\I18n::nativeName(current_lang())) ?></span>
    <svg class="w-3 h-3 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
  </button>
  <div x-show="open" class="lang-menu" x-cloak>
    <?php foreach (\App\I18n::SUPPORTED as $code):
      $isActive = current_lang() === $code;
      $href = lang_url($code);
    ?>
      <a href="<?= h($href) ?>" class="lang-item <?= $isActive ? 'active' : '' ?>" <?= $isActive ? 'aria-current=true' : '' ?>>
        <span class="lang-code"><?= h(\App\I18n::code($code)) ?></span>
        <span class="flex-1"><?= h(\App\I18n::nativeName($code)) ?></span>
        <span class="text-base"><?= h(\App\I18n::flag($code)) ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<style>[x-cloak]{display:none!important}</style>
