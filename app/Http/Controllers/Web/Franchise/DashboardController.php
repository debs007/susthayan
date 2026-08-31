<?php

namespace App\Http\Controllers\Web\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const int LOW_STOCK_THRESHOLD = 20;

    private const int URGENT_DAYS = 30;

    private const int APPROACHING_DAYS = 90;

    public function index(Request $request): View
    {
        $franchiseId = $request->user()->franchise_id;
        $today = today();

        $todaysOrders = Order::where('franchise_id', $franchiseId)
            ->whereDate('created_at', $today)
            ->whereNotIn('status', ['pending_payment'])
            ->count();

        $yesterdaysOrders = Order::where('franchise_id', $franchiseId)
            ->whereDate('created_at', $today->copy()->subDay())
            ->whereNotIn('status', ['pending_payment'])
            ->count();

        // Network-wide, not franchise-scoped - any pharmacist can review any
        // pending prescription (see Api\Franchise\PrescriptionVerificationController),
        // so this stays consistent with that rather than implying a
        // per-store queue that doesn't actually exist.
        $pendingPrescriptions = Prescription::where('verification_status', 'pending')->count();

        $lowStockCount = Inventory::where('franchise_id', $franchiseId)
            ->selectRaw('product_id, SUM(quantity - reserved_quantity) as available')
            ->groupBy('product_id')
            ->havingRaw('SUM(quantity - reserved_quantity) < ?', [self::LOW_STOCK_THRESHOLD])
            ->havingRaw('SUM(quantity - reserved_quantity) > 0')
            ->get()
            ->count();

        $recentOrders = Order::where('franchise_id', $franchiseId)
            ->whereNotIn('status', ['pending_payment'])
            ->with('user:id,name')
            ->latest()
            ->limit(6)
            ->get();

        $watchBatches = Inventory::where('franchise_id', $franchiseId)
            ->where('quantity', '>', 0)
            ->where('expiry_date', '<=', now()->addDays(self::APPROACHING_DAYS))
            ->with('product:id,name')
            ->orderBy('expiry_date')
            ->limit(6)
            ->get()
            ->map(function (Inventory $batch) {
                $daysToExpiry = (int) now()->diffInDays($batch->expiry_date, false);
                $batch->freshness = $daysToExpiry < self::URGENT_DAYS ? 'urgent' : 'approaching';
                $batch->days_to_expiry = $daysToExpiry;

                return $batch;
            });

        return view('franchise.dashboard', [
            'todaysOrderCount' => $todaysOrders,
            'orderChangeVsYesterday' => $todaysOrders - $yesterdaysOrders,
            'pendingPrescriptions' => $pendingPrescriptions,
            'lowStockCount' => $lowStockCount,
            'recentOrders' => $recentOrders,
            'watchBatches' => $watchBatches,
        ]);
    }
}
