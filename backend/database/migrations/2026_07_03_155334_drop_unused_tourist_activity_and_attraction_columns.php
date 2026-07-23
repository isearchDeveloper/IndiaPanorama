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
        Schema::table('tourist_activities', function (Blueprint $table) {
            $table->dropColumn([
                'about_title', 'about_description',
                'location_text', 'duration_text', 'best_for', 'best_season',
                'places_title',
            ]);
        });

        Schema::table('tourist_attractions', function (Blueprint $table) {
            $table->dropColumn(['about_title', 'about_description']);
        });

        Schema::dropIfExists('tourist_activity_places');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('tourist_activity_places', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('activities_text')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('activity_id')->references('id')->on('tourist_activities')->cascadeOnDelete();
        });

        Schema::table('tourist_attractions', function (Blueprint $table) {
            $table->string('about_title')->nullable();
            $table->longText('about_description')->nullable();
        });

        Schema::table('tourist_activities', function (Blueprint $table) {
            $table->string('about_title')->nullable();
            $table->longText('about_description')->nullable();
            $table->string('location_text')->nullable();
            $table->string('duration_text')->nullable();
            $table->string('best_for')->nullable();
            $table->string('best_season')->nullable();
            $table->string('places_title')->nullable();
        });
    }
};
