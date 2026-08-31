<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Local/dev driver - fabricates gateway ids and always verifies signatures
 * as valid, so the whole checkout -> payment -> fulfillment flow can be
 * exercised without a real Razorpay account. Set PAYMENT_GATEWAY=log.
 * Never use this outside local/testing - it accepts every signature.
 */
class LogPaymentGateway implements PaymentGatewayInterface
{
    public function createOrder(int $amountInPaise, string $receipt): array
    {
        $orderId = 'order_fake_'.Str::random(14);
        Log::info("[Payment] fake order {$orderId} for ₹".number_format($amountInPaise / 100, 2)." (receipt {$receipt})");

        return ['gateway_order_id' => $orderId, 'key' => 'fake_key'];
    }

    public function verifyPaymentSignature(string $gatewayOrderId, string $gatewayPaymentId, string $signature): bool
    {
        Log::info("[Payment] fake signature accepted for {$gatewayOrderId}/{$gatewayPaymentId}");

        return true;
    }

    public function verifyWebhookSignature(string $rawBody, string $signature): bool
    {
        return true;
    }

    public function refund(string $gatewayPaymentId, int $amountInPaise): string
    {
        $refundId = 'rfnd_fake_'.Str::random(14);
        Log::info("[Payment] fake refund {$refundId} for {$gatewayPaymentId}, ₹".number_format($amountInPaise / 100, 2));

        return $refundId;
    }
}
