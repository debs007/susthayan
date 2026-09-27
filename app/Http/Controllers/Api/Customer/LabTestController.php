<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\LabCenterResource;
use App\Http\Resources\LabTestResource;
use App\Models\LabCenter;
use App\Models\LabTest;
use App\Models\LabTestBlockedDate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LabTestController extends Controller
{
    /**
     * The multi-test equivalent of centers() below - a center that
     * qualifies for a batch booking has to offer every selected test,
     * not just one of them. Chaining whereHas() once per test id (rather
     * than a single whereIn) is what makes this a genuine intersection:
     * each call is its own EXISTS subquery, and Eloquent AND-combines
     * chained where clauses, so a center only matches if every one of
     * those subqueries finds a row.
     */
    public function centersForTests(Request $request): JsonResponse
    {
        $request->validate([
            'test_ids' => ['required', 'array', 'min:1'],
            'test_ids.*' => ['integer', Rule::exists('lab_tests', 'id')],
        ]);

        $testIds = $request->input('test_ids');
        $tests = LabTest::whereIn('id', $testIds)->get();

        // Same reasoning as fulfillment_type in LabTestBookingService -
        // if any selected test needs a center visit, only centers that
        // don't also require home-collection support still qualify;
        // if any test is home-collection eligible, the center has to
        // actually offer that.
        $anyHomeCollectionEligible = $tests->contains(fn (LabTest $test) => ! $test->requires_center_visit);

        $query = LabCenter::where('is_active', true)
            ->when($anyHomeCollectionEligible, fn ($q) => $q->where('offers_home_collection', true));

        foreach ($testIds as $testId) {
            $query->whereHas('tests', fn ($q) => $q->where('lab_tests.id', $testId));
        }

        $centers = $query->with(['tests' => fn ($q) => $q->whereIn('lab_tests.id', $testIds)])->get();

        $data = $centers->map(function (LabCenter $center) {
            return [
                ...(new LabCenterResource($center))->resolve(),
                // The one number the app actually needs here - every
                // selected test's price at this specific center, summed.
                // Each test's own pivot price is still available too, for
                // a line-by-line breakdown if the app wants to show one.
                'total_price' => (string) $center->tests->sum(fn ($test) => (float) $test->pivot->price),
                'test_prices' => $center->tests->map(fn ($test) => [
                    'lab_test_id' => $test->id,
                    'price' => (string) $test->pivot->price,
                ]),
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function index(Request $request): JsonResponse
    {
        $tests = LabTest::with('category')
            ->where('is_active', true)
            ->when($request->filled('category'), fn ($q) => $q->whereHas('category', fn ($c) => $c->where('name', $request->string('category'))))
            ->orderBy('name')
            ->get();

        return response()->json(['data' => LabTestResource::collection($tests)]);
    }

    public function show(LabTest $labTest): JsonResponse
    {
        abort_unless($labTest->is_active, 404);

        return response()->json(['data' => new LabTestResource($labTest->load('category'))]);
    }

    /**
     * Centers are always shown for a test (the customer's choice of who
     * processes their sample, even for a home collection) - the only
     * thing that differs by visit type is which centers qualify. A
     * center-visit test just needs the center to offer that test at all;
     * a home-collection test additionally requires offers_home_collection,
     * since going to a center in person isn't happening either way.
     */
    public function centers(LabTest $labTest): JsonResponse
    {
        abort_unless($labTest->is_active, 404);

        $centers = $labTest->centers()
            ->where('lab_centers.is_active', true)
            ->when(! $labTest->requires_center_visit, fn ($q) => $q->where('offers_home_collection', true))
            ->get();

        return response()->json(['data' => LabCenterResource::collection($centers)]);
    }

    /** Dates the app should disable in the date picker - network-wide only, see LabTestBlockedDate. */
    public function blockedDates(): JsonResponse
    {
        $dates = LabTestBlockedDate::whereNull('franchise_id')
            ->where('date', '>=', now()->toDateString())
            ->pluck('date')
            ->map(fn ($date) => $date->toDateString());

        return response()->json(['data' => $dates]);
    }
}
