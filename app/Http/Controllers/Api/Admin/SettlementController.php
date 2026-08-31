<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GenerateSettlementRequest;
use App\Http\Resources\FranchiseSettlementResource;
use App\Models\Franchise;
use App\Models\FranchiseSettlement;
use App\Services\Settlement\SettlementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;

class SettlementController extends Controller
{
    public function __construct(private readonly SettlementService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $settlements = FranchiseSettlement::query()
            ->when($request->filled('franchise_id'), fn ($q) => $q->where('franchise_id', $request->integer('franchise_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->with('franchise')
            ->latest()
            ->paginate(20);

        return FranchiseSettlementResource::collection($settlements);
    }

    public function generate(GenerateSettlementRequest $request, Franchise $franchise): JsonResponse
    {
        try {
            $settlement = $this->service->generate(
                $franchise,
                $request->validated('period_start'),
                $request->validated('period_end'),
            );
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new FranchiseSettlementResource($settlement->load('franchise')))
            ->response()
            ->setStatusCode(201);
    }

    /** Runs generate() for every active franchise in one go - a franchise with an overlap or nothing to settle doesn't block the rest. */
    public function generateAll(GenerateSettlementRequest $request): JsonResponse
    {
        $results = $this->service->generateForAllActiveFranchises(
            $request->validated('period_start'),
            $request->validated('period_end'),
        )->map(fn (array $result) => [
            'franchise_id' => $result['franchise_id'],
            'franchise_name' => $result['franchise_name'],
            'settlement' => isset($result['settlement']) ? new FranchiseSettlementResource($result['settlement']) : null,
            'error' => $result['error'] ?? null,
        ]);

        return response()->json(['results' => $results]);
    }

    public function release(FranchiseSettlement $settlement): JsonResponse
    {
        try {
            $settlement = $this->service->release($settlement);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['settlement' => new FranchiseSettlementResource($settlement)]);
    }
}
