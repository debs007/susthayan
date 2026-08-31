<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/** Cross-franchise oversight - creation/approval stays a franchise-level action (Api\Franchise\PurchaseOrderController). */
class PurchaseOrderController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $purchaseOrders = PurchaseOrder::query()
            ->when($request->filled('franchise_id'), fn ($query) => $query->where('franchise_id', $request->integer('franchise_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->with(['supplier', 'franchise', 'items.product'])
            ->latest()
            ->paginate(20);

        return PurchaseOrderResource::collection($purchaseOrders);
    }
}
