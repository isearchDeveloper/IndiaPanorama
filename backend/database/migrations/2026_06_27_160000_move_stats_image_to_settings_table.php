<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** One Stats image for the whole section, not one per row. */
    public function up(): void
    {
        Schema::table('tourist_activity_settings', function (Blueprint $table) {
            $table->string('stats_image')->nullable()->after('seasons_title');
            $table->string('stats_image_alt')->nullable()->after('stats_image');
        });

        Schema::table('tourist_activity_setting_highlights', function (Blueprint $table) {
            $table->dropColumn(['image', 'image_alt']);
        });
    }

    public function down(): void
    {
        Schema::table('tourist_activity_setting_highlights', function (Blueprint $table) {
            $table->string('image')->nullable()->after('setting_id');
            $table->string('image_alt')->nullable()->after('image');
        });

        Schema::table('tourist_activity_settings', function (Blueprint $table) {
            $table->dropColumn(['stats_image', 'stats_image_alt']);
        });
    }
};
