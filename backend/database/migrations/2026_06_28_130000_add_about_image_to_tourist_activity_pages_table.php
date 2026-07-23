<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** About image — only used on State-type pages, set from the Banner & Settings modal. */
    public function up(): void
    {
        Schema::table('tourist_activity_pages', function (Blueprint $table) {
            $table->string('about_image')->nullable()->after('short_description');
            $table->string('about_image_alt')->nullable()->after('about_image');
        });
    }

    public function down(): void
    {
        Schema::table('tourist_activity_pages', function (Blueprint $table) {
            $table->dropColumn(['about_image', 'about_image_alt']);
        });
    }
};
