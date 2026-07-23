<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menu System — Phase 3: Region / State types + schema safety
 * ─────────────────────────────────────────────────────────────
 *
 * Idempotent catch-all that reaches the correct final state whether
 * migration 020001 ran or not.
 *
 * Changes:
 *   1. menus.location   → VARCHAR(50) if still ENUM (020001 may have run)
 *   2. menus.is_system  → Add column if missing (020001 may not have run)
 *   3. menu_items.type  → Full final ENUM including menu_reference, region, state
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. menus.location: ensure it is VARCHAR, not ENUM ─────────────────
        // Safe even if 020001 already ran (MODIFY on VARCHAR → VARCHAR is a no-op).
        DB::statement(
            "ALTER TABLE menus MODIFY COLUMN location VARCHAR(50) NOT NULL DEFAULT 'header'"
        );

        // ── 2. menus.is_system: add only if missing ───────────────────────────
        if (! Schema::hasColumn('menus', 'is_system')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->boolean('is_system')
                      ->default(false)
                      ->after('is_active')
                      ->comment('true = header/footer system menu, cannot be deleted');
            });

            // Mark existing system menus
            DB::table('menus')
              ->whereIn('slug', ['header', 'footer'])
              ->update(['is_system' => true]);
        }

        // ── 3. menu_items.type: set the final complete ENUM ───────────────────
        // Includes: original types + menu_reference (020001) + region + state (this).
        // Setting the exact list is idempotent; adding new values is the only change.
        DB::statement(
            "ALTER TABLE menu_items MODIFY COLUMN type
             ENUM('custom','page','package','location','category','menu_reference','region','state')
             NOT NULL DEFAULT 'custom'"
        );
    }

    public function down(): void
    {
        // Remove region/state rows first
        DB::table('menu_items')->whereIn('type', ['region', 'state'])->delete();

        // Restore enum without region/state (but keep menu_reference for 020001 compatibility)
        DB::statement(
            "ALTER TABLE menu_items MODIFY COLUMN type
             ENUM('custom','page','package','location','category','menu_reference')
             NOT NULL DEFAULT 'custom'"
        );
    }
};
