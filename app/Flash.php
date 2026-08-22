<?php
declare(strict_types=1);

namespace App;

final class Flash
{
    public static function set(string $type, string $message): void
    {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }

    public static function pull(): array
    {
        $f = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $f;
    }

    public static function render(): string
    {
        $items = self::pull();
        if (!$items) return '';
        $html = '';
        $map = [
            'success' => 'bg-brand/10 text-brand border-brand/30',
            'error'   => 'bg-accent/10 text-accent border-accent/30',
            'info'    => 'bg-navy/10 text-navy border-navy/30',
            'warning' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
        ];
        foreach ($items as $f) {
            $cls = $map[$f['type']] ?? $map['info'];
            $html .= '<div class="border rounded-md px-4 py-3 text-sm ' . $cls . '">' . htmlspecialchars($f['message']) . '</div>';
        }
        return '<div class="space-y-2 mb-4">' . $html . '</div>';
    }
}
