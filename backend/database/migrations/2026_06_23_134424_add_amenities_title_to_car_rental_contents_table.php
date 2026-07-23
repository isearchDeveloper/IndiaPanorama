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
        Schema::table('car_rental_contents', function (Blueprint $table) {
            $table->string('amenities_title')->nullable()->after('popular_locations_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_rental_contents', function (Blueprint $table) {
            $table->dropColumn('amenities_title');
        });
    }
};
