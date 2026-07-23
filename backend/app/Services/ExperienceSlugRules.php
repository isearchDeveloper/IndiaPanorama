<?php

namespace App\Services;

use App\Models\ExperienceCategory;
use App\Models\ExperienceSubcategory;
use Illuminate\Validation\ValidationException;

/**
 * Experience categories and subcategories share ONE slug namespace (both are resolved
 * off a single `/experiences/{slug}` segment on the frontend), so uniqueness can't be
 * enforced with a plain per-table DB unique constraint — it has to be checked here,
 * across both tables, on every create/update of either.
 */
class ExperienceSlugRules
{
    public static function exists(string $slug, ?int $ignoreCategoryId = null, ?int $ignoreSubcategoryId = null): bool
    {
        $inCategories = ExperienceCategory::where('slug', $slug)
            ->when($ignoreCategoryId, fn ($q) => $q->where('id', '!=', $ignoreCategoryId))
            ->exists();

        $inSubcategories = ExperienceSubcategory::where('slug', $slug)
            ->when($ignoreSubcategoryId, fn ($q) => $q->where('id', '!=', $ignoreSubcategoryId))
            ->exists();

        return $inCategories || $inSubcategories;
    }

    /** @throws ValidationException */
    public static function assertUnique(string $slug, ?int $ignoreCategoryId = null, ?int $ignoreSubcategoryId = null): void
    {
        if (str_contains($slug, '-in-')) {
            throw ValidationException::withMessages([
                'name' => 'The slug cannot contain "-in-" — that token is reserved as the state-filter separator (e.g. waterfalls-tours-in-kerala).',
            ]);
        }

        if (self::exists($slug, $ignoreCategoryId, $ignoreSubcategoryId)) {
            throw ValidationException::withMessages([
                'name' => 'This slug is already used by another experience category or subcategory. Category and subcategory slugs must be unique across both.',
            ]);
        }
    }
}
