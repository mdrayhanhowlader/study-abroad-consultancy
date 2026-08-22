<?php
declare(strict_types=1);

namespace App\Payment;

use RuntimeException;

/**
 * SSLCommerz payment gateway adapter for Bangladesh.
 * Supports bKash, Nagad, cards, and net banking through one checkout.
 *
 * Requires: SSLCommerz merchant account with store_id and store_password.
 * Works on shared hosting (uses cURL).
 */
final class SSLCommerz implements Gateway
{
    private array $config;
    private bool $sandbox;

    public function __construct(array $config)
    {
        if (empty($config['store_id']) || empty($config['store_password'])) {
            throw new RuntimeException('SSLCommerz credentials are not configured.');
        }
        $this->config = $config;
        $this->sandbox = !empty($config['sandbox']);
    }

    public function name(): string { return 'sslcommerz'; }

    public function charge(array $customer, float $amount, ?string $txnId, ?string $screenshotPath = null): array
    {
        $postData = [
            'store_id'           => $this->config['store_id'],
            'store_passwd'       => $this->config['store_password'],
            'total_amount'       => number_format($amount, 2, '.', ''),
            'currency'           => 'BDT',
            'tran_id'            => 'MANAMI-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)),
            'success_url'        => $this->config['success_url'] ?? '',
            'fail_url'           => $this->config['fail_url'] ?? '',
            'cancel_url'         => $this->config['cancel_url'] ?? '',
            'emi_option'         => 0,
            'cus_name'           => $customer['name'] ?? '',
            'cus_email'          => $customer['email'] ?? '',
            'cus_add1'           => $customer['address'] ?? '',
            'cus_city'           => $customer['thana'] ?? '',
            'cus_country'        => 'Bangladesh',
            'cus_phone'          => $customer['phone'] ?? '',
            'shipping_method'    => 'NO',
            'product_name'       => 'Manami Education Order',
            'product_category'   => 'Education',
            'product_profile'    => 'general',
        ];

        $ch = curl_init();
        $url = $this->sandbox
            ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php'
            : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($postData),
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400 || !$response) {
            return ['ok' => false, 'message' => 'SSLCommerz connection failed.'];
        }

        $result = json_decode((string) $response, true);

        if (isset($result['status']) && $result['status'] === 'SUCCESS') {
            return [
                'ok'        => true,
                'reference' => $result['tran_id'] ?? '',
                'message'   => 'Redirect to payment gateway.',
                'redirect'  => $result['GatewayPageURL'] ?? null,
                'session'   => $result['sessionkey'] ?? null,
            ];
        }

        return ['ok' => false, 'message' => $result['failedreason'] ?? 'Payment initiation failed'];
    }

    /**
     * Validate a payment response from SSLCommerz callback.
     */
    public static function validate(array $post, array $config): array
    {
        $validationUrl = !empty($config['sandbox'])
            ? 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php'
            : 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $validationUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'val_id'       => $post['val_id'] ?? '',
                'store_id'     => $config['store_id'],
                'store_passwd' => $config['store_password'],
            ]),
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode((string) $response, true);

        if (isset($result['status']) && $result['status'] === 'VALID') {
            return [
                'ok'        => true,
                'status'    => 'verified',
                'amount'    => $result['amount'] ?? 0,
                'card_type' => $result['card_type'] ?? '',
                'bank_tran' => $result['bank_tran_id'] ?? '',
            ];
        }

        return ['ok' => false, 'message' => $result['status'] ?? 'Validation failed'];
    }
}
