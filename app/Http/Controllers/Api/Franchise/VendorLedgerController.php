<?php

namespace App\Http\Controllers\Api\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Services\Purchasing\VendorLedgerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorLedgerController extends Controller
{
    public function __construct(private readonly VendorLedgerService $service) {}

    public function outstanding(Request $request): JsonResponse
    {
        return response()->json(['vendors' => $this->service->outstanding($request->user()->franchise_id)]);
    }

    public function ledger(Request $request, Supplier $supplier): JsonResponse
    {
        return response()->json($this->service->ledger($supplier, $request->user()->franchise_id));
    }
}
