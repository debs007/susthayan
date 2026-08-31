<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignRoleRequest;
use App\Http\Requests\Admin\CreateStaffUserRequest;
use App\Http\Requests\Admin\ResetStaffPasswordRequest;
use App\Http\Requests\Admin\UpdateStaffUserRequest;
use App\Models\Franchise;
use App\Models\User;
use App\Traits\AssignsRoleAcrossGuards;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/** The only place any non-Customer account is ever created - same as the API, just a page instead of a JSON response. */
class UserController extends Controller
{
    use AssignsRoleAcrossGuards;
    private const array ALL_STAFF_ROLES = [
        'Pharmacist', 'Franchise Staff', 'Franchise Owner',
        'Delivery Agent', 'Super Admin', 'Accountant',
    ];

    public function index(Request $request): View
    {
        $users = User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', '!=', 'Customer'))
            ->when($request->filled('role'), fn ($query) => $query->whereHas(
                'roles',
                fn ($q) => $q->where('name', $request->string('role'))
            ))
            ->with(['franchise', 'roles'])
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => self::ALL_STAFF_ROLES,
        ]);
    }

    public function create(): View
    {
        $franchises = Franchise::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return view('admin.users.create', [
            'franchises' => $franchises,
            'roles' => self::ALL_STAFF_ROLES,
        ]);
    }

    public function store(CreateStaffUserRequest $request): RedirectResponse
    {
        $isSuperAdmin = $request->validated('role') === 'Super Admin';

        $user = User::create([
            'name' => $request->validated('name'),
            'mobile' => $request->validated('mobile'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'franchise_id' => $request->validated('franchise_id'),
            'two_factor_enabled' => $isSuperAdmin || $request->boolean('two_factor_enabled'),
            'is_active' => true,
            'mobile_verified_at' => now(),
        ]);

        $this->assignRoleAcrossGuards($user, $request->validated('role'));

        return redirect()->route('admin.users.index')->with('success', "{$user->name} was added as {$request->validated('role')}.");
    }

    public function edit(User $user): View
    {
        abort_if($user->hasRole('Customer'), 404);

        $franchises = Franchise::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return view('admin.users.edit', [
            'user' => $user->load(['franchise', 'roles']),
            'franchises' => $franchises,
            'roles' => self::ALL_STAFF_ROLES,
        ]);
    }

    public function update(UpdateStaffUserRequest $request, User $user): RedirectResponse
    {
        abort_if($user->hasRole('Customer'), 404);

        $user->update($request->validated());

        return redirect()->route('admin.users.edit', $user)->with('success', 'Profile updated.');
    }

    /** Deliberately its own action, not folded into update() - a role change has authorization implications a routine profile edit doesn't (see AssignRoleRequest). */
    public function changeRole(AssignRoleRequest $request, User $user): RedirectResponse
    {
        abort_if($user->hasRole('Customer'), 404);

        $this->syncRoleAcrossGuards($user, $request->validated('role'));
        $user->update(['franchise_id' => $request->validated('franchise_id')]);

        return redirect()->route('admin.users.edit', $user)->with('success', "Role changed to {$request->validated('role')}.");
    }

    public function resetPassword(ResetStaffPasswordRequest $request, User $user): RedirectResponse
    {
        abort_if($user->hasRole('Customer'), 404);

        $user->update(['password' => Hash::make($request->validated('password'))]);

        return redirect()->route('admin.users.edit', $user)->with('success', 'Password reset - share the new one through a secure channel outside this system.');
    }
}
