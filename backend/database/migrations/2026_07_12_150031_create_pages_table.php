<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backfills a migration for the pre-existing "pages" table, which was
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
        if (!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 256);
            $table->string('title', 256);
            $table->text('description');
            $table->text('banner_image');
            $table->string('banner_image_alt', 256)->nullable();
            $table->string('faq_title', 256)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
