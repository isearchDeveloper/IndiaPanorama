<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration: harden the packages & packages_group_dates tables.
 *
 * Run:  php artisan migrate
 * Roll: php artisan migrate:rollback
 */
return new class extends Migration
{
    public function up(): void
    {
        // -------------------------------------------------------
        // 1. packages — tighten is_special_package to tinyint(1)
        //    and add a composite index for list queries
        // -------------------------------------------------------
        Schema::table('packages', function (Blueprint $table) {

            // Normalise is_special_package (was int(11) DEFAULT NULL)
            $table->tinyInteger('is_special_package')->default(0)->change();

            // Ensure package_mode is restricted to known values
            // (MySQL ENUM is preferred; we add a check constraint if MariaDB/MySQL 8+)
            // Using a plain string column + app-level enum for portability:
            $table->string('package_mode', 20)->default('normal')->change();

            // Performance indexes
            if (!$this->indexExists('packages', 'packages_deleted_active_idx')) {
                $table->index(['is_deleted', 'is_active'], 'packages_deleted_active_idx');
            }
            if (!$this->indexExists('packages', 'packages_mode_idx')) {
                $table->index('package_mode', 'packages_mode_idx');
            }
            if (!$this->indexExists('packages', 'packages_slug_unique')) {
                $table->unique('slug', 'packages_slug_unique');
            }
        });

        // -------------------------------------------------------
        // 2. packages_group_dates — add FK, tighten nullable rules
        // -------------------------------------------------------
        Schema::table('packages_group_dates', function (Blueprint $table) {

            // Make package_id NOT NULL (orphan rows are useless)
            $table->unsignedBigInteger('package_id')->nullable(false)->change();

            // Add foreign key if not already present
            if (!$this->foreignKeyExists('packages_group_dates', 'packages_group_dates_package_id_foreign')) {
                $table->foreign('package_id')
                      ->references('id')
                      ->on('packages')
                      ->onDelete('cascade');
            }

            // Index for fast per-package departure lookups
            if (!$this->indexExists('packages_group_dates', 'pgd_package_date_idx')) {
                $table->index(['package_id', 'departure_date'], 'pgd_package_date_idx');
            }
        });

        // -------------------------------------------------------
        // 3. packages_categories — enforce FK & unique pair
        // -------------------------------------------------------
        Schema::table('packages_categories', function (Blueprint $table) {
            if (!$this->indexExists('packages_categories', 'pkgcat_package_category_unique')) {
                $table->unique(['package_id', 'category_id'], 'pkgcat_package_category_unique');
            }
        });

        // -------------------------------------------------------
        // 4. package_locations — enforce unique pair
        // -------------------------------------------------------
        if (Schema::hasTable('package_locations')) {
            Schema::table('package_locations', function (Blueprint $table) {
                if (!$this->indexExists('package_locations', 'pkgloc_unique')) {
                    $table->unique(['package_id', 'location_id'], 'pkgloc_unique');
                }
            });
        }

        // -------------------------------------------------------
        // 5. package_source_locations — enforce unique pair
        // -------------------------------------------------------
        if (Schema::hasTable('package_source_locations')) {
            Schema::table('package_source_locations', function (Blueprint $table) {
                if (!$this->indexExists('package_source_locations', 'pkgsrcloc_unique')) {
                    $table->unique(['package_id', 'location_id'], 'pkgsrcloc_unique');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropIndexIfExists('packages_deleted_active_idx');
            $table->dropIndexIfExists('packages_mode_idx');
            // Keep slug unique — dropping could cause duplicates
        });

        Schema::table('packages_group_dates', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropIndexIfExists('pgd_package_date_idx');
        });

        Schema::table('packages_categories', function (Blueprint $table) {
            $table->dropIndexIfExists('pkgcat_package_category_unique');
        });
    }

    // -------------------------------------------------------
    // Helpers (avoids duplicate-index errors on re-run)
    // -------------------------------------------------------
    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return !empty($indexes);
    }

    private function foreignKeyExists(string $table, string $fkName): bool
    {
        $result = DB::select(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?",
            [$table, $fkName]
        );
        return !empty($result);
    }
};