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
        Schema::table('regions_details', function (Blueprint $table) {
            $table->string('home_image')->nullable()->after('banner_image_alt');
            $table->string('home_image_alt')->nullable()->after('home_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions_details', function (Blueprint $table) {
            $table->dropColumn(['home_image', 'home_image_alt']);
        });
    }
};
