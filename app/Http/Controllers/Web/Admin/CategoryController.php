<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateCategoryRequest;
use App\Models\Category;
use App\Traits\GeneratesUniqueSlugs;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use GeneratesUniqueSlugs;

    public function index(): View
    {
        $categories = Category::with('parent:id,name')->withCount('products')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(CreateCategoryRequest $request): RedirectResponse
    {
        $category = Category::create([
            'name' => $request->validated('name'),
            'parent_id' => $request->validated('parent_id'),
            'slug' => $this->uniqueSlug(Category::class, $request->validated('name')),
            'is_active' => true,
        ]);

        return redirect()->route('admin.categories.index')->with('success', "\"{$category->name}\" was added.");
    }
}
