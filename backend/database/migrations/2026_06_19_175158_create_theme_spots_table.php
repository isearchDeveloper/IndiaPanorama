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
        Schema::create('theme_spots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_id')->constrained()->cascadeOnDelete();

            // Geographic scope — both null = root-level sub-category listing (e.g. /experiences/wildlife/caves);
            // state_id only = state-level spot detail page (e.g. /kerala/experiences/periyar-wildlife-sanctuary);
            // state_id + location_id = city-level spot detail page (e.g. /kerala/munnar/experiences/...).
            $table->integer('state_id')->nullable();
            $table->foreign('state_id')->references('id')->on('states')->cascadeOnDelete();
            $table->integer('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->cascadeOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_spots');
    }
};
