<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tourist_activity_setting_perfect_fors', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('tourist_activity_setting_perfect_fors', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
