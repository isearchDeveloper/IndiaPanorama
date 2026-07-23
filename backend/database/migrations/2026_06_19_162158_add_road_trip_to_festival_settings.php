<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('festival_settings', function (Blueprint $table) {
            $table->string('road_trip_title')->nullable();
            $table->text('road_trip_sub_title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('festival_settings', function (Blueprint $table) {
            $table->dropColumn(['road_trip_title', 'road_trip_sub_title']);
        });
    }
};
