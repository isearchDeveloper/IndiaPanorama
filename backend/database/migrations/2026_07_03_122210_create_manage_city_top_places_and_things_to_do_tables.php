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
        // Top Tourist Places
        Schema::create('manage_city_top_places', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manage_city_id')->index();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Things To Do
        Schema::create('manage_city_things_to_do', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manage_city_id')->index();
            $table->string('title')->nullable();
            $table->string('duration')->nullable();
            $table->string('best_for')->nullable();
            $table->string('approx_cost')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manage_city_things_to_do');
        Schema::dropIfExists('manage_city_top_places');
    }
};
