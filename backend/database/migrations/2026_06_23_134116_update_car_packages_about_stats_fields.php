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
        Schema::table('car_packages', function (Blueprint $table) {
            $table->string('ideal_for')->nullable()->after('best_season');
        });
        Schema::table('car_packages', function (Blueprint $table) {
            $table->dropColumn(['distance_text', 'route_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_packages', function (Blueprint $table) {
            $table->string('distance_text')->nullable();
            $table->string('route_number')->nullable();
            $table->dropColumn('ideal_for');
        });
    }
};
