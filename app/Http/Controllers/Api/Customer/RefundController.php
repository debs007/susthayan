<?php

namespace App\Http\Controllers\Api\Customer;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\InitiateRefundRequest;
use App\Models\Order;
use App\Models\Refund;
use App\Services\Inventory\StockService;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function __construct(
        private readonly PaymentGatewayInterface $gateway,
        private readonly StockService $stock,
    ) {}

    /**
     * Covers cancellation before fulfillment (confirmed/preparing/ready-for-
     * dispatch - stock was only ever reserved, so it's simply released).
     * Refunding a delivered/picked-up order is a return, which needs
     * physical stock to come back in and isn't built yet - rejected below
     * with an explicit message rather than silently mishandling it.
     */
    public function store(InitiateRefundRequest $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->status->isFulfilled()) {
            return response()->json([
                'message' => 'This order has already been delivered/picked up - returns on fulfilled orders aren\'t supported yet.',
            ], 422);
        }

        $orderItems = $order->items()->whereIn('id', $request->validated('order_item_ids'))->get();

        if ($orderItems->isEmpty()) {
            return response()->json(['message' => 'None of those items belong to this order.'], 422);
        }

        $payment = $order->payments()->where('status', PaymentStatus::Success)->latest()->first();

        if (! $payment) {
            return response()->json(['message' => 'No successful payment found for this order.'], 422);
        }

        $refundAmount = (float) $orderItems->sum('total_price');
        $amountInPaise = (int) round($refundAmount * 100);

        $gatewayRefundId = $this->gateway->refund($payment->gateway_txn_id, $amountInPaise);

        $refunds = $orderItems->map(fn ($item) => Refund::create([
            'customer_payment_id' => $payment->id,
            'order_item_id' => $item->id,
            'amount' => $item->total_price,
            'reason' => $request->validated('reason'),
            'gateway_refund_id' => $gatewayRefundId,
            'status' => 'success',
            'processed_at' => now(),
        ]));

        foreach ($orderItems as $item) {
            $this->stock->releaseForItem($order->franchise_id, $item);
        }

        // Whole order refunded (every item covered) vs. partial - only close
        // the order out in the former case.
        $allItemsRefunded = $order->items()->count() === $order->items()
            ->whereIn('id', Refund::where('customer_payment_id', $payment->id)->pluck('order_item_id'))
            ->count();

        if ($allItemsRefunded) {
            $order->update(['status' => 'refunded']);
        }

        return response()->json([
            'refund_ids' => $refunds->pluck('id'),
            'gateway_refund_id' => $gatewayRefundId,
            'amount_refunded' => (string) $refundAmount,
            'order_status' => $order->status,
        ]);
    }

    public function index(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $refunds = Refund::whereIn('order_item_id', $order->items()->pluck('id'))->get();

        return response()->json(['refunds' => $refunds]);
    }
}
