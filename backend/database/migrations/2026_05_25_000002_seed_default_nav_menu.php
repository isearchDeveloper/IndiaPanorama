<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Seed one default "Header Navigation" menu so the admin
 * builder is never empty on a fresh install.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // Guard: only insert if the table is empty
        if (DB::table('nav_menus')->count() === 0) {
            DB::table('nav_menus')->insert([
                'name'        => 'Header Navigation',
                'slug'        => 'header-nav',
                'location'    => 'header',
                'description' => 'Primary website header menu. Manage all top-level and mega-menu items here.',
                'is_active'   => 1,
                'sort_order'  => 0,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('nav_menus')->where('slug', 'header-nav')->delete();
    }
};
