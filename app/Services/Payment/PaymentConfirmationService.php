<?php

namespace App\Services\Payment;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Events\NewOrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Models\CustomerPayment;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;

/**
 * Both the webhook handler and the client-side post-checkout verify call
 * land here - Razorpay retries webhooks and a customer can trigger both
 * paths for the same payment, so every method here has to be safe to run
 * twice for the same payment.
 */
class PaymentConfirmationService
{
    public function __construct(private readonly WalletService $wallet) {}

    public function markSuccessful(CustomerPayment $payment, ?string $gatewayPaymentId = null, ?string $method = null): void
    {
        if ($payment->status === PaymentStatus::Success) {
            return; // already processed - webhook retry or a second verify call
        }

        DB::transaction(function () use ($payment, $gatewayPaymentId, $method) {
            $payment->update([
                'status' => PaymentStatus::Success,
                'gateway_txn_id' => $gatewayPaymentId ?? $payment->gateway_txn_id,
                'payment_mode' => $method ?? $payment->payment_mode,
            ]);

            $order = $payment->order;

            // Stock is deliberately NOT reserved here anymore. A product
            // order has no franchise yet at this point - that's assigned
            // afterward by an admin - so there's nothing to reserve stock
            // against. reserveForOrder() now runs at the point a franchise
            // is actually known (Admin\OrderController::assign()).
            // Lab test / appointment / wallet-topup orders don't use
            // order_items at all, so this was never relevant to them.
            $order->update(['status' => OrderStatus::Confirmed, 'confirmed_at' => now()]);

            // A wallet top-up is a real Order under the hood (same reason
            // lab test bookings and appointments are too) - this is the
            // one extra step specific to that order_type: the payment
            // succeeding is what actually credits the balance.
            if ($order->order_type === 'wallet_topup') {
                $this->wallet->credit($order->user, (float) $order->total_amount, 'Wallet top-up', $order);
            }

            OrderStatusUpdated::dispatch($order);
            NewOrderPlaced::dispatch($order);
        });
    }

    public function markFailed(CustomerPayment $payment): void
    {
        if ($payment->status === PaymentStatus::Failed) {
            return;
        }

        $payment->update(['status' => PaymentStatus::Failed]);
    }
}
