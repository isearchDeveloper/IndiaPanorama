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
        Schema::table('road_trip_destinations', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn(['image', 'image_alt', 'package_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('road_trip_destinations', function (Blueprint $table) {
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->integer('package_id')->nullable();
            $table->foreign('package_id')->references('id')->on('packages')->nullOnDelete();
        });
    }
};
