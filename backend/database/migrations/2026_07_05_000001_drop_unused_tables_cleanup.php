<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // Hotel module (Dec 2025) — no model, no controller, no route
        Schema::dropIfExists('hotecitymetadatas');
        Schema::dropIfExists('hotel_city_faqs');
        Schema::dropIfExists('hotel_cities');

        // Old city_pages system (Jun 2026) — replaced by manage_cities
        Schema::dropIfExists('city_page_activities');
        Schema::dropIfExists('city_page_gallery_items');
        Schema::dropIfExists('city_page_places');
        Schema::dropIfExists('city_page_faqs');
        Schema::dropIfExists('city_page_quick_facts');
        Schema::dropIfExists('city_pages');

        // Intermediate city guide system (Jun 2026) — abandoned before launch
        Schema::dropIfExists('city_guide_faqs');
        Schema::dropIfExists('city_guide_quick_facts');
        Schema::dropIfExists('city_guide_settings');

        // Booking / payment / coupon system — never completed, zero PHP references
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('coupons');

        // Old brand name remnant
        Schema::dropIfExists('why_cholan_tour');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // These tables were dead weight — no rollback defined intentionally.
        // Restore from a database backup if needed.
    }
};
