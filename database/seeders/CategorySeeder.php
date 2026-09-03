<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Names deliberately match the keywords the Flutter app's category
     * row/screen already searches for ('medic', 'health', 'mother'/'baby',
     * 'fitness') - using different wording here would leave those
     * categories unmatched in the app even though they exist.
     */
    public function run(): void
    {
        $categories = [
            'Medicines',
            'Healthcare',
            'Mother & Baby Care',
            'Fitness & Nutrition',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true]
            );
        }
    }
}
