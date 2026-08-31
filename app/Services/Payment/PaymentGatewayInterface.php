<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    /**
     * Create a gateway-side order/intent before the client opens checkout.
     * @return array{gateway_order_id: string, key: string}
     */
    public function createOrder(int $amountInPaise, string $receipt): array;

    /** Client-side checkout success callback - verify before trusting it (browser can lie). */
    public function verifyPaymentSignature(string $gatewayOrderId, string $gatewayPaymentId, string $signature): bool;

    /** Server-to-server webhook - verify against the RAW request body, not re-encoded JSON. */
    public function verifyWebhookSignature(string $rawBody, string $signature): bool;

    /** @return string the gateway's refund id */
    public function refund(string $gatewayPaymentId, int $amountInPaise): string;
}
