<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AwardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\LocationSettingController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\RegionsController;
use App\Http\Controllers\Admin\RootPageSettingController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\HomeAboutController;
use App\Http\Controllers\Admin\TourServiceController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\CmsPageController;
use App\Http\Controllers\Admin\CmsBuilderController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\CarCategoryController;
use App\Http\Controllers\Admin\CarRouteController;
use App\Http\Controllers\Admin\CarCityController;
use App\Http\Controllers\Admin\CarController;
use App\Http\Controllers\Admin\CarRentalContentController;
use App\Http\Controllers\Admin\CarDetailController;
use App\Http\Controllers\Admin\CarPackageController;
use App\Http\Controllers\Admin\CarDestinationController;
use App\Http\Controllers\Admin\SitemapController;
use App\Http\Controllers\Admin\MenuManagerController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\HomeSectionController;
use App\Http\Controllers\Admin\HomeBlogItemController;
use App\Http\Controllers\Admin\HolidayMenuController;
use App\Http\Controllers\Admin\HolidaySettingController;
use App\Http\Controllers\Admin\ManageCityController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\EnquiryController;



Route::prefix('admin')->name('admin.')->group(function () {
    //Route::middleware(['guest'])->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    //});

    Route::middleware(['auth', 'isadmin', 'checkpermission'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // ── Menu Management System ──────────────────────────────────────────
        // IMPORTANT: Static segments before {param} to avoid route conflicts.

        // Available-items AJAX (static path must come before /{menu})
        Route::get('menu-items/available/{type}', [MenuManagerController::class, 'available'])
             ->name('menus.available-items');

        // Builder pages + Menu CRUD (admin can create/delete custom menus)
        Route::get   ('menus',                  [MenuManagerController::class, 'index'])         ->name('menus.index');
        Route::post  ('menus',                  [MenuManagerController::class, 'store'])         ->name('menus.store');
        Route::get   ('menus/{menu}',           [MenuManagerController::class, 'show'])          ->name('menus.show');
        Route::delete('menus/{menu}',           [MenuManagerController::class, 'destroy'])       ->name('menus.destroy');
        Route::get   ('menus/{menu}/settings',  [MenuManagerController::class, 'settings'])      ->name('menus.settings');
        Route::post  ('menus/{menu}/settings',  [MenuManagerController::class, 'updateSettings'])->name('menus.update-settings');

        // Item CRUD — scoped under /{menu} for security context
        Route::post('menus/{menu}/items',         [MenuItemController::class, 'store'])   ->name('menu-items.store');
        Route::post('menus/{menu}/items/reorder', [MenuItemController::class, 'reorder'])->name('menu-items.reorder');

        // Item CRUD — not scoped (item ID is sufficient, menu_id verified in service)
        Route::get   ('menu-items/{item}',        [MenuItemController::class, 'show'])   ->name('menu-items.show');
        Route::put   ('menu-items/{item}',        [MenuItemController::class, 'update']) ->name('menu-items.update');
        Route::delete('menu-items/{item}',        [MenuItemController::class, 'destroy'])->name('menu-items.destroy');
        Route::post  ('menu-items/{item}/toggle', [MenuItemController::class, 'toggle']) ->name('menu-items.toggle');
        // ── End Menu Management System ───────────────────────────────────────

        // ── Holiday Packages Auto-Menu ─────────────────────────────────────
        // Read-only tree built from DB; admins can only reorder & toggle.
        Route::get ('holiday-menu',         [HolidayMenuController::class, 'show'])   ->name('holiday-menu.show');
        Route::post('holiday-menu/reorder', [HolidayMenuController::class, 'reorder'])->name('holiday-menu.reorder');
        Route::post('holiday-menu/toggle',  [HolidayMenuController::class, 'toggle']) ->name('holiday-menu.toggle');
        // ── End Holiday Packages Auto-Menu ──────────────────────────────────


        // Country is locked to India — view/update only (no create/delete)
        Route::get('countries/{country}', [CountryController::class, 'show'])->name('countries.show');
        Route::put('countries/{country}', [CountryController::class, 'update'])->name('countries.update');
        Route::get('countries', [CountryController::class, 'index'])->name('countries.index');

        // ── Holiday Setting ────────────────────────────────────────────────────
        Route::get('holiday-setting',                              [HolidaySettingController::class, 'index'])       ->name('holiday-setting.index');
        Route::post('holiday-setting',                             [HolidaySettingController::class, 'store'])       ->name('holiday-setting.store');
        Route::get('holiday-setting/{holidaySetting}',             [HolidaySettingController::class, 'show'])        ->name('holiday-setting.show');
        Route::put('holiday-setting/{holidaySetting}',             [HolidaySettingController::class, 'update'])      ->name('holiday-setting.update');
        Route::put('holiday-setting/{holidaySetting}/faq',         [HolidaySettingController::class, 'updateFaq'])   ->name('holiday-setting.faq');
        Route::post('holiday-setting/{holidaySetting}/toggle-status', [HolidaySettingController::class, 'toggleStatus'])->name('holiday-setting.toggle-status');
        Route::delete('holiday-setting/{holidaySetting}',          [HolidaySettingController::class, 'destroy'])     ->name('holiday-setting.destroy');
        // ── End Holiday Setting ────────────────────────────────────────────────

        // ── Location Setting (unified: Regions + States + Cities) ──
        Route::get('location-setting', [LocationSettingController::class, 'index'])->name('location-setting.index');

        // States
        Route::get('location-setting/states',               [StateController::class, 'index'])->name('location-setting.states.index');
        Route::post('location-setting/states',              [StateController::class, 'store'])->name('location-setting.states.store');
        Route::get('location-setting/states/{state}',       [StateController::class, 'show'])->name('location-setting.states.show');
        Route::put('location-setting/states/{state}',       [StateController::class, 'update'])->name('location-setting.states.update');
        Route::post('location-setting/states/{state}/toggle-status', [StateController::class, 'toggleStatus'])->name('location-setting.states.toggle-status');
        Route::put('location-setting/states/{state}/faq',   [StateController::class, 'updateFaq'])->name('location-setting.states.faq');
        Route::put('location-setting/states/{state}/best-time', [StateController::class, 'updateBestTime'])->name('location-setting.states.best-time');
        Route::delete('location-setting/states/{state}',    [StateController::class, 'destroy'])->name('location-setting.states.destroy');

        // Cities (via LocationSettingController)
        Route::post('location-setting/cities',              [LocationSettingController::class, 'storeCity'])->name('location-setting.cities.store');
        Route::get('location-setting/cities/{location}',    [LocationSettingController::class, 'showCity'])->name('location-setting.cities.show');
        Route::get('location-setting/cities/{location}/meta', [LocationSettingController::class, 'showCityMeta'])->name('location-setting.cities.meta');
        Route::put('location-setting/cities/{location}',    [LocationSettingController::class, 'updateCity'])->name('location-setting.cities.update');
        Route::put('location-setting/cities/{location}/faq', [LocationSettingController::class, 'updateCityFaq'])->name('location-setting.cities.faq');
        Route::put('location-setting/cities/{location}/best-time', [LocationSettingController::class, 'updateCityBestTime'])->name('location-setting.cities.best-time');
        Route::post('location-setting/cities/{location}/toggle-status', [LocationSettingController::class, 'toggleCityStatus'])->name('location-setting.cities.toggle-status');
        Route::delete('location-setting/cities/{location}', [LocationSettingController::class, 'destroyCity'])->name('location-setting.cities.destroy');
        // ── End Location Setting ───────────────────────────────────────────────

        // ── Root Page Setting (Region + State + City page content — banner, meta, FAQs, best time) ──
        Route::get('root-page-setting',               [RootPageSettingController::class, 'index'])->name('root-page-setting.index');
        Route::get('root-page-setting/states/search', [RootPageSettingController::class, 'statesSearch'])->name('root-page-setting.states.search');
        Route::get('root-page-setting/cities/search', [RootPageSettingController::class, 'citiesSearch'])->name('root-page-setting.cities.search');
        // ── End Root Page Setting ──────────────────────────────────────────────

        Route::resource('categories', CategoryController::class);

        Route::get('categories/slug/duplicate_check', [CategoryController::class, 'slugDuplicateCheck'])->name('categories.slug.duplicate_check');

        // ── Media Library ───────────────────────────────────────────────────
        Route::get('media',       [MediaController::class, 'index'])->name('media.index');
        Route::get('media/list',  [MediaController::class, 'list'])->name('media.list');
        Route::post('media',      [MediaController::class, 'store'])->name('media.store');
        Route::post('media/sync', [MediaController::class, 'sync'])->name('media.sync');
        Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
        Route::get('media/{media}/details', [MediaController::class, 'show'])->name('media.show');
        // ── End Media Library ──────────────────────────────────────────────────

        // ── Enquiries ───────────────────────────────────────────────────────────
        Route::get('enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('enquiries/{enquiry}', [EnquiryController::class, 'show'])->name('enquiries.show');
        Route::post('enquiries/{enquiry}/status', [EnquiryController::class, 'updateStatus'])->name('enquiries.update-status');
        Route::delete('enquiries/{enquiry}', [EnquiryController::class, 'destroy'])->name('enquiries.destroy');
        // ── End Enquiries ───────────────────────────────────────────────────────

        // Banners — home page slider management (accessed through page-settings/home)
        Route::post('banners/reorder', [BannerController::class, 'reorder'])->name('banners.reorder');
        Route::resource('banners', BannerController::class)->except(['create', 'show']);

        Route::delete('locations/city/{location}', [LocationController::class, 'destroyLocation'])->name('locations.city.delete');

        Route::get('locations/city-search', [LocationController::class, 'searchCity'])->name('city.search');

        Route::resource('locations', LocationController::class);

        Route::put('locations/faq/{location}', [LocationController::class, 'updateFaq'])->name('locations.faqUpdate');

        Route::get('locations-meta/{location}', [LocationController::class, 'showMeta'])->name('locations-meta.show.meta');

        Route::post('locations/add/mega_menu', [LocationController::class, 'addMegaMenu'])->name('locations.add.mega.menu');

        Route::put('locations_mega_menu/{menu_location}', [LocationController::class, 'updateMegaMenu'])->name('locations.mega.menu.update');

        Route::delete('locations_mega_menu/delete/{menu_location}', [LocationController::class, 'destroy'])->name('locations.mega.menu.delete');

        Route::resource('regions', RegionsController::class);
        Route::put('regions/{region}/faqs', [RegionsController::class, 'updateFaq'])->name('regions.faqUpdate');
        Route::post('regions/{region}/toggle-popular', [RegionsController::class, 'togglePopular'])->name('regions.toggle-popular');

        Route::get('/get-states', [LocationController::class, 'getStates'])->name('get.states');
        Route::get('/get-cities', [LocationController::class, 'getCities'])->name('get.cities');

        Route::get('packages/get-parent-by-type', [PackageController::class, 'getParentByType'])->name('packages.parent.category');

        Route::resource('packages', PackageController::class);

        Route::put('packages/faq/{package}', [PackageController::class, 'updateFaq'])->name('packages.faqUpdate');

        Route::get('packages-meta/{package}', [PackageController::class, 'showMeta'])->name('packages-meta.show.meta');

        Route::get('packages/slug/duplicate_check', [PackageController::class, 'slugDuplicateCheck'])->name('packages.slug.duplicate_check');

        Route::delete('packages/images/delete', [PackageController::class, 'deleteImage'])->name('packages.images.delete');

        // Returns booked_seats per departure for the admin edit form (AJAX)
        Route::get('packages/{package}/departure-seats', [PackageController::class, 'getDepartureSeats'])->name('packages.departure.seats');

        // About Section CMS — features only (stats removed)
        Route::prefix('home-about')->name('home-about.')->group(function () {
            Route::get('features',                    [HomeAboutController::class, 'featuresIndex'])->name('features.index');
            Route::post('features',                   [HomeAboutController::class, 'featuresStore'])->name('features.store');
            Route::put('features/{feature}',          [HomeAboutController::class, 'featuresUpdate'])->name('features.update');
            Route::patch('features/{feature}/toggle', [HomeAboutController::class, 'featuresToggle'])->name('features.toggle');
            Route::delete('features/{feature}',       [HomeAboutController::class, 'featuresDestroy'])->name('features.destroy');
            Route::post('features/reorder',           [HomeAboutController::class, 'featuresReorder'])->name('features.reorder');
        });

        Route::resource('tour-services', TourServiceController::class);

        Route::get('page-meta/{page}', [PageController::class, 'showMeta'])->name('page-meta.show.meta');

        Route::get('page/faq', [PageController::class, 'faqs'])->name('page.faq');

        Route::put('page/faq/{page}', [PageController::class, 'updateFaq'])->name('page.faqUpdate');

        Route::put('page/{page}', [PageController::class, 'update'])->name('page.update');

        Route::resource('cms-page', CmsPageController::class);
        Route::post('cms-page/create-new-page', [CmsPageController::class, 'create_page'])->name('cms-page.create.page');

        // ── Dynamic CMS Builder ──────────────────────────────────────────
        Route::prefix('cms-builder')->name('cms-builder.')->group(function () {
            Route::get('/',                                    [CmsBuilderController::class, 'index'])->name('index');
            Route::get('/create',                              [CmsBuilderController::class, 'create'])->name('create');
            Route::post('/',                                   [CmsBuilderController::class, 'store'])->name('store');
            Route::get('/{cmsBuilder}/edit',                   [CmsBuilderController::class, 'edit'])->name('edit');
            Route::put('/{cmsBuilder}',                        [CmsBuilderController::class, 'update'])->name('update');
            Route::delete('/{cmsBuilder}',                     [CmsBuilderController::class, 'destroy'])->name('destroy');
            Route::post('/{cmsBuilder}/toggle-publish',        [CmsBuilderController::class, 'togglePublish'])->name('toggle-publish');
            Route::put('/{cmsBuilder}/seo',                    [CmsBuilderController::class, 'updateSeo'])->name('seo.update');
            Route::get('/slug/generate',                       [CmsBuilderController::class, 'generateSlug'])->name('slug');
            Route::post('/team/member',                        [CmsBuilderController::class, 'quickAddTeamMember'])->name('team-member.store');
        });
        // Route::get('news-meta/{package}', [CmsPageController::class, 'showMeta'])->name('news-meta.show.meta');

        Route::resource('news', NewsController::class);

        Route::put('news/faq/{news}', [NewsController::class, 'updateFaq'])->name('news.faqUpdate');
        Route::get('news-meta/{news}', [NewsController::class, 'showMeta'])->name('news-meta.show.meta');

        Route::get('cms-page-meta/{cms_page}', [CmsPageController::class, 'showMeta'])->name('cms-page-meta.show.meta');

        Route::get('page-settings/home', [SettingsController::class, 'home'])->name('page-settings.home');

        // Homepage Section CMS
        Route::prefix('home-sections')->name('home-sections.')->group(function () {
            Route::get('/',                            [HomeSectionController::class, 'index'])->name('index');
            Route::post('reorder',                     [HomeSectionController::class, 'reorder'])->name('reorder');
            Route::post('customized-packages',         [HomeSectionController::class, 'bulkCustomized'])->name('customized-packages');
            Route::delete('{key}/image',               [HomeSectionController::class, 'deleteImage'])->name('delete-image');
            Route::put('{key}',                        [HomeSectionController::class, 'update'])->name('update');
            Route::patch('{key}/toggle',               [HomeSectionController::class, 'toggle'])->name('toggle');
        });

        // Home Blog Items CMS (Latest Blogs section)
        Route::post('home-blog-items/reorder/save', [HomeBlogItemController::class, 'reorder'])->name('home-blog-items.reorder');
        Route::resource('home-blog-items', HomeBlogItemController::class)->except(['create', 'edit']);

        Route::get('page-settings/car', [SettingsController::class, 'car'])->name('page-settings.car');

        Route::resource('car-categories', CarCategoryController::class);

        Route::get('car-categories/slug/duplicate_check', [CarCategoryController::class, 'slugDuplicateCheck'])->name('car-categories.slug.duplicate_check');

        Route::resource('cars', CarController::class);

        Route::get('cars/slug/duplicate_check', [CarController::class, 'slugDuplicateCheck'])->name('cars.slug.duplicate_check');

        Route::resource('car-routes', CarRouteController::class);

        Route::get('car-routes/slug/duplicate_check', [CarRouteController::class, 'slugDuplicateCheck'])->name('car-routes.slug.duplicate_check');

        Route::get('car-routes/cars/{id}', [CarRouteController::class, 'getRouteCars'])->name('car-routes.cars');

        Route::post('car-routes/sync-cars', [CarRouteController::class, 'syncCars'])->name('car-routes.syncCars');

        Route::get('car-routes-meta/{car_route}', [CarRouteController::class, 'showMeta'])->name('car-routes-meta.show.meta');
        Route::get('car-routes-page/{car_route}', [CarRouteController::class, 'showPage'])->name('car-routes-page.show.page');

        Route::get('car/routes/faq', [CarRouteController::class, 'faqs'])->name('car.routes.faq');

        Route::put('car-routes/faq/{car_route}', [CarRouteController::class, 'updateFaq'])->name('car-routes.faqUpdate');

        Route::get('car/routes/highlights', [CarRouteController::class, 'highlights'])->name('car.routes.highlights');

        Route::put('car-routes/highlights/{car_route}', [CarRouteController::class, 'updateHighlights'])->name('car-routes.highlightsUpdate');



        Route::resource('car-city', CarCityController::class);

        Route::get('car-city/slug/duplicate_check', [CarCityController::class, 'slugDuplicateCheck'])->name('car-city.slug.duplicate_check');

        Route::get('car-city/cars/{id}', [CarCityController::class, 'getCityCars'])->name('car-city.cars');

        Route::post('car-city/sync-cars', [CarCityController::class, 'syncCars'])->name('car-city.syncCars');

        Route::get('car-city-meta/{car_city}', [CarCityController::class, 'showMeta'])->name('car-city-meta.show.meta');

        Route::get('car/city/faq', [CarCityController::class, 'faqs'])->name('car.city.faq');

        Route::put('car-city/faq/{car_city}', [CarCityController::class, 'updateFaq'])->name('car-city.faqUpdate');

        // ── Car City — Features / Benefits / Gallery (per-city) ──
        Route::get('car/city/features-benefits', [CarCityController::class, 'getFeaturesBenefits'])->name('car.city.features-benefits');
        Route::put('car-city/features/{car_city}', [CarCityController::class, 'updateFeatures'])->name('car-city.features');
        Route::put('car-city/benefits/{car_city}', [CarCityController::class, 'updateBenefits'])->name('car-city.benefits');
        Route::post('car-city/{car_city}/gallery', [CarCityController::class, 'addGalleryImage'])->name('car-city.gallery.add');
        Route::delete('car-city/gallery/{gallery_image}', [CarCityController::class, 'deleteGalleryImage'])->name('car-city.gallery.delete');
        Route::get('car/city/highlights', [CarCityController::class, 'highlights'])->name('car.city.highlights');
        Route::put('car-city/highlights/{car_city}', [CarCityController::class, 'updateHighlights'])->name('car-city.highlightsUpdate');

        // ── Car Packages ──────────────────────────────────────────────────────
        Route::get('car-packages/slug/duplicate_check', [CarPackageController::class, 'slugDuplicateCheck'])->name('car-packages.slug.duplicate_check');
        Route::get('car-packages/cars/{id}', [CarPackageController::class, 'getPackageCars'])->name('car-packages.cars');
        Route::post('car-packages/sync-cars', [CarPackageController::class, 'syncCars'])->name('car-packages.syncCars');
        Route::get('car-packages-meta/{car_package}', [CarPackageController::class, 'showMeta'])->name('car-packages-meta.show.meta');
        Route::get('car-packages-page/{car_package}', [CarPackageController::class, 'showPage'])->name('car-packages-page.show.page');
        Route::get('car/packages/faq', [CarPackageController::class, 'faqs'])->name('car.packages.faq');
        Route::put('car-packages/faq/{car_package}', [CarPackageController::class, 'updateFaq'])->name('car-packages.faqUpdate');
        Route::get('car/packages/stops', [CarPackageController::class, 'stops'])->name('car.packages.stops');
        Route::put('car-packages/stops/{car_package}', [CarPackageController::class, 'updateStops'])->name('car-packages.stopsUpdate');
        Route::get('car/packages/amenities', [CarPackageController::class, 'amenities'])->name('car.packages.amenities');
        Route::put('car-packages/amenities/{car_package}', [CarPackageController::class, 'updateAmenities'])->name('car-packages.amenitiesUpdate');
        Route::resource('car-packages', CarPackageController::class);

        // ── Car Destinations ─────────────────────────────────────────────────
        Route::get('car-destinations/slug/duplicate_check', [CarDestinationController::class, 'slugDuplicateCheck'])->name('car-destinations.slug.duplicate_check');
        Route::get('car-destinations/cars/{id}', [CarDestinationController::class, 'getDestinationCars'])->name('car-destinations.cars');
        Route::post('car-destinations/sync-cars', [CarDestinationController::class, 'syncCars'])->name('car-destinations.syncCars');
        Route::get('car-destinations-meta/{car_destination}', [CarDestinationController::class, 'showMeta'])->name('car-destinations-meta.show.meta');
        Route::get('car-destinations-page/{car_destination}', [CarDestinationController::class, 'showPage'])->name('car-destinations-page.show.page');
        Route::get('car/destinations/faq', [CarDestinationController::class, 'faqs'])->name('car.destinations.faq');
        Route::put('car-destinations/faq/{car_destination}', [CarDestinationController::class, 'updateFaq'])->name('car-destinations.faqUpdate');
        Route::get('car/destinations/highlights', [CarDestinationController::class, 'highlights'])->name('car.destinations.highlights');
        Route::put('car-destinations/highlights/{car_destination}', [CarDestinationController::class, 'updateHighlights'])->name('car-destinations.highlightsUpdate');
        Route::resource('car-destinations', CarDestinationController::class);

        // ── Car Rental — Page Content (Welcome/Checklist/About/Gallery/Why-Choose/Section Titles) ──
        Route::post('car-rental-content/text', [CarRentalContentController::class, 'updateText'])->name('car-rental-content.text');
        Route::post('car-rental-content/checklist', [CarRentalContentController::class, 'updateChecklist'])->name('car-rental-content.checklist');
        Route::post('car-rental-content/features', [CarRentalContentController::class, 'updateFeatures'])->name('car-rental-content.features');
        Route::post('car-rental-content/benefits', [CarRentalContentController::class, 'updateBenefits'])->name('car-rental-content.benefits');
        Route::post('car-rental-content/gallery', [CarRentalContentController::class, 'addGalleryImage'])->name('car-rental-content.gallery.add');
        Route::delete('car-rental-content/gallery/{gallery_image}', [CarRentalContentController::class, 'deleteGalleryImage'])->name('car-rental-content.gallery.delete');
        Route::post('car-rental-content/why-choose-stats', [CarRentalContentController::class, 'updateWhyChooseStats'])->name('car-rental-content.why-choose-stats');
        Route::post('car-rental-content/amenity', [CarRentalContentController::class, 'addAmenity'])->name('car-rental-content.amenity.add');
        Route::delete('car-rental-content/amenity/{amenity}', [CarRentalContentController::class, 'deleteAmenity'])->name('car-rental-content.amenity.delete');

        // ── Car (Vehicle) detail page — gallery / highlight tags / amenities ──
        Route::get('car/settings', [CarDetailController::class, 'settings'])->name('car.settings');
        Route::post('cars/{car}/gallery', [CarDetailController::class, 'addGalleryImage'])->name('cars.gallery.add');
        Route::delete('cars/gallery/{gallery_image}', [CarDetailController::class, 'deleteGalleryImage'])->name('cars.gallery.delete');
        Route::put('cars/{car}/highlight-tags', [CarDetailController::class, 'updateHighlightTags'])->name('cars.highlight-tags');
        Route::get('car/amenities', [CarDetailController::class, 'amenities'])->name('car.amenities');
        Route::put('cars/{car}/amenities', [CarDetailController::class, 'updateAmenities'])->name('cars.amenities');

        Route::resource('awards', AwardController::class);

        Route::resource('departments', DepartmentController::class)->except(['create', 'show']);
        Route::resource('teams', TeamController::class);

        Route::resource('partners', \App\Http\Controllers\Admin\PartnerController::class)->except(['create', 'edit']);
        Route::resource('branches', \App\Http\Controllers\Admin\BranchController::class)->except(['create', 'edit']);

        // ── Manage Cities ─────────────────────────────────────────────────────
        Route::get ('city-pages',                              [ManageCityController::class, 'index'])            ->name('city-pages.index');
        // Add a state/city that already exists in Location Setting but is missing its City Guide entry
        Route::post('city-pages/add-state',                    [ManageCityController::class, 'storeState'])       ->name('city-pages.store-state');
        Route::post('city-pages/add-city',                     [ManageCityController::class, 'storeCity'])        ->name('city-pages.store-city');
        Route::get ('city-pages/{manageCity}/edit',            [ManageCityController::class, 'edit'])             ->name('city-pages.edit');
        // Settings modal
        Route::get ('city-pages/{manageCity}/settings',        [ManageCityController::class, 'show'])             ->name('city-pages.show');
        Route::post('city-pages/{manageCity}/settings',        [ManageCityController::class, 'updateSettings'])   ->name('city-pages.update-settings');
        // Status toggle
        Route::post('city-pages/{manageCity}/toggle-status',   [ManageCityController::class, 'toggleStatus'])     ->name('city-pages.toggle-status');
        Route::post('city-pages/{manageCity}/toggle-popular',  [ManageCityController::class, 'togglePopular'])    ->name('city-pages.toggle-popular');
        // Content tabs (edit page)
        Route::post('city-pages/{manageCity}/how-to-reach',    [ManageCityController::class, 'saveHowToReach'])   ->name('city-pages.how-to-reach');
        Route::post('city-pages/{manageCity}/top-places',      [ManageCityController::class, 'saveTopPlaces'])    ->name('city-pages.top-places');
        Route::post('city-pages/{manageCity}/things-to-do',    [ManageCityController::class, 'saveThingsToDo'])   ->name('city-pages.things-to-do');
        Route::post('city-pages/{manageCity}/travel-tips',     [ManageCityController::class, 'saveTravelTips'])   ->name('city-pages.travel-tips');
        Route::post('city-pages/{manageCity}/things-to-know',  [ManageCityController::class, 'saveThingsToKnow']) ->name('city-pages.things-to-know');
        Route::post('city-pages/{manageCity}/religious-tourism',[ManageCityController::class, 'saveReligiousTourism'])->name('city-pages.religious-tourism');
        Route::post('city-pages/{manageCity}/souvenirs',       [ManageCityController::class, 'saveSouvenirs'])    ->name('city-pages.souvenirs');
        Route::post('city-pages/{manageCity}/festivals-intro', [ManageCityController::class, 'saveFestivalsIntro'])->name('city-pages.festivals-intro');
        // Quick Facts modal
        Route::get ('city-pages/{manageCity}/quick-facts',     [ManageCityController::class, 'getQuickFacts'])    ->name('city-pages.quick-facts');
        Route::post('city-pages/{manageCity}/quick-facts',     [ManageCityController::class, 'saveQuickFacts'])   ->name('city-pages.save-quick-facts');
        // FAQs modal
        Route::get ('city-pages/{manageCity}/faqs',            [ManageCityController::class, 'getFaqs'])          ->name('city-pages.faqs');
        Route::post('city-pages/{manageCity}/faqs',            [ManageCityController::class, 'saveFaqs'])         ->name('city-pages.save-faqs');
        // Meta modal
        Route::get ('city-pages/{manageCity}/meta',            [ManageCityController::class, 'getMeta'])          ->name('city-pages.meta');
        Route::post('city-pages/{manageCity}/meta',            [ManageCityController::class, 'saveMeta'])         ->name('city-pages.save-meta');

        // ── Manage Festival ────────────────────────────────────────────────────
        // Festival Setting (single overview/hub landing page) — static segments, must precede {festival} wildcard
        Route::get ('festivals/setting',               [\App\Http\Controllers\Admin\FestivalSettingController::class, 'index'])         ->name('festival.setting.index');
        Route::get ('festivals/setting/data',          [\App\Http\Controllers\Admin\FestivalSettingController::class, 'data'])          ->name('festival.setting.data');
        Route::put ('festivals/setting/section',       [\App\Http\Controllers\Admin\FestivalSettingController::class, 'updateSection'])  ->name('festival.setting.update-section');
        Route::post('festivals/setting/toggle-status', [\App\Http\Controllers\Admin\FestivalSettingController::class, 'toggleStatus'])   ->name('festival.setting.toggle-status');

        Route::get   ('festivals',                  [\App\Http\Controllers\Admin\FestivalController::class, 'index'])       ->name('festival.index');
        Route::post  ('festivals',                  [\App\Http\Controllers\Admin\FestivalController::class, 'store'])       ->name('festival.store');
        Route::get   ('festivals/{festival}',        [\App\Http\Controllers\Admin\FestivalController::class, 'show'])       ->name('festival.show');
        Route::put   ('festivals/{festival}',        [\App\Http\Controllers\Admin\FestivalController::class, 'update'])     ->name('festival.update');
        Route::post  ('festivals/{festival}/toggle-status', [\App\Http\Controllers\Admin\FestivalController::class, 'toggleStatus'])->name('festival.toggle-status');
        Route::delete('festivals/{festival}',        [\App\Http\Controllers\Admin\FestivalController::class, 'destroy'])    ->name('festival.destroy');

        // Festival detail page sections — Setting (long description), Key Experiences,
        // How to Reach, Why Visit.
        Route::get('festivals/{festival}/detail',                [\App\Http\Controllers\Admin\FestivalController::class, 'detail'])               ->name('festival.detail');
        Route::put('festivals/{festival}/setting',                [\App\Http\Controllers\Admin\FestivalController::class, 'updateSetting'])         ->name('festival.update-setting');
        Route::put('festivals/{festival}/key-experiences',        [\App\Http\Controllers\Admin\FestivalController::class, 'updateKeyExperiences'])   ->name('festival.key-experiences');
        Route::put('festivals/{festival}/how-to-reach',           [\App\Http\Controllers\Admin\FestivalController::class, 'updateHowToReach'])       ->name('festival.how-to-reach');
        Route::put('festivals/{festival}/why-visits',             [\App\Http\Controllers\Admin\FestivalController::class, 'updateWhyVisits'])        ->name('festival.why-visits');
        Route::put('festivals/{festival}/faqs',                   [\App\Http\Controllers\Admin\FestivalController::class, 'updateFaqs'])            ->name('festival.faqs');
        Route::put('festivals/{festival}/meta',                   [\App\Http\Controllers\Admin\FestivalController::class, 'updateMeta'])            ->name('festival.meta');
        Route::put('festivals/{festival}/stats',                  [\App\Http\Controllers\Admin\FestivalController::class, 'updateStats'])           ->name('festival.stats');
        Route::put('festivals/{festival}/highlights',             [\App\Http\Controllers\Admin\FestivalController::class, 'updateHighlights'])      ->name('festival.highlights');
        Route::put('festivals/{festival}/places',                 [\App\Http\Controllers\Admin\FestivalController::class, 'updatePlaces'])          ->name('festival.places');
        // ── End Manage Festival ────────────────────────────────────────────────

        // ── Festival State Pages ("Festivals of {State}" per-state landing pages) ──
        Route::get   ('festival-state-pages',                          [\App\Http\Controllers\Admin\FestivalStatePageController::class, 'index'])         ->name('festival-state-pages.index');
        Route::post  ('festival-state-pages',                          [\App\Http\Controllers\Admin\FestivalStatePageController::class, 'store'])         ->name('festival-state-pages.store');
        Route::get   ('festival-state-pages/{festival_state_page}',    [\App\Http\Controllers\Admin\FestivalStatePageController::class, 'show'])          ->name('festival-state-pages.show');
        Route::put   ('festival-state-pages/{festival_state_page}/section', [\App\Http\Controllers\Admin\FestivalStatePageController::class, 'updateSection'])->name('festival-state-pages.update-section');
        Route::post  ('festival-state-pages/{festival_state_page}/toggle-status', [\App\Http\Controllers\Admin\FestivalStatePageController::class, 'toggleStatus'])->name('festival-state-pages.toggle-status');
        // ── End Festival State Pages ─────────────────────────────────────────────

        // ── Manage Experience (Category -> Subcategory -> Experience item) ─────
        // Experience Setting (single overview/hub landing page) — static segments, must precede {category} wildcard
        Route::get ('experiences/setting',               [\App\Http\Controllers\Admin\ExperienceSettingController::class, 'index'])         ->name('experiences.setting.index');
        Route::get ('experiences/setting/data',          [\App\Http\Controllers\Admin\ExperienceSettingController::class, 'data'])          ->name('experiences.setting.data');
        Route::put ('experiences/setting/section',       [\App\Http\Controllers\Admin\ExperienceSettingController::class, 'updateSection'])  ->name('experiences.setting.update-section');
        Route::post('experiences/setting/toggle-status', [\App\Http\Controllers\Admin\ExperienceSettingController::class, 'toggleStatus'])   ->name('experiences.setting.toggle-status');

        // Experience Pages (State / City hub pages)
        Route::get   ('experience-pages',                          [\App\Http\Controllers\Admin\ExperiencePageController::class, 'index'])         ->name('experience-pages.index');
        Route::get   ('experience-pages/{experience_page}',        [\App\Http\Controllers\Admin\ExperiencePageController::class, 'show'])         ->name('experience-pages.show');
        Route::put   ('experience-pages/{experience_page}/section', [\App\Http\Controllers\Admin\ExperiencePageController::class, 'updateSection']) ->name('experience-pages.update-section');
        Route::post  ('experience-pages/{experience_page}/toggle-status',   [\App\Http\Controllers\Admin\ExperiencePageController::class, 'toggleStatus'])   ->name('experience-pages.toggle-status');
        Route::post  ('experience-pages/{experience_page}/toggle-featured', [\App\Http\Controllers\Admin\ExperiencePageController::class, 'toggleFeatured']) ->name('experience-pages.toggle-featured');
        Route::delete('experience-pages/{experience_page}',        [\App\Http\Controllers\Admin\ExperiencePageController::class, 'destroy'])  ->name('experience-pages.destroy');

        // Experience Subcategories (e.g. Waterfalls Tours) — static segment, must precede {category} wildcard
        Route::get   ('experience-subcategories/slug/duplicate_check', [\App\Http\Controllers\Admin\ExperienceSubcategoryController::class, 'slugDuplicateCheck'])->name('experience-subcategories.slug.duplicate_check');
        Route::get   ('experience-subcategories',                [\App\Http\Controllers\Admin\ExperienceSubcategoryController::class, 'index'])   ->name('experience-subcategories.index');
        Route::post  ('experience-subcategories',                [\App\Http\Controllers\Admin\ExperienceSubcategoryController::class, 'store'])   ->name('experience-subcategories.store');
        Route::get   ('experience-subcategories/{subcategory}',  [\App\Http\Controllers\Admin\ExperienceSubcategoryController::class, 'show'])     ->name('experience-subcategories.show');
        Route::put   ('experience-subcategories/{subcategory}',  [\App\Http\Controllers\Admin\ExperienceSubcategoryController::class, 'update'])   ->name('experience-subcategories.update');
        Route::post  ('experience-subcategories/{subcategory}/toggle-status', [\App\Http\Controllers\Admin\ExperienceSubcategoryController::class, 'toggleStatus'])->name('experience-subcategories.toggle-status');
        Route::delete('experience-subcategories/{subcategory}',  [\App\Http\Controllers\Admin\ExperienceSubcategoryController::class, 'destroy'])   ->name('experience-subcategories.destroy');

        // Experience items (the individual named places — e.g. Attukad Waterfalls)
        Route::get   ('experiences/slug/duplicate_check', [\App\Http\Controllers\Admin\ExperienceController::class, 'slugDuplicateCheck'])->name('experiences.slug.duplicate_check');
        Route::delete('experiences/gallery/{gallery_image}', [\App\Http\Controllers\Admin\ExperienceController::class, 'deleteGalleryImage'])->name('experiences.gallery.destroy');
        Route::post  ('experiences/gallery/{gallery_image}/alt', [\App\Http\Controllers\Admin\ExperienceController::class, 'updateGalleryImageAlt'])->name('experiences.gallery.update-alt');

        Route::get   ('experiences',                          [\App\Http\Controllers\Admin\ExperienceController::class, 'index'])        ->name('experiences.index');
        Route::post  ('experiences',                          [\App\Http\Controllers\Admin\ExperienceController::class, 'store'])        ->name('experiences.store');
        Route::get   ('experiences/{experience}',             [\App\Http\Controllers\Admin\ExperienceController::class, 'show'])         ->name('experiences.show');
        Route::put   ('experiences/{experience}',             [\App\Http\Controllers\Admin\ExperienceController::class, 'update'])       ->name('experiences.update');
        Route::put   ('experiences/{experience}/section',     [\App\Http\Controllers\Admin\ExperienceController::class, 'updateSection']) ->name('experiences.update-section');
        Route::post  ('experiences/{experience}/toggle-status', [\App\Http\Controllers\Admin\ExperienceController::class, 'toggleStatus'])  ->name('experiences.toggle-status');
        Route::post  ('experiences/{experience}/toggle-popular', [\App\Http\Controllers\Admin\ExperienceController::class, 'togglePopular'])  ->name('experiences.toggle-popular');
        Route::post  ('experiences/{experience}/gallery',        [\App\Http\Controllers\Admin\ExperienceController::class, 'addGalleryImage']) ->name('experiences.gallery.store');
        Route::delete('experiences/{experience}',             [\App\Http\Controllers\Admin\ExperienceController::class, 'destroy'])      ->name('experiences.destroy');

        // Experience Categories (top-level themes — e.g. Nature and Wildlife)
        Route::get   ('experience-categories',               [\App\Http\Controllers\Admin\ExperienceCategoryController::class, 'index'])       ->name('experience-categories.index');
        Route::post  ('experience-categories',               [\App\Http\Controllers\Admin\ExperienceCategoryController::class, 'store'])       ->name('experience-categories.store');
        Route::get   ('experience-categories/slug/duplicate_check', [\App\Http\Controllers\Admin\ExperienceCategoryController::class, 'slugDuplicateCheck'])->name('experience-categories.slug.duplicate_check');
        Route::get   ('experience-categories/{category}',    [\App\Http\Controllers\Admin\ExperienceCategoryController::class, 'show'])       ->name('experience-categories.show');
        Route::put   ('experience-categories/{category}',    [\App\Http\Controllers\Admin\ExperienceCategoryController::class, 'update'])     ->name('experience-categories.update');
        Route::put   ('experience-categories/{category}/section', [\App\Http\Controllers\Admin\ExperienceCategoryController::class, 'updateSection']) ->name('experience-categories.update-section');
        Route::post  ('experience-categories/{category}/toggle-status', [\App\Http\Controllers\Admin\ExperienceCategoryController::class, 'toggleStatus'])->name('experience-categories.toggle-status');
        Route::delete('experience-categories/{category}',    [\App\Http\Controllers\Admin\ExperienceCategoryController::class, 'destroy'])    ->name('experience-categories.destroy');
        // ── End Manage Experience ────────────────────────────────────────────

        // ── Manage Attraction ─────────────────────────────────────────────────
        // Tourist Attraction Setting (root/index hub page) — static segments, must precede {tourist_attraction} wildcard
        Route::get ('tourist-attractions/setting',               [\App\Http\Controllers\Admin\TouristAttractionSettingController::class, 'index'])         ->name('tourist-attractions.setting.index');
        Route::get ('tourist-attractions/setting/data',          [\App\Http\Controllers\Admin\TouristAttractionSettingController::class, 'data'])          ->name('tourist-attractions.setting.data');
        Route::put ('tourist-attractions/setting/section',       [\App\Http\Controllers\Admin\TouristAttractionSettingController::class, 'updateSection'])  ->name('tourist-attractions.setting.update-section');
        Route::post('tourist-attractions/setting/toggle-status', [\App\Http\Controllers\Admin\TouristAttractionSettingController::class, 'toggleStatus'])   ->name('tourist-attractions.setting.toggle-status');

        // Tourist Attraction Pages (State / City hub pages)
        Route::get   ('tourist-attraction-pages',                          [\App\Http\Controllers\Admin\TouristAttractionPageController::class, 'index'])         ->name('tourist-attraction-pages.index');
        Route::post  ('tourist-attraction-pages',                          [\App\Http\Controllers\Admin\TouristAttractionPageController::class, 'store'])         ->name('tourist-attraction-pages.store');
        Route::get   ('tourist-attraction-pages/{tourist_attraction_page}',      [\App\Http\Controllers\Admin\TouristAttractionPageController::class, 'show'])     ->name('tourist-attraction-pages.show');
        Route::put   ('tourist-attraction-pages/{tourist_attraction_page}/section', [\App\Http\Controllers\Admin\TouristAttractionPageController::class, 'updateSection']) ->name('tourist-attraction-pages.update-section');
        Route::post  ('tourist-attraction-pages/{tourist_attraction_page}/toggle-status',   [\App\Http\Controllers\Admin\TouristAttractionPageController::class, 'toggleStatus'])   ->name('tourist-attraction-pages.toggle-status');
        Route::post  ('tourist-attraction-pages/{tourist_attraction_page}/toggle-featured', [\App\Http\Controllers\Admin\TouristAttractionPageController::class, 'toggleFeatured']) ->name('tourist-attraction-pages.toggle-featured');
        Route::post  ('tourist-attraction-pages/{tourist_attraction_page}/toggle-popular',  [\App\Http\Controllers\Admin\TouristAttractionPageController::class, 'togglePopular'])  ->name('tourist-attraction-pages.toggle-popular');
        Route::delete('tourist-attraction-pages/{tourist_attraction_page}',        [\App\Http\Controllers\Admin\TouristAttractionPageController::class, 'destroy'])  ->name('tourist-attraction-pages.destroy');

        // Tourist Attractions (the individual attraction detail entities)
        Route::get   ('tourist-attractions/slug/duplicate_check', [\App\Http\Controllers\Admin\TouristAttractionController::class, 'slugDuplicateCheck'])->name('tourist-attractions.slug.duplicate_check');
        Route::delete('tourist-attractions/gallery/{gallery_image}', [\App\Http\Controllers\Admin\TouristAttractionController::class, 'deleteGalleryImage'])->name('tourist-attractions.gallery.destroy');
        Route::post  ('tourist-attractions/gallery/{gallery_image}/alt', [\App\Http\Controllers\Admin\TouristAttractionController::class, 'updateGalleryImageAlt'])->name('tourist-attractions.gallery.update-alt');

        Route::get   ('tourist-attractions',                          [\App\Http\Controllers\Admin\TouristAttractionController::class, 'index'])        ->name('tourist-attractions.index');
        Route::post  ('tourist-attractions',                          [\App\Http\Controllers\Admin\TouristAttractionController::class, 'store'])        ->name('tourist-attractions.store');
        Route::get   ('tourist-attractions/{tourist_attraction}',      [\App\Http\Controllers\Admin\TouristAttractionController::class, 'show'])         ->name('tourist-attractions.show');
        Route::put   ('tourist-attractions/{tourist_attraction}',      [\App\Http\Controllers\Admin\TouristAttractionController::class, 'update'])       ->name('tourist-attractions.update');
        Route::put   ('tourist-attractions/{tourist_attraction}/section', [\App\Http\Controllers\Admin\TouristAttractionController::class, 'updateSection']) ->name('tourist-attractions.update-section');
        Route::post  ('tourist-attractions/{tourist_attraction}/toggle-status',  [\App\Http\Controllers\Admin\TouristAttractionController::class, 'toggleStatus'])  ->name('tourist-attractions.toggle-status');
        Route::post  ('tourist-attractions/{tourist_attraction}/toggle-popular', [\App\Http\Controllers\Admin\TouristAttractionController::class, 'togglePopular']) ->name('tourist-attractions.toggle-popular');
        Route::post  ('tourist-attractions/{tourist_attraction}/gallery',        [\App\Http\Controllers\Admin\TouristAttractionController::class, 'addGalleryImage']) ->name('tourist-attractions.gallery.store');
        Route::delete('tourist-attractions/{tourist_attraction}',      [\App\Http\Controllers\Admin\TouristAttractionController::class, 'destroy'])      ->name('tourist-attractions.destroy');
        // ── End Manage Attraction ────────────────────────────────────────────

        // ── Manage Activity ───────────────────────────────────────────────────
        // Tourist Activity Setting (root/index hub page) — static segments, must precede {tourist_activity} wildcard
        Route::get ('tourist-activities/setting',               [\App\Http\Controllers\Admin\TouristActivitySettingController::class, 'index'])         ->name('tourist-activities.setting.index');
        Route::get ('tourist-activities/setting/data',          [\App\Http\Controllers\Admin\TouristActivitySettingController::class, 'data'])          ->name('tourist-activities.setting.data');
        Route::put ('tourist-activities/setting/section',       [\App\Http\Controllers\Admin\TouristActivitySettingController::class, 'updateSection'])  ->name('tourist-activities.setting.update-section');
        Route::post('tourist-activities/setting/toggle-status', [\App\Http\Controllers\Admin\TouristActivitySettingController::class, 'toggleStatus'])   ->name('tourist-activities.setting.toggle-status');

        // Tourist Activity Pages (State / City hub pages)
        Route::get   ('tourist-activity-pages',                          [\App\Http\Controllers\Admin\TouristActivityPageController::class, 'index'])         ->name('tourist-activity-pages.index');
        Route::get   ('tourist-activity-pages/{tourist_activity_page}',      [\App\Http\Controllers\Admin\TouristActivityPageController::class, 'show'])     ->name('tourist-activity-pages.show');
        Route::put   ('tourist-activity-pages/{tourist_activity_page}/section', [\App\Http\Controllers\Admin\TouristActivityPageController::class, 'updateSection']) ->name('tourist-activity-pages.update-section');
        Route::post  ('tourist-activity-pages/{tourist_activity_page}/toggle-status',   [\App\Http\Controllers\Admin\TouristActivityPageController::class, 'toggleStatus'])   ->name('tourist-activity-pages.toggle-status');
        Route::post  ('tourist-activity-pages/{tourist_activity_page}/toggle-featured', [\App\Http\Controllers\Admin\TouristActivityPageController::class, 'toggleFeatured']) ->name('tourist-activity-pages.toggle-featured');
        Route::delete('tourist-activity-pages/{tourist_activity_page}',        [\App\Http\Controllers\Admin\TouristActivityPageController::class, 'destroy'])  ->name('tourist-activity-pages.destroy');

        // Tourist Activities (the individual activity detail entities)
        Route::get   ('tourist-activities/slug/duplicate_check', [\App\Http\Controllers\Admin\TouristActivityController::class, 'slugDuplicateCheck'])->name('tourist-activities.slug.duplicate_check');
        Route::delete('tourist-activities/gallery/{gallery_image}', [\App\Http\Controllers\Admin\TouristActivityController::class, 'deleteGalleryImage'])->name('tourist-activities.gallery.destroy');
        Route::post  ('tourist-activities/gallery/{gallery_image}/alt', [\App\Http\Controllers\Admin\TouristActivityController::class, 'updateGalleryImageAlt'])->name('tourist-activities.gallery.update-alt');

        Route::get   ('tourist-activities',                          [\App\Http\Controllers\Admin\TouristActivityController::class, 'index'])        ->name('tourist-activities.index');
        Route::post  ('tourist-activities',                          [\App\Http\Controllers\Admin\TouristActivityController::class, 'store'])        ->name('tourist-activities.store');
        Route::get   ('tourist-activities/{tourist_activity}',      [\App\Http\Controllers\Admin\TouristActivityController::class, 'show'])         ->name('tourist-activities.show');
        Route::put   ('tourist-activities/{tourist_activity}',      [\App\Http\Controllers\Admin\TouristActivityController::class, 'update'])       ->name('tourist-activities.update');
        Route::put   ('tourist-activities/{tourist_activity}/section', [\App\Http\Controllers\Admin\TouristActivityController::class, 'updateSection']) ->name('tourist-activities.update-section');
        Route::post  ('tourist-activities/{tourist_activity}/toggle-status',  [\App\Http\Controllers\Admin\TouristActivityController::class, 'toggleStatus'])  ->name('tourist-activities.toggle-status');
        Route::post  ('tourist-activities/{tourist_activity}/toggle-popular', [\App\Http\Controllers\Admin\TouristActivityController::class, 'togglePopular']) ->name('tourist-activities.toggle-popular');
        Route::post  ('tourist-activities/{tourist_activity}/gallery',        [\App\Http\Controllers\Admin\TouristActivityController::class, 'addGalleryImage']) ->name('tourist-activities.gallery.store');
        Route::delete('tourist-activities/{tourist_activity}',      [\App\Http\Controllers\Admin\TouristActivityController::class, 'destroy'])      ->name('tourist-activities.destroy');

        // ── End Manage Activity ───────────────────────────────────────────────

        // ── End Manage Cities ─────────────────────────────────────────────────

        Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');

        // Admin Management (Settings)
        Route::resource('admin-management', AdminManagementController::class)
            ->parameters(['admin-management' => 'adminUser']);
        Route::post('admin-management/{adminUser}/toggle-status', [AdminManagementController::class, 'toggleStatus'])
            ->name('admin-management.toggle-status');

        // Admins
        Route::resource('admins', AdminController::class);
        Route::post('admins/{admin}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admins.toggle-status');

        // Activity Logs
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');

        // ── Global Real-Time Slug Uniqueness Check ────────────────────────────
        Route::get('slug-check', [\App\Http\Controllers\Admin\SlugCheckController::class, 'check'])->name('slug.check');
    });
});
Route::middleware(['web', 'auth', 'isadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('upload-image', [CmsPageController::class, 'uploadImage'])->name('upload-image');
});

Route::fallback(function () {
    abort(404);
});
