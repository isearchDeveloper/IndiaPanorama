<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menu System — Phase 2: Menu Reference Type + Custom Menus
 * ──────────────────────────────────────────────────────────
 *
 * Changes:
 *   1. menus.location   ENUM('header','footer') → VARCHAR(50)
 *                       Allows unlimited custom menus.
 *
 *   2. menus.is_system  New boolean column (header/footer = true, custom = false).
 *                       Prevents accidental deletion of system menus.
 *
 *   3. menu_items.type  Add 'menu_reference' to ENUM.
 *                       When type = menu_reference, linked_id holds a menus.id.
 *                       The API/service inlines the referenced menu's items as children.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. menus.location: ENUM → VARCHAR ─────────────────────────────────
        DB::statement("ALTER TABLE menus MODIFY COLUMN location VARCHAR(50) NOT NULL DEFAULT 'header'");

        // ── 2. menus.is_system ────────────────────────────────────────────────
        Schema::table('menus', function (Blueprint $table) {
            $table->boolean('is_system')
                  ->default(false)
                  ->after('is_active')
                  ->comment('true = system menu (header/footer); cannot be deleted by admin');
        });

        // Mark existing system menus
        DB::table('menus')->whereIn('slug', ['header', 'footer'])->update(['is_system' => true]);

        // ── 3. menu_items.type: add 'menu_reference' ──────────────────────────
        DB::statement(
            "ALTER TABLE menu_items MODIFY COLUMN type
             ENUM('custom','page','package','location','category','menu_reference')
             NOT NULL DEFAULT 'custom'"
        );
    }

    public function down(): void
    {
        // Remove menu_reference rows first (safe drop)
        DB::table('menu_items')->where('type', 'menu_reference')->delete();

        DB::statement(
            "ALTER TABLE menu_items MODIFY COLUMN type
             ENUM('custom','page','package','location','category')
             NOT NULL DEFAULT 'custom'"
        );

        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('is_system');
        });

        DB::statement("ALTER TABLE menus MODIFY COLUMN location ENUM('header','footer') NOT NULL DEFAULT 'header'");
    }
};
