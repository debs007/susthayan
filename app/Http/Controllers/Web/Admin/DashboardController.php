<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAssignment;
use App\Models\Franchise;
use App\Models\FranchiseSettlement;
use App\Models\GoodsReceipt;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Prescription;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * No per-product reorder level exists yet (Product has no threshold
     * field) - this is a fixed, network-wide approximation, not a real
     * per-SKU reorder point. Good enough for a dashboard alert, not a
     * substitute for real inventory planning.
     */
    private const int LOW_STOCK_THRESHOLD = 20;

    private const int ACTIVITY_FEED_SIZE = 8;

    public function index(): View
    {
        $today = today();

        $todaysOrders = Order::whereDate('created_at', $today)
            ->whereNotIn('status', ['pending_payment'])
            ->get();

        $todaysRevenue = (float) Order::whereIn('status', ['delivered', 'picked_up'])
            ->whereDate('delivered_at', $today)
            ->sum('total_amount');

        $yesterdaysRevenue = (float) Order::whereIn('status', ['delivered', 'picked_up'])
            ->whereDate('delivered_at', $today->copy()->subDay())
            ->sum('total_amount');

        $revenueChangePercent = $yesterdaysRevenue > 0
            ? round((($todaysRevenue - $yesterdaysRevenue) / $yesterdaysRevenue) * 100, 1)
            : null;

        $pendingPrescriptions = Prescription::where('verification_status', 'pending')->count();

        $deliverySuccessRate = $this->deliverySuccessRate();

        $recentOrders = Order::whereNotIn('status', ['pending_payment'])
            ->with(['franchise:id,name', 'user:id,name'])
            ->latest()
            ->limit(8)
            ->get();

        $hourlyOrders = $this->hourlyOrderCounts($todaysOrders);

        $recentActivity = $this->recentActivityFeed();

        return view('admin.dashboard', [
            'activeFranchiseCount' => Franchise::where('status', 'active')->count(),
            'todaysRevenue' => $todaysRevenue,
            'revenueChangePercent' => $revenueChangePercent,
            'todaysOrderCount' => $todaysOrders->count(),
            'pendingPrescriptions' => $pendingPrescriptions,
            'deliverySuccessRate' => $deliverySuccessRate,
            'recentOrders' => $recentOrders,
            'hourlyOrders' => $hourlyOrders,
            'recentActivity' => $recentActivity,
        ]);
    }

    private function deliverySuccessRate(): ?float
    {
        $counts = DeliveryAssignment::where('created_at', '>=', now()->subDays(7))
            ->whereIn('status', ['delivered', 'failed'])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $delivered = (int) ($counts['delivered'] ?? 0);
        $failed = (int) ($counts['failed'] ?? 0);
        $attempts = $delivered + $failed;

        return $attempts > 0 ? round(($delivered / $attempts) * 100, 1) : null;
    }

    /** @return Collection<int, int> hour (0-23, only up to the current hour) => order count */
    private function hourlyOrderCounts(Collection $todaysOrders): Collection
    {
        $byHour = $todaysOrders->groupBy(fn (Order $order) => (int) $order->created_at->format('G'));

        return collect(range(0, now()->hour))->mapWithKeys(
            fn (int $hour) => [$hour => $byHour->get($hour, collect())->count()]
        );
    }

    /**
     * Deliberately mixes 4 different event types into one chronological
     * list rather than 4 separate widgets - GRNs, prescription reviews,
     * low-stock, and settlements are all "things that just happened"
     * from an admin's point of view, even though they come from
     * completely different tables.
     */
    private function recentActivityFeed(): Collection
    {
        $grns = GoodsReceipt::with(['franchise:id,name', 'items'])
            ->latest('received_date')
            ->limit(self::ACTIVITY_FEED_SIZE)
            ->get()
            ->map(fn (GoodsReceipt $grn) => [
                'message' => "GRN received at {$grn->franchise->name} — {$grn->items->count()} items",
                'timestamp' => $grn->created_at,
            ]);

        $prescriptions = Prescription::whereNotNull('verified_at')
            ->with('order:id')
            ->latest('verified_at')
            ->limit(self::ACTIVITY_FEED_SIZE)
            ->get()
            ->map(fn (Prescription $prescription) => [
                'message' => 'Prescription '.$prescription->verification_status.
                    ($prescription->order_id ? ": Order #{$prescription->order_id}" : ''),
                'timestamp' => $prescription->verified_at,
            ]);

        $lowStock = Inventory::with(['product:id,name', 'franchise:id,name'])
            ->selectRaw('product_id, franchise_id, SUM(quantity - reserved_quantity) as available, MAX(updated_at) as last_change')
            ->groupBy('product_id', 'franchise_id')
            ->havingRaw('SUM(quantity - reserved_quantity) < ?', [self::LOW_STOCK_THRESHOLD])
            ->havingRaw('SUM(quantity - reserved_quantity) > 0')
            ->orderByDesc('last_change')
            ->limit(self::ACTIVITY_FEED_SIZE)
            ->get()
            ->map(fn (Inventory $row) => [
                'message' => "Low stock: {$row->product->name} — {$row->franchise->name} ({$row->available} left)",
                'timestamp' => $row->last_change,
            ]);

        $settlements = FranchiseSettlement::where('status', 'paid')
            ->with('franchise:id,name')
            ->latest('paid_at')
            ->limit(self::ACTIVITY_FEED_SIZE)
            ->get()
            ->map(fn (FranchiseSettlement $settlement) => [
                'message' => "Franchise settlement paid: {$settlement->franchise->name} — ₹".number_format($settlement->net_payable, 0),
                'timestamp' => $settlement->paid_at,
            ]);

        return $grns->concat($prescriptions)->concat($lowStock)->concat($settlements)
            ->filter(fn ($item) => $item['timestamp'] !== null)
            ->sortByDesc('timestamp')
            ->take(self::ACTIVITY_FEED_SIZE)
            ->values();
    }
}
