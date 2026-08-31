<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateFranchiseRequest;
use App\Http\Requests\Admin\UpdateFranchiseBankDetailsRequest;
use App\Http\Requests\Admin\UpdateFranchiseRequest;
use App\Models\Franchise;
use App\Traits\GeneratesUniqueSlugs;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Deliberately separate from Api\Admin\FranchiseController rather than
 * sharing one - this one returns redirects + Blade views for a browser
 * form post, that one returns JSON for the Flutter apps. Same validation
 * rules (CreateFranchiseRequest/UpdateFranchiseRequest are reused as-is),
 * same underlying Franchise model, different response shape - the two
 * genuinely need to diverge there, not be forced into one controller.
 */
class FranchiseController extends Controller
{
    use GeneratesUniqueSlugs;

    public function index(): View
    {
        $franchises = Franchise::orderBy('name')->paginate(15);

        return view('admin.franchises.index', compact('franchises'));
    }

    public function create(): View
    {
        return view('admin.franchises.create');
    }

    public function store(CreateFranchiseRequest $request): RedirectResponse
    {
        $franchise = Franchise::create([
            ...$request->validated(),
            'slug' => $this->uniqueSlug(Franchise::class, $request->validated('name')),
            'status' => 'active',
        ]);

        return redirect()
            ->route('admin.franchises.index')
            ->with('success', "{$franchise->name} was created.");
    }

    public function edit(Franchise $franchise): View
    {
        return view('admin.franchises.edit', compact('franchise'));
    }

    public function update(UpdateFranchiseRequest $request, Franchise $franchise): RedirectResponse
    {
        $franchise->update($request->validated());

        return redirect()
            ->route('admin.franchises.edit', $franchise)
            ->with('success', 'Franchise details updated.');
    }

    public function updateBankDetails(UpdateFranchiseBankDetailsRequest $request, Franchise $franchise): RedirectResponse
    {
        $franchise->update($request->validated());

        return redirect()
            ->route('admin.franchises.edit', $franchise)
            ->with('success', 'Bank details updated - ready for settlement payouts.');
    }
}
