<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('city_pages', function (Blueprint $table) {
            $table->id();
            $table->integer('state_id')->unique();
            $table->foreign('state_id')->references('id')->on('states')->cascadeOnDelete();

            // Quick Facts
            $table->string('capital_city')->nullable();
            $table->string('best_season')->nullable();
            $table->string('major_cities')->nullable();
            $table->string('climate')->nullable();
            $table->string('famous_festivals')->nullable();
            $table->string('key_attractions')->nullable();
            $table->string('languages')->nullable();
            $table->string('cuisine')->nullable();
            $table->string('unique_experiences')->nullable();

            // Rich text sections
            $table->longText('how_to_reach')->nullable();
            $table->longText('fairs_festivals_intro')->nullable();
            $table->longText('religious_tourism_intro')->nullable();
            $table->longText('souvenirs')->nullable();
            $table->longText('popular_dishes')->nullable();

            // Simple bullet lists
            $table->json('travel_tips')->nullable();
            $table->json('things_to_know')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('city_page_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_page_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('city_page_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_page_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('duration')->nullable();
            $table->string('best_for')->nullable();
            $table->string('approx_cost')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('city_page_gallery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_page_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['attraction', 'festival', 'temple']);
            $table->string('image');
            $table->string('alt')->nullable();
            $table->string('title');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('city_page_gallery_items');
        Schema::dropIfExists('city_page_activities');
        Schema::dropIfExists('city_page_places');
        Schema::dropIfExists('city_pages');
    }
};
