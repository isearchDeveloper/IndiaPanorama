<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** City (location_id) is now optional on an Experience — state_id stays required. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
        });

        DB::statement('ALTER TABLE experiences MODIFY location_id INT NULL');

        Schema::table('experiences', function (Blueprint $table) {
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
        });

        DB::statement('ALTER TABLE experiences MODIFY location_id INT NOT NULL');

        Schema::table('experiences', function (Blueprint $table) {
            $table->foreign('location_id')->references('id')->on('locations')->cascadeOnDelete();
        });
    }
};
