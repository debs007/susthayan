<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Services\Purchasing\VendorLedgerService;
use Illuminate\Http\JsonResponse;

class VendorLedgerController extends Controller
{
    public function __construct(private readonly VendorLedgerService $service) {}

    /** Network-wide, every franchise's outstanding combined. */
    public function outstanding(): JsonResponse
    {
        return response()->json(['vendors' => $this->service->outstanding()]);
    }

    public function ledger(Supplier $supplier): JsonResponse
    {
        return response()->json($this->service->ledger($supplier));
    }
}
