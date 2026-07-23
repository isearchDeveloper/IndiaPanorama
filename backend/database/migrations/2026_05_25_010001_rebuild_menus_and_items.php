<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Menu Management System — Full Rebuild
 * ──────────────────────────────────────
 * Drops all old fragmented menu tables (all empty — verified before running).
 * Creates clean:
 *   • menus       → 2 fixed rows: Header Menu + Footer Menu
 *   • menu_items  → All nav items, self-referential, unified schema
 */
return new class extends Migration
{
    // ──────────────────────────────────────────────────────────────
    // UP
    // ──────────────────────────────────────────────────────────────
    public function up(): void
    {
        // ── Drop old fragmented tables (children before parents) ──
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('nav_menu_items');
        Schema::dropIfExists('nav_menus');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('mega_menu_locations');
        Schema::dropIfExists('menu_builders');

        Schema::enableForeignKeyConstraints();

        // ── 1. menus ─────────────────────────────────────────────
        Schema::create('menus', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100)
                  ->comment('e.g. "Header Menu" / "Footer Menu"');

            $table->string('slug', 50)->unique()
                  ->comment('"header" or "footer"');

            $table->enum('location', ['header', 'footer'])
                  ->default('header');

            $table->boolean('is_active')->default(true);

            $table->unsignedTinyInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['location', 'is_active']);
        });

        // ── 2. menu_items ─────────────────────────────────────────
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('menu_id');

            $table->unsignedBigInteger('parent_id')->nullable()
                  ->comment('NULL = root item; set = child/grandchild');

            $table->string('title', 200);

            $table->enum('type', ['custom', 'page', 'package', 'location', 'category'])
                  ->default('custom');

            $table->unsignedBigInteger('linked_id')->nullable()
                  ->comment('PK of linked record (page/package/location/category)');

            $table->string('url', 500)->nullable()
                  ->comment('Raw URL — only when type = custom');

            $table->enum('target', ['_self', '_blank'])
                  ->default('_self');

            // status (1 = active/visible, 0 = hidden) as requested
            $table->tinyInteger('status')->default(1)
                  ->comment('1 = active, 0 = hidden');

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            // ── Foreign keys ──────────────────────────────────────
            $table->foreign('menu_id')
                  ->references('id')->on('menus')
                  ->cascadeOnDelete();

            $table->foreign('parent_id')
                  ->references('id')->on('menu_items')
                  ->cascadeOnDelete();

            // ── Indexes ───────────────────────────────────────────
            $table->index('menu_id');
            $table->index('parent_id');
            $table->index('status');
            $table->index('type');
            $table->index(['menu_id', 'parent_id', 'sort_order'], 'idx_menu_items_tree');
        });

        // ── 3. Seed: Header Menu + Footer Menu ───────────────────
        $now = now();
        DB::table('menus')->insert([
            [
                'name'       => 'Header Menu',
                'slug'       => 'header',
                'location'   => 'header',
                'is_active'  => 1,
                'sort_order' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Footer Menu',
                'slug'       => 'footer',
                'location'   => 'footer',
                'is_active'  => 1,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // DOWN
    // ──────────────────────────────────────────────────────────────
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
        Schema::enableForeignKeyConstraints();
    }
};
