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
        Schema::create('car_destination_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('destination_id');
            $table->integer('car_id');
            $table->timestamps();

            $table->foreign('destination_id')->references('id')->on('car_destinations')->cascadeOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_destination_details');
    }
};
