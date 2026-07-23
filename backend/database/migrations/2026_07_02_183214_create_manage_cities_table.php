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
        Schema::create('manage_cities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('state_id')->nullable()->index();
            $table->unsignedBigInteger('location_id')->nullable()->index();
            $table->string('title')->nullable();
            $table->string('sub_title')->nullable();
            $table->longText('about')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('banner_image_alt')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manage_cities');
    }
};
