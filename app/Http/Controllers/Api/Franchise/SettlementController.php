<?php

namespace App\Http\Controllers\Api\Franchise;

use App\Http\Controllers\Controller;
use App\Http\Resources\FranchiseSettlementResource;
use App\Models\FranchiseSettlement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SettlementController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $settlements = FranchiseSettlement::where('franchise_id', $request->user()->franchise_id)
            ->latest('period_start')
            ->paginate(20);

        return FranchiseSettlementResource::collection($settlements);
    }
}
