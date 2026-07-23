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
        Schema::table('car_city', function (Blueprint $table) {
            $table->longText('why_choose_subtitle')->nullable()->change();
            $table->longText('popular_locations_subtitle')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_city', function (Blueprint $table) {
            $table->string('why_choose_subtitle')->nullable()->change();
            $table->string('popular_locations_subtitle')->nullable()->change();
        });
    }
};
