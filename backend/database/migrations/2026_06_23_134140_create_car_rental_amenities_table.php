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
        Schema::create('car_rental_amenities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_rental_content_id');
            $table->string('icon')->nullable();
            $table->string('icon_alt')->nullable();
            $table->string('label');
            $table->string('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('car_rental_content_id')->references('id')->on('car_rental_contents')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_rental_amenities');
    }
};
