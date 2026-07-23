<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seed the core pages required by SettingsController.
 *
 * Page ID mapping:
 *   2  = Luxury Train
 *   3  = India Tour Packages
 *   5  = Luxury Hotel
 *   6  = Car Rental
 *   7  = Customized Holiday
 *   8  = Home Page
 *   9  = Bus / Transport
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $pages = [
            [
                'id'               => 2,
                'slug'             => 'luxury-train',
                'title'            => 'Luxury Train',
                'description'      => '',
                'banner_image'     => null,
                'banner_image_alt' => null,
                'faq_title'        => null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'id'               => 3,
                'slug'             => 'india-tour-packages',
                'title'            => 'India Tour Packages',
                'description'      => '',
                'banner_image'     => null,
                'banner_image_alt' => null,
                'faq_title'        => null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'id'               => 5,
                'slug'             => 'luxury-hotels',
                'title'            => 'Luxury Hotels',
                'description'      => '',
                'banner_image'     => null,
                'banner_image_alt' => null,
                'faq_title'        => null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'id'               => 6,
                'slug'             => 'car-rental',
                'title'            => 'Car Rental',
                'description'      => '',
                'banner_image'     => null,
                'banner_image_alt' => null,
                'faq_title'        => null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'id'               => 7,
                'slug'             => 'customized-holiday',
                'title'            => 'Customized Holiday',
                'description'      => '',
                'banner_image'     => null,
                'banner_image_alt' => null,
                'faq_title'        => null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'id'               => 8,
                'slug'             => 'home',
                'title'            => 'Home',
                'description'      => '',
                'banner_image'     => null,
                'banner_image_alt' => null,
                'faq_title'        => null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'id'               => 9,
                'slug'             => 'bus-transport',
                'title'            => 'Bus & Transport',
                'description'      => '',
                'banner_image'     => null,
                'banner_image_alt' => null,
                'faq_title'        => null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
        ];

        foreach ($pages as $page) {
            DB::table('pages')->insertOrIgnore($page);
        }
    }

    public function down(): void
    {
        DB::table('pages')->whereIn('id', [2, 3, 5, 6, 7, 8, 9])->delete();
    }
};
