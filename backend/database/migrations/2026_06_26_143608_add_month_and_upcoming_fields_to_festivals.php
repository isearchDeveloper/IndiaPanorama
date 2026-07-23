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
        Schema::table('festivals', function (Blueprint $table) {
            $table->unsignedTinyInteger('month')->nullable()->after('state_id');
            $table->boolean('is_upcoming')->default(false)->after('is_active');
            $table->string('date_range_text')->nullable()->after('is_upcoming');
            $table->string('location_text')->nullable()->after('date_range_text');
            $table->unsignedSmallInteger('duration_days')->nullable()->after('location_text');
            $table->text('short_description')->nullable()->after('duration_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('festivals', function (Blueprint $table) {
            $table->dropColumn([
                'month', 'is_upcoming', 'date_range_text',
                'location_text', 'duration_days', 'short_description',
            ]);
        });
    }
};
