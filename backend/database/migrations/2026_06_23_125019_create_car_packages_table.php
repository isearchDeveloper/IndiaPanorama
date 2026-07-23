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
        Schema::create('car_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('banner_image')->nullable();
            $table->string('banner_image_alt')->nullable();
            $table->text('description')->nullable();

            $table->string('about_title')->nullable();
            $table->string('about_image')->nullable();
            $table->string('about_image_alt')->nullable();
            $table->longText('about_description')->nullable();
            $table->string('distance_text')->nullable();
            $table->string('duration_text')->nullable();
            $table->string('route_number')->nullable();
            $table->string('best_season')->nullable();

            $table->string('faq_title')->nullable();
            $table->string('faq_sub_title')->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('h1_heading')->nullable();
            $table->text('meta_details')->nullable();

            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(1);
            $table->boolean('is_popular')->default(0);
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_packages');
    }
};
