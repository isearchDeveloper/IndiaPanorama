<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Drops the legacy `is_deleted` boolean-flag column (and the indexes that
 * referenced it) now that every model uses real Eloquent SoftDeletes
 * (`deleted_at`, added in 2026_07_06_120000_add_deleted_at_to_all_tables).
 */
return new class extends Migration
{
    private array $tables = [
        'awards', 'banners', 'car_categories', 'car_city', 'car_destinations',
        'car_packages', 'car_routes', 'cars', 'categories', 'news', 'packages',
        'tour_services', 'tourist_activities', 'tourist_attractions',
    ];

    public function up(): void
    {
        // Drop indexes that reference is_deleted before dropping the column.
        if (Schema::hasTable('packages')) {
            Schema::table('packages', function (Blueprint $table) {
                foreach (['packages_deleted_active_idx', 'idx_pkg_active_deleted'] as $idx) {
                    if ($this->hasIndex('packages', $idx)) {
                        $table->dropIndex($idx);
                    }
                }
            });
        }

        if (Schema::hasTable('tour_services') && $this->hasIndex('tour_services', 'idx_ts_deleted')) {
            Schema::table('tour_services', fn (Blueprint $table) => $table->dropIndex('idx_ts_deleted'));
        }

        foreach ($this->tables as $t) {
            if (Schema::hasTable($t) && Schema::hasColumn($t, 'is_deleted')) {
                Schema::table($t, fn (Blueprint $table) => $table->dropColumn('is_deleted'));
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $t) {
            if (Schema::hasTable($t) && !Schema::hasColumn($t, 'is_deleted')) {
                Schema::table($t, fn (Blueprint $table) => $table->tinyInteger('is_deleted')->default(0));
            }
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        return !empty(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]));
    }
};
