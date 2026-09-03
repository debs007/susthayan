<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
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
