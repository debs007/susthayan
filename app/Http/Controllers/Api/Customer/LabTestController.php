<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\LabCenterResource;
use App\Http\Resources\LabTestResource;
use App\Models\LabTest;
use App\Models\LabTestBlockedDate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LabTestController extends Controller
{
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
