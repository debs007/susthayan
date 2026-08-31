<?php

namespace App\Http\Controllers\Api\Customer;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\PrescriptionRequiredException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Services\Orders\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function __construct(private readonly CheckoutService $checkout) {}

    /**
     * Cart -> Order (still pending_payment - see PaymentController for what
     * actually confirms it). Stock/prescription checks happen inside
     * CheckoutService; this just translates its exceptions to HTTP codes.
     */
    public function store(PlaceOrderRequest $request): JsonResponse
    {
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);

        try {
            $order = $this->checkout->placeOrder(
                user: $request->user(),
                cart: $cart,
                franchiseId: $request->validated('franchise_id'),
                fulfillmentType: $request->validated('fulfillment_type'),
                addressId: $request->validated('address_id'),
            );
        } catch (InsufficientStockException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        } catch (PrescriptionRequiredException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new OrderResource($order->load(['items.product', 'franchise'])))
            ->response()
            ->setStatusCode(201);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items.product', 'franchise'])
            ->latest()
            ->paginate(15);

        return OrderResource::collection($orders);
    }

    public function show(Request $request, Order $order): OrderResource
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        return new OrderResource($order->load(['items.product', 'items.batches', 'franchise']));
    }
}
