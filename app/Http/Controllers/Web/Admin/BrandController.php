<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateBrandRequest;
use App\Models\Brand;
use App\Traits\GeneratesUniqueSlugs;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BrandController extends Controller
{
    use GeneratesUniqueSlugs;

    public function index(): View
    {
        $brands = Brand::withCount('products')->orderBy('name')->get();

        return view('admin.brands.index', compact('brands'));
    }

    public function store(CreateBrandRequest $request): RedirectResponse
    {
        $logoPath = $request->hasFile('logo')
            ? $request->file('logo')->store('brands', 'r2')
            : null;

        $brand = Brand::create([
            'name' => $request->validated('name'),
            'slug' => $this->uniqueSlug(Brand::class, $request->validated('name')),
            'logo_path' => $logoPath,
            'is_active' => true,
        ]);

        return redirect()->route('admin.brands.index')->with('success', "\"{$brand->name}\" was added.");
    }

    public function toggleActive(Brand $brand): RedirectResponse
    {
        $brand->update(['is_active' => ! $brand->is_active]);

        return back()->with('success', $brand->is_active ? "\"{$brand->name}\" is now visible to customers." : "\"{$brand->name}\" is now hidden from customers.");
    }
}
