<?php
/**
 * Facebook/Meta Pixel base code.
 * Pixel ID is configured in admin → Settings → Pixel.
 * Include this in <head> of every page.
 */
if (!function_exists('h')) { require_once __DIR__ . '/../app/helpers.php'; }

$pixelEnabled = false;
$pixelId = '';

try {
    $pixelEnabled = App\Pixel::isEnabled();
    $pixelId = App\Pixel::getId();
} catch (\Throwable $e) {
    // Settings not available yet (e.g., during bootstrap)
}

if ($pixelEnabled && $pixelId): ?>
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '<?= h($pixelId) ?>');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=<?= h($pixelId) ?>&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
<?php endif; ?>
