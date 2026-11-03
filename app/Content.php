<?php
declare(strict_types=1);

namespace App;

final class Content
{
    private static bool $googleReviewsSchemaWarningLogged = false;

    public static function services(bool $activeOnly = true): array
    {
        $sql = 'SELECT * FROM services' . ($activeOnly ? ' WHERE active=1' : '') . ' ORDER BY sort ASC, id ASC';
        return Database::fetchAll($sql);
    }

    public static function serviceBySlug(string $slug): ?array
    {
        return Database::fetchOne('SELECT * FROM services WHERE slug=? AND active=1', [$slug]);
    }

    public static function countries(bool $activeOnly = true): array
    {
        $sql = 'SELECT * FROM countries' . ($activeOnly ? ' WHERE active=1' : '') . ' ORDER BY sort ASC, id ASC';
        return Database::fetchAll($sql);
    }

    public static function featuredCountries(): array
    {
        return Database::fetchAll('SELECT * FROM countries WHERE active=1 AND featured=1 ORDER BY sort ASC');
    }

    public static function testimonials(bool $activeOnly = true): array
    {
        $sql = 'SELECT * FROM testimonials' . ($activeOnly ? ' WHERE active=1' : '') . ' ORDER BY sort ASC, id ASC';
        return Database::fetchAll($sql);
    }

    public static function googleReviews(): array
    {
        $tableExists = Database::fetchValue(
            'SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=?',
            ['google_reviews']
        );
        if (!$tableExists) {
            if (!self::$googleReviewsSchemaWarningLogged) {
                error_log('Google reviews are disabled until config/migration.sql creates the google_reviews table.');
                self::$googleReviewsSchemaWarningLogged = true;
            }
            return [];
        }

        return Database::fetchAll(
            'SELECT reviewer_name, rating, comment FROM google_reviews WHERE active=1 ORDER BY review_created_at DESC, review_updated_at DESC'
        );
    }

    public static function faqs(bool $activeOnly = true): array
    {
        $sql = 'SELECT * FROM faqs' . ($activeOnly ? ' WHERE active=1' : '') . ' ORDER BY sort ASC, id ASC';
        return Database::fetchAll($sql);
    }

    public static function stats(): array
    {
        return Settings::getJson('stats_json', [
            ['label' => 'Students Placed', 'value' => 300],
            ['label' => 'Partner Institutions', 'value' => 85],
            ['label' => 'Courses Available', 'value' => 240],
            ['label' => 'Visa Success Rate', 'value' => '99%'],
        ]);
    }

    public static function posts(string $type = 'blog', int $limit = 6): array
    {
        return Database::fetchAll(
            'SELECT * FROM posts WHERE type=? AND status=\'published\' AND published_at <= NOW() ORDER BY published_at DESC LIMIT ' . (int) $limit,
            [$type]
        );
    }

    public static function recentPosts(int $limit = 3): array
    {
        return Database::fetchAll(
            'SELECT * FROM posts WHERE status=\'published\' AND published_at <= NOW() ORDER BY published_at DESC LIMIT ' . (int) $limit
        );
    }

    public static function mediaUrl(?int $id): ?string
    {
        if (!$id) return null;
        $m = Database::fetchOne('SELECT path FROM media WHERE id=?', [$id]);
        return $m ? Upload::url($m['path']) : null;
    }

    public static function log(?int $userId, string $action, ?string $entity = null, ?int $entityId = null, array $meta = []): void
    {
        Database::insert('activity_log', [
            'user_id'   => $userId,
            'action'    => $action,
            'entity'    => $entity,
            'entity_id' => $entityId,
            'meta'      => $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
            'ip'        => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }
}
