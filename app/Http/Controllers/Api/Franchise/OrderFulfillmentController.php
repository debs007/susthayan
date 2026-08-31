<?php

namespace App\Http\Controllers\Api\Franchise;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Franchise\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Orders\OrderFulfillmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;

class OrderFulfillmentController extends Controller
{
    public function __construct(private readonly OrderFulfillmentService $fulfillment) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = Order::where('franchise_id', $request->user()->franchise_id)
            ->whereNotIn('status', ['pending_payment'])
            ->with(['items.product', 'franchise'])
            ->latest()
            ->paginate(20);

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
