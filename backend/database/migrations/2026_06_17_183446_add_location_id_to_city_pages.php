<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A city_page can now belong to a State OR a Location (city) — make state_id optional.
        DB::statement('ALTER TABLE city_pages MODIFY state_id INT NULL');

        Schema::table('city_pages', function (Blueprint $table) {
            $table->integer('location_id')->nullable()->unique()->after('state_id');
            $table->foreign('location_id')->references('id')->on('locations')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('city_pages', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn('location_id');
        });

        DB::statement('ALTER TABLE city_pages MODIFY state_id INT NOT NULL');
    }
};
