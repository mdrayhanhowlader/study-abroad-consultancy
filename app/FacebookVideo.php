<?php
declare(strict_types=1);

namespace App;

final class FacebookVideo
{
    public static function embedUrl(string $url): ?string
    {
        $url = trim($url);
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) return null;

        $parts = parse_url($url);
        $host = strtolower((string) ($parts['host'] ?? ''));
        $allowedHosts = ['facebook.com', 'www.facebook.com', 'm.facebook.com', 'web.facebook.com', 'fb.watch', 'www.fb.watch'];
        if (!in_array($host, $allowedHosts, true) || strtolower((string) ($parts['scheme'] ?? '')) !== 'https') {
            return null;
        }

        $path = (string) ($parts['path'] ?? '');
        $query = [];
        parse_str((string) ($parts['query'] ?? ''), $query);
        $isWatch = $host === 'fb.watch' || $host === 'www.fb.watch';
        $isFacebookVideo = preg_match('#/(videos|reel|share/v)/#i', $path) === 1
            || (preg_match('#/watch/?$#i', $path) === 1 && !empty($query['v']));
        if ((!$isWatch && !$isFacebookVideo) || str_contains($path, '/plugins/')) return null;

        $videoUrl = 'https://' . $host . $path;
        if (!$isWatch && preg_match('#/watch/?$#i', $path) === 1) {
            $videoUrl .= '?v=' . rawurlencode((string) $query['v']);
        }

        return 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode($videoUrl)
            . '&show_text=false&width=1280';
    }
}
