<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_about_features', function (Blueprint $table) {
            $table->string('heading_text', 255)->nullable()->after('id');
            $table->text('feature_description')->nullable()->after('icon_class');
        });
    }

    public function down(): void
    {
        Schema::table('home_about_features', function (Blueprint $table) {
            $table->dropColumn(['heading_text', 'feature_description']);
        });
    }
};
