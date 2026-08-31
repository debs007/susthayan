<?php

namespace App\Traits;

use App\Models\User;
use Spatie\Permission\Models\Role;

/**
 * Spatie's assignRole()/syncRoles() resolve the role by name using
 * whatever the model's CURRENT default guard is - fine when called from
 * an API request (sanctum), but throws RoleDoesNotExist when called from
 * a web request (guard becomes 'web') if the role was only ever seeded
 * under sanctum. These helpers look up the Role model explicitly for
 * BOTH guards and assign both, so it doesn't matter which guard context
 * the call happens to run under.
 */
trait AssignsRoleAcrossGuards
{
    protected function assignRoleAcrossGuards(User $user, string $roleName): void
    {
        foreach (['sanctum', 'web'] as $guard) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);
            $user->assignRole($role);
        }
    }

    protected function syncRoleAcrossGuards(User $user, string $roleName): void
    {
        $roles = collect(['sanctum', 'web'])
            ->map(fn ($guard) => Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]));

        $user->syncRoles($roles);
    }
}
