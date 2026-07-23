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
        Schema::table('city_guide_settings', function (Blueprint $table) {
            $table->dropColumn([
                'travel_tips', 'things_to_know', 'religious_tourism_intro', 'souvenirs', 'popular_dishes',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('city_guide_settings', function (Blueprint $table) {
            $table->longText('travel_tips')->nullable();
            $table->longText('things_to_know')->nullable();
            $table->longText('religious_tourism_intro')->nullable();
            $table->longText('souvenirs')->nullable();
            $table->longText('popular_dishes')->nullable();
        });
    }
};
