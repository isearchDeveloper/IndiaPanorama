<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Permission Modules Configuration
    |--------------------------------------------------------------------------
    | Each module has a key (used in permission names) and supported actions.
    | Permission names are generated as: "{action}-{key}" e.g. "view-packages"
    */
    'modules' => [
        ['label' => 'Dashboard',            'key' => 'dashboard',            'actions' => ['view']],
        ['label' => 'Admin Management',     'key' => 'admin-management',     'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Role Management',      'key' => 'role-management',      'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Permission Management','key' => 'permission-management', 'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Activity Logs',        'key' => 'activity-logs',        'actions' => ['view']],
        ['label' => 'Login History',        'key' => 'login-history',        'actions' => ['view']],
        ['label' => 'Packages',             'key' => 'packages',             'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Special Packages',     'key' => 'special-packages',     'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Deal Packages',        'key' => 'deal-packages',        'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Categories',           'key' => 'categories',           'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Countries',            'key' => 'countries',            'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Locations',            'key' => 'locations',            'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Regions',              'key' => 'regions',              'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Hotels',               'key' => 'hotels',               'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Trains',               'key' => 'trains',               'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Cars',                 'key' => 'cars',                 'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Buses',                'key' => 'buses',                'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'CMS Pages',            'key' => 'cms-pages',            'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'News',                 'key' => 'news',                 'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Enquiries',            'key' => 'enquiries',            'actions' => ['view', 'delete']],
        ['label' => 'Reviews',              'key' => 'reviews',              'actions' => ['view', 'edit', 'delete']],
        ['label' => 'Banners',              'key' => 'banners',              'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Menu Management',      'key' => 'menus',                'actions' => ['view', 'edit']],
        ['label' => 'Page Settings',        'key' => 'settings',             'actions' => ['view', 'edit']],
        ['label' => 'Awards',               'key' => 'awards',               'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Teams',                'key' => 'teams',                'actions' => ['view', 'add', 'edit', 'delete']],
        ['label' => 'Sitemap',              'key' => 'sitemap',              'actions' => ['view']],
    ],

    'all_actions' => ['view', 'add', 'edit', 'delete'],
];
