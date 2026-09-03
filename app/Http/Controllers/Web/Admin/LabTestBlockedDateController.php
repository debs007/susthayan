<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlockTestDateRequest;
use App\Models\LabTestBlockedDate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * System-wide only (franchise_id always null here) - see the migration's
 * docblock for why this manages one shared calendar rather than a
 * per-franchise one.
 */
class LabTestBlockedDateController extends Controller
{
    public function index(): View
    {
        $blockedDates = LabTestBlockedDate::whereNull('franchise_id')
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->get();

        return view('admin.lab-test-blocked-dates.index', compact('blockedDates'));
    }

    public function store(BlockTestDateRequest $request): RedirectResponse
    {
        $blocked = LabTestBlockedDate::create([
            'date' => $request->validated('date'),
            'reason' => $request->validated('reason'),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.lab-test-blocked-dates.index')
            ->with('success', "{$blocked->date->format('d M Y')} is now blocked for lab test bookings.");
    }

    public function destroy(LabTestBlockedDate $labTestBlockedDate): RedirectResponse
    {
        $date = $labTestBlockedDate->date->format('d M Y');
        $labTestBlockedDate->delete();

        return redirect()->route('admin.lab-test-blocked-dates.index')->with('success', "{$date} is bookable again.");
    }
}
