<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\HealthArticleResource;
use App\Models\HealthArticle;
use Illuminate\Http\JsonResponse;

class HealthArticleController extends Controller
{
    public function index(): JsonResponse
    {
        $articles = HealthArticle::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();

        return response()->json(['data' => HealthArticleResource::collection($articles)]);
    }
}
