<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSupplierRequest;
use App\Http\Requests\Admin\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/** Full CRUD - suppliers are onboarded/vetted centrally, not per-franchise. */
class SupplierController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return SupplierResource::collection(Supplier::orderBy('name')->paginate(30));
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = Supplier::create([...$request->validated(), 'is_active' => true]);

        return (new SupplierResource($supplier))->response()->setStatusCode(201);
    }

    public function show(Supplier $supplier): SupplierResource
    {
        return new SupplierResource($supplier);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): SupplierResource
    {
        $supplier->update($request->validated());

        return new SupplierResource($supplier->fresh());
    }
}
