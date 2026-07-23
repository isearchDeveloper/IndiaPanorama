<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Home CMS cleanup — removes modules not in the simplified 7-section spec:
 *  • home_about_stats        (counters/bubbles — replaced by feature list only)
 *  • home_inquiry_fields     (dynamic form builder — removed section)
 *  • homepage_tab_settings   (Explore-by-Region tabs — removed section)
 *  • homepage_tab_locations  (locations per tab — removed section)
 *  • car_categories.is_homepage column
 *  • home_sections rows for: car_rental, inquiry_form, tour_services, home_locations
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // 1. Drop removed feature tables
        Schema::dropIfExists('home_about_stats');
        Schema::dropIfExists('home_inquiry_fields');
        Schema::dropIfExists('homepage_tab_locations');
        Schema::dropIfExists('homepage_tab_settings');

        // 2. Drop is_homepage from car_categories (if exists)
        if (Schema::hasColumn('car_categories', 'is_homepage')) {
            Schema::table('car_categories', function (Blueprint $table) {
                $table->dropColumn('is_homepage');
            });
        }

        // 3. Delete removed section rows from home_sections
        DB::table('home_sections')
            ->whereIn('section_key', ['car_rental', 'inquiry_form', 'tour_services', 'home_locations'])
            ->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Intentionally not reversible — data was intentionally removed.
    }
};
