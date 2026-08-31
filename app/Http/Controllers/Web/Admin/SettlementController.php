<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GenerateSettlementRequest;
use App\Models\Franchise;
use App\Models\FranchiseSettlement;
use App\Services\Settlement\SettlementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class SettlementController extends Controller
{
    public function __construct(private readonly SettlementService $service) {}

    public function index(Request $request): View
    {
        $settlements = FranchiseSettlement::query()
            ->when($request->filled('franchise_id'), fn ($q) => $q->where('franchise_id', $request->integer('franchise_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->with('franchise')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $franchises = Franchise::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return view('admin.settlements.index', compact('settlements', 'franchises'));
    }

    public function generate(GenerateSettlementRequest $request): RedirectResponse
    {
        $periodStart = $request->validated('period_start');
        $periodEnd = $request->validated('period_end');

        // "Generate for all" wins if a franchise_id wasn't also given -
        // the form has two submit buttons sharing one set of date fields.
        if (! $request->filled('franchise_id')) {
            $results = $this->service->generateForAllActiveFranchises($periodStart, $periodEnd);
            $succeeded = $results->whereNull('error')->count();
            $failed = $results->whereNotNull('error')->count();

            return redirect()->route('admin.settlements.index')->with(
                'success',
                "Generated {$succeeded} settlement(s).".($failed ? " {$failed} skipped (overlapping period or no sales)." : '')
            );
        }

        try {
            $franchise = Franchise::findOrFail($request->validated('franchise_id'));
            $this->service->generate($franchise, $periodStart, $periodEnd);
        } catch (RuntimeException $e) {
            return back()->withErrors(['period_end' => $e->getMessage()])->withInput();
        }

        return redirect()->route('admin.settlements.index')->with('success', "Settlement generated for {$franchise->name}.");
    }

    public function release(FranchiseSettlement $settlement): RedirectResponse
    {
        try {
            $this->service->release($settlement);
        } catch (RuntimeException $e) {
            return back()->withErrors(['release' => $e->getMessage()]);
        }

        return redirect()->route('admin.settlements.index')->with('success', 'Settlement released - payout sent.');
    }
}
