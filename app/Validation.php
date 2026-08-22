<?php
declare(strict_types=1);

namespace App;

final class Validation
{
    public static function str(string $v, int $min = 1, int $max = 255): string
    {
        $v = trim($v);
        if (mb_strlen($v) < $min) throw new \InvalidArgumentException("Field too short (min $min).");
        if (mb_strlen($v) > $max) throw new \InvalidArgumentException("Field too long (max $max).");
        return $v;
    }

    public static function email(string $v): string
    {
        $v = trim($v);
        if (!filter_var($v, FILTER_VALIDATE_EMAIL)) throw new \InvalidArgumentException("Invalid email.");
        return $v;
    }

    public static function phone(string $v): string
    {
        $v = trim($v);
        $v = preg_replace('/[^\d+]/', '', $v) ?? '';
        if (strlen($v) < 7) throw new \InvalidArgumentException("Invalid phone.");
        return $v;
    }

    public static function decimal(string $v, float $min = 0): float
    {
        $v = trim($v);
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $v)) throw new \InvalidArgumentException("Invalid amount.");
        $f = (float) $v;
        if ($f < $min) throw new \InvalidArgumentException("Amount must be >= $min.");
        return $f;
    }

    public static function slug(string $v): string
    {
        $v = strtolower(trim($v));
        $v = preg_replace('/[^a-z0-9]+/', '-', $v) ?? '';
        return trim($v, '-');
    }
}
