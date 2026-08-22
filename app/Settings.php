<?php
declare(strict_types=1);

namespace App;

final class Settings
{
    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache !== null) return self::$cache;
        $rows = Database::fetchAll('SELECT `key`, `value` FROM settings');
        $out = [];
        foreach ($rows as $r) {
            $out[$r['key']] = $r['value'];
        }
        self::$cache = $out;
        return $out;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $a = self::all();
        return $a[$key] ?? $default;
    }

    public static function getJson(string $key, array $default = []): array
    {
        $raw = self::get($key);
        if (!$raw) return $default;
        $d = json_decode($raw, true);
        return is_array($d) ? $d : $default;
    }

    public static function set(string $key, string $value): void
    {
        Database::execute(
            'INSERT INTO settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)',
            [$key, $value]
        );
        self::$cache = null;
    }
}
