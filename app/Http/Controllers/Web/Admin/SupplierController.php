<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSupplierRequest;
use App\Http\Requests\Admin\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::withCount('purchaseOrders')->orderBy('name')->paginate(20);

        return view('admin.vendors.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('admin.vendors.create');
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $supplier = Supplier::create([...$request->validated(), 'is_active' => true]);

        return redirect()->route('admin.vendors.index')->with('success', "\"{$supplier->name}\" was added.");
    }

    public function edit(Supplier $supplier): View
    {
        return view('admin.vendors.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return redirect()->route('admin.vendors.edit', $supplier)->with('success', 'Supplier details updated.');
    }
}
