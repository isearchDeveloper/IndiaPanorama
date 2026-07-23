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
        Schema::table('car_city', function (Blueprint $table) {
            $table->boolean('why_choose_enabled')->default(false)->after('why_choose_subtitle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_city', function (Blueprint $table) {
            $table->dropColumn('why_choose_enabled');
        });
    }
};
