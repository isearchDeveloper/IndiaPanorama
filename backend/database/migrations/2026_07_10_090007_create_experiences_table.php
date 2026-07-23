<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcategory_id')->constrained('experience_subcategories')->cascadeOnDelete();

            $table->integer('state_id');
            $table->foreign('state_id')->references('id')->on('states')->cascadeOnDelete();
            $table->integer('location_id');
            $table->foreign('location_id')->references('id')->on('locations')->cascadeOnDelete();

            $table->string('title');
            $table->string('slug')->unique(); // WITHOUT the "-experience" suffix (frontend adds/strips it)
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('banner_image_alt')->nullable();

            // Fixed-shape quick info shown on the detail page
            $table->string('best_time')->nullable();
            $table->string('duration')->nullable();
            $table->string('entry_fee')->nullable();
            $table->string('location_text')->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('h1_heading')->nullable();
            $table->text('meta_details')->nullable();

            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
