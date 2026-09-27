<?php

namespace App\Http\Controllers\Api\Franchise;

use App\Http\Controllers\Controller;
use App\Http\Requests\Franchise\CreatePurchaseOrderRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Services\Purchasing\PurchaseOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;

class PurchaseOrderController extends Controller
{
    public function __construct(private readonly PurchaseOrderService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $purchaseOrders = PurchaseOrder::where('franchise_id', $request->user()->franchise_id)
            ->with(['supplier', 'items.product'])
            ->latest()
            ->paginate(20);

        return PurchaseOrderResource::collection($purchaseOrders);
    }

    // franchise.scope (route middleware) already confirms this PO belongs to the caller's franchise.
    public function show(PurchaseOrder $purchaseOrder): PurchaseOrderResource
    {
        return new PurchaseOrderResource(
            $purchaseOrder->load(['supplier', 'items.product', 'goodsReceipts.items.product', 'invoices.payments'])
        );
    }

    public function store(CreatePurchaseOrderRequest $request): JsonResponse
    {
        $po = $this->service->create(
            creator: $request->user(),
            franchiseId: $request->user()->franchise_id,
            supplierId: $request->validated('supplier_id'),
            items: $request->validated('items'),
            expectedDate: $request->validated('expected_date'),
        );

        return (new PurchaseOrderResource($po))->response()->setStatusCode(201);
    }

    // role:Franchise Owner is applied on this route in routes/api.php - Staff can create POs, only the Owner approves them.
    public function approve(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        try {
            $po = $this->service->approve($purchaseOrder, $request->user());
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['purchase_order' => new PurchaseOrderResource($po)]);
    }

    public function reject(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        try {
            $po = $this->service->reject($purchaseOrder, $request->user());
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['purchase_order' => new PurchaseOrderResource($po)]);
    }
}
