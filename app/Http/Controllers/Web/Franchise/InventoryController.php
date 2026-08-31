<?php

namespace App\Http\Controllers\Web\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    /** Below this many days to expiry: urgent. Below this: approaching. Otherwise: ok. Same signature every stat card/badge in the app uses. */
    private const int URGENT_DAYS = 30;

    private const int APPROACHING_DAYS = 90;

    public function index(Request $request): View
    {
        $batches = Inventory::where('franchise_id', $request->user()->franchise_id)
            ->where('quantity', '>', 0)
            ->with('product')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.trim($request->string('q')).'%';
                $query->whereHas('product', fn ($q) => $q->where('name', 'like', $term));
            })
            ->when($request->string('filter') === 'expiring', fn ($q) => $q->where('expiry_date', '<=', now()->addDays(self::APPROACHING_DAYS)))
            ->orderBy('expiry_date')
            ->paginate(30)
            ->withQueryString();

        $batches->getCollection()->transform(function (Inventory $batch) {
            $daysToExpiry = (int) now()->diffInDays($batch->expiry_date, false);
            $batch->freshness = match (true) {
                $daysToExpiry < self::URGENT_DAYS => 'urgent',
                $daysToExpiry < self::APPROACHING_DAYS => 'approaching',
                default => 'ok',
            };
            $batch->days_to_expiry = $daysToExpiry;

            return $batch;
        });

        return view('franchise.inventory.index', compact('batches'));
    }
}
