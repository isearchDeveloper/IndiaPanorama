<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarCategoryResource;
use App\Http\Resources\CarResource;
use App\Models\Car;
use App\Models\CarCategory;
use App\Models\CarCity;
use App\Models\CarDestination;
use App\Models\CarPackage;
use App\Models\CarRentalContent;
use App\Models\CarRentalRoadTrip;
use App\Models\CarRoute;
use App\Models\Page;

class CarRentalController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/car-rental/city/{slug}  — e.g. "Car Rental in Chennai"
    // ──────────────────────────────────────────────────────────────────────────

    public function cityDetail(string $slug)
    {
        $city = CarCity::with([
            'details', 'faqs', 'meta', 'features', 'benefits', 'galleryImages', 'highlights',
        ])->where('slug', $slug)->where('is_active', 1)->first();

        if (!$city) {
            return invalidRequest();
        }

        $content = CarRentalContent::current()->load(['whyChooseStats', 'features', 'benefits']);
        $page = Page::find(6);

        $features = $city->features->count() ? $city->features : $content->features;
        $benefits = $city->benefits->count() ? $city->benefits : $content->benefits;

        $cityCars = $city->cars()->where('is_active', 1)->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Car Rental City Detail',
            'type'    => 'city',
            'data'    => [
                // 1. Banner
                'banner' => [
                    'title'     => $city->details?->title ?: $page?->title,
                    'image'     => $city->details?->banner_image ? storage_link($city->details->banner_image) : ($page?->banner_image ? storage_link($page->banner_image) : null),
                    'image_alt' => $city->details?->banner_image_alt ?: $page?->banner_image_alt,
                ],

                // 2. Short Description
                'short_description' => $city->details?->description,

                // 3. Gallery
                'gallery' => [
                    'title'       => $city->details?->gallery_title,
                    'description' => $city->details?->gallery_description,
                    'images'      => $city->galleryImages->map(fn ($img) => [
                        'image'     => storage_link($img->image),
                        'image_alt' => $img->image_alt,
                    ])->values(),
                ],

                // 4. Highlights
                'highlights' => [
                    'title' => 'Highlights',
                    'items' => $city->highlights->map(fn ($h) => [
                        'title'       => $h->title,
                        'description' => $h->description,
                    ])->values(),
                ],

                // 5. Features & Benefits
                'features' => [
                    'title' => $city->features_title ?: $content->features_title,
                    'items' => $features->pluck('text')->values(),
                ],

                'benefits' => [
                    'title' => $city->benefits_title ?: $content->benefits_title,
                    'items' => $benefits->pluck('text')->values(),
                ],

                // 6. Fleets with tabs
                'fleet' => $this->fleetSection($cityCars),

                // 7. Why Choose Us — shown only when enabled for this city; title/description/stats are global (shared)
                'why_choose' => $city->why_choose_enabled ? [
                    'title'       => $content->why_choose_title,
                    'description' => $content->why_choose_description,
                    'stats'       => $content->whyChooseStats->map(fn ($stat) => [
                        'icon'  => $stat->icon ? storage_link($stat->icon) : null,
                        'label' => $stat->label,
                    ])->values(),
                ] : null,

                // 8. Popular Routes
                'routes' => [
                    'title' => 'Popular Car Rental Routes',
                    'items' => $this->popularRoutesItems(),
                ],

                // 9. Popular Destinations
                'destination' => [
                    'title' => 'Popular Destinations',
                    'items' => $this->popularDestinationsItems(),
                ],

                // 10. Popular Car Rental Packages
                'car_rental_packages' => [
                    'title' => 'Popular Car Rental Packages',
                    'items' => $this->popularPackagesItems(),
                ],

                // 11. Popular Locations — global (shared across every city)
                'popular_locations' => [
                    'title'       => $content->popular_locations_title,
                    'description' => $content->popular_locations_description,
                    'items'       => $this->popularCitiesItems(),
                ],

                // 12. FAQs (title + sub_title)
                'faqs' => [
                    'title'     => $city->faq_title,
                    'sub_title' => $city->faq_sub_title,
                    'items'     => $city->faqs->map(fn ($f) => [
                        'question' => $f->question,
                        'answer'   => $f->answer,
                    ])->values(),
                ],

                // 13. Meta
                'meta' => [
                    'meta_title'       => $city->meta?->meta_title,
                    'meta_description' => $city->meta?->meta_description,
                    'meta_keywords'    => $city->meta?->meta_keywords,
                    'h1_heading'       => $city->meta?->h1_heading,
                    'meta_details'     => $city->meta?->meta_details,
                ],

                // 14. Road Trips
                'road_trips' => [
                    'title'    => $content->road_trip_title,
                    'subtitle' => $content->road_trip_subtitle,
                    'items'    => $this->roadTripItems(),
                ],
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/car-rental/{slug}  — auto-detects City-wise / Destination-wise
    // ──────────────────────────────────────────────────────────────────────────

    public function resolve(string $slug)
    {
        if (CarCity::where('slug', $slug)->where('is_active', 1)->exists()) {
            return $this->cityDetail($slug);
        }

        if (CarDestination::where('slug', $slug)->where('is_active', 1)->exists()) {
            return $this->destinationDetail($slug);
        }

        return invalidRequest();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Route-wise  — e.g. "Cochin to Munnar"
    // ──────────────────────────────────────────────────────────────────────────

    public function routeDetail(string $slug)
    {
        $route = CarRoute::with(['details', 'faqs', 'meta', 'highlights'])
            ->where('slug', $slug)->where('is_active', 1)->first();

        if (!$route) {
            return invalidRequest();
        }

        $content = CarRentalContent::current()->load(['whyChooseStats']);
        $page = Page::find(6);

        $routeCars = $route->cars()->where('is_active', 1)->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Car Rental Route Detail',
            'type'    => 'route',
            'data'    => [
                // 1. Banner Details
                'banner' => [
                    'title'     => $route->details?->title ?: $page?->title,
                    'image'     => $route->details?->banner_image ? storage_link($route->details->banner_image) : ($page?->banner_image ? storage_link($page->banner_image) : null),
                    'image_alt' => $route->details?->banner_image_alt ?: $page?->banner_image_alt,
                ],

                // 2. Short Description
                'short_description' => $route->details?->description,
                'from_location'      => $route->from_location,
                'to_location'        => $route->to_location,

                // 3. About {From} to {To} Road Trip
                'about' => [
                    'title'       => $route->details?->about_title ?: ('About ' . $route->from_location . ' to ' . $route->to_location . ' Road Trip'),
                    'image'       => $route->details?->about_image ? storage_link($route->details->about_image) : null,
                    'image_alt'   => $route->details?->about_image_alt,
                    'description' => $route->details?->about_description,
                    'stats'       => [
                        'distance'    => $route->details?->distance_text,
                        'duration'    => $route->details?->duration_text,
                        'route'       => $route->details?->route_number,
                        'best_season' => $route->details?->best_season,
                    ],
                ],

                // 4. Route Highlights
                'highlights' => [
                    'title' => 'Route Highlights',
                    'items' => $route->highlights->map(fn ($h) => [
                        'title'       => $h->title,
                        'description' => $h->description,
                    ])->values(),
                ],

                // 5. Popular Routes — other popular routes from the same source city
                'popular_routes' => [
                    'title' => 'Popular ' . $route->from_location . ' Car Rental Routes',
                    'items' => $this->popularRoutesFromSameCity($route),
                ],

                // 6. Fleets with tabs
                'fleet' => $this->fleetSection($routeCars),

                // 7. Why Choose Us (global)
                'why_choose' => [
                    'title'       => $content->why_choose_title,
                    'description' => $content->why_choose_description,
                    'stats'       => $content->whyChooseStats->map(fn ($stat) => [
                        'icon'  => $stat->icon ? storage_link($stat->icon) : null,
                        'label' => $stat->label,
                    ])->values(),
                ],

                // 8. Popular Locations (global)
                'popular_locations' => [
                    'title'       => $content->popular_locations_title,
                    'description' => $content->popular_locations_description,
                    'items'       => $this->popularCitiesItems(),
                ],

                // 9. FAQs
                'faqs' => [
                    'title'     => $route->faq_title,
                    'sub_title' => $route->faq_sub_title,
                    'items'     => $route->faqs->map(fn ($f) => [
                        'question' => $f->question,
                        'answer'   => $f->answer,
                    ])->values(),
                ],

                // 10. Meta
                'meta' => [
                    'meta_title'       => $route->meta?->meta_title,
                    'meta_description' => $route->meta?->meta_description,
                    'meta_keywords'    => $route->meta?->meta_keywords,
                    'h1_heading'       => $route->meta?->h1_heading,
                    'meta_details'     => $route->meta?->meta_details,
                ],

                // 11. Road Trips (title/subtitle global)
                'road_trips' => [
                    'title'    => $content->road_trip_title,
                    'subtitle' => $content->road_trip_subtitle,
                    'items'    => $this->roadTripItems(),
                ],
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Package-wise  — e.g. "Golden Triangle Tour"
    // ──────────────────────────────────────────────────────────────────────────

    public function packageDetail(string $slug)
    {
        $package = CarPackage::with(['faqs', 'stops.state', 'stops.location', 'amenities'])
            ->where('slug', $slug)->where('is_active', 1)->first();

        if (!$package) {
            return invalidRequest();
        }

        $content = CarRentalContent::current()->load(['whyChooseStats']);
        $page = Page::find(6);

        $packageCars = $package->cars()->where('is_active', 1)->get();

        $routeChain = $package->stops->pluck('name')->implode(' → ');

        return response()->json([
            'status'  => 'success',
            'message' => 'Car Rental Package Detail',
            'type'    => 'package',
            'data'    => [
                // 1. Banner
                'banner' => [
                    'title'     => $package->name ?: $page?->title,
                    'image'     => $package->banner_image ? storage_link($package->banner_image) : ($page?->banner_image ? storage_link($page->banner_image) : null),
                    'image_alt' => $package->banner_image_alt ?: $page?->banner_image_alt,
                ],

                // 2. Short Description
                'short_description' => $package->description,

                // 3. About (with Tour Overview stats)
                'about' => [
                    'title'       => $package->about_title ?: ('About the ' . $package->name),
                    'image'       => $package->about_image ? storage_link($package->about_image) : null,
                    'image_alt'   => $package->about_image_alt,
                    'description' => $package->about_description,
                    'stats'       => [
                        'duration'            => $package->duration_text,
                        'best_time_to_visit'  => $package->best_season,
                        'route'               => $routeChain,
                        'ideal_for'           => $package->ideal_for,
                    ],
                ],

                // 4. Route Highlights (grouped by stop)
                'highlights' => [
                    'title'  => 'Route Highlights',
                    'groups' => $package->stops->map(fn ($s) => [
                        'name'        => $s->name,
                        'attractions' => $s->attractions ?? [],
                    ])->values(),
                ],

                // 5. Fleets with tabs
                'fleet' => $this->fleetSection($packageCars),

                // 6. Why Choose Us
                'why_choose' => [
                    'title'       => $content->why_choose_title,
                    'description' => $content->why_choose_description,
                    'stats'       => $content->whyChooseStats->map(fn ($stat) => [
                        'icon'  => $stat->icon ? storage_link($stat->icon) : null,
                        'label' => $stat->label,
                    ])->values(),
                ],

                // 7. Features & Amenities — managed per-package
                'features' => [
                    'title' => 'Features & Amenities',
                    'items' => $package->amenities->map(fn ($a) => [
                        'icon'        => $a->icon ? storage_link($a->icon) : null,
                        'label'       => $a->label,
                        'description' => $a->description,
                    ])->values(),
                ],

                // 8. Popular Routes
                'routes' => [
                    'title' => 'Popular Car Rental Routes',
                    'items' => $this->popularRoutesItems(),
                ],

                // 9. Popular Locations (global)
                'popular_locations' => [
                    'title'       => $content->popular_locations_title,
                    'description' => $content->popular_locations_description,
                    'items'       => $this->popularCitiesItems(),
                ],

                // 10. Popular Destinations
                'destination' => [
                    'title' => 'Popular Destinations',
                    'items' => $this->popularDestinationsItems(),
                ],

                // 11. FAQs
                'faqs' => [
                    'title'     => $package->faq_title,
                    'sub_title' => $package->faq_sub_title,
                    'items'     => $package->faqs->map(fn ($f) => [
                        'question' => $f->question,
                        'answer'   => $f->answer,
                    ])->values(),
                ],

                // 12. Meta
                'meta' => [
                    'meta_title'       => $package->meta_title,
                    'meta_description' => $package->meta_description,
                    'meta_keywords'    => $package->meta_keywords,
                    'h1_heading'       => $package->h1_heading,
                    'meta_details'     => $package->meta_details,
                ],

                // 13. Road Trips (title/subtitle global)
                'road_trips' => [
                    'title'    => $content->road_trip_title,
                    'subtitle' => $content->road_trip_subtitle,
                    'items'    => $this->roadTripItems(),
                ],
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Destination-wise  — e.g. "Jaipur Palaces"
    // ──────────────────────────────────────────────────────────────────────────

    public function destinationDetail(string $slug)
    {
        $destination = CarDestination::with(['faqs', 'highlights', 'state', 'location'])
            ->where('slug', $slug)->where('is_active', 1)->first();

        if (!$destination) {
            return invalidRequest();
        }

        $content = CarRentalContent::current()->load(['whyChooseStats']);
        $page = Page::find(6);

        $destinationCars = $destination->cars()->where('is_active', 1)->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Car Rental Destination Detail',
            'type'    => 'destination',
            'data'    => [
                'banner' => [
                    'title'     => $destination->name ?: $page?->title,
                    'image'     => $destination->banner_image ? storage_link($destination->banner_image) : ($page?->banner_image ? storage_link($page->banner_image) : null),
                    'image_alt' => $destination->banner_image_alt ?: $page?->banner_image_alt,
                ],

                'short_description' => $destination->description,
                'state_name'         => $destination->state?->name,
                'city_name'          => $destination->location?->name,

                'about' => [
                    'title'       => $destination->about_title ?: ('About ' . $destination->name),
                    'image'       => $destination->about_image ? storage_link($destination->about_image) : null,
                    'image_alt'   => $destination->about_image_alt,
                    'description' => $destination->about_description,
                    'stats'       => [
                        'distance'    => $destination->distance_text,
                        'duration'    => $destination->duration_text,
                        'ideal_for'   => $destination->ideal_for,
                        'best_season' => $destination->best_season,
                    ],
                ],

                'highlights' => [
                    'title' => 'Highlights',
                    'items' => $destination->highlights->map(fn ($h) => [
                        'title'       => $h->title,
                        'description' => $h->description,
                    ])->values(),
                ],

                'why_choose' => [
                    'title'       => $content->why_choose_title,
                    'description' => $content->why_choose_description,
                    'stats'       => $content->whyChooseStats->map(fn ($stat) => [
                        'icon'  => $stat->icon ? storage_link($stat->icon) : null,
                        'label' => $stat->label,
                    ])->values(),
                ],

                'routes' => [
                    'title' => 'Popular Car Rental Routes',
                    'items' => $this->popularRoutesItems(),
                ],

                'popular_locations' => [
                    'title'       => $content->popular_locations_title,
                    'description' => $content->popular_locations_description,
                    'items'       => $this->popularCitiesItems(),
                ],

                'destination' => [
                    'title' => 'Popular Destinations',
                    'items' => $this->popularDestinationsItems(),
                ],

                'car_rental_packages' => [
                    'title' => 'Popular Car Rental Packages',
                    'items' => $this->popularPackagesItems(),
                ],

                'fleet' => $this->fleetSection($destinationCars),

                'road_trips' => [
                    'title'    => $content->road_trip_title,
                    'subtitle' => $content->road_trip_subtitle,
                    'items'    => $this->roadTripItems(),
                ],

                'faqs' => [
                    'title'     => $destination->faq_title,
                    'sub_title' => $destination->faq_sub_title,
                    'items'     => $destination->faqs->map(fn ($f) => [
                        'question' => $f->question,
                        'answer'   => $f->answer,
                    ])->values(),
                ],

                'meta' => [
                    'meta_title'       => $destination->meta_title,
                    'meta_description' => $destination->meta_description,
                    'meta_keywords'    => $destination->meta_keywords,
                    'h1_heading'       => $destination->h1_heading,
                    'meta_details'     => $destination->meta_details,
                ],
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Fleet Type  — e.g. "Mini Coach 18-Seater"
    // ──────────────────────────────────────────────────────────────────────────

    public function categoryDetail(string $slug)
    {
        $category = CarCategory::where('slug', $slug)
            ->where('is_active', 1)->first();

        if (!$category) {
            return invalidRequest();
        }

        $content = CarRentalContent::current()->load(['whyChooseStats']);
        $page = Page::find(6);

        $categoryCars = Car::where('category_id', $category->id)
            ->where('is_active', 1)->with('category')
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Car Rental Fleet Type Detail',
            'type'    => 'category',
            'data'    => [
                'banner' => [
                    'title'     => $category->name . ' Car Rental',
                    'image'     => $category->icon ? storage_link($category->icon) : ($page?->banner_image ? storage_link($page->banner_image) : null),
                    'image_alt' => $category->icon_alt ?: $page?->banner_image_alt,
                ],

                'category' => [
                    'name' => $category->name,
                    'slug' => $category->slug,
                ],

                'cars' => CarResource::collection($categoryCars->values()),

                'fleet' => $this->fleetSection($categoryCars),

                'why_choose' => [
                    'title'       => $content->why_choose_title,
                    'description' => $content->why_choose_description,
                    'stats'       => $content->whyChooseStats->map(fn ($stat) => [
                        'icon'  => $stat->icon ? storage_link($stat->icon) : null,
                        'label' => $stat->label,
                    ])->values(),
                ],

                'routes' => [
                    'title' => 'Popular Car Rental Routes',
                    'items' => $this->popularRoutesItems(),
                ],

                'popular_locations' => [
                    'title'       => $content->popular_locations_title,
                    'description' => $content->popular_locations_description,
                    'items'       => $this->popularCitiesItems(),
                ],

                'destination' => [
                    'title' => 'Popular Destinations',
                    'items' => $this->popularDestinationsItems(),
                ],

                'car_rental_packages' => [
                    'title' => 'Popular Car Rental Packages',
                    'items' => $this->popularPackagesItems(),
                ],

                'faqs' => [
                    'title' => $page?->faq_title,
                    'items' => $page ? $page->faqs->map(fn ($f) => [
                        'question' => $f->question,
                        'answer'   => $f->answer,
                    ])->values() : [],
                ],

                'road_trips' => [
                    'title'    => $content->road_trip_title,
                    'subtitle' => $content->road_trip_subtitle,
                    'items'    => $this->roadTripItems(),
                ],

                'meta' => [
                    'meta_title'       => $category->name . ' Car Rental | Indian Panorama',
                    'meta_description' => 'Book a ' . $category->name . ' for your next trip with an experienced driver, available for both city rides and outstation travel.',
                    'meta_keywords'    => null,
                    'h1_heading'       => $category->name . ' Car Rental',
                    'meta_details'     => null,
                ],
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/car-rental/vehicle/{slug}  — e.g. "Maruti Suzuki Ertiga"
    // ──────────────────────────────────────────────────────────────────────────

    public function vehicleDetail(string $slug)
    {
        $car = Car::with(['category', 'galleryImages', 'highlightTags', 'amenities'])
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->first();

        if (!$car) {
            return invalidRequest();
        }

        $content = CarRentalContent::current()->load('whyChooseStats');
        $page = Page::find(6);

        // The fleet section shows every active car here, same as the landing page.
        $allCars = Car::where('is_active', 1)
            ->with('category')
            ->orderBy(list_config()['order_by'], list_config()['direction'])
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Car Rental Vehicle Detail',
            'type'    => 'vehicle',
            'data'    => [
                // 1. Banner
                'banner' => [
                    'title'     => $car->title ?: $page?->title,
                    'image'     => $car->banner_image ? storage_link($car->banner_image) : ($page?->banner_image ? storage_link($page->banner_image) : null),
                    'image_alt' => $car->banner_image_alt ?: $page?->banner_image_alt,
                ],

                // 2. Short Description
                'short_description' => $car->description,

                // 3. About the Car (gallery + highlight tags)
                'about_car' => [
                    'title'       => $car->title,
                    'image'       => $car->primary_image ? storage_link($car->primary_image) : null,
                    'image_alt'   => $car->primary_image_alt,
                    'description' => $car->description,
                    'gallery'     => [
                        'title'       => $car->gallery_title,
                        'description' => $car->gallery_description,
                        'images'      => $car->galleryImages->map(fn ($img) => [
                            'image'     => storage_link($img->image),
                            'image_alt' => $img->image_alt,
                        ])->values(),
                    ],
                    'highlights'  => $car->highlightTags->map(fn ($h) => [
                        'icon' => $h->icon ? storage_link($h->icon) : null,
                        'text' => $h->text,
                    ])->values(),
                ],

                // 4. Features & Amenities — managed per-car
                'features' => [
                    'title' => 'Features & Amenities',
                    'items' => $car->amenities->map(fn ($a) => [
                        'icon'        => $a->icon ? storage_link($a->icon) : null,
                        'label'       => $a->label,
                        'description' => $a->description,
                    ])->values(),
                ],

                // 5. Vehicle Specifications
                'specification' => [
                    'title'       => $car->specs_title,
                    'description' => $car->specs_description,
                    'items'       => [
                        'vehicle_type'     => $car->vehicle_type,
                        'model'            => $car->title,
                        'transmission'     => $car->transmission,
                        'seating_capacity' => $car->seats,
                        'luggage_capacity' => $car->luggage_capacity,
                        'mileage'          => $car->mileage,
                        'fuel_type'        => $car->fuel_type,
                    ],
                ],

                // 6. Fleets with tabs
                'fleet' => $this->fleetSection($allCars),

                // 7. Why Choose Us (global)
                'why_choose' => [
                    'title'       => $content->why_choose_title,
                    'description' => $content->why_choose_description,
                    'stats'       => $content->whyChooseStats->map(fn ($stat) => [
                        'icon'  => $stat->icon ? storage_link($stat->icon) : null,
                        'label' => $stat->label,
                    ])->values(),
                ],

                // 8. Popular Locations (global)
                'popular_locations' => [
                    'title'       => $content->popular_locations_title,
                    'description' => $content->popular_locations_description,
                    'items'       => $this->popularCitiesItems(),
                ],

                // 9. FAQs
                'faqs' => [
                    'title' => $page?->faq_title,
                    'items' => $page ? $page->faqs->map(fn ($f) => [
                        'question' => $f->question,
                        'answer'   => $f->answer,
                    ])->values() : [],
                ],

                // 10. Meta
                'meta' => [
                    'meta_title'       => $car->title . ' Car Rental | Indian Panorama',
                    'meta_description' => $car->description,
                    'meta_keywords'    => null,
                    'h1_heading'       => $car->title,
                    'meta_details'     => null,
                ],

                // 11. Road Trips (title/subtitle global)
                'road_trips' => [
                    'title'    => $content->road_trip_title,
                    'subtitle' => $content->road_trip_subtitle,
                    'items'    => $this->roadTripItems(),
                ],
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/car-rental
    // ──────────────────────────────────────────────────────────────────────────

    public function landingPage()
    {
        $page = Page::with(['faqs', 'meta'])->find(6);
        $content = CarRentalContent::current()->load(['galleryImages', 'whyChooseStats']);

        // The landing page is the one place every active car shows up, regardless of
        // any per-page admin curation.
        $allCars = Car::where('is_active', 1)
            ->with('category')
            ->orderBy(list_config()['order_by'], list_config()['direction'])
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Car Rental Landing Page',
            'type'    => 'landing',
            'data'    => [
                'banner' => [
                    'title'     => $page?->title,
                    'image'     => $page?->banner_image ? storage_link($page->banner_image) : null,
                    'image_alt' => $page?->banner_image_alt,
                ],

                'short_description' => $page?->description,

                'fleet' => $this->fleetSection($allCars),

                'long_description' => $content->short_description,

                // Managed via the admin "Gallery" action (Page Setting tab).
                'gallery' => [
                    'title'       => $content->gallery_title,
                    'description' => $content->gallery_description,
                    'images'      => $content->galleryImages->map(fn ($img) => [
                        'image'     => storage_link($img->image),
                        'image_alt' => $img->image_alt,
                    ])->values(),
                ],

                'why_choose' => [
                    'title'       => $content->why_choose_title,
                    'description' => $content->why_choose_description,
                    'stats'       => $content->whyChooseStats->map(fn ($stat) => [
                        'icon'  => $stat->icon ? storage_link($stat->icon) : null,
                        'label' => $stat->label,
                    ])->values(),
                ],

                'routes' => [
                    'title' => 'Popular Car Rental Routes',
                    'items' => $this->popularRoutesItems(),
                ],

                'popular_locations' => [
                    'title'       => $content->popular_locations_title,
                    'description' => $content->popular_locations_description,
                    'items'       => $this->popularCitiesItems(),
                ],

                'destination' => [
                    'title' => 'Popular Destinations',
                    'items' => $this->popularDestinationsItems(),
                ],

                'car_rental_packages' => [
                    'title' => 'Popular Car Rental Packages',
                    'items' => $this->popularPackagesItems(),
                ],

                'faqs' => [
                    'title' => $page?->faq_title,
                    'items' => $page ? $page->faqs->map(fn ($f) => [
                        'question' => $f->question,
                        'answer'   => $f->answer,
                    ])->values() : [],
                ],

                'road_trips' => [
                    'title'    => $content->road_trip_title,
                    'subtitle' => $content->road_trip_subtitle,
                    'items'    => $this->roadTripItems(),
                ],

                'meta' => [
                    'meta_title'       => $page?->meta?->meta_title,
                    'meta_description' => $page?->meta?->meta_description,
                    'meta_keywords'    => $page?->meta?->meta_keywords,
                    'h1_heading'       => $page?->meta?->h1_heading,
                    'meta_details'     => $page?->meta?->meta_details,
                ],
            ],
        ]);
    }

    /**
     * "Our Best Batch Of Fleets" — pre-grouped by category so the frontend can build
     * tabs directly: an "All" tab with every car, plus one tab per active category.
     *
     * Only includes cars when the caller passes an explicitly admin-curated list
     * (e.g. the cars synced to a city/route/package/destination, or every car for
     * the landing page). Without one, the fleet is empty rather than silently
     * falling back to every car in the system.
     */
    private function fleetSection(?\Illuminate\Support\Collection $scopedCars = null): array
    {
        $categories = CarCategory::where('is_active', 1)->get();

        $cars = $scopedCars ?? collect();

        $tabs = collect();

        if ($cars->isNotEmpty()) {
            $tabs->push([
                'name' => 'All',
                'slug' => 'all',
                'cars' => CarResource::collection($cars->values()),
            ]);
        }

        foreach ($categories as $category) {
            $categoryCars = $cars->where('category_id', $category->id)->values();
            if ($categoryCars->isEmpty()) {
                continue;
            }
            $tabs->push([
                'name' => $category->name,
                'slug' => $category->slug,
                'cars' => CarResource::collection($categoryCars),
            ]);
        }

        return ['categories' => $tabs->values()];
    }

    /** "Popular Road Trip Destinations" — shared across the landing/city/vehicle pages. */
    private function roadTripItems(): \Illuminate\Support\Collection
    {
        return CarRentalRoadTrip::with('state')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($trip) => [
                'state'           => $trip->state->name ?? null,
                'image'           => $trip->image ? storage_link($trip->image) : null,
                'image_alt'       => $trip->image_alt,
                'rating'          => (float) $trip->rating,
                'route_text'      => $trip->route_text,
                'duration_days'   => $trip->duration_days,
                'duration_nights' => $trip->duration_nights,
            ])->values();
    }

    /** "Popular Car Rental Routes" — popular CarRoute entries. */
    private function popularRoutesItems(): \Illuminate\Support\Collection
    {
        return CarRoute::where('is_active', 1)->where('is_popular', 1)
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($r) => [
                'label' => $r->display_label ?: ($r->from_location . ' to ' . $r->to_location . ' Car Rental'),
                'slug'  => $r->slug,
            ])->values();
    }

    /** Popular routes from the same source city, excluding the route itself — e.g. on "Kochi to Munnar" show other "Kochi to ..." routes. */
    private function popularRoutesFromSameCity(CarRoute $route): \Illuminate\Support\Collection
    {
        return CarRoute::where('is_active', 1)
            ->where('is_popular', 1)
            ->where('from_location', $route->from_location)
            ->where('id', '!=', $route->id)
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($r) => [
                'label' => $r->display_label ?: ($r->from_location . ' to ' . $r->to_location . ' Car Rental'),
                'slug'  => $r->slug,
            ])->values();
    }

    /** "Popular Car Rental Locations" — popular CarCity entries. */
    private function popularCitiesItems(): \Illuminate\Support\Collection
    {
        return CarCity::where('is_active', 1)->where('is_popular', 1)
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($c) => [
                'label' => $c->display_label ?: ('Car Rental in ' . $c->location),
                'slug'  => $c->slug,
            ])->values();
    }

    /** "Popular Destinations" — popular CarDestination entries. */
    private function popularDestinationsItems(): \Illuminate\Support\Collection
    {
        return CarDestination::where('is_active', 1)->where('is_popular', 1)
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($d) => [
                'label' => $d->name,
                'slug'  => $d->slug,
            ])->values();
    }

    /** "Popular Car Rental Packages" — popular CarPackage entries. */
    private function popularPackagesItems(): \Illuminate\Support\Collection
    {
        return CarPackage::where('is_active', 1)->where('is_popular', 1)
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($p) => [
                'label' => $p->name,
                'slug'  => $p->slug,
            ])->values();
    }
}
