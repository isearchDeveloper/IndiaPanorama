<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('festivals', function (Blueprint $table) {
            $table->string('location_text')->nullable()->after('state_id');
            $table->string('month_text')->nullable()->after('month');
            $table->string('duration_text')->nullable()->after('month_text');
        });
    }

    public function down(): void
    {
        Schema::table('festivals', function (Blueprint $table) {
            $table->dropColumn(['location_text', 'month_text', 'duration_text']);
        });
    }
};
