<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // Add state_id if missing
            if (!Schema::hasColumn('locations', 'state_id')) {
                $table->unsignedBigInteger('state_id')
                      ->nullable()
                      ->after('country_id')
                      ->index('locations_state_id_idx');
            }

            // Add region_id if missing
            if (!Schema::hasColumn('locations', 'region_id')) {
                $table->unsignedBigInteger('region_id')
                      ->nullable()
                      ->after('state_id')
                      ->index('locations_region_id_idx');
            }

            // Add name if missing (legacy migration uses 'city')
            if (!Schema::hasColumn('locations', 'name') && Schema::hasColumn('locations', 'city')) {
                $table->string('name', 191)->nullable()->after('region_id');
            }

            // Add slug if missing
            if (!Schema::hasColumn('locations', 'slug')) {
                $table->string('slug', 220)->nullable()->after('name');
            }

            // Add sort_order if missing
            if (!Schema::hasColumn('locations', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('slug');
            }

            // Add is_top_trending if missing
            if (!Schema::hasColumn('locations', 'is_top_trending')) {
                $table->boolean('is_top_trending')->default(false)->after('is_active');
            }

            // Add faq_title if missing
            if (!Schema::hasColumn('locations', 'faq_title')) {
                $table->string('faq_title', 255)->nullable()->after('is_top_trending');
            }

            // Add author_name if missing
            if (!Schema::hasColumn('locations', 'author_name')) {
                $table->string('author_name', 255)->nullable()->after('faq_title');
            }
        });

        // Add composite performance indexes if not present
        $existingIndexes = collect(DB::select("SHOW INDEX FROM `locations`"))
                            ->pluck('Key_name')
                            ->unique()
                            ->toArray();

        Schema::table('locations', function (Blueprint $table) use ($existingIndexes) {
            if (!in_array('loc_country_state_idx', $existingIndexes)) {
                $table->index(['country_id', 'state_id'], 'loc_country_state_idx');
            }
            if (!in_array('loc_active_country_idx', $existingIndexes)) {
                $table->index(['is_active', 'country_id'], 'loc_active_country_idx');
            }
        });

        // Add FK from locations.state_id → states.id (after states table is ensured)
        if (Schema::hasTable('states')) {
            $fkExists = collect(DB::select(
                "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'locations'
                   AND CONSTRAINT_NAME = 'locations_state_id_foreign'"
            ))->isNotEmpty();

            if (!$fkExists) {
                Schema::table('locations', function (Blueprint $table) {
                    $table->foreign('state_id', 'locations_state_id_foreign')
                          ->references('id')
                          ->on('states')
                          ->onDelete('set null');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // Drop FK first
            try { $table->dropForeign('locations_state_id_foreign'); } catch (\Exception $e) {}
            // Drop indexes
            try { $table->dropIndex('loc_country_state_idx'); }    catch (\Exception $e) {}
            try { $table->dropIndex('loc_active_country_idx'); }   catch (\Exception $e) {}
        });
    }
};
