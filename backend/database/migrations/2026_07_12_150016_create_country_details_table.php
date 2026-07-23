<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backfills a migration for the pre-existing "country_details" table, which was
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
        if (!Schema::hasTable('country_details')) {
            Schema::create('country_details', function (Blueprint $table) {
            $table->id();
            $table->integer('country_id');
            $table->string('title', 255);
            $table->string('sub_title', 256)->nullable();
            $table->text('banner_image');
            $table->string('banner_image_alt', 256)->nullable();
            $table->text('about');
            $table->string('author_name', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->index(['country_id'], 'countries_continent_id_foreign');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('country_details');
    }
};
