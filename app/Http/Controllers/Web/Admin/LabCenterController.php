<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateLabCenterRequest;
use App\Models\Franchise;
use App\Models\LabCenter;
use App\Models\LabTest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LabCenterController extends Controller
{
    public function index(): View
    {
        $centers = LabCenter::with('franchise')->withCount('tests')->orderBy('name')->get();

        return view('admin.lab-centers.index', compact('centers'));
    }

    public function create(): View
    {
        $franchises = Franchise::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $tests = LabTest::with('category')->where('is_active', true)->orderBy('name')->get();

        return view('admin.lab-centers.create', compact('franchises', 'tests'));
    }

    public function store(CreateLabCenterRequest $request): RedirectResponse
    {
        $center = LabCenter::create([
            ...$request->safe()->except(['tests']),
            'offers_home_collection' => $request->boolean('offers_home_collection'),
            'is_active' => true,
        ]);

        $center->tests()->sync($request->selectedTestPrices());

        return redirect()->route('admin.lab-centers.edit', $center)->with('success', "\"{$center->name}\" was added.");
    }

    public function edit(LabCenter $labCenter): View
    {
        $franchises = Franchise::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $tests = LabTest::with('category')->where('is_active', true)->orderBy('name')->get();
        $labCenter->load('tests');

        return view('admin.lab-centers.edit', ['center' => $labCenter, 'franchises' => $franchises, 'tests' => $tests]);
    }

    public function update(CreateLabCenterRequest $request, LabCenter $labCenter): RedirectResponse
    {
        $labCenter->update([
            ...$request->safe()->except(['tests']),
            'offers_home_collection' => $request->boolean('offers_home_collection'),
        ]);

        $labCenter->tests()->sync($request->selectedTestPrices());

        return redirect()->route('admin.lab-centers.edit', $labCenter)->with('success', 'Saved.');
    }

    public function toggleActive(LabCenter $labCenter): RedirectResponse
    {
        $labCenter->update(['is_active' => ! $labCenter->is_active]);

        return back()->with('success', $labCenter->is_active ? "\"{$labCenter->name}\" is now visible to customers." : "\"{$labCenter->name}\" is now hidden from customers.");
    }
}
