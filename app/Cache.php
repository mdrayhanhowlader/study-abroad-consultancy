<?php
declare(strict_types=1);

namespace App;

/**
 * Simple file-based output cache for shared hosting.
 * No Redis/Memcached needed — just writes HTML to disk.
 */
final class Cache
{
    private static string $dir;

    public static function init(?string $cacheDir = null): void
    {
        self::$dir = $cacheDir ?? __DIR__ . '/../cache';
        if (!is_dir(self::$dir)) {
            @mkdir(self::$dir, 0755, true);
        }
    }

    /**
     * Try to serve a cached page. Returns true if served (and exits).
     */
    public static function serve(string $key, int $ttlSeconds = 3600): bool
    {
        $file = self::file($key);
        if (is_file($file) && (time() - filemtime($file)) < $ttlSeconds) {
            header('X-Cache: HIT');
            readfile($file);
            exit;
        }
        header('X-Cache: MISS');
        return false;
    }

    /**
     * Start output buffering for caching.
     */
    public static function start(string $key): void
    {
        ob_start();
        $_SESSION['_cache_key'] = $key;
    }

    /**
     * End output buffering and write cache file.
     */
    public static function end(?int $ttlSeconds = null): void
    {
        $key = $_SESSION['_cache_key'] ?? null;
        if (!$key) { ob_end_flush(); return; }

        $html = ob_get_contents();
        ob_end_flush();

        $file = self::file($key);
        @file_put_contents($file, $html, LOCK_EX);
    }

    /**
     * Clear all cached files.
     */
    public static function clear(): void
    {
        $dir = self::$dir ?? (__DIR__ . '/../cache');
        if (!is_dir($dir)) return;
        $files = glob($dir . '/*.html.cache');
        if ($files) {
            foreach ($files as $f) @unlink($f);
        }
    }

    /**
     * Clear cache for a specific key.
     */
    public static function clearKey(string $key): void
    {
        $file = self::file($key);
        if (is_file($file)) @unlink($file);
    }

    private static function file(string $key): string
    {
        return self::$dir . '/' . md5($key) . '.html.cache';
    }
}
