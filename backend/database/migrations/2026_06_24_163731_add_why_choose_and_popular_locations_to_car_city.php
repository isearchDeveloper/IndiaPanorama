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
            $table->string('why_choose_title')->nullable();
            $table->string('why_choose_subtitle')->nullable();
            $table->string('popular_locations_title')->nullable();
            $table->string('popular_locations_subtitle')->nullable();
        });

        if (!Schema::hasTable('car_city_why_choose_stats')) {
            Schema::create('car_city_why_choose_stats', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('city_id')->index();
                $table->string('label');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_city_why_choose_stats');

        Schema::table('car_city', function (Blueprint $table) {
            $table->dropColumn([
                'why_choose_title', 'why_choose_subtitle',
                'popular_locations_title', 'popular_locations_subtitle',
            ]);
        });
    }
};
