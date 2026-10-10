<?php
declare(strict_types=1);

namespace App;

final class Homepage
{
    private static array $content = [];

    public static function text(string $key, string $fallback = ''): string
    {
        $value = self::content()[$key] ?? $fallback;
        return is_scalar($value) ? (string) $value : $fallback;
    }

    public static function content(): array
    {
        $lang = I18n::current();
        if (!array_key_exists($lang, self::$content)) {
            self::$content[$lang] = Settings::getJson('homepage_content_' . $lang, []);
        }
        return self::$content[$lang];
    }

    public static function stories(array $fallback): array
    {
        $content = self::content();
        $stories = $content['success.stories'] ?? $fallback;
        if (!is_array($stories)) $stories = $fallback;
        foreach ($stories as $index => &$story) {
            foreach (['name', 'destination', 'year', 'program', 'photo', 'quote'] as $field) {
                $key = 'success.story.' . $index . '.' . $field;
                if (array_key_exists($key, $content) && ($field !== 'photo' || $content[$key] !== '')) {
                    $story[$field] = $content[$key];
                }
            }
        }
        unset($story);
        return $stories;
    }

    public static function destinations(string $fallback = 'Tokyo,Osaka,Kyoto,Yokohama'): array
    {
        $items = array_map('trim', explode(',', self::text('visual.route_destinations', $fallback)));
        $items = array_values(array_filter($items, static fn(string $item): bool => $item !== ''));
        return $items ?: array_map('trim', explode(',', $fallback));
    }
}
