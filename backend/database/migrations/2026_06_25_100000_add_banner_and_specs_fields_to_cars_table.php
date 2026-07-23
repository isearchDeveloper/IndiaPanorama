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
        Schema::table('cars', function (Blueprint $table) {
            $table->string('banner_image')->nullable()->after('primary_image_alt');
            $table->string('banner_image_alt')->nullable()->after('banner_image');
            $table->string('specs_title')->nullable()->after('mileage');
            $table->text('specs_description')->nullable()->after('specs_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['banner_image', 'banner_image_alt', 'specs_title', 'specs_description']);
        });
    }
};
