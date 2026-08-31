<?php

namespace Database\Factories;

use App\Models\Franchise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Franchise>
 */
class FranchiseFactory extends Factory
{
    protected $model = Franchise::class;

    public function definition(): array
    {
        $name = fake()->city().' Pharmacy';

        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'gstin' => null,
            'drug_license_number' => null,
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'pincode' => fake()->numerify('######'),
            'latitude' => fake()->latitude(8, 28),   // rough India bounding box
            'longitude' => fake()->longitude(70, 90),
            'phone' => fake()->numerify('9#########'),
            'email' => fake()->unique()->companyEmail(),
            'commission_percentage' => 10,
            'status' => 'active',
        ];
    }
}
