<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\PackageResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\RegionResource;
use App\Http\Resources\GroupTourPackageResource;
use App\Models\Package;
use App\Models\PackageLocation;
use App\Models\Location;
use App\Models\Country;
use App\Models\Region;
use App\Models\State;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    // "Popular"/"Special" are hit on nearly every page load (widgets, homepage
    // sections) and previously ran an uncached query every request — the one gap
    // versus Home/HeaderMenu, which are cached. Short TTL keeps admin edits
    // showing up quickly while still absorbing traffic bursts without hitting
    // the DB on every single request.
    public const POPULAR_CACHE_KEY = 'api_popular_packages_v1';
    public const SPECIAL_CACHE_KEY = 'api_special_packages_v1';
    public const CACHE_TTL = 60; // seconds

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/packages/popular
    //
    // Query params (all optional):
    //   limit          — number of results (default from list_config)
    //   state          — filter by state slug
    //   location       — filter by location slug
    //   category_slug  — filter by category slug
    // ──────────────────────────────────────────────────────────────────────────

    public function popularPackages(Request $r)
    {
        $limit     = (int) $r->get('limit', list_config()['limit']);
        $orderBy   = list_config()['order_by'];
        $direction = list_config()['direction'];

        $cacheKey = self::POPULAR_CACHE_KEY . '_' . md5(json_encode([
            $limit, $r->get('state'), $r->get('location'), $r->get('category_slug'),
        ]));

        $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($r, $limit, $orderBy, $direction) {
            $packages = Package::where('is_active', 1)
                ->where('is_top_trending', 1)
                ->when($r->filled('state'), fn ($q) =>
                    $q->where(fn ($q2) =>
                        $q2->whereHas('location', fn ($ql) => $ql->whereHas('state', fn ($qs) => $qs->where('slug', $r->state)))
                           ->orWhereHas('extraDestinations.location', fn ($ql) => $ql->whereHas('state', fn ($qs) => $qs->where('slug', $r->state)))
                    )
                )
                ->when($r->filled('location'), fn ($q) =>
                    $q->where(fn ($q2) =>
                        $q2->whereHas('location', fn ($ql) => $ql->where('slug', $r->location))
                           ->orWhereHas('extraDestinations.location', fn ($ql) => $ql->where('slug', $r->location))
                    )
                )
                ->when($r->filled('category_slug'), fn ($q) =>
                    $q->whereHas('category', fn ($qc) => $qc->where('slug', $r->category_slug))
                )
                ->with(['images', 'category', 'details', 'location.country', 'reviews'])
                ->withAvg('reviews', 'rating')
                ->withCount('reviews')
                ->orderBy($orderBy, $direction)
                ->limit($limit)
                ->get();

            return PackageResource::collection($packages)->resolve();
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Popular Packages',
            'data'    => $data,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/packages/special
    // ──────────────────────────────────────────────────────────────────────────

    public function specialPackages()
    {
        $data = Cache::remember(self::SPECIAL_CACHE_KEY, self::CACHE_TTL, function () {
            $packages = Package::where('is_active', 1)
                ->where('is_special_package', 1)
                ->with(['images', 'category', 'details', 'location.country', 'reviews'])
                ->withAvg('reviews', 'rating')
                ->orderBy(list_config()['order_by'], list_config()['direction'])
                ->limit(6)
                ->get();

            return PackageResource::collection($packages)->resolve();
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Listing',
            'data'    => $data,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/packages/group-tour
    // ──────────────────────────────────────────────────────────────────────────

    public function GroupTourPackages(Request $r)
    {
        $limit     = $r->get('limit', list_config()['limit']);
        $page      = $r->get('page', 1);
        $orderBy   = list_config()['order_by'];
        $direction = list_config()['direction'];

        $query = Package::where('is_active', 1)
            ->where('package_mode', 'group_tour')
            ->when($r->filled('country'), fn ($q) =>
                $q->whereHas('country', fn ($qc) => $qc->where('slug', $r->country))
            )
            ->when($r->filled('location'), fn ($q) =>
                $q->whereHas('location', fn ($ql) => $ql->where('slug', $r->location))
            )
            ->when($r->filled('category_slug'), fn ($q) =>
                $q->whereHas('category', fn ($qc) => $qc->where('slug', $r->category_slug))
            );

        $total = (clone $query)->count();

        $packages = $query
            ->with(['details', 'reviews', 'location.country', 'groupDepartures', 'images', 'category', 'extraDestinations'])
            ->withAvg('reviews', 'rating')
            ->orderBy($orderBy, $direction)
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Listing',
            'data'    => [
                'packages'   => GroupTourPackageResource::collection($packages),
                'pagination' => ['total' => $total, 'page' => (int) $page, 'limit' => (int) $limit],
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/packages/group-tour/{slug}
    // ──────────────────────────────────────────────────────────────────────────

    public function groupTourDetails(Request $r)
    {
        $package = Package::where('slug', $r->slug)
            ->where('is_active', 1)
            ->where('package_mode', 'group_tour')
            ->with(['images', 'details', 'itineraries', 'location.country', 'groupDepartures', 'faqs', 'meta'])
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', 1)])
            ->withAvg(['reviews' => fn ($q) => $q->where('is_approved', 1)], 'rating')
            ->firstOrFail();

        $similar = Package::where('id', '!=', $package->id)
            ->where('is_active', 1)
            ->where('category_id', $package->category_id)
            ->where('country_id', $package->country_id)
            ->with(['images', 'details', 'location.country', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->take(3)
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Details',
            'data'    => [
                'package'          => new PackageResource($package),
                'similar_packages' => PackageResource::collection($similar),
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/packages/top-trending
    // ──────────────────────────────────────────────────────────────────────────

    public function topTrendingCountryPackages()
    {
        $packages = Package::where('is_active', 1)
            ->where('country_id', 1)
            ->where('is_top_trending', 1)
            ->with(['images', 'category', 'details', 'location.country', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->orderBy(list_config()['order_by'], list_config()['direction'])
            ->limit(4)
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Listing',
            'data'    => PackageResource::collection($packages),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/packages/discover-india
    // ──────────────────────────────────────────────────────────────────────────

    public function discoverIndiaTourPackages()
    {
        $packages = Package::where('is_active', 1)
            ->where('country_id', 1)
            ->with(['images', 'category', 'details', 'location.country'])
            ->withAvg('reviews', 'rating')
            ->orderBy(list_config()['order_by'], list_config()['direction'])
            ->get();

        // Unique locations, limited to 5
        $result = $packages->unique('location_id')->take(5)->values();

        return response()->json([
            'status'  => 'success',
            'message' => 'Listing',
            'data'    => PackageResource::collection($result),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/packages/city/{slug}  — resolves: region / country / location
    // ──────────────────────────────────────────────────────────────────────────

    public function packagesResolver(Request $r)
    {
        $slug = Str::lower($r->slug);
        $r->merge(['slug' => $slug]);

        // 1. Region (DB lookup first)
        $region = Region::where('slug', $slug)->with(['details', 'faqs', 'meta'])->first();
        if ($region) {
            return $this->packagesByRegionDB($r, $region);
        }

        // 2. Country
        $response = $this->packagesByCountry($r);

        // 3. Location fallback
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            if (($data['status'] ?? '') === 'failed') {
                return $this->packagesByLoction($r);
            }
        }

        return $response;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/packages/region/{slug}  — e.g. "north-india", "south-india"
    // ──────────────────────────────────────────────────────────────────────────

    public function packagesByRegion(Request $r, string $slug)
    {
        $limit     = $r->get('limit', list_config()['limit']);
        $page      = $r->get('page', 1);
        $orderBy   = list_config()['order_by'];
        $direction = list_config()['direction'];

        $region = Region::with(['details', 'faqs', 'meta'])
            ->where('slug', $slug)->first();

        if (!$region) {
            return invalidRequest();
        }

        $query = Package::where('is_active', 1)
            ->where(fn ($q) =>
                $q->whereHas('location', fn ($qq) => $qq->where('region_id', $region->id)->where('is_active', 1))
                  ->orWhereHas('extraDestinations.location', fn ($qq) => $qq->where('region_id', $region->id)->where('is_active', 1))
            )
            ->when($r->filled('category_slug'), fn ($q) =>
                $q->whereHas('category', fn ($qc) => $qc->where('slug', $r->category_slug))
            );

        $total    = (clone $query)->count();
        $packages = $query
            ->with('details')
            ->orderBy($orderBy, $direction)
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Region Listing',
            'data'    => [
                'region'             => ['name' => $region->name, 'slug' => $region->slug],
                'banner'             => $this->bannerDetails($region->details),
                'short_description'  => $region->details?->about,
                'packages'           => $this->lightPackages($packages),
                'pagination'         => ['total' => $total, 'page' => (int) $page, 'limit' => (int) $limit],
                'top_destinations'   => $this->statesForRegion($region->id),
                'faqs'               => $this->faqSection($region->faq_title, $region->faqs),
                'meta'               => $this->metaSection($region->meta),
            ],
        ]);
    }

    /**
     * "Top Destinations" — states under this region that actually have at least one
     * active package (directly, or via a city belonging to the state — e.g. Kerala
     * still shows if only its city Munnar has packages, not Kerala itself).
     */
    private function statesForRegion(int $regionId): \Illuminate\Support\Collection
    {
        return State::active()
            ->forRegion($regionId)
            ->where(fn ($q) => $q
                ->whereIn('id', fn ($sub) => $sub
                    ->select('locations.state_id')
                    ->from('packages')
                    ->join('locations', 'locations.id', '=', 'packages.location_id')
                    ->where('packages.is_active', 1))
                ->orWhereIn('id', fn ($sub) => $sub
                    ->select('locations.state_id')
                    ->from('package_locations')
                    ->join('packages', 'packages.id', '=', 'package_locations.package_id')
                    ->join('locations', 'locations.id', '=', 'package_locations.location_id')
                    ->where('packages.is_active', 1)
                    ->whereNull('package_locations.deleted_at')))
            ->with('details')
            ->get()
            ->map(fn ($state) => [
                'name'      => $state->name,
                'slug'      => $state->slug,
                'image'     => $state->details?->banner_image ? storage_link($state->details->banner_image) : null,
                'image_alt' => $state->details?->banner_image_alt,
            ])
            ->values();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/packages/state/{slug}
    // ──────────────────────────────────────────────────────────────────────────

    public function packagesByState(Request $r, string $slug)
    {
        $limit     = $r->get('limit', list_config()['limit']);
        $page      = $r->get('page', 1);
        $orderBy   = list_config()['order_by'];
        $direction = list_config()['direction'];

        $state = State::with(['details', 'faqs', 'meta', 'bestTimes'])
            ->where('slug', $slug)->where('is_active', 1)->first();

        if (!$state) {
            return invalidRequest();
        }

        $query = Package::where('is_active', 1)
            ->where(fn ($q) =>
                $q->whereHas('location', fn ($qq) => $qq->where('state_id', $state->id)->where('is_active', 1))
                  ->orWhereHas('extraDestinations.location', fn ($qq) => $qq->where('state_id', $state->id)->where('is_active', 1))
            )
            ->when($r->filled('category_slug'), fn ($q) =>
                $q->whereHas('category', fn ($qc) => $qc->where('slug', $r->category_slug))
            );

        $total    = (clone $query)->count();
        $packages = $query
            ->with('details')
            ->orderBy($orderBy, $direction)
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'State Listing',
            'data'    => [
                'state'              => ['name' => $state->name, 'slug' => $state->slug],
                'banner'             => $this->bannerDetails($state->details),
                'short_description'  => $state->details?->about,
                'packages'           => $this->lightPackages($packages),
                'pagination'         => ['total' => $total, 'page' => (int) $page, 'limit' => (int) $limit],
                'top_destinations'   => $this->citiesForState($state->id),
                'best_time_to_visit' => $this->bestTimeToVisit($state->best_time_title, $state->bestTimes),
                'faqs'               => $this->faqSection($state->faq_title, $state->faqs),
                'popular_packages'   => $this->popularPackagesForState($state->id),
                'meta'               => $this->metaSection($state->meta),
            ],
        ]);
    }

    /** "Popular Tour Packages" — top-trending packages within a state, e.g. for a sidebar/footer widget. */
    private function popularPackagesForState(int $stateId, int $limit = 4): \Illuminate\Support\Collection
    {
        $packages = Package::where('is_active', 1)
            ->where('is_top_trending', 1)
            ->where(fn ($q) =>
                $q->whereHas('location', fn ($qq) => $qq->where('state_id', $stateId)->where('is_active', 1))
                  ->orWhereHas('extraDestinations.location', fn ($qq) => $qq->where('state_id', $stateId)->where('is_active', 1))
            )
            ->with('details')
            ->withAvg('reviews', 'rating')
            ->orderBy(list_config()['order_by'], list_config()['direction'])
            ->limit($limit)
            ->get();

        return $packages->map(fn ($p) => [
            'title'             => $p->title,
            'slug'              => $p->slug,
            'primary_image'     => $p->primary_image ? storage_link($p->primary_image) : null,
            'primary_image_alt' => $p->primary_image_alt,
            'duration_days'     => $p->details?->duration_days,
            'duration_nights'   => $p->details?->duration_nights,
            'rating'            => round($p->reviews_avg_rating ?? 0, 1),
        ])->values();
    }

    /** Trimmed package fields for state/city listing — title, slug, image, duration only. */
    private function lightPackages(\Illuminate\Support\Collection $packages): \Illuminate\Support\Collection
    {
        return $packages->map(fn ($p) => [
            'title'             => $p->title,
            'slug'              => $p->slug,
            'primary_image'     => $p->primary_image ? storage_link($p->primary_image) : null,
            'primary_image_alt' => $p->primary_image_alt,
            'duration_days'     => $p->details?->duration_days,
            'duration_nights'   => $p->details?->duration_nights,
        ])->values();
    }

    /** Page banner — title, sub-title, banner image — from {state|location}_details. */
    private function bannerDetails($details): array
    {
        return [
            'title'      => $details?->title,
            'sub_title'  => $details?->sub_title,
            'image'      => $details?->banner_image ? storage_link($details->banner_image) : null,
            'image_alt'  => $details?->banner_image_alt,
        ];
    }

    /** FAQs — section title + question/answer pairs. */
    private function faqSection(?string $title, \Illuminate\Support\Collection $faqs): array
    {
        return [
            'title' => $title,
            'items' => $faqs->map(fn ($f) => [
                'question' => $f->question,
                'answer'   => $f->answer,
            ])->values(),
        ];
    }

    /** SEO meta fields. */
    private function metaSection($meta): array
    {
        return [
            'meta_title'       => $meta?->meta_title,
            'meta_description' => $meta?->meta_description,
            'meta_keywords'    => $meta?->meta_keywords,
            'h1_heading'       => $meta?->h1_heading,
            'meta_details'     => $meta?->meta_details,
        ];
    }

    /** "Best Time to Visit" section — section title + month-range/tagline cards. */
    private function bestTimeToVisit(?string $title, \Illuminate\Support\Collection $bestTimes): array
    {
        return [
            'title' => $title,
            'items' => $bestTimes->map(fn ($b) => [
                'month_range' => $b->month_range,
                'tagline'     => $b->tagline,
            ])->values(),
        ];
    }

    /** "Top Destinations" — cities under this state that actually have at least one active package. */
    private function citiesForState(int $stateId): \Illuminate\Support\Collection
    {
        return Location::active()
            ->forState($stateId)
            ->where(fn ($q) => $q
                ->whereIn('id', fn ($sub) => $sub
                    ->select('location_id')
                    ->from('packages')
                    ->where('is_active', 1)
                    ->whereNotNull('location_id'))
                ->orWhereIn('id', fn ($sub) => $sub
                    ->select('package_locations.location_id')
                    ->from('package_locations')
                    ->join('packages', 'packages.id', '=', 'package_locations.package_id')
                    ->where('packages.is_active', 1)
                    ->whereNull('package_locations.deleted_at')))
            ->with('details')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($loc) => [
                'name'      => $loc->name,
                'slug'      => $loc->slug,
                'image'     => $loc->details?->banner_image ? storage_link($loc->details->banner_image) : null,
                'image_alt' => $loc->details?->banner_image_alt,
            ])
            ->values();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/packages/state/{slug}/city/{citySlug}
    // ──────────────────────────────────────────────────────────────────────────

    public function packagesByStateCity(Request $r, string $slug, string $citySlug)
    {
        $limit     = $r->get('limit', list_config()['limit']);
        $page      = $r->get('page', 1);
        $orderBy   = list_config()['order_by'];
        $direction = list_config()['direction'];

        $state = State::where('slug', $slug)->where('is_active', 1)->first();

        if (!$state) {
            return invalidRequest();
        }

        $location = Location::with(['details', 'faqs', 'meta', 'bestTimes'])
            ->where('slug', $citySlug)
            ->where('state_id', $state->id)
            ->where('is_active', 1)
            ->first();

        if (!$location) {
            return invalidRequest();
        }

        $query = Package::where('is_active', 1)
            ->where(fn ($q) =>
                $q->where('location_id', $location->id)
                  ->orWhereHas('extraDestinations', fn ($qq) => $qq->where('location_id', $location->id))
            )
            ->when($r->filled('category_slug'), fn ($q) =>
                $q->whereHas('category', fn ($qc) => $qc->where('slug', $r->category_slug))
            );

        $total    = (clone $query)->count();
        $packages = $query
            ->with('details')
            ->orderBy($orderBy, $direction)
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'State/City Listing',
            'data'    => [
                'state'              => ['name' => $state->name, 'slug' => $state->slug],
                'banner'             => $this->bannerDetails($location->details),
                'short_description'  => $location->details?->about,
                'packages'           => $this->lightPackages($packages),
                'pagination'         => ['total' => $total, 'page' => (int) $page, 'limit' => (int) $limit],
                'top_destinations'   => $this->citiesForState($state->id),
                'best_time_to_visit' => $this->bestTimeToVisit($location->best_time_title, $location->bestTimes),
                'faqs'               => $this->faqSection($location->faq_title, $location->faqs),
                'popular_packages'   => $this->popularPackagesForState($state->id),
                'meta'               => $this->metaSection($location->meta),
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/package/{slug}
    // ──────────────────────────────────────────────────────────────────────────

    public function packageDetails(Request $r)
    {
        $package = Package::where('slug', $r->slug)
            ->where('is_active', 1)
            ->with(['images', 'details', 'itineraries', 'faqs', 'meta'])
            ->firstOrFail();

        $similar = Package::where('id', '!=', $package->id)
            ->where('is_active', 1)
            ->where('category_id', $package->category_id)
            ->where('country_id', $package->country_id)
            ->with('details')
            ->withAvg('reviews', 'rating')
            ->take(3)
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Details',
            'data'    => [
                'banner' => [
                    'title'             => $package->title,
                    'slug'              => $package->slug,
                    'tour_highlights'   => $package->details?->tour_highlights,
                    'duration_days'     => $package->details?->duration_days,
                    'duration_nights'   => $package->details?->duration_nights,
                    'primary_image'     => $package->primary_image ? storage_link($package->primary_image) : null,
                    'primary_image_alt' => $package->primary_image_alt,
                    'images'            => $package->images->map(fn ($img) => [
                        'image_path' => $img->image_path ? storage_link($img->image_path) : null,
                        'image_alt'  => $img->image_alt,
                    ])->values(),
                ],

                'destination_covered' => [
                    'description' => $package->details?->destination_covered_description,
                    'items'       => PackageLocation::with('location')
                        ->where('package_id', $package->id)
                        ->get()
                        ->filter(fn ($item) => $item->location)
                        ->map(fn ($item) => [
                            'name'       => $item->location->name,
                            'slug'       => $item->location->slug,
                            'highlights' => $item->highlights,
                        ])
                        ->values(),
                ],

                'itineraries' => $package->itineraries->map(fn ($it) => [
                    'title'   => $it->title,
                    'details' => $it->details,
                ])->values(),

                'faqs' => [
                    'title' => $package->faq_title,
                    'items' => $package->faqs->map(fn ($faq) => [
                        'question' => $faq->question,
                        'answer'   => $faq->answer,
                    ])->values(),
                ],

                'similar_packages' => $similar->map(fn ($p) => [
                    'title'             => $p->title,
                    'slug'              => $p->slug,
                    'primary_image'     => $p->primary_image ? storage_link($p->primary_image) : null,
                    'primary_image_alt' => $p->primary_image_alt,
                    'rating'            => round($p->reviews_avg_rating ?? 0, 1),
                    'duration_days'     => $p->details?->duration_days,
                    'duration_nights'   => $p->details?->duration_nights,
                    'tour_highlights'   => $p->details?->tour_highlights,
                ])->values(),

                'meta' => [
                    'meta_title'       => $package->meta?->meta_title,
                    'meta_description' => $package->meta?->meta_description,
                    'meta_keywords'    => $package->meta?->meta_keywords,
                    'h1_heading'       => $package->meta?->h1_heading,
                    'meta_details'     => $package->meta?->meta_details,
                ],
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/packages/{slug}  — country-level listing
    // ──────────────────────────────────────────────────────────────────────────

    public function packagesByCountry(Request $r)
    {
        $limit     = $r->get('limit', list_config()['limit']);
        $page      = $r->get('page', 1);
        $orderBy   = list_config()['order_by'];
        $direction = list_config()['direction'];

        $country = Country::where('slug', $r->slug)
            ->with(['details', 'faqs', 'meta'])
            ->first();

        if ($country) {
            $query = $country->packages()
                ->where('is_active', 1)
                ->when($r->filled('category_slug'), fn ($q) =>
                    $q->whereHas('category', fn ($qc) => $qc->where('slug', $r->category_slug))
                );

            $total    = (clone $query)->count();
            $packages = $query
                ->with(['details', 'reviews'])
                ->withAvg('reviews', 'rating')
                ->orderBy($orderBy, $direction)
                ->skip(($page - 1) * $limit)
                ->take($limit)
                ->get();

            $categories = \App\Models\Category::whereIn('id', function ($q) use ($country) {
                $q->select('category_id')->from('packages')
                  ->where('country_id', $country->id)
                  ->where('is_active', 1);
            })->select('name', 'slug', 'title')->distinct()->get();

            return response()->json([
                'status'  => 'success',
                'message' => 'Listing',
                'data'    => [
                    'country'    => new CountryResource($country),
                    'packages'   => PackageResource::collection($packages),
                    'categories' => $categories,
                    'pagination' => ['total' => $total, 'page' => (int) $page, 'limit' => (int) $limit],
                ],
            ]);
        }

        return $this->packagesByLocationNationalInternational($r);
    }

    public function packagesByLoction(Request $r)
    {
        return $this->packagesByLocationNationalInternational($r);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PRIVATE — helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function packagesByRegionDB(Request $r, Region $region): \Illuminate\Http\JsonResponse
    {
        $limit     = $r->get('limit', list_config()['limit']);
        $page      = $r->get('page', 1);
        $orderBy   = list_config()['order_by'];
        $direction = list_config()['direction'];

        $query = Package::where('is_active', 1)
            ->whereHas('location', fn ($q) =>
                $q->where('region_id', $region->id)->where('is_active', 1)
            )
            ->when($r->filled('category_slug'), fn ($q) =>
                $q->whereHas('category', fn ($qc) => $qc->where('slug', $r->category_slug))
            );

        $total    = (clone $query)->count();
        $packages = $query
            ->with(['details', 'reviews', 'location.country'])
            ->withAvg('reviews', 'rating')
            ->orderBy($orderBy, $direction)
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Region Listing',
            'data'    => [
                'region'     => new RegionResource($region),
                'packages'   => PackageResource::collection($packages),
                'pagination' => ['total' => $total, 'page' => (int) $page, 'limit' => (int) $limit],
            ],
        ]);
    }

    private function packagesByLocationNationalInternational(Request $r): \Illuminate\Http\JsonResponse
    {
        $limit     = $r->get('limit', list_config()['limit']);
        $page      = $r->get('page', 1);
        $orderBy   = list_config()['order_by'];
        $direction = list_config()['direction'];

        $location = Location::where('slug', $r->slug)
            ->where('is_active', 1)
            ->with(['country', 'details', 'faqs', 'meta', 'bestTimes'])
            ->first();

        if (! $location) {
            return invalidRequest();
        }

        $query = Package::where('is_active', 1)
            ->when($r->filled('category_slug'), fn ($q) =>
                $q->whereHas('category', fn ($qc) => $qc->where('slug', $r->category_slug))
            )
            ->where(fn ($q) =>
                $q->where('location_id', $location->id)
                  ->orWhereHas('extraDestinations', fn ($qd) => $qd->where('location_id', $location->id))
                  ->orWhereHas('extraSources', fn ($qs) => $qs->where('location_id', $location->id))
            );

        $total    = (clone $query)->count();
        $packages = $query
            ->with(['details', 'reviews', 'location'])
            ->withAvg('reviews', 'rating')
            ->orderBy($orderBy, $direction)
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        $categories = \App\Models\Category::whereIn('id', function ($q) use ($location) {
            $q->select('category_id')->from('packages')
              ->where('location_id', $location->id)
              ->where('is_active', 1);
        })->select('name', 'slug', 'title')->distinct()->get();

        $sourceLocations = Location::whereIn('id', function ($q) use ($location) {
            $q->select('source_location_id')->from('packages')
              ->where('location_id', $location->id)
              ->where('is_active', 1)->whereNotNull('source_location_id');
        })->where('id', '!=', $location->id)
          ->select('name', 'slug')
          ->distinct()
          ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Listing',
            'data'    => [
                'location'           => ['name' => $location->name, 'slug' => $location->slug],
                'banner'             => $this->bannerDetails($location->details),
                'short_description'  => $location->details?->about,
                'packages'           => PackageResource::collection($packages),
                'categories'         => $categories,
                'sourceLocations'    => $sourceLocations,
                'pagination'         => ['total' => $total, 'page' => (int) $page, 'limit' => (int) $limit],
                'top_destinations'   => $location->state_id ? $this->citiesForState($location->state_id) : collect(),
                'best_time_to_visit' => $this->bestTimeToVisit($location->best_time_title, $location->bestTimes),
                'faqs'               => $this->faqSection($location->faq_title, $location->faqs),
                'meta'               => $this->metaSection($location->meta),
            ],
        ]);
    }
}
