<?php

/**
 * Sitemap Configuration
 *
 * ── TO ADD A NEW SECTION ────────────────────────────────────────────────────
 * Add one entry to the `sections` array. No controller changes needed.
 *
 * Simple example (model with slug column):
 *
 *   ['model' => \App\Models\Festival::class, 'where' => [['is_active', 1]],
 *    'url' => '/festivals/{slug}', 'priority' => '0.7'],
 *
 * Relation example (URL built from related model fields):
 *
 * ── URL PLACEHOLDER SYNTAX ──────────────────────────────────────────────────
 *   {slug}              →  $record->slug
 *   {field}             →  $record->field  (any column or accessor)
 *   {relation.field}    →  $record->relation->field  (eager-loaded via 'with')
 *   {parent}            →  parent record's slug  (for children entries)
 *
 * ── SECTION KEYS ────────────────────────────────────────────────────────────
 *   model            (required) Fully-qualified model class
 *   url              (required) URL pattern with placeholders
 *   priority         (optional) 0.0–1.0, default '0.8'
 *   changefreq       (optional) default = changefreq_default below
 *   where            (optional) [['col', 'val'], ...] WHERE conditions
 *   where_null       (optional) ['col'] → WHERE col IS NULL
 *   where_not_null   (optional) ['col'] → WHERE col IS NOT NULL
 *   where_has        (optional) ['relation' => [['col', 'val']]] filter by relation existence
 *   where_doesnt_have(optional) ['relation'] filter by relation non-existence
 *   unique_url       (optional) true → deduplicate identical generated URLs
 *   with             (optional) ['relation', ...] eager load for URL building
 *   children         (optional) nested config {relation, where, url, priority}
 */

return [

    'base_url'           => env('FRONTEND_URL', 'https://www.indianpanorama.in'),
    'changefreq_default' => 'weekly',

    // ── Static pages (verified live on https://projects.isearchsolution.com/) ─
    'static' => [
        ['path' => '/',                      'priority' => '1.0', 'changefreq' => 'daily'],
        ['path' => '/tour-packages',         'priority' => '0.9'],
        ['path' => '/experiences',           'priority' => '0.9'],
        ['path' => '/activities',            'priority' => '0.9'],
        ['path' => '/car-rental',            'priority' => '0.9'],
        ['path' => '/festivals',             'priority' => '0.9'],
        ['path' => '/tourist-attractions',   'priority' => '0.9'],
        ['path' => '/about-us',              'priority' => '0.9'],
        ['path' => '/contact-us',            'priority' => '0.9'],
        ['path' => '/our-team',              'priority' => '0.8'],
        ['path' => '/privacy-policy',        'priority' => '0.6'],
        ['path' => '/terms-and-conditions',  'priority' => '0.6'],
        ['path' => '/faq',                   'priority' => '0.7'],
        ['path' => '/awards-achievements',   'priority' => '0.8'],
    ],

    // ── Dynamic sections ─────────────────────────────────────────────────────
    'sections' => [

        // ── Packages — individual (/tour-packages/{slug}) ─────────────────
        [
            'model'           => \App\Models\Package::class,
            'where'           => [['is_active', 1]],
            'url'             => '/tour-packages/{slug}',
            'priority'        => '0.8',
            'changefreq'      => 'weekly',
        ],

        // ── Packages — state-wise listing (/{state}/tour-packages) ────────
        // Queries Location records that have active packages; deduplicates same-state URLs.
        [
            'model'      => \App\Models\Location::class,
            'where_has'  => ['packages' => [['is_active', 1]]],
            'with'       => ['state'],
            'url'        => '/{state.city_guide_slug}/tour-packages',
            'unique_url' => true,
            'priority'   => '0.8',
            'changefreq' => 'weekly',
        ],

        // ── Packages — city-wise listing (/{state}/{city}/tour-packages) ──
        [
            'model'      => \App\Models\Location::class,
            'where_has'  => ['packages' => [['is_active', 1]]],
            'with'       => ['state'],
            'url'        => '/{state.city_guide_slug}/{city_guide_slug}/tour-packages',
            'priority'   => '0.7',
            'changefreq' => 'weekly',
        ],

        // ── Festivals (/festivals/{slug}) ─────────────────────────────────
        [
            'model'    => \App\Models\Festival::class,
            'where'    => [['is_active', 1]],
            'url'      => '/festivals/{slug}',
            'priority' => '0.8',
        ],

        // ── Tourist Attractions (/tourist-attractions/{slug}) ─────────────
        [
            'model'           => \App\Models\TouristAttraction::class,
            'where'           => [['is_active', 1]],
            'url'             => '/tourist-attractions/{slug}',
            'priority'        => '0.7',
        ],

        // ── Car Rental — Routes (/car-rental/{slug}) ──────────────────────
        [
            'model'           => \App\Models\CarRoute::class,
            'where'           => [['is_active', 1]],
            'url'             => '/car-rental/{slug}',
            'priority'        => '0.8',
        ],

        // ── Car Rental — Cities (/car-rental/{slug}) ──────────────────────
        [
            'model'           => \App\Models\CarCity::class,
            'where'           => [['is_active', 1]],
            'url'             => '/car-rental/{slug}',
            'priority'        => '0.8',
        ],

        // ── Activities — State-level (/{state}/activities) ────────────────
        [
            'model'      => \App\Models\TouristActivityPage::class,
            'where'      => [['is_active', 1]],
            'where_null' => ['location_id'],
            'with'       => ['state'],
            'url'        => '/{state.city_guide_slug}/activities',
            'priority'   => '0.7',
        ],

        // ── Activities — City-level (/{state}/{city}/activities) ──────────
        // city-level records have state_id=NULL; state resolved via location.state
        [
            'model'          => \App\Models\TouristActivityPage::class,
            'where'          => [['is_active', 1]],
            'where_not_null' => ['location_id'],
            'with'           => ['location.state'],
            'url'            => '/{location.state.city_guide_slug}/{location.city_guide_slug}/activities',
            'priority'       => '0.8',
        ],

        // ─────────────────────────────────────────────────────────────────
        // ── Uncomment when frontend pages are live ────────────────────────
        // ─────────────────────────────────────────────────────────────────

        // Themes / Experiences (/experiences/{slug}) — uncomment when live
        // [
        //     'model'    => \App\Models\Theme::class,
        //     'where'    => [['is_active', 1]],
        //     'url'      => '/experiences/{slug}',
        //     'priority' => '0.7',
        // ],

        // Tourist Activities (/tourist-activities/{slug}) — uncomment when live
        // [
        //     'model'           => \App\Models\TouristActivity::class,
        //     'where'           => [['is_active', 1]],
        //     'url'             => '/tourist-activities/{slug}',
        //     'priority'        => '0.7',
        // ],

        // News / Blog (/blog/{slug}) — uncomment when live
        // [
        //     'model'    => \App\Models\News::class,
        //     'where'    => [['is_active', 1]],
        //     'url'      => '/blog/{slug}',
        //     'priority' => '0.6',
        // ],

    ],

];
