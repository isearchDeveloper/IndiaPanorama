<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('holiday_settings', function (Blueprint $table) {
            $table->string('faq_image')->nullable()->after('faq_title');
            $table->string('faq_image_alt')->nullable()->after('faq_image');
        });
    }

    public function down(): void
    {
        Schema::table('holiday_settings', function (Blueprint $table) {
            $table->dropColumn(['faq_image', 'faq_image_alt']);
        });
    }
};
