<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * An Experience must always belong to a Category, but a Subcategory is now
 * optional — some experiences sit directly under a category with no
 * subcategory yet. Adds `category_id` (backfilled from each row's existing
 * subcategory, then made required) and relaxes `subcategory_id` to nullable.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
        });

        DB::statement('ALTER TABLE experiences MODIFY subcategory_id BIGINT UNSIGNED NULL');

        Schema::table('experiences', function (Blueprint $table) {
            $table->foreign('subcategory_id')->references('id')->on('experience_subcategories')->nullOnDelete();
            $table->unsignedBigInteger('category_id')->nullable()->after('subcategory_id');
        });

        DB::statement('
            UPDATE experiences e
            JOIN experience_subcategories es ON es.id = e.subcategory_id
            SET e.category_id = es.category_id
            WHERE e.subcategory_id IS NOT NULL
        ');

        DB::statement('ALTER TABLE experiences MODIFY category_id BIGINT UNSIGNED NOT NULL');

        Schema::table('experiences', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('experience_categories')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            $table->dropForeign(['subcategory_id']);
        });

        DB::statement('ALTER TABLE experiences MODIFY subcategory_id BIGINT UNSIGNED NOT NULL');

        Schema::table('experiences', function (Blueprint $table) {
            $table->foreign('subcategory_id')->references('id')->on('experience_subcategories')->cascadeOnDelete();
        });
    }
};
