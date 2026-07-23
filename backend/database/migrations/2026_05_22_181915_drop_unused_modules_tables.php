<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Themes
        Schema::dropIfExists('theme_location_metas');
        Schema::dropIfExists('theme_location_faqs');
        Schema::dropIfExists('themes_packages');
        Schema::dropIfExists('theme_locations');
        Schema::dropIfExists('theme_faqs');
        Schema::dropIfExists('theme_metas');
        Schema::dropIfExists('themes');

        // Fairs & Festivals
        Schema::dropIfExists('festival_page_metas');
        Schema::dropIfExists('festival_page_faqs');
        Schema::dropIfExists('festival_pages');
        Schema::dropIfExists('fair_festival_metas');
        Schema::dropIfExists('fair_festival_faqs');
        Schema::dropIfExists('fairs_festivals');

        // City Pages
        Schema::dropIfExists('city_attraction_todos');
        Schema::dropIfExists('city_things_todos');
        Schema::dropIfExists('city_visit_todos');
        Schema::dropIfExists('city_metas');
        Schema::dropIfExists('city_faqs');
        Schema::dropIfExists('city_page');

        // Events
        Schema::dropIfExists('events');

        // Go Exploring
        Schema::dropIfExists('go_exploring_enquiries');
        Schema::dropIfExists('go_exploring_features');
        Schema::dropIfExists('go_exploring_images');
        Schema::dropIfExists('explore_faqs');
        Schema::dropIfExists('explore_metas');
        Schema::dropIfExists('go_explorings');

        // Offers & Deals
        Schema::dropIfExists('offer_pages');
        Schema::dropIfExists('promotional_ads');

        // Summer Specials
        Schema::dropIfExists('summer_faqs');
        Schema::dropIfExists('summers');

        // Holiday menu (was driven by city pages)
        Schema::dropIfExists('holiday_menu_cities');
        Schema::dropIfExists('holiday_menu_states');
        Schema::dropIfExists('menu_state_orders');

        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Intentionally empty — these tables belong to removed modules.
    }
};
