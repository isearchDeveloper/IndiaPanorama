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
        Schema::table('car_routes_page_details', function (Blueprint $table) {
            $table->string('about_title')->nullable()->after('description');
            $table->string('about_image')->nullable()->after('about_title');
            $table->string('about_image_alt')->nullable()->after('about_image');
            $table->longText('about_description')->nullable()->after('about_image_alt');
            $table->string('distance_text')->nullable()->after('about_description');
            $table->string('duration_text')->nullable()->after('distance_text');
            $table->string('route_number')->nullable()->after('duration_text');
            $table->string('best_season')->nullable()->after('route_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_routes_page_details', function (Blueprint $table) {
            $table->dropColumn([
                'about_title', 'about_image', 'about_image_alt', 'about_description',
                'distance_text', 'duration_text', 'route_number', 'best_season',
            ]);
        });
    }
};
