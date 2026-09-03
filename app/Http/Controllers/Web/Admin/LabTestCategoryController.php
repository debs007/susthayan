<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateLabTestCategoryRequest;
use App\Models\LabTestCategory;
use App\Traits\GeneratesUniqueSlugs;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LabTestCategoryController extends Controller
{
    use GeneratesUniqueSlugs;

    public function index(): View
    {
        $categories = LabTestCategory::withCount('labTests')->orderBy('name')->get();

        return view('admin.lab-test-categories.index', compact('categories'));
    }

    public function store(CreateLabTestCategoryRequest $request): RedirectResponse
    {
        $category = LabTestCategory::create([
            'name' => $request->validated('name'),
            'slug' => $this->uniqueSlug(LabTestCategory::class, $request->validated('name')),
            'is_active' => true,
        ]);

        return redirect()->route('admin.lab-test-categories.index')->with('success', "\"{$category->name}\" was added.");
    }
}
