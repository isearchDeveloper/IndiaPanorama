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
        Schema::table('festival_setting_why_experiences', function (Blueprint $table) {
            $table->text('tagline')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('festival_setting_why_experiences', function (Blueprint $table) {
            $table->dropColumn('tagline');
        });
    }
};
