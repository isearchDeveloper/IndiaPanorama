<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'dashboard.view',        'label' => 'Dashboard View',           'group' => 'Dashboard'],
            ['name' => 'categories.manage',      'label' => 'Categories Manage',         'group' => 'Themes'],
            ['name' => 'themes.manage',          'label' => 'Themes Manage',             'group' => 'Themes'],
            ['name' => 'festival.manage',        'label' => 'Fairs & Festivals Manage',  'group' => 'Themes'],
            ['name' => 'tourist-attractions.manage', 'label' => 'Tourist Attractions Manage', 'group' => 'Themes'],
            ['name' => 'tourist-activities.manage', 'label' => 'Tourist Activities Manage', 'group' => 'Themes'],
            ['name' => 'locations.manage',       'label' => 'Locations Manage',          'group' => 'Locations'],
            ['name' => 'packages.manage',        'label' => 'Packages Manage',           'group' => 'Packages'],
            ['name' => 'hotels.manage',          'label' => 'Hotels Manage',             'group' => 'Hotels & Stays'],
            ['name' => 'trains.manage',          'label' => 'Trains Manage',             'group' => 'Trains'],
            ['name' => 'cars.manage',            'label' => 'Cars Manage',               'group' => 'Cars & Buses'],
            ['name' => 'buses.manage',           'label' => 'Buses Manage',              'group' => 'Cars & Buses'],
            ['name' => 'page-settings.manage',   'label' => 'Page Settings Manage',      'group' => 'Page Settings'],
            ['name' => 'banners.manage',         'label' => 'Banners Manage',            'group' => 'Page Settings'],
            ['name' => 'events.manage',          'label' => 'Events Manage',             'group' => 'Page Settings'],
            ['name' => 'news.manage',            'label' => 'News Manage',               'group' => 'Page Settings'],
            ['name' => 'city-pages.manage',      'label' => 'City Pages Manage',         'group' => 'Page Settings'],
            ['name' => 'cms-pages.manage',       'label' => 'CMS Pages Manage',          'group' => 'Explore'],
            ['name' => 'go-explore.manage',      'label' => 'Go Explore Manage',         'group' => 'Explore'],
            ['name' => 'summer-setting.manage',  'label' => 'Summer Setting Manage',     'group' => 'Explore'],
            ['name' => 'offer-setting.manage',   'label' => 'Offer Setting Manage',      'group' => 'Explore'],
            ['name' => 'enquiries.view',         'label' => 'Enquiries View',            'group' => 'Enquiries'],
            ['name' => 'bookings.view',           'label' => 'Bookings View',             'group' => 'More'],
            ['name' => 'reviews.manage',         'label' => 'Reviews Manage',            'group' => 'More'],
            ['name' => 'sitemap.manage',         'label' => 'Sitemap Manage',            'group' => 'More'],
            ['name' => 'admin.manage',           'label' => 'Admin Management',          'group' => 'Settings'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p['name']], $p);
        }
    }
}
