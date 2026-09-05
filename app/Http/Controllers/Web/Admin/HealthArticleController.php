<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateHealthArticleRequest;
use App\Models\HealthArticle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HealthArticleController extends Controller
{
    public function index(): View
    {
        $articles = HealthArticle::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.health-articles.index', compact('articles'));
    }

    public function store(CreateHealthArticleRequest $request): RedirectResponse
    {
        HealthArticle::create([
            ...$request->safe()->except('thumbnail'),
            'thumbnail_path' => $request->file('thumbnail')->store('health-articles', 'r2'),
            'is_active' => true,
        ]);

        return redirect()->route('admin.health-articles.index')->with('success', 'Article added.');
    }

    public function toggleActive(HealthArticle $healthArticle): RedirectResponse
    {
        $healthArticle->update(['is_active' => ! $healthArticle->is_active]);

        return back()->with('success', $healthArticle->is_active ? 'Article is now live in the app.' : 'Article is now hidden from the app.');
    }

    public function destroy(HealthArticle $healthArticle): RedirectResponse
    {
        Storage::disk('r2')->delete($healthArticle->thumbnail_path);
        $healthArticle->delete();

        return back()->with('success', 'Article removed.');
    }
}
