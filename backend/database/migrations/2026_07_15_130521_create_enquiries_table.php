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
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();

            // Which admin tab this enquiry is filed under — mirrors the site's main
            // modules (Holidays/Experiences/Destination/Activities/Car Rental), with
            // "general" as the catch-all for enquiry types that don't map to one.
            $table->string('category')->default('general')->index();

            // The exact option the customer picked in the enquiry form (e.g.
            // "Tour Booking", "Train Booking") — kept separate from `category`
            // since several types can roll up into the same category.
            $table->string('enquiry_type');

            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->text('message')->nullable();

            // Follow-up workflow for the CRM list.
            $table->string('status')->default('new')->index();

            // Page the enquiry was submitted from, for context in the admin view.
            $table->string('source_url')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
