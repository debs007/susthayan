<?php

namespace App\Http\Controllers\Api\Customer;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\CustomerPayment;
use App\Models\Order;
use App\Services\Payment\PaymentConfirmationService;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentGatewayInterface $gateway,
        private readonly PaymentConfirmationService $confirmation,
    ) {}

    public function initiate(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        abort_unless($order->status === OrderStatus::PendingPayment, 422, 'This order is not awaiting payment.');

        $amountInPaise = (int) round((float) $order->total_amount * 100);

        $gatewayOrder = $this->gateway->createOrder($amountInPaise, "order-{$order->id}");

        $payment = CustomerPayment::create([
            'order_id' => $order->id,
            'amount' => $order->total_amount,
            'gateway' => config('services.payment.gateway'),
            'gateway_order_id' => $gatewayOrder['gateway_order_id'],
            'status' => PaymentStatus::Initiated,
        ]);

        return response()->json([
            'payment_id' => $payment->id,
            'gateway_order_id' => $gatewayOrder['gateway_order_id'],
            'key' => $gatewayOrder['key'],
            'amount' => $amountInPaise,
            'currency' => 'INR',
        ]);
    }

    /**
     * Fast path for immediate UI feedback right after Razorpay's checkout
     * handler fires client-side. The webhook below is still what's
     * authoritative - 3-5% of customers close the browser before this ever
     * gets called, which is exactly what the webhook exists to catch.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $payment = CustomerPayment::where('gateway_order_id', $request->string('razorpay_order_id'))
            ->firstOrFail();

        abort_unless($payment->order->user_id === $request->user()->id, 403);

        $valid = $this->gateway->verifyPaymentSignature(
            $request->string('razorpay_order_id'),
            $request->string('razorpay_payment_id'),
            $request->string('razorpay_signature'),
        );

        if (! $valid) {
            return response()->json(['message' => 'Signature verification failed.'], 422);
        }

        $this->confirmation->markSuccessful($payment, $request->string('razorpay_payment_id'));

        return response()->json(['order' => new OrderResource($payment->order->fresh(['items.product', 'franchise']))]);
    }

    /**
     * Server-to-server, no auth:sanctum - Razorpay signs the request instead
     * (see verifyWebhookSignature). This is the source of truth for payment
     * status; verify() above is just a latency optimisation on top of it.
     */
    public function webhook(Request $request): Response
    {
        $rawBody = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature', '');

        if (! $this->gateway->verifyWebhookSignature($rawBody, $signature)) {
            Log::warning('Razorpay webhook signature verification failed');

            return response('Invalid signature', 400);
        }

        $payload = json_decode($rawBody, true) ?? [];
        $event = $payload['event'] ?? null;
        $entity = $payload['payload']['payment']['entity'] ?? null;

        if (! $entity || ! isset($entity['order_id'])) {
            return response('OK', 200); // event we don't care about - still 2xx so Razorpay doesn't retry it forever
        }

        $payment = CustomerPayment::where('gateway_order_id', $entity['order_id'])->first();

        if (! $payment) {
            Log::warning('Razorpay webhook for unknown gateway_order_id', ['order_id' => $entity['order_id']]);

            return response('OK', 200);
        }

        match ($event) {
            'payment.captured' => $this->confirmation->markSuccessful(
                $payment,
                $entity['id'] ?? null,
                $this->normalizeMethod($entity['method'] ?? null),
            ),
            'payment.failed' => $this->confirmation->markFailed($payment),
            default => null,
        };

        return response('OK', 200); // any non-2xx makes Razorpay retry this same event
    }

    /** Razorpay's "wallet"/"emi" methods don't map onto our payment_mode enum - fold them into the closest bucket rather than erroring. */
    private function normalizeMethod(?string $method): ?string
    {
        return match ($method) {
            'upi' => 'upi',
            'card', 'wallet', 'emi' => 'card',
            'netbanking' => 'netbanking',
            default => null,
        };
    }
}
