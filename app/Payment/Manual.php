<?php
declare(strict_types=1);

namespace App\Payment;

use App\Order;

final class Manual implements Gateway
{
    public function __construct(private array $config) {}

    public function name(): string { return 'manual'; }

    public function charge(array $customer, float $amount, ?string $txnId, ?string $screenshotPath = null): array
    {
        if (!$txnId) {
            return ['ok' => false, 'message' => 'bKash Transaction ID is required.'];
        }
        return [
            'ok'        => true,
            'reference' => $txnId,
            'message'   => 'Order placed. We will verify your bKash payment and confirm shortly.',
        ];
    }
}
