<?php

namespace App\Http\Controllers\Api\Franchise;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Franchise\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Inventory\StockService;
use App\Services\Orders\OrderFulfillmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;

class OrderFulfillmentController extends Controller
{
    public function __construct(
        private readonly OrderFulfillmentService $fulfillment,
        private readonly StockService $stock,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $franchiseId = $request->user()->franchise_id;

        $orders = Order::where('franchise_id', $franchiseId)
            ->whereNotIn('status', ['pending_payment'])
            ->with(['items.product', 'franchise', 'user'])
            ->latest()
            ->paginate(20);

        // Same dynamic-property pattern as ProductPricingService::attach()
        // - lets each item show whether this franchise can actually
        // fulfil it, without a separate request per order or per item.
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $item->available_quantity = $this->stock->available($franchiseId, $item->product_id);
            }
        }

        return OrderResource::collection($orders);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        try {
            $order = $this->fulfillment->transition($order, $request->validated('status'));
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (InsufficientStockException $e) {
            // Physical stock moved (damage, another sale) between reservation
            // and fulfillment - rare, but real. Needs ops attention, not a
            // silent failure.
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return response()->json(['order' => new OrderResource($order)]);
    }
}
