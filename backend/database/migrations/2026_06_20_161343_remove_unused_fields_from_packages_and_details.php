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
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'is_offer_page']);
        });

        Schema::table('package_details', function (Blueprint $table) {
            $table->dropColumn([
                'start_date', 'end_date', 'route_details',
                'itinerary_overview', 'includes', 'excludes',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_offer_page')->default(false);
        });

        Schema::table('package_details', function (Blueprint $table) {
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('route_details')->nullable();
            $table->longText('itinerary_overview')->nullable();
            $table->longText('includes')->nullable();
            $table->longText('excludes')->nullable();
        });
    }
};
