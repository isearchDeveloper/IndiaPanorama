<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * The Theme/ThemeSpot module (2-level Category->Spot, spots doubling as either a
 * root "subcategory" listing or a state/city-scoped leaf depending on nullable
 * columns) is replaced by the new Experience module (proper 3-level
 * Category->Subcategory->Experience hierarchy). Both tables were empty in every
 * environment at replacement time, so this is a straight drop, not a data migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Schema::dropIfExists('theme_setting_faqs');
        Schema::dropIfExists('theme_settings');
        Schema::dropIfExists('theme_faqs');
        Schema::dropIfExists('theme_spots');
        Schema::dropIfExists('themes');

        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Intentionally empty — replaced by the Experience module.
    }
};
