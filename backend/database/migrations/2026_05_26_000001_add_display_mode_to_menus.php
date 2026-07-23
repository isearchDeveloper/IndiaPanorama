<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menu Display Mode — Phase 1
 * ────────────────────────────
 * Adds two columns to the `menus` table so each menu can choose
 * how it renders its content:
 *
 *   display_mode     VARCHAR(30)  'manual' (default) | 'region_state_city'
 *                                 | 'region_state' | 'state_city' | 'city_only'
 *
 *   display_settings JSON NULL    Serialised filter options:
 *                                 {
 *                                   "region_ids":  [],      // [] = all
 *                                   "state_ids":   [],      // [] = all
 *                                   "active_only": true,
 *                                   "package_only": false
 *                                 }
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            // display_mode — idempotent: skip if already exists
            if (! Schema::hasColumn('menus', 'display_mode')) {
                $table->string('display_mode', 30)
                      ->default('manual')
                      ->after('sort_order')
                      ->comment('manual | region_state_city | region_state | state_city | city_only');
            }

            if (! Schema::hasColumn('menus', 'display_settings')) {
                $table->json('display_settings')
                      ->nullable()
                      ->after('display_mode')
                      ->comment('Filter options for auto display modes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['display_mode', 'display_settings']);
        });
    }
};
