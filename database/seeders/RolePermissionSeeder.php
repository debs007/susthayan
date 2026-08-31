<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * The 7 roles from SRS section 2. These map onto 4 login "portals" via
     * route-group middleware (see routes/api.php), not 4 separate guards:
     *   - customer         -> Customer
     *   - franchise portal -> Franchise Owner, Franchise Staff, Pharmacist
     *   - delivery app     -> Delivery Agent
     *   - admin portal     -> Super Admin, Accountant
     *
     * Seeded under BOTH sanctum and web guards - the web portal's login
     * flow makes 'web' the active guard for that request, and Spatie
     * throws RoleDoesNotExist if a role was only ever seeded under
     * 'sanctum'. API-only roles never technically need the web guard
     * copy, but seeding all of them under both is simpler than tracking
     * which ones do.
     */
    public function run(): void
    {
        $roles = [
            'Customer',
            'Pharmacist',
            'Franchise Staff',
            'Franchise Owner',
            'Delivery Agent',
            'Super Admin',
            'Accountant',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'sanctum']);
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Fine-grained permissions (e.g. 'verify prescriptions', 'approve purchase orders')
        // get layered on per-role as we build out each module - roles alone are enough
        // to protect route groups for now.
    }
}
