<?php

namespace App\Services\Payment;

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayGateway implements PaymentGatewayInterface
{
    private Api $api;

    public function __construct(
        private readonly string $keyId,
        private readonly string $keySecret,
        private readonly string $webhookSecret,
    ) {
        $this->api = new Api($this->keyId, $this->keySecret);
    }

    public function createOrder(int $amountInPaise, string $receipt): array
    {
        $order = $this->api->order->create([
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'receipt' => $receipt,
        ]);

        return [
            'gateway_order_id' => $order['id'],
            'key' => $this->keyId, // public key - fine to hand to the client, secret never leaves the server
        ];
    }

    public function verifyPaymentSignature(string $gatewayOrderId, string $gatewayPaymentId, string $signature): bool
    {
        try {
            $this->api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $gatewayOrderId,
                'razorpay_payment_id' => $gatewayPaymentId,
                'razorpay_signature' => $signature,
            ]);

            return true;
        } catch (SignatureVerificationError) {
            return false;
        }
    }

    public function verifyWebhookSignature(string $rawBody, string $signature): bool
    {
        try {
            $this->api->utility->verifyWebhookSignature($rawBody, $signature, $this->webhookSecret);

            return true;
        } catch (SignatureVerificationError) {
            return false;
        }
    }

    public function refund(string $gatewayPaymentId, int $amountInPaise): string
    {
        $refund = $this->api->payment->fetch($gatewayPaymentId)->refund(['amount' => $amountInPaise]);

        return $refund['id'];
    }
}
