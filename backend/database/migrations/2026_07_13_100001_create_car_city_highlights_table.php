<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_city_highlights', function (Blueprint $table) {
            $table->id();
            // car_city.id is a legacy plain `int` (not bigint), so this can't use
            // foreignId()->constrained() — matches the unconstrained `integer('city_id')`
            // convention already used by the other car_city_* child tables.
            $table->integer('city_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_city_highlights');
    }
};
