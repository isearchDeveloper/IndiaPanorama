<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guard: skip if table already exists (production DB may have it)
        if (Schema::hasTable('states')) {
            // Ensure required columns & indexes exist
            Schema::table('states', function (Blueprint $table) {
                if (!Schema::hasColumn('states', 'country_id')) {
                    $table->unsignedBigInteger('country_id')->after('id');
                }
                if (!Schema::hasColumn('states', 'region_id')) {
                    $table->unsignedBigInteger('region_id')->nullable()->after('country_id');
                }
                if (!Schema::hasColumn('states', 'name')) {
                    $table->string('name', 150)->after('region_id');
                }
                if (!Schema::hasColumn('states', 'slug')) {
                    $table->string('slug', 170)->nullable()->after('name');
                }
                if (!Schema::hasColumn('states', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('slug');
                }
            });
            return;
        }

        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')
                  ->constrained('countries')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('region_id')->nullable()->index();
            $table->string('name', 150);
            $table->string('slug', 170)->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('country_id', 'states_country_id_idx');
            $table->index(['country_id', 'is_active'], 'states_country_active_idx');
        });

        // Add FK for region_id after regions table is guaranteed to exist
        if (Schema::hasTable('regions')) {
            Schema::table('states', function (Blueprint $table) {
                $table->foreign('region_id', 'states_region_id_foreign')
                      ->references('id')
                      ->on('regions')
                      ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
