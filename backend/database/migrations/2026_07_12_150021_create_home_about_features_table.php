<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backfills a migration for the pre-existing "home_about_features" table, which was
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
        if (!Schema::hasTable('home_about_features')) {
            Schema::create('home_about_features', function (Blueprint $table) {
            $table->id();
            $table->string('text', 255)->comment('e.g. "Best Price Guarantee"');
            $table->string('icon_class', 100)->default('fas fa-check-circle')->comment('FontAwesome class for the icon');
            $table->text('feature_description')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->index(['is_active', 'sort_order'], 'idx_haf_active_sort');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('home_about_features');
    }
};
