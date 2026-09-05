<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Wallet\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(private readonly WalletService $wallet) {}

    public function show(Request $request): JsonResponse
    {
        $wallet = $this->wallet->getOrCreateForUser($request->user());
        $transactions = $wallet->transactions()->limit(50)->get();

        return response()->json([
            'balance' => (string) $wallet->balance,
            'transactions' => $transactions->map(fn ($t) => [
                'id' => $t->id,
                'type' => $t->type,
                'amount' => (string) $t->amount,
                'description' => $t->description,
                'created_at' => $t->created_at->toIso8601String(),
            ]),
        ]);
    }

    /**
     * Returns an Order, not a payment-specific shape - same reasoning as
     * every other "this costs money" feature in this app (lab tests,
     * appointments): the Flutter app hands this straight to the existing
     * OrderProvider.initiatePayment()/verifyPayment() flow unchanged, and
     * PaymentConfirmationService credits the wallet once that payment
     * actually succeeds - nothing is credited here, before payment.
     */
    public function topup(Request $request): JsonResponse
    {
        $request->validate(['amount' => ['required', 'numeric', 'min:1', 'max:50000']]);

        $amount = $request->float('amount');

        $order = Order::create([
            'order_type' => 'wallet_topup',
            'user_id' => $request->user()->id,
            'franchise_id' => null,
            'status' => 'pending_payment',
            'fulfillment_type' => 'pickup', // not a physical fulfillment at all - closest existing enum value, same reasoning as lab test/appointment orders
            'address_id' => null,
            'subtotal_amount' => $amount,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'delivery_charge' => 0,
            'total_amount' => $amount,
        ]);

        return (new OrderResource($order))->response()->setStatusCode(201);
    }
}
