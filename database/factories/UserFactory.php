<?php

namespace Database\Factories;

use App\Models\Franchise;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create();

        return [
            'franchise_id' => null,
            'name' => $faker->name(),
            'mobile' => $faker->unique()->numerify('9#########'), // 10 digits, starts 9
            'email' => $faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'mobile_verified_at' => now(),
            'password' => Hash::make('password'), // override in tests/seeders as needed
            'two_factor_enabled' => false,
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    /** Staff/admin users belong to a franchise; customers/Super Admin/Accountant don't. */
    public function forFranchise(?Franchise $franchise = null): static
    {
        return $this->state(fn () => [
            'franchise_id' => ($franchise ?? Franchise::factory())->id,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
            'mobile_verified_at' => null,
        ]);
    }
}