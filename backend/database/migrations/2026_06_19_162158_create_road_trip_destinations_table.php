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
        Schema::create('road_trip_destinations', function (Blueprint $table) {
            $table->id();
            $table->integer('state_id');
            $table->foreign('state_id')->references('id')->on('states')->cascadeOnDelete();
            $table->integer('package_id')->nullable();
            $table->foreign('package_id')->references('id')->on('packages')->nullOnDelete();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->decimal('rating', 2, 1)->default(5.0);
            $table->string('route_text')->nullable();
            $table->unsignedSmallInteger('duration_days')->nullable();
            $table->unsignedSmallInteger('duration_nights')->nullable();
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
        Schema::dropIfExists('road_trip_destinations');
    }
};
