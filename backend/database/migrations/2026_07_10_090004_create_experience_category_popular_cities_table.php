<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experience_category_popular_cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('experience_categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->text('description')->nullable();
            $table->string('popular_tag')->nullable();

            $table->integer('state_id');
            $table->foreign('state_id')->references('id')->on('states')->cascadeOnDelete();
            $table->integer('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->cascadeOnDelete();

            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_category_popular_cities');
    }
};
