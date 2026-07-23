<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backfills a migration for the pre-existing "cars" table, which was
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
        if (!Schema::hasTable('cars')) {
            Schema::create('cars', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id');
            $table->string('slug', 256);
            $table->string('title', 256);
            $table->text('description')->nullable();
            $table->string('primary_image', 256);
            $table->string('primary_image_alt', 256);
            $table->string('banner_image', 255)->nullable();
            $table->string('banner_image_alt', 255)->nullable();
            $table->string('seats', 50);
            $table->string('vehicle_type', 100)->nullable();
            $table->string('transmission', 100)->nullable();
            $table->string('luggage_capacity', 100)->nullable();
            $table->string('mileage', 100)->nullable();
            $table->string('specs_title', 255)->nullable();
            $table->text('specs_description')->nullable();
            $table->string('fuel_type', 50)->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->string('gallery_title', 255)->nullable();
            $table->text('gallery_description')->nullable();
            $table->timestamp('deleted_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
