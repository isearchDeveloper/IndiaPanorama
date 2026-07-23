<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backfills a migration for the pre-existing "car_routes_page_details" table, which was
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
        if (!Schema::hasTable('car_routes_page_details')) {
            Schema::create('car_routes_page_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('route_id');
            $table->string('title', 256)->nullable();
            $table->text('description')->nullable();
            $table->string('about_title', 255)->nullable();
            $table->string('about_image', 255)->nullable();
            $table->string('about_image_alt', 255)->nullable();
            $table->longText('about_description')->nullable();
            $table->string('distance_text', 255)->nullable();
            $table->string('duration_text', 255)->nullable();
            $table->string('route_number', 255)->nullable();
            $table->string('best_season', 255)->nullable();
            $table->text('banner_image')->nullable();
            $table->string('banner_image_alt', 256)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('car_routes_page_details');
    }
};
