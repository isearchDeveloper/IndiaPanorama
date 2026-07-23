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
        Schema::create('tourist_attraction_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('banner_image_alt')->nullable();
            $table->text('banner_text')->nullable();
            $table->text('short_description')->nullable();
            $table->string('faq_title')->nullable();
            $table->string('faq_sub_title')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('h1_heading')->nullable();
            $table->text('meta_details')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tourist_attraction_settings');
    }
};
