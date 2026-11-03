<?php
require_once __DIR__ . '/../app/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

App\Csrf::verifyRequest();
$orderCode = preg_replace('/[^A-Z0-9-]/i', '', (string) ($_POST['order_code'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$productId = (int) ($_POST['product_id'] ?? 0);
$order = App\Order::findForTracking($orderCode, $phone);
if (!$order || !in_array($order['status'], ['paid', 'completed'], true) || $productId < 1) {
    http_response_code(404);
    exit('Preview not available for this order.');
}

$product = App\Database::fetchOne(
    'SELECT p.title, p.preview_pdf_path FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=? AND oi.product_id=? LIMIT 1',
    [(int) $order['id'], $productId]
);
if (!$product || empty($product['preview_pdf_path'])) {
    http_response_code(404);
    exit('Preview not available for this order.');
}

try {
    $filePath = App\PrivateBookPreview::absolutePath((string) $product['preview_pdf_path']);
} catch (RuntimeException $e) {
    error_log('Book preview download failed: ' . $e->getMessage());
    http_response_code(404);
    exit('Preview not available for this order.');
}

$downloadName = $product['title'] . ' - sample pages.pdf';
$asciiName = preg_replace('/[^A-Za-z0-9._-]+/', '-', $downloadName) ?: 'book-sample.pdf';
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $asciiName . '"; filename*=UTF-8\'\'' . rawurlencode($downloadName));
header('Content-Length: ' . (string) filesize($filePath));
header('Cache-Control: private, no-store, max-age=0');
header('X-Content-Type-Options: nosniff');
readfile($filePath);
exit;
