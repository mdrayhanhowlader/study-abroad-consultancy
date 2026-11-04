<?php
require_once __DIR__ . '/../app/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    redirect('books.php');
}

App\Csrf::verifyRequest();

try {
    $name    = App\Validation::str((string) ($_POST['name'] ?? ''), 2, 120);
    $address = App\Validation::str((string) ($_POST['address'] ?? ''), 2, 255);
    $thana   = App\Validation::str((string) ($_POST['thana'] ?? ''), 2, 80);
    $phone   = App\Validation::phone((string) ($_POST['phone'] ?? ''));
    $email   = trim((string) ($_POST['email'] ?? ''));
    if ($email !== '') $email = App\Validation::email($email);
    $paymentMethod = in_array(($_POST['payment_method'] ?? ''), ['bkash','cod'], true) ? $_POST['payment_method'] : 'bkash';

    $txn = null;
    $screenshotPath = null;
    if ($paymentMethod === 'bkash') {
        $txn = trim((string) ($_POST['bkash_txn'] ?? ''));
        if ($txn === '') throw new RuntimeException('Please enter your bKash Transaction ID.');
        if (!empty($_FILES['bkash_screenshot']) && $_FILES['bkash_screenshot']['error'] === UPLOAD_ERR_OK) {
            $cfg = require __DIR__ . '/../config/config.php';
            $r = App\Upload::handle(
                $_FILES['bkash_screenshot'],
                'payments',
                App\Upload::ALLOWED_IMAGE,
                $cfg['uploads']['max_screenshot_bytes'],
                1600
            );
            $screenshotPath = $r['path'];
        }
    }

    $orderId = App\Order::place(
        ['name' => $name, 'address' => $address, 'thana' => $thana, 'phone' => $phone, 'email' => $email ?: null],
        $paymentMethod,
        $txn,
        $screenshotPath
    );
    $code = App\Order::code($orderId);

    $cfg = require __DIR__ . '/../config/config.php';
    if ($email) {
        $body  = '<p>Hello ' . h($name) . ',</p>';
        $body .= '<p>Thank you for your order. We received it with code <strong>' . h($code) . '</strong> for a total of <strong>' . format_bdt(App\Database::fetchValue('SELECT total FROM orders WHERE id=?', [$orderId])) . '</strong>.</p>';
        $body .= '<p>We will verify your bKash Transaction ID and call you within 24 hours.</p>';
        $body .= '<p>After your payment is marked as paid, use your order code and phone number to download any available book sample PDF from <a href="' . h(url('order-track.php?code=' . urlencode($code))) . '">Track Order</a>.</p>';
        $body .= '<p>— The Manami Education team</p>';
        App\Mail::send($email, $name, 'Order ' . $code . ' received', App\Mail::wrap('Order received', $body));
    }

    App\Pixel::logEvent('InitiateCheckout', ['order_id' => $orderId, 'total' => App\Database::fetchValue('SELECT total FROM orders WHERE id=?', [$orderId])]);

    $_SESSION['_order_thanks_code'] = $code;
    header('Location: ../thanks.php?code=' . urlencode($code), true, 303);
    exit;
} catch (Throwable $e) {
    $_SESSION['_order_error'] = $e->getMessage();
    header('Location: ../books.php#checkout', true, 303);
    exit;
}
