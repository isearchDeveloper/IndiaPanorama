<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experience_settings', function (Blueprint $table) {
            $table->string('best_time_title')->nullable();
            $table->string('why_choose_title')->nullable();
            $table->text('why_choose_description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('experience_settings', function (Blueprint $table) {
            $table->dropColumn(['best_time_title', 'why_choose_title', 'why_choose_description']);
        });
    }
};
