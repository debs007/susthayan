<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomeBannerResource;
use App\Models\HomeBanner;
use Illuminate\Http\JsonResponse;

class HomeBannerController extends Controller
{
    public function index(): JsonResponse
    {
        $banners = HomeBanner::where('is_active', true)->where('platform', 'mobile')->orderBy('sort_order')->orderBy('id')->get();

        return response()->json(['data' => HomeBannerResource::collection($banners)]);
    }
}
