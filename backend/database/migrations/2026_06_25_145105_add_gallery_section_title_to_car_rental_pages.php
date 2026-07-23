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
            $table->string('gallery_title')->nullable();
            $table->text('gallery_description')->nullable();
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->string('gallery_title')->nullable();
            $table->text('gallery_description')->nullable();
        });

        Schema::table('car_city_page_details', function (Blueprint $table) {
            $table->string('gallery_title')->nullable();
            $table->text('gallery_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_rental_contents', function (Blueprint $table) {
            $table->dropColumn(['gallery_title', 'gallery_description']);
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['gallery_title', 'gallery_description']);
        });

        Schema::table('car_city_page_details', function (Blueprint $table) {
            $table->dropColumn(['gallery_title', 'gallery_description']);
        });
    }
};
