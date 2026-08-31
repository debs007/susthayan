<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateCategoryRequest;
use App\Models\Category;
use App\Traits\GeneratesUniqueSlugs;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use GeneratesUniqueSlugs;

    public function index(): JsonResponse
    {
        return response()->json([
            'categories' => Category::with('parent:id,name')->orderBy('name')->get(),
        ]);
    }

    public function store(CreateCategoryRequest $request): JsonResponse
    {
        $category = Category::create([
            'name' => $request->validated('name'),
            'parent_id' => $request->validated('parent_id'),
            'slug' => $this->uniqueSlug(Category::class, $request->validated('name')),
            'is_active' => true,
        ]);

        return response()->json(['category' => $category], 201);
    }
}
