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
        Schema::table('enquiries', function (Blueprint $table) {
            // Extra fields for richer "Plan Trip" / "Customized Tour" style forms —
            // nullable since simpler enquiry types (General, Tour Booking, ...) never
            // send these; only this endpoint's caller decides which fields are
            // required for its own form.
            $table->string('country')->nullable()->after('message');
            $table->string('budget')->nullable()->after('country');
            $table->unsignedInteger('no_of_persons')->nullable()->after('budget');
            $table->date('travel_date')->nullable()->after('no_of_persons');
            $table->string('duration')->nullable()->after('travel_date');
            $table->string('arrival_city')->nullable()->after('duration');
            $table->string('departure_city')->nullable()->after('arrival_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn([
                'country', 'budget', 'no_of_persons', 'travel_date', 'duration', 'arrival_city', 'departure_city',
            ]);
        });
    }
};
