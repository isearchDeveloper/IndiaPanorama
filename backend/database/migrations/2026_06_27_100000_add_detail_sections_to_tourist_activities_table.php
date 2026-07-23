<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tourist_activities', function (Blueprint $table) {
            $table->string('experiences_title')->nullable()->after('faq_sub_title');
            $table->string('places_title')->nullable()->after('experiences_title');
            $table->string('things_to_do_title')->nullable()->after('places_title');
            $table->string('itinerary_title')->nullable()->after('things_to_do_title');
        });
    }

    public function down(): void
    {
        Schema::table('tourist_activities', function (Blueprint $table) {
            $table->dropColumn(['experiences_title', 'places_title', 'things_to_do_title', 'itinerary_title']);
        });
    }
};
