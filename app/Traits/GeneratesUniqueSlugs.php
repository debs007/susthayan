<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Shared by every admin controller that creates something with a unique
 * slug (Franchise, Category, Product, both API and web versions) - three
 * near-identical private methods before this, now one.
 */
trait GeneratesUniqueSlugs
{
    protected function uniqueSlug(string $modelClass, string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 1;

        while ($modelClass::where('slug', $slug)->exists()) {
            $slug = "{$base}-".++$suffix;
        }

        return $slug;
    }
}
