<?php

namespace App\Services\Payment;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Events\NewOrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Exceptions\InsufficientStockException;
use App\Models\CustomerPayment;
use App\Services\Inventory\StockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Both the webhook handler and the client-side post-checkout verify call
 * land here - Razorpay retries webhooks and a customer can trigger both
 * paths for the same payment, so every method here has to be safe to run
 * twice for the same payment.
 */
class PaymentConfirmationService
{
    public function __construct(private readonly StockService $stock) {}

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

            try {
                $this->stock->reserveForOrder($order);
            } catch (InsufficientStockException $e) {
                // Money has already left the customer's account at this point -
                // stock ran out in the gap between checkout's soft check and
                // payment actually completing. This needs a refund and a
                // human/ops look, not a silent failure. Refund automation isn't
                // built yet (flagged in the README) - for now this at minimum
                // logs loudly instead of quietly leaving the order stuck.
                Log::critical('Payment captured but stock unavailable at confirmation', [
                    'order_id' => $order->id,
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }

            $order->update(['status' => OrderStatus::Confirmed, 'confirmed_at' => now()]);

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
