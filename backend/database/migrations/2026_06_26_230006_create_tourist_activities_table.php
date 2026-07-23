<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tourist_activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('state_id');
            $table->integer('location_id');
            $table->string('banner_image')->nullable();
            $table->string('banner_image_alt')->nullable();
            $table->string('tagline')->nullable();
            $table->text('short_description')->nullable();

            $table->string('about_title')->nullable();
            $table->longText('about_description')->nullable();

            $table->string('location_text')->nullable();
            $table->string('duration_text')->nullable();
            $table->string('best_for')->nullable();
            $table->string('best_season')->nullable();

            $table->string('why_visit_title')->nullable();
            $table->string('why_visit_image')->nullable();
            $table->string('why_visit_image_alt')->nullable();
            $table->text('why_visit_description')->nullable();

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

            $table->foreign('state_id')->references('id')->on('states')->cascadeOnDelete();
            $table->foreign('location_id')->references('id')->on('locations')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tourist_activities');
    }
};
