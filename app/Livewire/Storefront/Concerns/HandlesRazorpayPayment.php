<?php

namespace App\Livewire\Storefront\Concerns;

use App\Enums\PaymentStatus;
use App\Models\CustomerPayment;
use App\Models\Order;
use App\Services\Payment\PaymentConfirmationService;
use App\Services\Payment\PaymentGatewayInterface;

/**
 * Any Livewire component that produces a real Order (product checkout,
 * lab test booking, appointment booking) mixes this in rather than
 * reimplementing the same Razorpay initiate/verify calls three times.
 * Mirrors PaymentController::initiate()/verify() in the API exactly -
 * the webhook (already registered, server-to-server) remains the
 * authoritative confirmation regardless of what happens here; these
 * two methods are only the fast-path UI feedback on top of it.
 *
 * Using classes must declare their own ?string $errorMessage property -
 * this trait assumes it exists (as a convention, not a declared
 * property here) so each component can display it however fits its own
 * view, rather than the trait dictating one shared error-display shape.
 */
trait HandlesRazorpayPayment
{
    public ?int $orderId = null;

    public ?string $razorpayOrderId = null;

    public ?string $razorpayKey = null;

    public ?int $amountInPaise = null;

    protected function initiatePayment(Order $order): void
    {
        $gateway = app(PaymentGatewayInterface::class);

        $amountInPaise = (int) round((float) $order->total_amount * 100);
        $gatewayOrder = $gateway->createOrder($amountInPaise, "order-{$order->id}");

        CustomerPayment::create([
            'order_id' => $order->id,
            'amount' => $order->total_amount,
            'gateway' => config('services.payment.gateway'),
            'gateway_order_id' => $gatewayOrder['gateway_order_id'],
            'status' => PaymentStatus::Initiated,
        ]);

        $this->orderId = $order->id;
        $this->razorpayOrderId = $gatewayOrder['gateway_order_id'];
        $this->razorpayKey = $gatewayOrder['key'];
        $this->amountInPaise = $amountInPaise;

        // JS listens for this to open the Razorpay checkout widget -
        // Livewire itself can't call an arbitrary third-party JS SDK
        // directly, only dispatch a browser event for it to react to.
        $this->dispatch('payment-ready');
    }

    public function verifyPayment(string $razorpayOrderId, string $razorpayPaymentId, string $razorpaySignature): void
    {
        $gateway = app(PaymentGatewayInterface::class);
        $confirmation = app(PaymentConfirmationService::class);

        $payment = CustomerPayment::where('gateway_order_id', $razorpayOrderId)->first();

        if (! $payment || $payment->order->user_id !== auth('web')->id()) {
            $this->errorMessage = 'We could not find that payment.';

            return;
        }

        $valid = $gateway->verifyPaymentSignature($razorpayOrderId, $razorpayPaymentId, $razorpaySignature);

        if (! $valid) {
            $this->errorMessage = 'Payment verification failed. If money was deducted, it will be refunded automatically.';

            return;
        }

        $confirmation->markSuccessful($payment, $razorpayPaymentId);

        $this->redirect(route('storefront.orders.confirmation', $payment->order_id), navigate: true);
    }
}
