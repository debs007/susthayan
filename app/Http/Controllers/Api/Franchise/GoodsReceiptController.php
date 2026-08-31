<?php

namespace App\Http\Controllers\Api\Franchise;

use App\Http\Controllers\Controller;
use App\Http\Requests\Franchise\ReceiveGoodsRequest;
use App\Http\Resources\GoodsReceiptResource;
use App\Models\PurchaseOrder;
use App\Services\Purchasing\GoodsReceiptService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class GoodsReceiptController extends Controller
{
    public function __construct(private readonly GoodsReceiptService $service) {}

    // franchise.scope already confirmed $purchaseOrder belongs to the caller's franchise.
    public function store(ReceiveGoodsRequest $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        try {
            $grn = $this->service->receive(
                receiver: $request->user(),
                po: $purchaseOrder,
                items: $request->validated('items'),
                receivedDate: $request->validated('received_date'),
            );
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new GoodsReceiptResource($grn))->response()->setStatusCode(201);
    }
}
