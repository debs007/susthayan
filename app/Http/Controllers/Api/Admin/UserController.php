<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateStaffUserRequest;
use App\Http\Requests\Admin\ResetStaffPasswordRequest;
use App\Http\Requests\Admin\UpdateStaffUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\AssignsRoleAcrossGuards;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use AssignsRoleAcrossGuards;
    public function index(Request $request): AnonymousResourceCollection
    {
        $users = User::query()
            // Staff only - Customer accounts are self-service and huge in
            // number; they don't belong in a staff-directory listing.
            ->whereHas('roles', fn ($query) => $query->where('name', '!=', 'Customer'))
            ->when($request->filled('role'), fn ($query) => $query->whereHas(
                'roles',
                fn ($q) => $q->where('name', $request->string('role'))
            ))
            ->when($request->filled('franchise_id'), fn ($query) => $query->where('franchise_id', $request->integer('franchise_id')))
            ->with(['franchise', 'roles'])
            ->orderBy('name')
            ->paginate(30);

        return UserResource::collection($users);
    }

    public function store(CreateStaffUserRequest $request): JsonResponse
    {
        $isSuperAdmin = $request->validated('role') === 'Super Admin';

        $user = User::create([
            'name' => $request->validated('name'),
            'mobile' => $request->validated('mobile'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'franchise_id' => $request->validated('franchise_id'),
            // Super Admin always goes through 2FA regardless of this flag
            // (StaffAuthController checks the role too) - set here anyway
            // so the flag on the record reflects reality, not just the
            // role-based fallback.
            'two_factor_enabled' => $isSuperAdmin || $request->boolean('two_factor_enabled'),
            'is_active' => true,
            // Admin-created, not self-verified via OTP - the admin creating
            // the account is vouching for it.
            'mobile_verified_at' => now(),
        ]);

        $this->assignRoleAcrossGuards($user, $request->validated('role'));

        return (new UserResource($user->load('franchise')))->response()->setStatusCode(201);
    }

    public function show(User $user): UserResource
    {
        abort_if($user->hasRole('Customer'), 404);

        return new UserResource($user->load(['franchise', 'roles']));
    }

    public function update(UpdateStaffUserRequest $request, User $user): UserResource
    {
        abort_if($user->hasRole('Customer'), 404);

        $user->update($request->validated());

        return new UserResource($user->fresh(['franchise', 'roles']));
    }

    public function resetPassword(ResetStaffPasswordRequest $request, User $user): JsonResponse
    {
        abort_if($user->hasRole('Customer'), 404);

        $user->update(['password' => Hash::make($request->validated('password'))]);

        return response()->json(['message' => 'Password reset - let them know the new one through a secure channel outside this system.']);
    }
}
