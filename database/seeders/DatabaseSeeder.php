<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // Local-dev Super Admin so you have something to log in with via
        // POST /api/auth/staff/login. Change the mobile/password (and
        // remove this block entirely) before staging/production.
        if (! \App\Models\User::where('mobile', '9999999999')->exists()) {
            \App\Models\User::factory()->create([
                'name' => 'Super Admin',
                'mobile' => '9999999999',
                'email' => 'admin@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'two_factor_enabled' => true,
            ])->assignRole('Super Admin');
        }
    }
}
