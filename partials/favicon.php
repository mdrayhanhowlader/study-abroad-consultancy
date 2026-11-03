<?php /** @var array $site */
$favicon = $site['brand']['favicon_url'] ?? '';
$apple   = $site['brand']['apple_touch_url'] ?? '';
$defaultApple = asset('img/icon-192.png');
?>
  <link rel="icon" href="<?= h($favicon ?: asset('img/icon-192.png')) ?>">
  <link rel="shortcut icon" href="<?= h($favicon ?: asset('img/icon-192.png')) ?>">
  <link rel="apple-touch-icon" href="<?= h($apple ?: $defaultApple) ?>">
  <meta name="theme-color" content="#006837">
