<?php

/**
 * Admin Permission Configuration — Metadata Only
 *
 * ── HOW AUTO-DISCOVERY WORKS ────────────────────────────────────────────────
 * The middleware and Admin Management UI automatically discover modules by
 * scanning all registered admin.* routes. This config provides optional
 * metadata (labels, groups) and a few structural hints (aliases, fixed perms).
 *
 * ── TO ADD A COMPLETELY NEW SECTION ─────────────────────────────────────────
 * 1. Add your routes: Route::prefix('testimonials')->name('testimonials.')->group(...)
 *    inside the admin prefix group in routes/web.php.
 * 2. DONE. The new "Testimonials" module appears automatically in Admin
 *    Management with 4 permission chips (View / Add / Edit / Delete).
 *
 * Optionally add one line to _modules for a custom label or group:
 *   'testimonials' => ['label' => 'Client Testimonials', 'group' => 'Content'],
 *
 * ── TO ADD A SUB-RESOURCE ────────────────────────────────────────────────────
 * If your new routes share permissions with an existing module, add one alias:
 *   'testimonials-meta' => 'testimonials',
 * Now admin.testimonials-meta.* routes are governed by the testimonials perms.
 */

return [

    // ── Module display metadata (optional overrides) ─────────────────────────
    // Key = route 2nd-segment slug. Absent → label auto-generated, group = 'General'
    '_modules' => [

        // Packages
        'packages'           => ['label' => 'Packages & Tours',      'group' => 'Packages'],
        'categories'         => ['label' => 'Categories',             'group' => 'Packages'],

        // Destinations
        'locations'          => ['label' => 'Locations & Cities',     'group' => 'Destinations'],
        'city-pages'         => ['label' => 'Manage Cities',          'group' => 'Destinations'],

        // Experiences — kept in its own group (not "Themes & Content") so that
        // group's "Select All" button doesn't also grant Festival / Tourist
        // Attractions / Tourist Activities permissions, and vice versa. Each
        // group's "Select All" bulk-toggles every module inside it, so modules
        // that must stay independently grantable need to be in separate groups.
        'experiences'              => ['label' => 'Experiences',               'group' => 'Experiences'],
        'experience-pages'         => ['label' => 'Experience Pages',          'group' => 'Experiences'],
        'experience-subcategories' => ['label' => 'Experience Subcategories',  'group' => 'Experiences'],
        'experience-categories'    => ['label' => 'Experience Categories',     'group' => 'Experiences'],

        // Themes & Content
        'festival'           => ['label' => 'Fairs & Festivals',      'group' => 'Themes & Content'],
        'tourist-attractions'=> ['label' => 'Tourist Attractions',    'group' => 'Themes & Content'],
        'tourist-activities' => ['label' => 'Tourist Activities',     'group' => 'Themes & Content'],

        // Cars
        'cars'               => ['label' => 'Cars',                   'group' => 'Cars'],

        // Page Settings
        'page-settings'      => ['label' => 'Page Settings',          'group' => 'Page Settings'],
        'banners'            => ['label' => 'Banners',                 'group' => 'Page Settings'],
        'events'             => ['label' => 'Events',                  'group' => 'Page Settings'],
        'news'               => ['label' => 'News & Blog',             'group' => 'Page Settings'],

        // Content
        'cms-pages'          => ['label' => 'CMS Pages',              'group' => 'Content'],
        'go-explore'         => ['label' => 'Go Explore',             'group' => 'Content'],
        'summer-setting'     => ['label' => 'Summer Setting',         'group' => 'Content'],
        'offer-setting'      => ['label' => 'Offer Setting',          'group' => 'Content'],
    ],

    // ── Sub-resource aliases — merge into parent module ──────────────────────
    // Key = route 2nd-segment slug, Value = parent module slug
    // Without this, sub-resources appear as separate modules (still works, just separate).
    '_aliases' => [

        // Packages
        'spackages'              => 'packages',
        'packages-meta'          => 'packages',

        // Locations
        'regions'                => 'locations',
        'countries'              => 'locations',
        'location-setting'       => 'locations',
        'root-page-setting'      => 'locations',
        'locations-meta'         => 'locations',
        'master'                 => 'locations',

        // Festival
        'festival-state-pages'   => 'festival',

        // Tourist Attractions
        'tourist-attraction-pages' => 'tourist-attractions',

        // Tourist Activities
        'tourist-activity-pages' => 'tourist-activities',

        // CMS
        'cms-builder'    => 'cms-pages',
        'cms-page'       => 'cms-pages',
        'cms-page-meta'  => 'cms-pages',
        'page'           => 'cms-pages',
        'page-meta'      => 'cms-pages',

        // Go Explore
        'explore'        => 'go-explore',

        // Cars (all sub-resources)
        'car'                    => 'cars',
        'car-categories'         => 'cars',
        'car-routes'             => 'cars',
        'car-routes-meta'        => 'cars',
        'car-routes-page'        => 'cars',
        'car-city'               => 'cars',
        'car-city-meta'          => 'cars',
        'car-packages'           => 'cars',
        'car-packages-meta'      => 'cars',
        'car-packages-page'      => 'cars',
        'car-destinations'       => 'cars',
        'car-destinations-meta'  => 'cars',
        'car-destinations-page'  => 'cars',
        'car-rental-content'     => 'cars',
        'car-rental-road-trips'  => 'cars',

        // Page Settings (all sub-resources)
        'menus'          => 'page-settings',
        'menu-items'     => 'page-settings',
        'homepage-tabs'  => 'page-settings',
        'promotional-ads'=> 'page-settings',
        'home-about'     => 'page-settings',
        'home-sections'  => 'page-settings',
        'home-blog-items'=> 'page-settings',
        'holiday-menu'   => 'page-settings',
        'holiday-setting'=> 'page-settings',
        'tour-services'  => 'page-settings',
        'awards'         => 'page-settings',
        'teams'          => 'page-settings',
        'partners'       => 'page-settings',

        // News
        'news-meta'      => 'news',
    ],

    // ── Full-route-name overrides — for a single route whose module differs
    // from its siblings under the same 2nd-segment. Checked before the
    // segment-based _aliases above, so it can pull one route out of its
    // "natural" module. Example: admin.page-settings.car is the Car Page hub
    // screen (Cars module), unlike every other admin.page-settings.* route
    // (Home, Holiday, etc.), which are genuinely generic page settings. ──────
    '_route_overrides' => [
        'admin.page-settings.car' => 'cars',
    ],

    // ── Routes accessible to all logged-in admins (no permission check) ──────
    // Supports prefix matching: 'admin.get' catches admin.get.states, admin.get.cities, etc.
    '_public' => [
        'admin.login',
        'admin.login.post',
        'admin.logout',
        'admin.dashboard',
        'admin.admin.dashboard',
        'admin.upload-image',
        'admin.get',            // admin.get.states, admin.get.cities
        'admin.city.search',
    ],

    // ── Fixed (non-granular) permissions — explicit route bindings ───────────
    // These do NOT follow the module.action pattern. Permission name = exact key.
    '_fixed' => [

        'enquiries.view' => [
            'label'  => 'View Enquiries',
            'group'  => 'Enquiries',
            'routes' => ['admin.enquiries'],
        ],

        'admin.manage' => [
            'label'  => 'Admin Management',
            'group'  => 'System',
            'routes' => ['admin.admin-management', 'admin.admins', 'admin.activity-logs'],
        ],

        'sitemap.manage' => [
            'label'  => 'Sitemap',
            'group'  => 'System',
            'routes' => ['admin.sitemap'],
        ],
    ],

    // ── Group display order in the Admin Management permission UI ────────────
    // Groups not listed here appear at the end in discovery order.
    '_group_order' => [
        'Packages',
        'Destinations',
        'Experiences',
        'Themes & Content',
        'Cars',
        'Page Settings',
        'Content',
        'Enquiries',
        'System',
        'General',              // catch-all for auto-discovered modules with no group
    ],

];
