<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('road_trip_destinations');

        Schema::table('festival_settings', function (Blueprint $table) {
            if (Schema::hasColumn('festival_settings', 'road_trip_title')) {
                $table->dropColumn(['road_trip_title', 'road_trip_sub_title']);
            }
        });
    }

    public function down(): void
    {
        Schema::create('road_trip_destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->constrained()->cascadeOnDelete();
            $table->string('route_text')->nullable();
            $table->decimal('rating', 3, 1)->nullable();
            $table->unsignedSmallInteger('duration_days')->nullable();
            $table->unsignedSmallInteger('duration_nights')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('festival_settings', function (Blueprint $table) {
            $table->string('road_trip_title')->nullable();
            $table->text('road_trip_sub_title')->nullable();
        });
    }
};
