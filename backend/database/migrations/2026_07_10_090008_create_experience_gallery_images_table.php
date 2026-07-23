<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experience_gallery_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experience_id')->constrained('experiences')->cascadeOnDelete();
            $table->string('image');
            $table->string('image_alt')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0); // lowest sort_order = banner/first image
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_gallery_images');
    }
};
