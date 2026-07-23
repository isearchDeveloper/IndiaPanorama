<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backfills a migration for the pre-existing "car_city" table, which was
 * created directly in the database (outside Laravel's migration system)
 * before this project adopted migrations for its schema. Guarded with
 * Schema::hasTable() so it's a no-op on any environment where the table
 * already exists, and only actually creates the table on a fresh install
 * (e.g. `migrate:fresh`) where it would otherwise be silently missing.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('car_city')) {
            Schema::create('car_city', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 256);
            $table->string('location', 256);
            $table->string('thumbnail_image', 255)->nullable();
            $table->string('thumbnail_alt', 255)->nullable();
            $table->boolean('is_active')->default(0);
            $table->boolean('is_popular')->default(0);
            $table->string('display_label', 256)->nullable();
            $table->string('features_title', 255)->nullable();
            $table->string('benefits_title', 255)->nullable();
            $table->string('faq_title', 256)->nullable();
            $table->string('faq_sub_title', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->string('why_choose_title', 255)->nullable();
            $table->longText('why_choose_subtitle')->nullable();
            $table->boolean('why_choose_enabled')->default(0);
            $table->string('popular_locations_title', 255)->nullable();
            $table->longText('popular_locations_subtitle')->nullable();
            $table->timestamp('deleted_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('car_city');
    }
};
