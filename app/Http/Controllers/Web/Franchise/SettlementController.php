<?php

namespace App\Http\Controllers\Web\Franchise;

use App\Http\Controllers\Controller;
use App\Models\FranchiseSettlement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettlementController extends Controller
{
    public function index(Request $request): View
    {
        $settlements = FranchiseSettlement::where('franchise_id', $request->user()->franchise_id)
            ->latest('period_start')
            ->paginate(20);

        return view('franchise.settlements.index', compact('settlements'));
    }
}
