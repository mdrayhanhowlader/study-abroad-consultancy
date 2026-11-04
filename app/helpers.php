<?php
declare(strict_types=1);

if (!function_exists('h')) {
    function h(?string $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('base_path')) {
    function base_path(): string
    {
        $script = $_SERVER['SCRIPT_NAME'] ?? '/';
        $script = str_replace('\\', '/', $script);
        foreach (['/admin/', '/api/', '/books/', '/courses/', '/blog/', '/country/', '/assets/', '/uploads/', '/partials/', '/app/', '/config/', '/data/'] as $marker) {
            $pos = strpos($script, $marker);
            if ($pos !== false) return rtrim(substr($script, 0, $pos), '/');
        }
        // The current script is in the site root (possibly under a subdirectory).
        $dir = dirname($script);
        return $dir === '/' ? '' : rtrim($dir, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return base_path() . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path, int $code = 302): void
    {
        header('Location: ' . url($path), true, $code);
        exit;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('uploads_url')) {
    function uploads_url(?string $rel): string
    {
        if (!$rel) return '';
        return url('uploads/' . ltrim($rel, '/'));
    }
}

if (!function_exists('now')) {
    function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('t')) {
    function t(string $key, array $params = []): string
    {
        return \App\I18n::t($key, $params);
    }
}

if (!function_exists('current_lang')) {
    function current_lang(): string
    {
        return \App\I18n::current();
    }
}

if (!function_exists('lang_url')) {
    function lang_url(string $code, ?string $path = null): string
    {
        $base = base_path();
        $path = $path ?? ($_SERVER['REQUEST_URI'] ?? '/');
        // REQUEST_URI includes the base path; strip it so url() doesn't duplicate it.
        if ($base !== '' && strpos($path, $base . '/') === 0) {
            $path = substr($path, strlen($base));
        }
        $path = ltrim($path, '/');
        $path = preg_replace('/([?&])lang=[^&]+&?/', '$1', $path) ?? $path;
        $path = rtrim($path, '?&');
        $sep  = str_contains($path, '?') ? '&' : '?';
        return url($path) . $sep . 'lang=' . urlencode($code);
    }
}

if (!function_exists('format_bdt')) {
    function format_bdt(float $n): string
    {
        return '৳' . number_format($n, 0, '.', ',');
    }
}

if (!function_exists('order_code')) {
    function order_code(): string
    {
        return 'MAN-' . date('Ymd') . '-' . bin2hex(random_bytes(12));
    }
}
