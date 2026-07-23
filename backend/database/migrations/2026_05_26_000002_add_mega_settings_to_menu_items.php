<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add mega_settings JSON column to menu_items.
 *
 * Stores per-item mega-menu configuration:
 * {
 *   "content_type":   "normal" | "mega_menu",
 *   "display_source": "auto" | "custom_menu",
 *   "display_mode":   "region_state_city" | "region_state" | "state_city" | "city_only",
 *   "linked_menu_id": null | int,    // when source = custom_menu
 *   "region_ids":     [],
 *   "state_ids":      [],
 *   "active_only":    true,
 *   "package_only":   false,
 *   "banner": {
 *     "image": "", "alt": "", "title": "",
 *     "description": "", "cta_text": "", "cta_url": ""
 *   }
 * }
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->json('mega_settings')->nullable()->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('mega_settings');
        });
    }
};
