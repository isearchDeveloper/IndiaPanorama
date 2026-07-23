<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backfills a migration for the pre-existing "banners" table, which was
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
        if (!Schema::hasTable('banners')) {
            Schema::create('banners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->string('subtitle', 255)->nullable();
            $table->string('button_text', 100)->nullable();
            $table->string('url', 300)->nullable();
            $table->text('banner_image');
            $table->string('banner_image_alt', 256)->nullable();
            $table->boolean('is_static')->default(0);
            $table->boolean('is_active')->default(1);
            $table->tinyInteger('sort_order')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->index(['is_active', 'sort_order'], 'idx_ban_active_sort');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
