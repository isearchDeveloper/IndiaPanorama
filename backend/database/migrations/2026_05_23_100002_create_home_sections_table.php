<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Homepage CMS — Section Control Table
 *
 * Each row controls one named section on the public homepage.
 * Sections are keyed (slug-like) for reliable code references.
 * The `extra_data` JSON column holds section-specific overrides
 * (e.g. package count, blog count, promo image path) without
 * requiring extra columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key', 60)->unique()->comment('Machine identifier e.g. hero_slider');
            $table->string('label', 120)->comment('Human-readable admin label');
            $table->string('title', 255)->nullable()->comment('Section heading shown on frontend');
            $table->string('subtitle', 255)->nullable()->comment('Section sub-heading');
            $table->text('description')->nullable()->comment('Body copy / intro text');
            $table->string('button_text', 100)->nullable();
            $table->string('button_url', 500)->nullable();
            $table->string('image', 500)->nullable()->comment('S3 key for optional section image');
            $table->string('image_alt', 255)->nullable();
            $table->boolean('is_visible')->default(true)->comment('Toggle section visibility on homepage');
            $table->unsignedTinyInteger('sort_order')->default(0)->comment('Display order on homepage');
            $table->json('extra_data')->nullable()->comment('Section-specific flexible config (JSON)');
            $table->timestamps();

            $table->index(['is_visible', 'sort_order'], 'idx_hs_visible_sort');
        });

        // Seed default sections in display order
        $now = now();
        DB::table('home_sections')->insert([
            [
                'section_key' => 'hero_slider',
                'label'       => 'Hero Slider / Banner',
                'title'       => 'Discover the Magic of India',
                'subtitle'    => 'Unforgettable Journeys Across Every Corner of India',
                'description' => "Explore India's rich heritage, stunning landscapes and vibrant culture with Indian Panorama.",
                'button_text' => 'Explore Packages',
                'button_url'  => '/packages',
                'image'       => null,
                'image_alt'   => null,
                'is_visible'  => true,
                'sort_order'  => 1,
                'extra_data'  => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'section_key' => 'india_tours',
                'label'       => 'India Tour Packages',
                'title'       => 'Explore India with Us',
                'subtitle'    => 'Handcrafted Tours for Every Traveller',
                'description' => 'From the snowy peaks of Himalayas to the beaches of Goa — we have a package for every dream.',
                'button_text' => 'View All Packages',
                'button_url'  => '/india-tour-packages',
                'image'       => null,
                'image_alt'   => null,
                'is_visible'  => true,
                'sort_order'  => 2,
                'extra_data'  => json_encode(['package_count' => 8]),
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'section_key' => 'customized_tours',
                'label'       => 'Customized India Tours',
                'title'       => 'Your Dream Trip, Customized',
                'subtitle'    => 'We Plan. You Explore.',
                'description' => "Tell us your preferences and we'll craft a tailor-made itinerary just for you.",
                'button_text' => 'Start Planning',
                'button_url'  => '/customized-tour',
                'image'       => null,
                'image_alt'   => null,
                'is_visible'  => true,
                'sort_order'  => 3,
                'extra_data'  => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'section_key' => 'about_intro',
                'label'       => 'About / Intro Section',
                'title'       => 'About Indian Panorama',
                'subtitle'    => 'Your Trusted Travel Partner Since 1983',
                'description' => 'Indian Panorama is a premier tour operator with over four decades of experience crafting memorable journeys across India. Our passion is turning your travel dreams into reality.',
                'button_text' => 'Learn More About Us',
                'button_url'  => '/about-us',
                'image'       => null,
                'image_alt'   => 'About Indian Panorama',
                'is_visible'  => true,
                'sort_order'  => 4,
                'extra_data'  => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'section_key' => 'why_choose',
                'label'       => 'Why Choose Indian Panorama',
                'title'       => 'Why Indian Panorama?',
                'subtitle'    => 'Trusted by Thousands of Happy Travellers',
                'description' => 'We offer unmatched expertise, personalised service and the best value for your travel rupee.',
                'button_text' => null,
                'button_url'  => null,
                'image'       => null,
                'image_alt'   => null,
                'is_visible'  => true,
                'sort_order'  => 5,
                'extra_data'  => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'section_key' => 'home_locations',
                'label'       => 'Explore by Destination',
                'title'       => 'Explore India by Region',
                'subtitle'    => 'Pick Your Perfect Destination',
                'description' => 'Browse our curated selection of destinations across North, South, East and West India.',
                'button_text' => 'View All Destinations',
                'button_url'  => '/destinations',
                'image'       => null,
                'image_alt'   => null,
                'is_visible'  => true,
                'sort_order'  => 6,
                'extra_data'  => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'section_key' => 'car_rental',
                'label'       => 'Car Rental Section',
                'title'       => 'Hassle-free Car Rentals Across India',
                'subtitle'    => 'Comfortable, Safe & Affordable',
                'description' => 'Self-drive or chauffeur-driven — explore India on your terms with our wide fleet.',
                'button_text' => 'Book a Car',
                'button_url'  => '/car-rental',
                'image'       => null,
                'image_alt'   => 'Car rental India',
                'is_visible'  => true,
                'sort_order'  => 7,
                'extra_data'  => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'section_key' => 'inquiry_form',
                'label'       => 'Inquiry / Contact Form',
                'title'       => 'Plan Your Perfect Trip',
                'subtitle'    => 'Get a Free Customised Itinerary',
                'description' => 'Share your travel dates and preferences — our experts will get back within 24 hours.',
                'button_text' => 'Send Enquiry',
                'button_url'  => null,
                'image'       => null,
                'image_alt'   => null,
                'is_visible'  => true,
                'sort_order'  => 8,
                'extra_data'  => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'section_key' => 'latest_blogs',
                'label'       => 'Latest Blogs / News',
                'title'       => 'Travel Tips & Inspiration',
                'subtitle'    => 'Stories from the Road',
                'description' => 'Read our latest travel guides, tips and destination spotlights to fuel your wanderlust.',
                'button_text' => 'Read All Articles',
                'button_url'  => '/travel-blog',
                'image'       => null,
                'image_alt'   => null,
                'is_visible'  => true,
                'sort_order'  => 9,
                'extra_data'  => json_encode(['blog_count' => 3]),
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'section_key' => 'promotional_banner',
                'label'       => 'Promotional Banner / CTA',
                'title'       => 'Special Deals & Offers',
                'subtitle'    => 'Limited Time — Book Now & Save',
                'description' => "Exclusive offers on our most popular tour packages. Don't miss out!",
                'button_text' => 'See All Offers',
                'button_url'  => '/special-offers',
                'image'       => null,
                'image_alt'   => 'Special travel deals India',
                'is_visible'  => true,
                'sort_order'  => 10,
                'extra_data'  => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'section_key' => 'tour_services',
                'label'       => 'Tour Services Icons',
                'title'       => 'Our Tour Services',
                'subtitle'    => 'Everything You Need for a Perfect Holiday',
                'description' => 'From accommodation to transfers — we handle every detail so you can just enjoy.',
                'button_text' => null,
                'button_url'  => null,
                'image'       => null,
                'image_alt'   => null,
                'is_visible'  => true,
                'sort_order'  => 11,
                'extra_data'  => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('home_sections');
    }
};
