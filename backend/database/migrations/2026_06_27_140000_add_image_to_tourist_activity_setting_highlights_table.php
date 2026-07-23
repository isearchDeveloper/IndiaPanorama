<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tourist_activity_setting_highlights', function (Blueprint $table) {
            $table->string('image')->nullable()->after('setting_id');
            $table->string('image_alt')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('tourist_activity_setting_highlights', function (Blueprint $table) {
            $table->dropColumn(['image', 'image_alt']);
        });
    }
};
