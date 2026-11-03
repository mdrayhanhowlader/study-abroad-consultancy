<?php /** @var array $posts */ ?>
<?php
$facebookVideoUrl = App\FacebookVideo::embedUrl((string) App\Settings::get('homepage_video_url', ''));
$facebookVideoTitle = trim((string) App\Settings::get('homepage_video_title', ''));
$facebookVideoCaption = trim((string) App\Settings::get('homepage_video_caption', ''));
?>
<section id="blog" class="insights-section journal-section bg-paper-2">
  <div class="mx-auto max-w-7xl px-4 lg:px-6">
    <div class="journal-heading">
      <div class="journal-heading-copy">
        <div class="eyebrow" data-num="09"><?= h(App\Homepage::text('blog.eyebrow', t('blog.eyebrow'))) ?></div>
        <h2 class="font-display"><?= h(App\Homepage::text('blog.headline', t('blog.headline'))) ?></h2>
      </div>
      <a href="<?= h(url('blog.php')) ?>" class="journal-browse">
        Explore the journal <span class="text-lg transition-transform group-hover:translate-x-1" aria-hidden="true">→</span>
      </a>
    </div>

    <?php if ($facebookVideoUrl): ?>
      <div class="journal-video">
        <div class="aspect-video bg-black">
          <iframe
            src="<?= h($facebookVideoUrl) ?>"
            title="<?= h($facebookVideoTitle ?: 'Facebook student insights video') ?>"
            class="h-full w-full"
            loading="lazy"
            allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
            allowfullscreen
            referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>
        <div class="journal-video-copy">
          <span class="text-[10px] font-bold uppercase tracking-[0.24em] text-gold" style="color:#D8BA72">Student insights</span>
          <h3 class="mt-4 font-display text-2xl font-bold leading-tight sm:text-3xl"><?= h($facebookVideoTitle ?: 'Stories and guidance from our community') ?></h3>
          <?php if ($facebookVideoCaption !== ''): ?>
            <p class="mt-4 whitespace-pre-line text-sm leading-7 text-white/70"><?= h($facebookVideoCaption) ?></p>
          <?php endif; ?>
          <a href="<?= h(App\Settings::get('homepage_video_url', '')) ?>" target="_blank" rel="noopener noreferrer" class="mt-7 inline-flex items-center gap-2 text-sm font-semibold text-white hover:text-gold">
            Watch on Facebook <span aria-hidden="true">↗</span>
          </a>
        </div>
      </div>
    <?php endif; ?>

    <div id="insightsSlider" class="insights-grid journal-layout">
      <?php if (!$posts): ?>
        <div class="rounded-2xl border border-[#ded9cf] bg-white px-6 py-14 text-center text-sm text-ink-3"><?= h(App\Homepage::text('blog.empty', t('blog.empty'))) ?></div>
      <?php else: foreach ($posts as $i => $p):
        $num = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
        $artwork = 'journal-art--' . (($i % 3) + 1);
      ?>
        <a href="<?= h(url($p['href'])) ?>" class="journal-feature group reveal">
          <div class="journal-art <?= $artwork ?>">
            <?php if (!empty($p['cover'])): ?>
              <img src="<?= h($p['cover']) ?>" alt="" class="journal-cover" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
            <?php else: ?>
              <div class="journal-artwork" aria-hidden="true">
                <?php if ($i % 3 === 0): ?>
                  <svg viewBox="0 0 720 420" preserveAspectRatio="xMidYMid slice">
                    <circle cx="520" cy="148" r="90" fill="#d95b43"/>
                    <path d="M0 315 162 178l127 111 112-93 148 119 171-112v217H0Z" fill="#35475e"/>
                    <path d="m88 259 73-81 76 81M268 292l143-96 132 111" fill="none" stroke="#f4ead8" stroke-opacity=".7" stroke-width="3"/>
                    <path d="M0 344h720v76H0z" fill="#17283c"/>
                    <path d="M516 280h107v140H516zM499 280h141v16H499zM537 316h18v37h-18zm47 0h18v37h-18z" fill="#d7b986"/>
                    <path d="M555 220h29v61h-29zm-7 0 22-25 22 25z" fill="#d95b43"/>
                  </svg>
                <?php elseif ($i % 3 === 1): ?>
                  <svg viewBox="0 0 720 420" preserveAspectRatio="xMidYMid slice">
                    <circle cx="548" cy="98" r="62" fill="#e2b65f"/>
                    <path d="M0 320c110-72 205-70 310 0s205 73 410-15v115H0z" fill="#294e48"/>
                    <path d="M0 356c142-72 260-57 365 3s234 41 355-20v81H0z" fill="#183b39"/>
                    <path d="M176 176h36v163h-36zm160-16h38v179h-38zm-208 0h92v15h-92zm-32 0h158l-18 25H114zm232-19h111v15H328zm-31 0h171l-20 24H277z" fill="#d6b77c"/>
                    <path d="M195 192h125v13H195zm171-17h115v13H366z" fill="#b84e3e"/>
                    <path d="M224 205h14v47h-14zm33 0h14v47h-14zm131-13h14v46h-14zm34 0h14v46h-14z" fill="#f3e7d2"/>
                  </svg>
                <?php else: ?>
                  <svg viewBox="0 0 720 420" preserveAspectRatio="xMidYMid slice">
                    <path d="M0 0h720v420H0z" fill="#dfe4dc"/>
                    <circle cx="566" cy="104" r="74" fill="#c95443"/>
                    <path d="M0 300 170 150l160 150 138-105 252 144v81H0z" fill="#425b59"/>
                    <path d="M0 341h720v79H0z" fill="#263e3c"/>
                    <path d="M170 182h46v158h-46zm-53 0h153v17H117zm-29-1h211l-22 26H110z" fill="#d8bb82"/>
                    <path d="M188 207h12v40h-12zm25 0h12v40h-12z" fill="#b84e3e"/>
                    <path d="M354 270h124v70H354zm-14-14h152v15H340zm40-70h72v67h-72z" fill="#e9ddc9"/>
                  </svg>
                <?php endif; ?>
              </div>
            <?php endif; ?>
            <span class="journal-art-tag"><?= h($p['tag']) ?></span>
          </div>
          <div class="journal-feature-copy">
            <div class="journal-meta"><span>ARTICLE <?= $num ?></span><span><?= h($p['day'] . ' ' . $p['mo']) ?></span></div>
            <h3><?= h($p['title']) ?></h3>
            <?php if (!empty($p['excerpt'])): ?><p><?= h($p['excerpt']) ?></p><?php endif; ?>
            <span class="journal-read-more"><?= h(App\Homepage::text('blog.read_more', t('blog.read_more'))) ?><span aria-hidden="true">↗</span></span>
          </div>
        </a>
      <?php endforeach; endif; ?>
    </div>
  </div>
</section>
