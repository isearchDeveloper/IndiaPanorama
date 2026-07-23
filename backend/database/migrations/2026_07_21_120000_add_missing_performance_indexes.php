<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds indexes on foreign-key-shaped columns (*_id), status/visibility flags
 * (is_active, is_popular, is_visible, is_top_trending, status), slug lookups,
 * and sort_order — none of these had a dedicated index before, so every admin
 * list filter, relation load, and ORDER BY sort_order was a full table scan.
 * Columns already covered by an existing index (including as part of a
 * composite/polymorphic index, e.g. image_licenses' [licensable_type,
 * licensable_id]) are intentionally skipped.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('awards', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->index('sort_order');
            $table->index('is_active');
        });

        Schema::table('car_amenities', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_categories', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('car_city', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('is_popular');
        });

        Schema::table('car_city_benefits', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_city_details', function (Blueprint $table) {
            $table->index('city_id');
            $table->index('car_id');
        });

        Schema::table('car_city_faqs', function (Blueprint $table) {
            $table->index('city_id');
        });

        Schema::table('car_city_features', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_city_gallery_images', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_city_highlights', function (Blueprint $table) {
            $table->index('city_id');
            $table->index('sort_order');
        });

        Schema::table('car_city_meta_data', function (Blueprint $table) {
            $table->index('city_id');
        });

        Schema::table('car_city_page_details', function (Blueprint $table) {
            $table->index('city_id');
        });

        Schema::table('car_city_why_choose_stats', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_destination_faqs', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_destination_highlights', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_destinations', function (Blueprint $table) {
            $table->index('sort_order');
            $table->index('is_active');
            $table->index('is_popular');
        });

        Schema::table('car_gallery_images', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_highlight_tags', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_package_amenities', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_package_faqs', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_package_stops', function (Blueprint $table) {
            $table->index('state_id');
            $table->index('location_id');
            $table->index('sort_order');
        });

        Schema::table('car_packages', function (Blueprint $table) {
            $table->index('sort_order');
            $table->index('is_active');
            $table->index('is_popular');
        });

        Schema::table('car_rental_amenities', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_rental_benefits', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_rental_checklist_items', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_rental_features', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_rental_gallery_images', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_rental_road_trips', function (Blueprint $table) {
            $table->index('sort_order');
            $table->index('is_active');
        });

        Schema::table('car_rental_why_choose_stats', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_route_highlights', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('car_routes', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('is_popular');
        });

        Schema::table('car_routes_details', function (Blueprint $table) {
            $table->index('route_id');
            $table->index('car_id');
        });

        Schema::table('car_routes_faqs', function (Blueprint $table) {
            $table->index('route_id');
        });

        Schema::table('car_routes_meta_data', function (Blueprint $table) {
            $table->index('route_id');
        });

        Schema::table('car_routes_page_details', function (Blueprint $table) {
            $table->index('route_id');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->index('category_id');
            $table->index('is_active');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('cms_page_meta_data', function (Blueprint $table) {
            $table->index('page_id');
        });

        Schema::table('cms_page_sections', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('countries', function (Blueprint $table) {
            $table->index('continent_id');
        });

        Schema::table('country_faqs', function (Blueprint $table) {
            $table->index('country_id');
        });

        Schema::table('country_meta_data', function (Blueprint $table) {
            $table->index('country_id');
        });

        Schema::table('experience_categories', function (Blueprint $table) {
            $table->index('sort_order');
            $table->index('is_active');
        });

        Schema::table('experience_category_faqs', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('experience_category_perfect_fors', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('experience_category_popular_cities', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('experience_category_quick_infos', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('experience_faqs', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('experience_gallery_images', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('experience_highlights', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('experience_page_activities', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('experience_page_faqs', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('experience_page_highlights', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('experience_pages', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('sort_order');
        });

        Schema::table('experience_quick_infos', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('experience_setting_best_times', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('experience_setting_faqs', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('experience_setting_why_choose_items', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('experience_settings', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('experience_subcategories', function (Blueprint $table) {
            $table->index('sort_order');
            $table->index('is_active');
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->index('sort_order');
            $table->index('is_active');
            $table->index('is_popular');
        });

        Schema::table('festival_faqs', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('festival_highlights', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('festival_how_to_reach', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('festival_key_experiences', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('festival_places', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('festival_setting_faqs', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('festival_setting_highlights', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('festival_setting_why_experiences', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('festival_settings', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('festival_state_page_faqs', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('festival_state_page_why_visits', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('festival_state_pages', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('sort_order');
        });

        Schema::table('festival_stats', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('festival_why_visits', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('festivals', function (Blueprint $table) {
            $table->index('sort_order');
            $table->index('is_active');
        });

        Schema::table('holiday_menu_settings', function (Blueprint $table) {
            $table->index('is_visible');
        });

        Schema::table('holiday_settings', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('home_blog_items', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('image_licenses', function (Blueprint $table) {
            $table->index('account_id');
        });

        Schema::table('location_details', function (Blueprint $table) {
            $table->index('location_id');
        });

        Schema::table('location_faqs', function (Blueprint $table) {
            $table->index('location_id');
        });

        Schema::table('location_meta_data', function (Blueprint $table) {
            $table->index('location_id');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->index('region_id');
            $table->index('slug');
            $table->index('is_top_trending');
            $table->index('sort_order');
        });

        Schema::table('manage_cities', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('is_popular');
            $table->index('sort_order');
        });

        Schema::table('manage_city_faqs', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('manage_city_how_to_reach', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('manage_city_quick_facts', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('manage_city_things_to_do', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('manage_city_top_places', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->index('linked_id');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('package_faqs', function (Blueprint $table) {
            $table->index('package_id');
        });

        Schema::table('package_images', function (Blueprint $table) {
            $table->index('account_id');
            $table->index('sort_order');
        });

        Schema::table('package_itinerary', function (Blueprint $table) {
            $table->index('package_id');
        });

        Schema::table('package_locations', function (Blueprint $table) {
            $table->index('package_id');
            $table->index('location_id');
        });

        Schema::table('package_meta_data', function (Blueprint $table) {
            $table->index('package_id');
        });

        Schema::table('package_source_locations', function (Blueprint $table) {
            $table->index('package_id');
            $table->index('location_id');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->index('source_location_id');
            $table->index('account_id');
            $table->index('is_top_trending');
            $table->index('is_active');
        });

        Schema::table('packages_categories', function (Blueprint $table) {
            $table->index('package_id');
            $table->index('category_id');
        });

        Schema::table('packages_group_dates', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('page_faqs', function (Blueprint $table) {
            $table->index('page_id');
        });

        Schema::table('page_meta_data', function (Blueprint $table) {
            $table->index('page_id');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->index('slug');
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->index('sort_order');
            $table->index('is_active');
        });

        Schema::table('region_faqs', function (Blueprint $table) {
            $table->index('region_id');
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->index('is_popular');
        });

        Schema::table('regions_details', function (Blueprint $table) {
            $table->index('region_id');
        });

        Schema::table('regions_meta_data', function (Blueprint $table) {
            $table->index('region_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('package_id');
            $table->index('hotel_id');
            $table->index('car_id');
        });

        Schema::table('state_details', function (Blueprint $table) {
            $table->index('state_id');
        });

        Schema::table('states', function (Blueprint $table) {
            $table->index('region_id');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->index('dep_id');
            $table->index('is_active');
        });

        Schema::table('theme_location_meta_data', function (Blueprint $table) {
            $table->index('theme_id');
            $table->index('location_id');
        });

        Schema::table('themes_faq', function (Blueprint $table) {
            $table->index('theme_id');
        });

        Schema::table('themes_meta_data', function (Blueprint $table) {
            $table->index('theme_id');
        });

        Schema::table('tourist_activities', function (Blueprint $table) {
            $table->index('sort_order');
            $table->index('is_active');
            $table->index('is_popular');
        });

        Schema::table('tourist_activity_experiences', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_activity_faqs', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_activity_gallery_images', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_activity_itinerary_steps', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_activity_page_experiences', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_activity_page_faqs', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_activity_page_things_to_do', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_activity_page_waterfalls', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_activity_pages', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('sort_order');
        });

        Schema::table('tourist_activity_setting_faqs', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_activity_setting_highlights', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_activity_setting_perfect_fors', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_activity_setting_seasons', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_activity_setting_why_chooses', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_activity_settings', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('tourist_activity_things_to_do', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_attraction_activities', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_attraction_faqs', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_attraction_gallery_images', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_attraction_highlights', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_attraction_page_best_times', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_attraction_page_faqs', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_attraction_pages', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('is_popular');
            $table->index('sort_order');
        });

        Schema::table('tourist_attraction_setting_faqs', function (Blueprint $table) {
            $table->index('sort_order');
        });

        Schema::table('tourist_attraction_settings', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('tourist_attractions', function (Blueprint $table) {
            $table->index('sort_order');
            $table->index('is_active');
            $table->index('is_popular');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('status');
        });

    }

    public function down(): void
    {
        Schema::table('awards', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('car_amenities', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_categories', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('car_city', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_popular']);
        });

        Schema::table('car_city_benefits', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_city_details', function (Blueprint $table) {
            $table->dropIndex(['city_id']);
            $table->dropIndex(['car_id']);
        });

        Schema::table('car_city_faqs', function (Blueprint $table) {
            $table->dropIndex(['city_id']);
        });

        Schema::table('car_city_features', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_city_gallery_images', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_city_highlights', function (Blueprint $table) {
            $table->dropIndex(['city_id']);
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_city_meta_data', function (Blueprint $table) {
            $table->dropIndex(['city_id']);
        });

        Schema::table('car_city_page_details', function (Blueprint $table) {
            $table->dropIndex(['city_id']);
        });

        Schema::table('car_city_why_choose_stats', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_destination_faqs', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_destination_highlights', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_destinations', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_popular']);
        });

        Schema::table('car_gallery_images', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_highlight_tags', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_package_amenities', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_package_faqs', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_package_stops', function (Blueprint $table) {
            $table->dropIndex(['state_id']);
            $table->dropIndex(['location_id']);
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_packages', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_popular']);
        });

        Schema::table('car_rental_amenities', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_rental_benefits', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_rental_checklist_items', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_rental_features', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_rental_gallery_images', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_rental_road_trips', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('car_rental_why_choose_stats', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_route_highlights', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('car_routes', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_popular']);
        });

        Schema::table('car_routes_details', function (Blueprint $table) {
            $table->dropIndex(['route_id']);
            $table->dropIndex(['car_id']);
        });

        Schema::table('car_routes_faqs', function (Blueprint $table) {
            $table->dropIndex(['route_id']);
        });

        Schema::table('car_routes_meta_data', function (Blueprint $table) {
            $table->dropIndex(['route_id']);
        });

        Schema::table('car_routes_page_details', function (Blueprint $table) {
            $table->dropIndex(['route_id']);
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('cms_page_meta_data', function (Blueprint $table) {
            $table->dropIndex(['page_id']);
        });

        Schema::table('cms_page_sections', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('countries', function (Blueprint $table) {
            $table->dropIndex(['continent_id']);
        });

        Schema::table('country_faqs', function (Blueprint $table) {
            $table->dropIndex(['country_id']);
        });

        Schema::table('country_meta_data', function (Blueprint $table) {
            $table->dropIndex(['country_id']);
        });

        Schema::table('experience_categories', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('experience_category_faqs', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('experience_category_perfect_fors', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('experience_category_popular_cities', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('experience_category_quick_infos', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('experience_faqs', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('experience_gallery_images', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('experience_highlights', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('experience_page_activities', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('experience_page_faqs', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('experience_page_highlights', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('experience_pages', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['sort_order']);
        });

        Schema::table('experience_quick_infos', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('experience_setting_best_times', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('experience_setting_faqs', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('experience_setting_why_choose_items', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('experience_settings', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('experience_subcategories', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_popular']);
        });

        Schema::table('festival_faqs', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('festival_highlights', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('festival_how_to_reach', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('festival_key_experiences', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('festival_places', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('festival_setting_faqs', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('festival_setting_highlights', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('festival_setting_why_experiences', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('festival_settings', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('festival_state_page_faqs', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('festival_state_page_why_visits', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('festival_state_pages', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['sort_order']);
        });

        Schema::table('festival_stats', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('festival_why_visits', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('festivals', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('holiday_menu_settings', function (Blueprint $table) {
            $table->dropIndex(['is_visible']);
        });

        Schema::table('holiday_settings', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('home_blog_items', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('image_licenses', function (Blueprint $table) {
            $table->dropIndex(['account_id']);
        });

        Schema::table('location_details', function (Blueprint $table) {
            $table->dropIndex(['location_id']);
        });

        Schema::table('location_faqs', function (Blueprint $table) {
            $table->dropIndex(['location_id']);
        });

        Schema::table('location_meta_data', function (Blueprint $table) {
            $table->dropIndex(['location_id']);
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropIndex(['region_id']);
            $table->dropIndex(['slug']);
            $table->dropIndex(['is_top_trending']);
            $table->dropIndex(['sort_order']);
        });

        Schema::table('manage_cities', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_popular']);
            $table->dropIndex(['sort_order']);
        });

        Schema::table('manage_city_faqs', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('manage_city_how_to_reach', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('manage_city_quick_facts', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('manage_city_things_to_do', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('manage_city_top_places', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex(['linked_id']);
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('package_faqs', function (Blueprint $table) {
            $table->dropIndex(['package_id']);
        });

        Schema::table('package_images', function (Blueprint $table) {
            $table->dropIndex(['account_id']);
            $table->dropIndex(['sort_order']);
        });

        Schema::table('package_itinerary', function (Blueprint $table) {
            $table->dropIndex(['package_id']);
        });

        Schema::table('package_locations', function (Blueprint $table) {
            $table->dropIndex(['package_id']);
            $table->dropIndex(['location_id']);
        });

        Schema::table('package_meta_data', function (Blueprint $table) {
            $table->dropIndex(['package_id']);
        });

        Schema::table('package_source_locations', function (Blueprint $table) {
            $table->dropIndex(['package_id']);
            $table->dropIndex(['location_id']);
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropIndex(['source_location_id']);
            $table->dropIndex(['account_id']);
            $table->dropIndex(['is_top_trending']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('packages_categories', function (Blueprint $table) {
            $table->dropIndex(['package_id']);
            $table->dropIndex(['category_id']);
        });

        Schema::table('packages_group_dates', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('page_faqs', function (Blueprint $table) {
            $table->dropIndex(['page_id']);
        });

        Schema::table('page_meta_data', function (Blueprint $table) {
            $table->dropIndex(['page_id']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex(['slug']);
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('region_faqs', function (Blueprint $table) {
            $table->dropIndex(['region_id']);
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->dropIndex(['is_popular']);
        });

        Schema::table('regions_details', function (Blueprint $table) {
            $table->dropIndex(['region_id']);
        });

        Schema::table('regions_meta_data', function (Blueprint $table) {
            $table->dropIndex(['region_id']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['package_id']);
            $table->dropIndex(['hotel_id']);
            $table->dropIndex(['car_id']);
        });

        Schema::table('state_details', function (Blueprint $table) {
            $table->dropIndex(['state_id']);
        });

        Schema::table('states', function (Blueprint $table) {
            $table->dropIndex(['region_id']);
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropIndex(['dep_id']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('theme_location_meta_data', function (Blueprint $table) {
            $table->dropIndex(['theme_id']);
            $table->dropIndex(['location_id']);
        });

        Schema::table('themes_faq', function (Blueprint $table) {
            $table->dropIndex(['theme_id']);
        });

        Schema::table('themes_meta_data', function (Blueprint $table) {
            $table->dropIndex(['theme_id']);
        });

        Schema::table('tourist_activities', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_popular']);
        });

        Schema::table('tourist_activity_experiences', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_activity_faqs', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_activity_gallery_images', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_activity_itinerary_steps', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_activity_page_experiences', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_activity_page_faqs', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_activity_page_things_to_do', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_activity_page_waterfalls', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_activity_pages', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_activity_setting_faqs', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_activity_setting_highlights', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_activity_setting_perfect_fors', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_activity_setting_seasons', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_activity_setting_why_chooses', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_activity_settings', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('tourist_activity_things_to_do', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_attraction_activities', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_attraction_faqs', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_attraction_gallery_images', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_attraction_highlights', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_attraction_page_best_times', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_attraction_page_faqs', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_attraction_pages', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_popular']);
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_attraction_setting_faqs', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('tourist_attraction_settings', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('tourist_attractions', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_popular']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['status']);
        });

    }
};
