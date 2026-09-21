<?php

namespace App\Http\Controllers\Web\Storefront;

use App\Http\Controllers\Controller;
use App\Models\HealthArticle;
use Illuminate\View\View;

class HealthArticleController extends Controller
{
    public function index(): View
    {
        $articles = HealthArticle::where('is_active', true)->orderBy('sort_order')->get();

        return view('storefront.health-articles', compact('articles'));
    }
}
