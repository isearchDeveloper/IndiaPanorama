<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Mega Menu legacy tables (replaced by menu_items system)
        Schema::dropIfExists('mega_menu_cities');
        Schema::dropIfExists('mega_menu_countries');
        Schema::dropIfExists('mega_menu_regions');

        // Nav Menu legacy tables (replaced by menus/menu_items system)
        Schema::dropIfExists('nav_menu_items');
        Schema::dropIfExists('nav_menus');

        // Women Chauffeur — module removed, no routes/models/views exist
        Schema::dropIfExists('women_chauffeur_sections');
        Schema::dropIfExists('women_chauffeur_cards');
        Schema::dropIfExists('women_chauffeur_banner');

        // Home Section Items — replaced by home_about_features / home_blog_items
        Schema::dropIfExists('home_section_items');

        // Countries master — duplicate of countries table; no model references it
        Schema::dropIfExists('countries_master');

        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Intentionally empty — these tables belong to removed modules.
    }
};
