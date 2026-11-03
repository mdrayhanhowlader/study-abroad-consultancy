<?php
declare(strict_types=1);

namespace App;

use RuntimeException;

final class PrivateBookPreview
{
    private const MAX_BYTES = 25 * 1024 * 1024;

    public static function upload(array $file): string
    {
        $uploadError = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($uploadError !== UPLOAD_ERR_OK) {
            $message = match ($uploadError) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'PDF exceeds the configured PHP upload size limit.',
                UPLOAD_ERR_NO_FILE => 'Select a PDF file to upload.',
                default => 'PDF upload failed (code ' . $uploadError . ').',
            };
            throw new RuntimeException($message);
        }
        if ((int) ($file['size'] ?? 0) > self::MAX_BYTES) {
            throw new RuntimeException('PDF is too large (maximum 25 MB).');
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $tmp) : false;
        if ($finfo) {
            finfo_close($finfo);
        }
        $handle = @fopen($tmp, 'rb');
        $signature = $handle ? fread($handle, 5) : false;
        if ($handle) {
            fclose($handle);
        }
        if ($mime !== 'application/pdf' || $signature !== '%PDF-') {
            throw new RuntimeException('Only valid PDF files can be uploaded.');
        }

        $relativePath = date('Y/m') . '/' . bin2hex(random_bytes(16)) . '.pdf';
        $directory = self::rootPath() . '/' . dirname($relativePath);
        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            throw new RuntimeException('Could not create the private PDF directory.');
        }
        $destination = self::rootPath() . '/' . $relativePath;
        if (!move_uploaded_file($tmp, $destination)) {
            throw new RuntimeException('Could not save the private PDF.');
        }
        if (!chmod($destination, 0640)) {
            if (!unlink($destination)) {
                error_log('Could not remove a book preview after setting restrictive permissions failed: ' . $destination);
            }
            throw new RuntimeException('Could not secure the uploaded PDF permissions.');
        }

        return $relativePath;
    }

    public static function absolutePath(string $relativePath): string
    {
        if (!preg_match('#^\d{4}/\d{2}/[a-f0-9]{32}\.pdf$#', $relativePath)) {
            throw new RuntimeException('The book preview file path is invalid.');
        }
        $root = realpath(self::rootPath());
        $file = realpath(self::rootPath() . '/' . $relativePath);
        if (!$root || !$file || !str_starts_with($file, $root . DIRECTORY_SEPARATOR) || !is_file($file)) {
            throw new RuntimeException('The book preview file could not be found.');
        }
        return $file;
    }

    private static function rootPath(): string
    {
        $cfg = require __DIR__ . '/../config/config.php';
        return rtrim((string) $cfg['private_book_preview_path'], '/');
    }
}
