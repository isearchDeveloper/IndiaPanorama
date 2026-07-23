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
        Schema::create('tourist_attraction_gallery_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attraction_id');
            $table->string('image');
            $table->string('image_alt')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('attraction_id')->references('id')->on('tourist_attractions')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tourist_attraction_gallery_images');
    }
};
