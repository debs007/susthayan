<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateHospitalRequest;
use App\Models\Franchise;
use App\Models\Hospital;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HospitalController extends Controller
{
    public function index(): View
    {
        $hospitals = Hospital::with('franchise')->withCount('doctors')->orderBy('name')->get();

        return view('admin.hospitals.index', compact('hospitals'));
    }

    public function create(): View
    {
        $franchises = Franchise::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return view('admin.hospitals.create', compact('franchises'));
    }

    public function store(CreateHospitalRequest $request): RedirectResponse
    {
        $hospital = Hospital::create([...$request->validated(), 'is_active' => true]);

        return redirect()->route('admin.hospitals.index')->with('success', "\"{$hospital->name}\" was added.");
    }

    public function toggleActive(Hospital $hospital): RedirectResponse
    {
        $hospital->update(['is_active' => ! $hospital->is_active]);

        return back()->with('success', $hospital->is_active ? "\"{$hospital->name}\" is now active." : "\"{$hospital->name}\" is now hidden.");
    }
}
