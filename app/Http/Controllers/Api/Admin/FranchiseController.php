<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateFranchiseRequest;
use App\Http\Requests\Admin\UpdateFranchiseBankDetailsRequest;
use App\Http\Requests\Admin\UpdateFranchiseRequest;
use App\Models\Franchise;
use App\Traits\GeneratesUniqueSlugs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class FranchiseController extends Controller
{
    use GeneratesUniqueSlugs;

    public function index(): AnonymousResourceCollection
    {
        return JsonResource::collection(
            Franchise::orderBy('name')->get([
                'id', 'name', 'city', 'status', 'commission_percentage', 'bank_account_number',
            ])
        );
    }

    public function store(CreateFranchiseRequest $request): JsonResponse
    {
        $franchise = Franchise::create([
            ...$request->validated(),
            'slug' => $this->uniqueSlug(Franchise::class, $request->validated('name')),
            'status' => 'active',
        ]);

        return response()->json(['franchise' => $franchise], 201);
    }

    public function show(Franchise $franchise): JsonResponse
    {
        return response()->json(['franchise' => $franchise]);
    }

    public function update(UpdateFranchiseRequest $request, Franchise $franchise): JsonResponse
    {
        $franchise->update($request->validated());

        return response()->json(['franchise' => $franchise->fresh()]);
    }

    public function updateBankDetails(UpdateFranchiseBankDetailsRequest $request, Franchise $franchise): JsonResponse
    {
        $franchise->update($request->validated());

        return response()->json(['franchise' => $franchise->fresh()]);
    }
}
