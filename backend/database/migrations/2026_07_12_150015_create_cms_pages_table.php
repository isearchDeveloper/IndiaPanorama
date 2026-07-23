<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backfills a migration for the pre-existing "cms_pages" table, which was
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
        if (!Schema::hasTable('cms_pages')) {
            Schema::create('cms_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 256);
            $table->string('title', 256)->nullable();
            $table->text('sub_title')->nullable();
            $table->longText('description')->nullable();
            $table->text('banner_image')->nullable();
            $table->string('banner_image_alt', 256)->nullable();
            $table->boolean('is_published')->default(0);
            $table->string('template', 50)->default('default');
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('canonical_url', 255)->nullable();
            $table->string('og_image', 255)->nullable();
            $table->boolean('in_sitemap')->default(1);
            $table->boolean('in_menu')->default(0);
            $table->string('menu_label', 255)->nullable();
            $table->integer('menu_order')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_pages');
    }
};
