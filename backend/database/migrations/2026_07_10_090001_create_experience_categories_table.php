<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experience_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->text('description')->nullable();

            // Category page banner
            $table->string('banner_title')->nullable();
            $table->string('banner_tagline')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('banner_image_alt')->nullable();

            // Category page intro section
            $table->string('intro_heading')->nullable();
            $table->text('intro_description')->nullable();
            $table->string('intro_image')->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('h1_heading')->nullable();
            $table->text('meta_details')->nullable();
            $table->string('faq_title')->nullable();
            $table->string('faq_sub_title')->nullable();

            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_categories');
    }
};
