<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\FranchiseResource;
use App\Models\Franchise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * The endpoint that was missing this whole build - without this, a
 * customer's cart could never resolve a franchise_id, which blocked
 * checkout from ever completing end-to-end. See the Flutter app's
 * CartScreen/CheckoutScreen for the workaround that existed before this.
 */
class FranchiseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $franchises = Franchise::where('status', 'active')
            ->when($request->filled('city'), fn ($q) => $q->where('city', 'like', '%'.$request->string('city').'%'))
            ->when($request->filled('pincode'), fn ($q) => $q->where('pincode', $request->string('pincode')))
            ->orderBy('name')
            ->get();

        return response()->json(['data' => FranchiseResource::collection($franchises)]);
    }
}
