<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds a `deleted_at` column (Laravel SoftDeletes) to every table backing
 * an Eloquent model in app/Models, replacing the old per-module `is_deleted`
 * boolean-flag approach with the framework's native soft-delete mechanism.
 */
return new class extends Migration
{
    private array $tables = [
        'activity_logs', 'awards', 'banners', 'car_amenities', 'car_categories',
        'car_city', 'car_city_benefits', 'car_city_details', 'car_city_faqs',
        'car_city_features', 'car_city_gallery_images', 'car_city_meta_data',
        'car_city_page_details', 'car_city_why_choose_stats', 'car_destination_details',
        'car_destination_faqs', 'car_destination_highlights', 'car_destinations',
        'car_gallery_images', 'car_highlight_tags', 'car_package_amenities',
        'car_package_details', 'car_package_faqs', 'car_package_stops', 'car_packages',
        'car_rental_amenities', 'car_rental_benefits', 'car_rental_checklist_items',
        'car_rental_contents', 'car_rental_features', 'car_rental_gallery_images',
        'car_rental_road_trips', 'car_rental_why_choose_stats', 'car_route_highlights',
        'car_routes', 'car_routes_details', 'car_routes_faqs', 'car_routes_meta_data',
        'car_routes_page_details', 'cars', 'categories', 'cms_page_meta_data',
        'cms_page_sections', 'cms_pages', 'countries', 'country_details', 'country_faqs',
        'country_meta_data', 'departments', 'festival_faqs', 'festival_highlights',
        'festival_how_to_reach', 'festival_key_experiences', 'festival_meta_data',
        'festival_places', 'festival_setting_faqs', 'festival_setting_highlights',
        'festival_setting_why_experiences', 'festival_settings', 'festival_state_page_faqs',
        'festival_state_page_why_visits', 'festival_state_pages', 'festival_stats',
        'festival_why_visits', 'festivals', 'holiday_menu_settings', 'holiday_setting_details',
        'holiday_setting_faqs', 'holiday_setting_meta', 'holiday_settings', 'home_about_features',
        'home_blog_items', 'home_sections', 'location_best_times', 'location_details',
        'location_faqs', 'location_meta_data', 'locations', 'login_histories', 'manage_cities',
        'manage_city_faqs', 'manage_city_how_to_reach', 'manage_city_meta',
        'manage_city_quick_facts', 'manage_city_things_to_do', 'manage_city_top_places',
        'menu_items', 'menus', 'news', 'news_faqs', 'news_meta_data', 'package_details',
        'package_faqs', 'package_images', 'package_itinerary', 'package_locations',
        'package_meta_data', 'package_source_locations', 'packages', 'packages_categories',
        'packages_group_dates', 'page_faqs', 'page_meta_data', 'pages', 'partners',
        'region_faqs', 'regions', 'regions_details', 'regions_meta_data', 'reviews',
        'state_best_times', 'state_details', 'state_faqs', 'state_meta_data', 'states',
        'teams', 'theme_faqs', 'theme_setting_faqs', 'theme_settings', 'theme_spots',
        'themes', 'tour_services', 'tourist_activities', 'tourist_activity_experiences',
        'tourist_activity_faqs', 'tourist_activity_gallery_images',
        'tourist_activity_itinerary_steps', 'tourist_activity_page_experiences',
        'tourist_activity_page_faqs', 'tourist_activity_page_things_to_do',
        'tourist_activity_page_waterfalls', 'tourist_activity_pages',
        'tourist_activity_setting_faqs', 'tourist_activity_setting_highlights',
        'tourist_activity_setting_perfect_fors', 'tourist_activity_setting_seasons',
        'tourist_activity_setting_why_chooses', 'tourist_activity_settings',
        'tourist_activity_things_to_do', 'tourist_attraction_activities',
        'tourist_attraction_faqs', 'tourist_attraction_gallery_images',
        'tourist_attraction_highlights', 'tourist_attraction_page_best_times',
        'tourist_attraction_page_faqs', 'tourist_attraction_pages',
        'tourist_attraction_setting_faqs', 'tourist_attraction_settings',
        'tourist_attractions', 'users',
    ];

    public function up(): void
    {
        foreach ($this->tables as $t) {
            if (Schema::hasTable($t) && !Schema::hasColumn($t, 'deleted_at')) {
                Schema::table($t, fn (Blueprint $table) => $table->softDeletes());
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $t) {
            if (Schema::hasTable($t) && Schema::hasColumn($t, 'deleted_at')) {
                Schema::table($t, fn (Blueprint $table) => $table->dropSoftDeletes());
            }
        }
    }
};
