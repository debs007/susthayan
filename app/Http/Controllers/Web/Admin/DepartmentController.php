<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateDepartmentRequest;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::withCount('doctors')->orderBy('name')->get();

        return view('admin.departments.index', compact('departments'));
    }

    public function store(CreateDepartmentRequest $request): RedirectResponse
    {
        $department = Department::create(['name' => $request->validated('name'), 'is_active' => true]);

        return redirect()->route('admin.departments.index')->with('success', "\"{$department->name}\" was added.");
    }
}
