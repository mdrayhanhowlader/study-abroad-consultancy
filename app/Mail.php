<?php
declare(strict_types=1);

namespace App;

final class Mail
{
    public static function send(string $toEmail, string $toName, string $subject, string $bodyHtml): bool
    {
        $cfg = require __DIR__ . '/../config/config.php';
        $fromEmail = $cfg['mail']['from_email'];
        $fromName  = $cfg['mail']['from_name'];

        $boundary = md5(uniqid('', true));
        $headers  = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . self::encodeHeader($fromName) . " <{$fromEmail}>",
            'Reply-To: ' . $fromEmail,
            'X-Mailer: PHP/' . PHP_VERSION,
        ];

        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $recipient = $toName ? $toName . ' <' . $toEmail . '>' : $toEmail;

        $ok = @mail($recipient, $encodedSubject, $bodyHtml, implode("\r\n", $headers));
        if (!$ok) {
            error_log('Mail send failed: ' . $subject . ' -> ' . $toEmail);
        }
        return $ok;
    }

    public static function wrap(string $title, string $body): string
    {
        $brand = 'Manami Education';
        $year  = date('Y');
        return '<!doctype html><html><body style="margin:0;background:#f5f7f5;font-family:Inter,Helvetica,Arial,sans-serif;color:#1E242B;">'
            . '<div style="max-width:560px;margin:24px auto;background:#fff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">'
            . '<div style="background:#006837;color:#fff;padding:20px 24px;font-family:Playfair Display,Georgia,serif;font-size:20px;font-weight:700;">' . htmlspecialchars($brand) . '</div>'
            . '<div style="padding:24px;">'
            . '<h1 style="margin:0 0 12px;font-size:20px;color:#1A2B6D;">' . htmlspecialchars($title) . '</h1>'
            . $body
            . '</div>'
            . '<div style="padding:14px 24px;background:#f8fafc;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280;">© ' . $year . ' ' . htmlspecialchars($brand) . ' · Dhanmondi, Dhaka</div>'
            . '</div></body></html>';
    }

    private static function encodeHeader(string $s): string
    {
        return '=?UTF-8?B?' . base64_encode($s) . '?=';
    }
}
