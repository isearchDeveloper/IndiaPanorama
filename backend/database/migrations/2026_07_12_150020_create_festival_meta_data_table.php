<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backfills a migration for the pre-existing "festival_meta_data" table, which was
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
        if (!Schema::hasTable('festival_meta_data')) {
            Schema::create('festival_meta_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('festival_id');
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('h1_heading', 255)->nullable();
            $table->longText('meta_details')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->index(['festival_id'], 'festival_id_index');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('festival_meta_data');
    }
};
