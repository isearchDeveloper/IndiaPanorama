<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Editorial intro text for the city-only "Activities in {City}" section. */
    public function up(): void
    {
        Schema::table('tourist_activity_pages', function (Blueprint $table) {
            $table->text('activities_in_city_sub_title')->nullable()->after('things_to_do_title');
        });
    }

    public function down(): void
    {
        Schema::table('tourist_activity_pages', function (Blueprint $table) {
            $table->dropColumn('activities_in_city_sub_title');
        });
    }
};
