<?php
declare(strict_types=1);

namespace App;

final class Product
{
    public static function all(bool $activeOnly = true): array
    {
        $sql = 'SELECT * FROM products' . ($activeOnly ? ' WHERE active=1' : '') . ' ORDER BY sort ASC, id ASC';
        return Database::fetchAll($sql);
    }

    public static function bySlug(string $slug): ?array
    {
        return Database::fetchOne('SELECT * FROM products WHERE slug=? AND active=1', [$slug]);
    }

    public static function find(int $id): ?array
    {
        return Database::fetchOne('SELECT * FROM products WHERE id=?', [$id]);
    }

    public static function decrementStock(int $id, int $qty): void
    {
        Database::execute(
            'UPDATE products SET stock = GREATEST(stock - ?, 0), sold = sold + ? WHERE id=?',
            [$qty, $qty, $id]
        );
    }
}
