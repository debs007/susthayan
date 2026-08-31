<?php

namespace App\Http\Controllers\Api\Franchise;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/** Read-only here - suppliers are onboarded centrally, see Api\Admin\SupplierController. */
class SupplierController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return SupplierResource::collection(Supplier::where('is_active', true)->orderBy('name')->get());
    }
}
