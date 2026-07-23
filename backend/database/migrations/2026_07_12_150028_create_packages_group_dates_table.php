<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backfills a migration for the pre-existing "packages_group_dates" table, which was
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
        if (!Schema::hasTable('packages_group_dates')) {
            Schema::create('packages_group_dates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id')->nullable();
            $table->date('departure_date');
            $table->decimal('price', 10, 2);
            $table->integer('total_seats')->nullable()->default(20);
            $table->integer('booked_seats')->nullable()->default(0);
            $table->enum('status', ['available', 'soldout', 'cancelled'])->nullable()->default('available');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->index(['package_id'], 'fk_group_dates_group');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('packages_group_dates');
    }
};
