<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Package;
use App\Models\State;
use App\Models\TouristActivity;
use App\Models\TouristActivityPage;
use App\Models\TouristActivitySetting;
use App\Models\TouristAttraction;
use Illuminate\Support\Str;

class TouristActivityController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/tourist-activities  — Root index page
    // ──────────────────────────────────────────────────────────────────────────

    public function index()
    {
        $setting = TouristActivitySetting::current()->load([
            'faqs', 'highlights', 'perfectFors', 'seasons',
        ]);

        // "Explore Tour Activities In India" — every real active activity (no separate static list)
        $realActivities = TouristActivity::with(['state', 'location'])
            ->where('is_active', 1)->orderBy('sort_order')
            ->limit(12)
            ->get();

        // "Popular City Experiences" — auto-derived from real city pages + real activities, no manual entry
        $cityExperiences = TouristActivityPage::with('location.state')
            ->whereNotNull('location_id')
            ->where('is_active', 1)
            ->get()
            ->map(function ($p) {
                $cityActivities = TouristActivity::where('location_id', $p->location_id)
                    ->where('is_active', 1)->orderBy('sort_order')
                    ->get();

                return [
                    'title'              => $p->title ?: (($p->location->name ?? '') . ' City Tours'),
                    'image'              => $p->banner_image ? storage_link($p->banner_image) : null,
                    'image_alt'          => $p->banner_image_alt,
                    'description'        => $p->short_description,
                    'city_name'          => $p->location->name ?? null,
                    'state_slug'         => $p->location?->state ? Str::slug($p->location->state->name) : null,
                    'city_slug'          => $p->location ? Str::slug(preg_replace('/\s*\(.*?\)\s*/', '', $p->location->name)) : null,
                    'tours_count'        => $cityActivities->count(),
                    'popular_activities' => $cityActivities->take(3)->pluck('name')->values(),
                ];
            })
            ->filter(fn ($c) => $c['tours_count'] > 0)
            ->sortByDesc('tours_count')
            ->take(8)
            ->values();

        // "Popular Activities Across States" — featured state AND city pages, mixed, with a real tour count each
        $popularPages = TouristActivityPage::with(['state', 'location.state'])
            ->where('is_active', 1)
            ->where('is_featured', 1)
            ->where(fn ($q) => $q->whereNotNull('state_id')->orWhereNotNull('location_id'))
            ->limit(capped_limit(request()))
            ->get()
            ->map(function ($p) {
                $isCity = (bool) $p->location_id;
                $name = $isCity ? $p->location->name ?? null : $p->state->name ?? null;
                $state = $isCity ? $p->location?->state : $p->state;

                return [
                    'name'        => $name,
                    'type'        => $isCity ? 'city' : 'state',
                    'state_slug'  => $state ? Str::slug($state->name) : null,
                    'city_slug'   => $isCity && $p->location ? Str::slug(preg_replace('/\s*\(.*?\)\s*/', '', $p->location->name)) : null,
                    'image'       => $p->banner_image ? storage_link($p->banner_image) : null,
                    'tours_count' => $isCity
                        ? TouristActivity::where('location_id', $p->location_id)->where('is_active', 1)->count()
                        : TouristActivity::where('state_id', $p->state_id)->count(),
                ];
            })->values();

        $popularPackages = $this->popularPackages();

        return response()->json([
            'status'  => 'success',
            'message' => 'Tourist Activities Landing Page',
            'type'    => 'landing',
            'data'    => [
                'banner' => [
                    'title'     => $setting->title,
                    'image'     => $setting->banner_image ? storage_link($setting->banner_image) : null,
                    'image_alt' => $setting->banner_image_alt,
                    'text'      => $setting->banner_text,
                ],

                'short_description' => $setting->short_description,

                'stats' => [
                    'image'     => $setting->stats_image ? storage_link($setting->stats_image) : null,
                    'image_alt' => $setting->stats_image_alt,
                    'items'     => $setting->highlights->map(fn ($h) => [
                        'stat'  => $h->stat,
                        'label' => $h->label,
                    ])->values(),
                ],

                'activity_types' => [
                    'title' => 'Explore Tour Activities In India',
                    'items' => $realActivities->map(fn ($a) => [
                        'name'       => $a->name,
                        'slug'       => $a->slug,
                        'image'      => $a->banner_image ? storage_link($a->banner_image) : null,
                        'image_alt'  => $a->banner_image_alt,
                        'state_slug' => $a->state ? Str::slug($a->state->name) : null,
                        'city_slug'  => $a->location ? Str::slug(preg_replace('/\s*\(.*?\)\s*/', '', $a->location->name)) : null,
                    ])->values(),
                ],

                'perfect_for' => [
                    'title' => 'Perfect For',
                    'items' => $setting->perfectFors->map(fn ($p) => [
                        'title' => $p->title,
                        'icon'  => $p->icon ? storage_link($p->icon) : null,
                    ])->values(),
                ],

                'seasonal_activities' => [
                    'title' => $setting->seasons_title ?: "Seasonal Activities You Shouldn't Miss",
                    'items' => $setting->seasons->map(fn ($s) => [
                        'season_label'    => $s->season_label,
                        'period_text'     => $s->period_text,
                        'activities_text' => $s->activities_text,
                    ])->values(),
                ],

                'top_activities_destination' => [
                    'title' => 'Popular Activities Across States',
                    'items' => $popularPages,
                ],

                'city_experiences' => [
                    'title' => 'Popular City Experiences',
                    'items' => $cityExperiences,
                ],

                'faqs' => [
                    'title'     => $setting->faq_title,
                    'sub_title' => $setting->faq_sub_title,
                    'items'     => $setting->faqs->map(fn ($f) => [
                        'question' => $f->question,
                        'answer'   => $f->answer,
                    ])->values(),
                ],

                'popular_packages' => [
                    'title' => 'Popular Activities in India',
                    'items' => $popularPackages,
                ],

                'meta' => [
                    'meta_title'       => $setting->meta_title,
                    'meta_description' => $setting->meta_description,
                    'meta_keywords'    => $setting->meta_keywords,
                    'h1_heading'       => $setting->h1_heading,
                    'meta_details'     => $setting->meta_details,
                ],
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/tourist-activities/state/{slug}  — e.g. "Kerala Activities"
    // ──────────────────────────────────────────────────────────────────────────

    public function statePage(string $stateSlug)
    {
        $state = State::get()->first(fn ($s) => Str::slug($s->name) === $stateSlug);
        if (!$state) {
            return invalidRequest();
        }

        $page = TouristActivityPage::with(['faqs', 'experiences', 'waterfalls', 'thingsToDo'])
            ->where('state_id', $state->id)->where('is_active', 1)->first();

        if (!$page) {
            return invalidRequest();
        }

        $activities = TouristActivity::with('location')
            ->where('state_id', $state->id)
            ->where('is_active', 1)->orderBy('sort_order')
            ->limit(capped_limit(request()))
            ->get();

        $data = $this->pageData($page, $activities, $state->name, 'state');
        $data = $this->insertAfter($data, 'popular_experience', 'top_destinations', [
            'title' => 'Top Destinations for Activities',
            'items' => $this->topDestinationsForState($state->id),
        ]);
        return response()->json([
            'status'  => 'success',
            'message' => 'Tourist Activity State Page',
            'type'    => 'state',
            'data'    => $data,
        ]);
    }

    /** Move an existing key to right after $afterKey, preserving the rest of the order. No-op if $key isn't present. */
    // "Popular Packages" — real top-trending packages (same flag used by the global "Popular Packages" widgets elsewhere)
    // "Top Attractions in {City}" — real TouristAttraction records for this city, no separate data entry.
    private function topAttractionsForCity(int $locationId): \Illuminate\Support\Collection
    {
        return TouristAttraction::where('location_id', $locationId)
            ->where('is_active', 1)->orderBy('sort_order')
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($a) => [
                'name'        => $a->name,
                'slug'        => $a->slug,
                'image'       => $a->banner_image ? storage_link($a->banner_image) : null,
                'image_alt'   => $a->banner_image_alt,
                'description' => $a->short_description,
            ])->values();
    }

    private function popularPackages(): \Illuminate\Support\Collection
    {
        return Package::where('is_active', 1)
            ->where('is_top_trending', 1)
            ->with(['details', 'location'])
            ->orderBy(list_config()['order_by'], list_config()['direction'])
            ->limit(8)
            ->get()
            ->map(fn ($p) => [
                'title'           => $p->title,
                'slug'            => $p->slug,
                'image'           => $p->primary_image ? storage_link($p->primary_image) : null,
                'image_alt'       => $p->primary_image_alt,
                'duration_days'   => $p->details?->duration_days,
                'duration_nights' => $p->details?->duration_nights,
                'location'        => $p->location?->name,
            ])->values();
    }

    private function moveKeyAfter(array $array, string $key, string $afterKey): array
    {
        if (!array_key_exists($key, $array)) {
            return $array;
        }

        $value = $array[$key];
        unset($array[$key]);

        $result = [];
        foreach ($array as $k => $v) {
            $result[$k] = $v;
            if ($k === $afterKey) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /** Insert $value under $newKey right after $afterKey in an associative array, preserving the rest of the order. */
    private function insertAfter(array $array, string $afterKey, string $newKey, $value): array
    {
        $result = [];
        foreach ($array as $key => $existing) {
            $result[$key] = $existing;
            if ($key === $afterKey) {
                $result[$newKey] = $value;
            }
        }
        return $result;
    }

    // Cities within this state that have real activities — auto-derived, no manual entry.
    private function topDestinationsForState(int $stateId): \Illuminate\Support\Collection
    {
        return TouristActivityPage::with('location')
            ->whereNotNull('location_id')
            ->whereHas('location', fn ($q) => $q->where('state_id', $stateId))
            ->where('is_active', 1)
            ->get()
            ->map(function ($p) {
                $cityActivities = TouristActivity::where('location_id', $p->location_id)
                    ->where('is_active', 1)->orderBy('sort_order')
                    ->get();

                return [
                    'city_name'          => $p->location ? preg_replace('/\s*\(.*?\)\s*/', '', $p->location->name) : null,
                    'city_slug'          => $p->location ? Str::slug(preg_replace('/\s*\(.*?\)\s*/', '', $p->location->name)) : null,
                    'image'              => $p->banner_image ? storage_link($p->banner_image) : null,
                    'image_alt'          => $p->banner_image_alt,
                    'description'        => $p->short_description,
                    'tours_count'        => $cityActivities->count(),
                    'popular_activities' => $cityActivities->take(3)->pluck('name')->values(),
                ];
            })
            ->filter(fn ($c) => $c['tours_count'] > 0)
            ->sortByDesc('tours_count')
            ->take(capped_limit(request()))
            ->values();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // City-level: e.g. "Munnar Activities"
    // GET /api/v1/tourist-activities/{stateSlug}/{citySlug}
    // ──────────────────────────────────────────────────────────────────────────

    public function cityPage(string $stateSlug, string $citySlug)
    {
        $state = State::get()->first(fn ($s) => Str::slug($s->name) === $stateSlug);
        if (!$state) {
            return invalidRequest();
        }

        $location = Location::where('state_id', $state->id)->get()
            ->first(fn ($l) => Str::slug(preg_replace('/\s*\(.*?\)\s*/', '', $l->name)) === $citySlug);
        if (!$location) {
            return invalidRequest();
        }

        $page = TouristActivityPage::with(['faqs', 'experiences', 'waterfalls', 'thingsToDo'])
            ->where('location_id', $location->id)->where('is_active', 1)->first();

        if (!$page) {
            return invalidRequest();
        }

        $activities = TouristActivity::with('location')
            ->where('location_id', $location->id)
            ->where('is_active', 1)->orderBy('sort_order')
            ->limit(capped_limit(request()))
            ->get();

        $data = $this->pageData($page, $activities, $location->name, 'city');
        $data = $this->insertAfter($data, 'top_activities', 'activities_in_city', [
            'title'     => 'Activities in ' . $location->name,
            'sub_title' => $page->activities_in_city_sub_title,
            'items'     => $activities->map(fn ($a) => [
                'name'      => $a->name,
                'slug'      => $a->slug,
                'image'     => $a->banner_image ? storage_link($a->banner_image) : null,
                'image_alt' => $a->banner_image_alt,
            ])->values(),
        ]);
        $data = $this->insertAfter($data, 'top_things_to_do', 'top_attractions', [
            'title' => 'Top Attractions in ' . $location->name,
            'items' => $this->topAttractionsForCity($location->id),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Tourist Activity City Page',
            'type'    => 'city',
            'data'    => $data,
        ]);
    }

    /** $pageType: 'state' or 'city' — 'popular_experience' is state-only (not used on the real city frontend). */
    private function pageData(TouristActivityPage $page, $activities, string $subjectName, string $pageType): array
    {
        $data = [
            'banner' => [
                'title'     => $page->title ?: ($subjectName . ' Activities'),
                'image'     => $page->banner_image ? storage_link($page->banner_image) : null,
                'image_alt' => $page->banner_image_alt,
            ],

            'short_description' => $page->short_description,
            'about_image'       => $page->about_image ? storage_link($page->about_image) : null,
            'about_image_alt'   => $page->about_image_alt,

            'top_activities' => [
                'title' => $pageType === 'city'
                    ? 'Must-do Experiences in ' . $subjectName
                    : 'Discover the Best Activities in ' . $subjectName,
                'items' => $activities->map(fn ($a) => [
                    'name'          => $a->name,
                    'slug'          => $a->slug,
                    'image'         => $a->banner_image ? storage_link($a->banner_image) : null,
                    'image_alt'     => $a->banner_image_alt,
                    'description'   => $a->short_description,
                    'location_name' => $a->location?->name ? preg_replace('/\s*\(.*?\)\s*/', '', $a->location->name) : null,
                    'location_slug' => $a->location ? Str::slug(preg_replace('/\s*\(.*?\)\s*/', '', $a->location->name)) : null,
                ])->values(),
            ],
        ];

        if ($pageType !== 'city') {
            $data['popular_experience'] = [
                'title' => $page->experiences_title ?: ('Popular Experience in ' . $subjectName),
                'items' => $page->experiences->map(fn ($e) => [
                    'title'       => $e->title,
                    'description' => $e->description,
                    'icon'        => $e->icon ? storage_link($e->icon) : null,
                ])->values(),
            ];
        }

        $data['waterfalls'] = [
            'title' => $page->waterfalls_title ?: ('Explore Waterfalls in ' . $subjectName),
            'items' => $page->waterfalls->map(fn ($w) => [
                'label' => $w->label,
                'image' => $w->image ? storage_link($w->image) : null,
            ])->values(),
        ];

        $data['top_things_to_do'] = [
            'title' => $page->things_to_do_title ?: ('Top Things to Do in ' . $subjectName),
            'items' => $page->thingsToDo->map(fn ($t) => [
                'title'             => $t->title,
                'description'       => $t->description,
                'duration_timing'   => $t->duration_timing,
                'best_for'          => $t->best_for,
                'approximate_cost'  => $t->approximate_cost,
            ])->values(),
        ];

        $data['faqs'] = [
            'title'     => $page->faq_title,
            'sub_title' => $page->faq_sub_title,
            'items'     => $page->faqs->map(fn ($f) => [
                'question' => $f->question,
                'answer'   => $f->answer,
            ])->values(),
        ];

        if ($pageType !== 'city') {
            $data['popular_packages'] = [
                'title' => 'Popular Activities in India',
                'items' => $this->popularPackages(),
            ];
        }

        $data['meta'] = [
            'meta_title'       => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keywords'    => $page->meta_keywords,
            'h1_heading'       => $page->h1_heading,
            'meta_details'     => $page->meta_details,
        ];

        return $data;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Activity detail: e.g. "Houseboat Cruise in Alleppey"
    // ──────────────────────────────────────────────────────────────────────────

    public function activityDetail(string $slug)
    {
        $activity = TouristActivity::with(['state', 'location', 'itinerarySteps', 'experiences', 'thingsToDo', 'galleryImages', 'faqs'])
            ->where('slug', $slug)->where('is_active', 1)->first();

        if (!$activity) {
            return invalidRequest();
        }

        $cleanCityName = preg_replace('/\s*\(.*?\)\s*/', '', $activity->location->name ?? '');

        // Real attractions in the same city — excludes any attraction sharing this activity's name (e.g. "Tea Gardens in Munnar").
        $nearbyAttractions = TouristAttraction::where('location_id', $activity->location_id)
            ->where('is_active', 1)->where('name', '!=', $activity->name)
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Tourist Activity Detail',
            'type'    => 'activity',
            'data'    => [
                'banner' => [
                    'title'     => $activity->name,
                    'tagline'   => $activity->tagline,
                    'image'     => $activity->banner_image ? storage_link($activity->banner_image) : null,
                    'image_alt' => $activity->banner_image_alt,
                ],

                'short_description' => $activity->short_description,
                'state_name'         => $activity->state->name ?? null,
                'city_name'          => $activity->location->name ?? null,

                'popular_activity' => [
                    'title' => $activity->experiences_title,
                    'items' => $activity->experiences->map(fn ($e) => [
                        'image'       => $e->image ? storage_link($e->image) : null,
                        'image_alt'   => $e->image_alt,
                        'title'       => $e->title,
                        'description' => $e->description,
                    ])->values(),
                ],

                'things_to_do' => [
                    'title' => $activity->things_to_do_title,
                    'items' => $activity->thingsToDo->map(fn ($t) => [
                        'title'       => $t->title,
                        'description' => $t->description,
                    ])->values(),
                ],

                'itinerary' => [
                    'title' => $activity->itinerary_title ?: ('Itinerary For ' . $activity->name),
                    'items' => $activity->itinerarySteps->map(fn ($s) => [
                        'title'       => $s->title,
                        'description' => $s->description,
                    ])->values(),
                ],

                'gallery' => $activity->galleryImages->map(fn ($img) => [
                    'image'     => storage_link($img->image),
                    'image_alt' => $img->image_alt,
                ])->values(),

                'faqs' => [
                    'title'     => $activity->faq_title,
                    'sub_title' => $activity->faq_sub_title,
                    'items'     => $activity->faqs->map(fn ($f) => [
                        'question' => $f->question,
                        'answer'   => $f->answer,
                    ])->values(),
                ],

                'explore_more_attractions' => [
                    'title' => 'Explore More Attractions in ' . $cleanCityName,
                    'items' => $nearbyAttractions->map(fn ($a) => [
                        'name'        => $a->name,
                        'slug'        => $a->slug,
                        'image'       => $a->banner_image ? storage_link($a->banner_image) : null,
                        'image_alt'   => $a->banner_image_alt,
                        'description' => $a->short_description,
                    ])->values(),
                ],

                'meta' => [
                    'meta_title'       => $activity->meta_title,
                    'meta_description' => $activity->meta_description,
                    'meta_keywords'    => $activity->meta_keywords,
                    'h1_heading'       => $activity->h1_heading,
                    'meta_details'     => $activity->meta_details,
                ],
            ],
        ]);
    }
}
