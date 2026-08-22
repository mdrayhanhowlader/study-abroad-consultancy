<?php
declare(strict_types=1);

namespace App;

final class Cart
{
    public const SESSION_KEY = '_cart';

    public static function items(): array
    {
        return $_SESSION[self::SESSION_KEY] ?? [];
    }

    public static function add(int $productId, int $qty = 1): void
    {
        $p = Product::find($productId);
        if (!$p || !$p['active']) return;
        $cart = self::items();
        if (isset($cart[$productId])) {
            $cart[$productId]['qty'] += $qty;
        } else {
            $cart[$productId] = [
                'id'         => (int) $p['id'],
                'slug'       => $p['slug'],
                'title'      => $p['title'],
                'type'       => $p['type'],
                'unit_price' => (float) $p['price_bdt'],
                'cover_id'   => $p['cover_id'] ? (int) $p['cover_id'] : null,
                'qty'        => max(1, $qty),
            ];
        }
        $_SESSION[self::SESSION_KEY] = $cart;
    }

    public static function set(int $productId, int $qty): void
    {
        $cart = self::items();
        if (!isset($cart[$productId])) return;
        if ($qty <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId]['qty'] = $qty;
        }
        $_SESSION[self::SESSION_KEY] = $cart;
    }

    public static function remove(int $productId): void
    {
        $cart = self::items();
        unset($cart[$productId]);
        $_SESSION[self::SESSION_KEY] = $cart;
    }

    public static function clear(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
    }

    public static function subtotal(): float
    {
        $s = 0.0;
        foreach (self::items() as $i) $s += (float) $i['unit_price'] * (int) $i['qty'];
        return $s;
    }

    public static function shipping(): float
    {
        $s = self::subtotal();
        $hasPhysical = false;
        foreach (self::items() as $i) if ($i['type'] === 'physical') { $hasPhysical = true; break; }
        if (!$hasPhysical) return 0.0;
        return $s >= 2000 ? 0.0 : 80.0;
    }

    public static function total(): float
    {
        return self::subtotal() + self::shipping();
    }

    public static function isEmpty(): bool
    {
        return empty(self::items());
    }
}
