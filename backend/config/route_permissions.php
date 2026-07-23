<?php

/*
|--------------------------------------------------------------------------
| Route-to-Permission Module Map
|--------------------------------------------------------------------------
| Maps the stripped route name prefix (after removing leading "admin.")
| to a Spatie permission module key (as defined in config/permissions.php).
|
| The AuthorizeAdminRoute middleware finds the LONGEST matching prefix and
| then derives the action (view/add/edit/delete) from the HTTP method + name.
|
| Routes with NO matching prefix pass through (still protected by isadmin).
*/

return [
    // Dashboard
    'dashboard'              => 'dashboard',

    // Admin System Management
    'admins'                 => 'admin-management',
    'roles'                  => 'role-management',
    'permissions'            => 'permission-management',
    'activity-logs'          => 'activity-logs',
    'login-history'          => 'login-history',

    // Categories
    'categories'             => 'categories',

    // Geography
    'countries'              => 'countries',
    'locations'              => 'locations',
    'locations-meta'         => 'locations',
    'regions'                => 'regions',

    // Packages
    'packages'               => 'packages',
    'packages-meta'          => 'packages',
    'spackages'              => 'special-packages',
    'banners'                => 'banners',

    // Hotels
    'hotels'                 => 'hotels',
    'hotelscities'           => 'hotels',
    'hotel'                  => 'hotels',
    'hotel-meta'             => 'hotels',
    'hotel-city-meta'        => 'hotels',
    'hotels-cities-meta'     => 'hotels',

    // Trains
    'trains'                 => 'trains',
    'train-tours'            => 'trains',
    'train'                  => 'trains',
    'train-meta'             => 'trains',
    'train-tour-meta'        => 'trains',

    // Cars
    'cars'                   => 'cars',
    'car-categories'         => 'cars',
    'car-routes'             => 'cars',
    'car-routes-meta'        => 'cars',
    'car-routes-page'        => 'cars',
    'car-city'               => 'cars',
    'car-city-meta'          => 'cars',
    'car-city-page'          => 'cars',
    'car'                    => 'cars',

    // Buses
    'bus'                    => 'buses',
    'bus-categories'         => 'buses',
    'bus-routes'             => 'buses',
    'bus-routes-meta'        => 'buses',
    'bus-routes-page'        => 'buses',

    // CMS & Content
    'cms-page'               => 'cms-pages',
    'cms-page-meta'          => 'cms-pages',
    'news'                   => 'news',
    'news-meta'              => 'news',

    // Page & Menu Settings
    'menus'                  => 'menus',
    'menu'                   => 'menus',
    'page-settings'          => 'settings',
    'homepage-tabs'          => 'settings',

    // Other
    'awards'                 => 'awards',
    'teams'                  => 'teams',
    'sitemap'                => 'sitemap',

    // Enquiries & Reviews
    'enquiries'              => 'enquiries',
    'reviews'                => 'reviews',
];
