<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * holiday_menu_settings
 * ─────────────────────
 * Stores admin overrides for the auto-generated Holiday Packages menu.
 * The menu tree itself is built live from regions → states → locations
 * (only where packages exist). This table only persists:
 *   • sort_order  — admin drag-drop position
 *   • is_visible  — admin show/hide toggle
 *
 * All other data (name, slug, URL, etc.) comes from the source tables.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holiday_menu_settings', function (Blueprint $table) {
            $table->id();

            // Which level does this row control?
            $table->enum('type', ['region', 'state', 'location']);

            // FK into regions.id / states.id / locations.id — no DB-level FK
            // because the three types point to different tables.
            $table->unsignedBigInteger('reference_id');

            // Admin-controlled ordering (lower = first in list)
            $table->unsignedSmallInteger('sort_order')->default(0);

            // 1 = visible in menu, 0 = hidden
            $table->tinyInteger('is_visible')->default(1);

            $table->timestamps();

            // One setting row per (type, reference_id) pair
            $table->unique(['type', 'reference_id'], 'hms_type_ref_unique');

            // Index used by the service query
            $table->index(['type', 'sort_order'], 'hms_type_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holiday_menu_settings');
    }
};
