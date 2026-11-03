<?php
declare(strict_types=1);

namespace App;

use RuntimeException;

final class Upload
{
    public const ALLOWED_IMAGE = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'image/gif'];
    public const ALLOWED_VIDEO = ['video/mp4', 'video/webm'];

    public static function handle(array $file, string $subdir, array $allowedMime, int $maxBytes, ?int $newWidth = null): array
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload failed (code ' . ($file['error'] ?? '?') . ').');
        }
        if ($file['size'] > $maxBytes) {
            throw new RuntimeException('File too large (max ' . self::human($maxBytes) . ').');
        }
        $cfg = require __DIR__ . '/../config/config.php';
        $mime = self::detectMime($file['tmp_name']);
        if (!in_array($mime, $allowedMime, true)) {
            throw new RuntimeException('File type not allowed: ' . $mime);
        }
        $ext = self::extFromMime($mime, $file['name']);
        $name = bin2hex(random_bytes(8)) . ($ext ? '.' . $ext : '');
        $relDir = trim($subdir, '/') . '/' . date('Y') . '/' . date('m');
        $absDir = $cfg['uploads']['base_path'] . '/' . $relDir;
        if (!is_dir($absDir) && !mkdir($absDir, 0755, true) && !is_dir($absDir)) {
            throw new RuntimeException('Could not create upload directory.');
        }
        $absPath = $absDir . '/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $absPath)) {
            throw new RuntimeException('Could not save uploaded file.');
        }
        @chmod($absPath, 0644);
        if ($newWidth && str_starts_with($mime, 'image/') && $mime !== 'image/svg+xml') {
            self::resizeIfPossible($absPath, $mime, $newWidth);
        }
        $relPath = $relDir . '/' . $name;
        $id = Database::insert('media', [
            'type'          => str_starts_with($mime, 'video/') ? 'video' : 'image',
            'path'          => $relPath,
            'original_name' => substr((string)$file['name'], 0, 255),
            'mime'          => $mime,
            'size_bytes'    => (int) $file['size'],
        ]);
        return ['id' => $id, 'path' => $relPath, 'mime' => $mime];
    }

    public static function url(?string $relPath): string
    {
        if (!$relPath) return '';
        $cfg = require __DIR__ . '/../config/config.php';
        $baseUrl = rtrim($cfg['uploads']['base_url'], '/');
        $path = trim($baseUrl, '/') . '/' . ltrim($relPath, '/');
        if (preg_match('#^https?://#i', $baseUrl)) {
            return $baseUrl . '/' . ltrim($relPath, '/');
        }
        return \url($path);
    }

    private static function detectMime(string $tmp): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $m = $finfo ? finfo_file($finfo, $tmp) : false;
        if ($finfo) finfo_close($finfo);
        return $m ?: 'application/octet-stream';
    }

    private static function extFromMime(string $mime, string $orig): string
    {
        $map = [
            'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp',
            'image/gif'  => 'gif', 'image/svg+xml' => 'svg',
            'video/mp4'  => 'mp4', 'video/webm' => 'webm',
        ];
        if (isset($map[$mime])) return $map[$mime];
        $e = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        return preg_replace('/[^a-z0-9]/', '', $e) ?: '';
    }

    private static function human(int $b): string
    {
        $units = ['B','KB','MB','GB'];
        $i = 0;
        while ($b >= 1024 && $i < count($units) - 1) { $b /= 1024; $i++; }
        return round($b, 1) . ' ' . $units[$i];
    }

    private static function resizeIfPossible(string $absPath, string $mime, int $maxW): void
    {
        if (!function_exists('getimagesize')) return;
        [$w, $h] = getimagesize($absPath) ?: [0, 0];
        if ($w <= 0 || $w <= $maxW) return;
        $ratio = $maxW / $w;
        $newW = $maxW; $newH = (int) round($h * $ratio);
        $src = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($absPath),
            'image/png'  => @imagecreatefrompng($absPath),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($absPath) : false,
            'image/gif'  => @imagecreatefromgif($absPath),
            default      => false,
        };
        if (!$src) return;
        $dst = imagecreatetruecolor($newW, $newH);
        if ($mime === 'image/png' || $mime === 'image/gif') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
        match ($mime) {
            'image/jpeg' => imagejpeg($dst, $absPath, 85),
            'image/png'  => imagepng($dst, $absPath, 6),
            'image/webp' => imagewebp($dst, $absPath, 85),
            'image/gif'  => imagegif($dst, $absPath),
            default      => null,
        };
        imagedestroy($src);
        imagedestroy($dst);
    }
}
