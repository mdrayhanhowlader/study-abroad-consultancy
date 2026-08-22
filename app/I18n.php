<?php
declare(strict_types=1);

namespace App;

final class I18n
{
    public const SUPPORTED = ['en-GB', 'bn', 'ja', 'hi'];
    public const DEFAULT    = 'en-GB';
    public const COOKIE     = 'manami_lang';
    public const COOKIE_TTL = 60 * 60 * 24 * 365;

    private static ?string $current = null;
    private static array $cache = [];

    public static function boot(): void
    {
        if (self::$current !== null) return;

        $lang = null;

        if (!empty($_GET['lang']) && self::isSupported($_GET['lang'])) {
            $lang = $_GET['lang'];
            setcookie(self::COOKIE, $lang, [
                'expires'  => time() + self::COOKIE_TTL,
                'path'     => '/',
                'httponly' => false,
                'samesite' => 'Lax',
            ]);
            $_COOKIE[self::COOKIE] = $lang;
        } elseif (!empty($_COOKIE[self::COOKIE]) && self::isSupported($_COOKIE[self::COOKIE])) {
            $lang = $_COOKIE[self::COOKIE];
        } else {
            $lang = self::detectFromHeader($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
        }

        self::$current = $lang;
    }

    public static function current(): string
    {
        return self::$current ?? self::DEFAULT;
    }

    public static function set(string $lang): bool
    {
        if (!self::isSupported($lang)) return false;
        self::$current = $lang;
        setcookie(self::COOKIE, $lang, [
            'expires'  => time() + self::COOKIE_TTL,
            'path'     => '/',
            'httponly' => false,
            'samesite' => 'Lax',
        ]);
        $_COOKIE[self::COOKIE] = $lang;
        return true;
    }

    public static function isSupported(string $code): bool
    {
        return in_array($code, self::SUPPORTED, true);
    }

    public static function t(string $key, array $params = []): string
    {
        $value = self::lookup($key, self::dict(self::current()));
        if ($value === null) {
            $value = self::lookup($key, self::dict(self::DEFAULT)) ?? $key;
        }
        if ($params && is_string($value)) {
            $value = strtr($value, $params);
        }
        return (string) $value;
    }

    private static function lookup(string $key, array $dict)
    {
        if (array_key_exists($key, $dict)) return $dict[$key];
        $segments = explode('.', $key);
        $cur = $dict;
        foreach ($segments as $seg) {
            if (!is_array($cur) || !array_key_exists($seg, $cur)) return null;
            $cur = $cur[$seg];
        }
        return $cur;
    }

    public static function nativeName(string $code): string
    {
        $names = [
            'en-GB' => 'English (UK)',
            'bn'    => 'বাংলা',
            'ja'    => '日本語',
            'hi'    => 'हिन्दी',
        ];
        return $names[$code] ?? $code;
    }

    public static function flag(string $code): string
    {
        $flags = [
            'en-GB' => '🇬🇧',
            'bn'    => '🇧🇩',
            'ja'    => '🇯🇵',
            'hi'    => '🇮🇳',
        ];
        return $flags[$code] ?? '🌐';
    }

    public static function code(string $code): string
    {
        return $code === 'en-GB' ? 'EN' : strtoupper($code);
    }

    public static function dir(string $code): string
    {
        return 'ltr';
    }

    private static function detectFromHeader(string $header): string
    {
        if ($header === '') return self::DEFAULT;
        $parts = array_map('trim', explode(',', $header));
        $candidates = [];
        foreach ($parts as $p) {
            $bits = explode(';', $p);
            $tag  = strtolower(trim($bits[0]));
            $q    = 1.0;
            if (isset($bits[1]) && preg_match('/q=([0-9.]+)/', $bits[1], $m)) {
                $q = (float) $m[1];
            }
            $primary = strtolower(strtok($tag, '-'));
            $map = ['en' => 'en-GB', 'bn' => 'bn', 'ja' => 'ja', 'hi' => 'hi'];
            if (isset($map[$primary])) {
                $candidates[$map[$primary]] = ($candidates[$map[$primary]] ?? 0) + $q;
            }
        }
        if (!$candidates) return self::DEFAULT;
        arsort($candidates);
        $top = array_key_first($candidates);
        return self::isSupported($top) ? $top : self::DEFAULT;
    }

    private static function dict(string $code): array
    {
        if (isset(self::$cache[$code])) return self::$cache[$code];
        $path = __DIR__ . '/i18n/' . $code . '.php';
        if (!is_file($path)) {
            $path = __DIR__ . '/i18n/' . self::DEFAULT . '.php';
        }
        self::$cache[$code] = require $path;
        return self::$cache[$code];
    }
}
