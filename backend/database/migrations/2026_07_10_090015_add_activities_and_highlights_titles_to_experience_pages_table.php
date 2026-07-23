<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experience_pages', function (Blueprint $table) {
            $table->string('activities_title')->nullable()->after('faq_sub_title');
            $table->string('highlights_title')->nullable()->after('activities_title');
        });
    }

    public function down(): void
    {
        Schema::table('experience_pages', function (Blueprint $table) {
            $table->dropColumn(['activities_title', 'highlights_title']);
        });
    }
};
