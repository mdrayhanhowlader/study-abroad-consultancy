<?php
declare(strict_types=1);

namespace App;

/**
 * Facebook/Meta Pixel helper.
 * - Provides the base pixel snippet with configurable Pixel ID
 * - Logs server-side events to pixel_events_log table
 */
final class Pixel
{
    public static function isEnabled(): bool
    {
        return Settings::get('pixel_enabled') === '1';
    }

    public static function getId(): string
    {
        return Settings::get('pixel_id') ?: '';
    }

    /**
     * Returns the base pixel <script> to inject in <head>.
     * Returns empty string if pixel is disabled or not configured.
     */
    public static function baseScript(): string
    {
        if (!self::isEnabled()) return '';
        $id = self::getId();
        if (!$id) return '';

        return '<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version=\'2.0\';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,\'script\',
\'https://connect.facebook.net/en_US/fbevents.js\');
fbq(\'init\', \'' . h($id) . '\');
fbq(\'track\', \'PageView\');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=' . h($id) . '&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->';
    }

    /**
     * Returns a JS snippet to fire a specific pixel event.
     * Use this inline on pages for client-side events.
     */
    public static function eventScript(string $event, array $params = []): string
    {
        if (!self::isEnabled()) return '';
        $js = "fbq('track', '" . h($event) . "'";
        if ($params) {
            $js .= ', ' . json_encode($params, JSON_UNESCAPED_SLASHES);
        }
        $js .= ');';
        return '<script>' . $js . '</script>';
    }

    /**
     * Log a pixel event server-side for auditing/debugging.
     */
    public static function logEvent(string $event, array $meta = []): void
    {
        if (!self::isEnabled()) return;
        Database::insert('pixel_events_log', [
            'event_name'    => $event,
            'page_url'      => substr((string) ($_SERVER['REQUEST_URI'] ?? ''), 0, 500),
            'referrer'      => substr((string) ($_SERVER['HTTP_REFERER'] ?? ''), 0, 500),
            'fb_browser_id' => substr((string) ($_COOKIE['_fbp'] ?? ''), 0, 120),
            'fb_pixel_id'   => self::getId(),
            'meta_json'     => $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
            'ip'            => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }
}
