<?php
return [
    'app' => [
        'env'      => getenv('APP_ENV') ?: 'production',
        'debug'    => filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOL),
        'name'     => 'Manami Education',
        'url'      => getenv('APP_URL') ?: 'https://manamieducation.com.bd',
        'timezone' => 'Asia/Dhaka',
        'key'      => getenv('APP_KEY') ?: 'manami-change-me-in-prod-32bytes-min',
    ],
    'db' => [
        'host'    => getenv('DB_HOST') ?: '127.0.0.1',
        'port'    => getenv('DB_PORT') ?: '3306',
        'name'    => getenv('DB_NAME') ?: 'manami_education',
        'user'    => getenv('DB_USER') ?: 'root',
        'pass'    => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
    'mail' => [
        'from_email' => getenv('MAIL_FROM') ?: 'office@manami.education',
        'from_name'  => 'Manami Education',
        'driver'     => getenv('MAIL_DRIVER') ?: 'php',
    ],
    'bkash' => [
        'mode'            => getenv('BKASH_MODE') ?: 'manual',
        'merchant_number' => getenv('BKASH_MERCHANT') ?: '+8801700000000',
        'instructions'    => 'Send the exact total to the merchant bKash number, then enter the Transaction ID below. Your order will be confirmed after verification.',
        'charge_percent'  => 0,
    ],
    'sslcommerz' => [
        'store_id'     => getenv('SSLCOMMERZ_STORE_ID') ?: '',
        'store_pass'   => getenv('SSLCOMMERZ_STORE_PASS') ?: '',
        'sandbox'      => filter_var(getenv('SSLCOMMERZ_SANDBOX') ?: 'true', FILTER_VALIDATE_BOOL),
        'success_url'  => getenv('APP_URL') ?: 'https://manamieducation.com.bd' . '/api/payment-return.php?status=success',
        'fail_url'     => getenv('APP_URL') ?: 'https://manamieducation.com.bd' . '/api/payment-return.php?status=failed',
        'cancel_url'   => getenv('APP_URL') ?: 'https://manamieducation.com.bd' . '/api/payment-return.php?status=cancel',
    ],
    'uploads' => [
        'base_path' => __DIR__ . '/../uploads',
        'base_url'  => '/uploads',
        'max_image_bytes' => 5 * 1024 * 1024,
        'max_video_bytes' => 100 * 1024 * 1024,
        'max_screenshot_bytes' => 4 * 1024 * 1024,
    ],
    'session' => [
        'name'        => 'manami_sess',
        'lifetime'    => 60 * 60 * 8,
        'cookie_path' => '/',
    ],
    'login' => [
        'max_attempts'      => 5,
        'lockout_minutes'   => 15,
    ],
];
