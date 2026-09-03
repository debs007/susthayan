<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateLabTestRequest;
use App\Models\LabTest;
use App\Models\LabTestCategory;
use App\Traits\GeneratesUniqueSlugs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LabTestController extends Controller
{
    use GeneratesUniqueSlugs;

    public function index(Request $request): View
    {
        $tests = LabTest::with('category')
            ->when($request->filled('category'), fn ($q) => $q->where('lab_test_category_id', $request->integer('category')))
            ->orderBy('name')
            ->get();

        $categories = LabTestCategory::orderBy('name')->get();

        return view('admin.lab-tests.index', compact('tests', 'categories'));
    }

    public function create(): View
    {
        $categories = LabTestCategory::orderBy('name')->get();

        return view('admin.lab-tests.create', compact('categories'));
    }

    public function store(CreateLabTestRequest $request): RedirectResponse
    {
        $test = LabTest::create([
            ...$request->validated(),
            'slug' => $this->uniqueSlug(LabTest::class, $request->validated('name')),
            'requires_center_visit' => $request->boolean('requires_center_visit'),
            'is_active' => true,
        ]);

        return redirect()->route('admin.lab-tests.edit', $test)->with('success', "\"{$test->name}\" was added to the catalog.");
    }

    public function edit(LabTest $labTest): View
    {
        $categories = LabTestCategory::orderBy('name')->get();

        return view('admin.lab-tests.edit', ['test' => $labTest, 'categories' => $categories]);
    }

    public function update(CreateLabTestRequest $request, LabTest $labTest): RedirectResponse
    {
        $labTest->update([
            ...$request->validated(),
            'requires_center_visit' => $request->boolean('requires_center_visit'),
        ]);

        return redirect()->route('admin.lab-tests.edit', $labTest)->with('success', 'Saved.');
    }

    public function toggleActive(LabTest $labTest): RedirectResponse
    {
        $labTest->update(['is_active' => ! $labTest->is_active]);

        return back()->with('success', $labTest->is_active ? "\"{$labTest->name}\" is now visible to customers." : "\"{$labTest->name}\" is now hidden from customers.");
    }
}
