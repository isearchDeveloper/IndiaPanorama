<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\CmsPageController;
use App\Http\Controllers\Api\LocationController as ApiLocationController;
use App\Http\Controllers\Api\HeaderMenuController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\BuilderPageController;
use App\Http\Controllers\Api\HolidayController;
use App\Http\Controllers\Api\PartnerController as ApiPartnerController;
use App\Http\Controllers\Api\EnquiryController;

// ═══════════════════════════════════════════════════════════════════════════
// Versioned under /api/v1/*
// All endpoints require the X-Public-Token header (see PublicApiToken
// middleware, aliased as "public.token") — direct/anonymous access is
// rejected with 401. The Razorpay webhook is the one exception: Razorpay
// calls it directly and verifies authenticity via its own HMAC signature,
// so it cannot send our custom header and stays outside this gate.
// ═══════════════════════════════════════════════════════════════════════════

Route::prefix('v1')->group(function () {

    Route::middleware('public.token')->group(function () {

        // ── Header Menu ───────────────────────────────────────────────────────
        Route::get('header-menu', [HeaderMenuController::class, 'index']);

        // ── Home Page ─────────────────────────────────────────────────────────
        Route::get('home', [HomeController::class, 'index']);

        // ── Cascade Dropdown APIs ─────────────────────────────────────────────
        Route::get('countries',           [ApiLocationController::class, 'countries']);
        Route::get('states',              [ApiLocationController::class, 'states']);
        Route::get('cities',              [ApiLocationController::class, 'cities']);
        Route::get('states-with-cities',  [ApiLocationController::class, 'statesWithCities']);

        // ── CMS Pages ─────────────────────────────────────────────────────────
        Route::get('cms/page/details/',        [CmsPageController::class, 'pageDetails']);
        Route::get('news/page/list',           [CmsPageController::class, 'newsList']);
        Route::get('news/{slug}',              [CmsPageController::class, 'newsDetails']);
        Route::get('cms/team-list/',           [CmsPageController::class, 'team_list']);


        // ── CMS Builder Pages ─────────────────────────────────────────────────────
        Route::get('page/setting/{slug}',      [BuilderPageController::class, 'show']);

        // ── Page Settings ─────────────────────────────────────────────────────
        Route::get('page/settings/menu',                 [SettingsController::class, 'menu']);
        Route::get('page/settings/home',                 [SettingsController::class, 'home']);
        Route::get('page/settings/car',                  [SettingsController::class, 'car']);
        Route::get('page/settings/holidays',             [HolidayController::class, 'index']);
        Route::get('page/settings/holidays/{slug}',      [HolidayController::class, 'show']);

        // ── Partners ──────────────────────────────────────────────────
        Route::get('partners', [ApiPartnerController::class, 'index']);

        // ── Festivals landing page ───────────────────────────────────────────
        Route::get('page/settings/festivals', [\App\Http\Controllers\Api\FestivalSettingController::class, 'show']);
        Route::get('page/settings/festivals/state/{slug}', [\App\Http\Controllers\Api\FestivalController::class, 'statePage']);
        Route::get('page/settings/festivals/{slug}', [\App\Http\Controllers\Api\FestivalController::class, 'show']);

        // ── Experiences (Category -> Subcategory -> Experience item) ─────────
        // Hub           /experiences                                   → index
        //   (kept alive at its original path too — the frontend's experiencesService.ts
        //   already calls GET page/settings/experiences and must keep working unchanged)
        // Category page /experiences/{category}                        → category
        // Subcategory   /experiences/{subcategory}[-in-{state}]         → subcategory (?state= query)
        // Detail        /{state}/{city}/{slug}-experience                → detail (slug w/o suffix)
        // State hub     /{state}/experiences                            → statePage
        // City hub      /{state}/{city}/experiences                     → cityPage
        // Static segments (settings/category/subcategory/detail/state) must be registered
        // BEFORE the generic {stateSlug}/{citySlug} wildcard below, or it would swallow them.
        Route::get('page/settings/experiences', [\App\Http\Controllers\Api\ExperienceController::class, 'index']);
        Route::get('experiences/settings', [\App\Http\Controllers\Api\ExperienceController::class, 'index']);
        Route::get('experiences/category/{slug}', [\App\Http\Controllers\Api\ExperienceController::class, 'category']);
        Route::get('experiences/subcategory/{slug}', [\App\Http\Controllers\Api\ExperienceController::class, 'subcategory']);
        Route::get('experiences/detail/{slug}', [\App\Http\Controllers\Api\ExperienceController::class, 'detail']);
        Route::get('experiences/state/{slug}', [\App\Http\Controllers\Api\ExperienceController::class, 'statePage']);
        Route::get('experiences/{stateSlug}/{citySlug}', [\App\Http\Controllers\Api\ExperienceController::class, 'cityPage']);

        // ── Packages ──────────────────────────────────────────────────────────
        Route::get('packages/popular',           [PackageController::class, 'popularPackages']);
        Route::get('packages/special',           [PackageController::class, 'specialPackages']);
        Route::get('packages/group-tour',        [PackageController::class, 'GroupTourPackages']);
        Route::get('packages/group-tour/{slug}', [PackageController::class, 'groupTourDetails']);
        Route::get('packages/top-trending',      [PackageController::class, 'topTrendingCountryPackages']);
        Route::get('packages/discover-india',    [PackageController::class, 'discoverIndiaTourPackages']);
        Route::get('package/{slug}',             [PackageController::class, 'packageDetails']);
        Route::get('packages/city/{slug}',       [PackageController::class, 'packagesResolver']);
        Route::get('packages/state/{slug}/city/{citySlug}', [PackageController::class, 'packagesByStateCity']);
        Route::get('packages/state/{slug}',      [PackageController::class, 'packagesByState']);
        Route::get('packages/region/{slug}',     [PackageController::class, 'packagesByRegion']);
        Route::get('packages/{slug}',            [PackageController::class, 'packagesByCountry']);

        // ── Categories ────────────────────────────────────────────────────────
        Route::get('packages/category/{category}', [CategoryController::class, 'packagesByCategory']);

        // ── Enquiries ─────────────────────────────────────────────────────────
        Route::post('enquiries', [EnquiryController::class, 'store']);

        // ── Cars ──────────────────────────────────────────────────────────────
        Route::get('car-rental',                  [\App\Http\Controllers\Api\CarRentalController::class, 'landingPage']);
        Route::get('car-rental/city/{slug}',       [\App\Http\Controllers\Api\CarRentalController::class, 'cityDetail']);
        Route::get('car-rental/detail/{slug}',     [\App\Http\Controllers\Api\CarRentalController::class, 'vehicleDetail']);
        Route::get('car-rental/route/{slug}',      [\App\Http\Controllers\Api\CarRentalController::class, 'routeDetail']);
        Route::get('car-rental/category/{slug}',   [\App\Http\Controllers\Api\CarRentalController::class, 'categoryDetail']);
        Route::get('car-rental/packages/{slug}',   [\App\Http\Controllers\Api\CarRentalController::class, 'packageDetail']);
        // Unified path-based resolver — auto-detects City-wise / Destination-wise by slug
        Route::get('car-rental/{slug}',            [\App\Http\Controllers\Api\CarRentalController::class, 'resolve']);
        Route::get('cars',         [CarController::class, 'index']);
        Route::get('route/cars',   [CarController::class, 'getCarsByRoute']);
        Route::get('city/cars',    [CarController::class, 'getCarsByCity']);

        // ── Tourist Attractions ──────────────────────────────────────────────────
        Route::get('tourist-attractions',                       [\App\Http\Controllers\Api\TouristAttractionController::class, 'index']);
        Route::get('tourist-attractions/state/{slug}',          [\App\Http\Controllers\Api\TouristAttractionController::class, 'statePage']);
        Route::get('tourist-attractions/{stateSlug}/{citySlug}', [\App\Http\Controllers\Api\TouristAttractionController::class, 'cityPage']);
        Route::get('tourist-attractions/{slug}',                [\App\Http\Controllers\Api\TouristAttractionController::class, 'attractionDetail']);

        // ── Tourist Activities ────────────────────────────────────────────────────
        Route::get('tourist-activities',                       [\App\Http\Controllers\Api\TouristActivityController::class, 'index']);
        Route::get('tourist-activities/state/{slug}',          [\App\Http\Controllers\Api\TouristActivityController::class, 'statePage']);
        Route::get('tourist-activities/{stateSlug}/{citySlug}', [\App\Http\Controllers\Api\TouristActivityController::class, 'cityPage']);
        Route::get('tourist-activities/{slug}',                [\App\Http\Controllers\Api\TouristActivityController::class, 'activityDetail']);

        // ── Manage City Pages ─────────────────────────────────────────────────
        // NOTE: state/city/{state}/{city} must be before state/{state} to avoid wildcard conflict
        Route::get('state/city/{state}/{city}', [\App\Http\Controllers\Api\ManageCityController::class, 'cityPage']);
        Route::get('state/{state}',             [\App\Http\Controllers\Api\ManageCityController::class, 'statePage']);

    });
});
