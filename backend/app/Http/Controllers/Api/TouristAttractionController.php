<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\State;
use App\Models\TouristAttraction;
use App\Models\TouristAttractionPage;
use App\Models\TouristAttractionSetting;
use Illuminate\Support\Str;

class TouristAttractionController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/tourist-attractions  — Root index page
    // ──────────────────────────────────────────────────────────────────────────

    public function index()
    {
        $setting = TouristAttractionSetting::current()->load('faqs');

        // "Top Tourist Attractions" — popular-marked attractions only
        $topAttractions = TouristAttraction::with(['state', 'location'])
            ->where('is_active', 1)
            ->where('is_popular', 1)
            ->orderBy('sort_order')
            ->orderBy('id', 'desc')
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($a) => [
                'name'      => $a->name,
                'slug'      => $a->slug,
                'image'     => $a->banner_image ? storage_link($a->banner_image) : null,
                'image_alt' => $a->banner_image_alt,
                'state'     => $a->state?->slug,
                'city'      => $a->location?->slug,
            ])->values();

        // "Explore States" — every state that has an active Tourist Attraction page
        $attractionCountByState = TouristAttraction::where('is_active', 1)
            ->selectRaw('state_id, COUNT(*) as total')
            ->groupBy('state_id')
            ->pluck('total', 'state_id');

        $exploreStates = TouristAttractionPage::with('state')
            ->whereNotNull('state_id')
            ->where('is_active', 1)
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($p) => [
                'name'             => $p->state->name ?? null,
                'slug'             => $p->state ? Str::slug($p->state->name) : null,
                'image'            => $p->banner_image ? storage_link($p->banner_image) : null,
                'image_alt'        => $p->banner_image_alt ?: ($p->state->name ?? null),
                'attraction_count' => (int) ($attractionCountByState[$p->state_id] ?? 0),
            ])->values();

        return response()->json([
            'status'  => 'success',
            'message' => 'Tourist Attractions Landing Page',
            'type'    => 'landing',
            'data'    => [
                'banner' => [
                    'title'     => $setting->title,
                    'image'     => $setting->banner_image ? storage_link($setting->banner_image) : null,
                    'image_alt' => $setting->banner_image_alt,
                    'text'      => $setting->banner_text,
                ],

                'short_description' => $setting->short_description,

                'top_attractions' => [
                    'title' => 'Top Tourist Attraction Tours',
                    'items' => $topAttractions,
                ],

                'explore_states' => [
                    'title' => 'Explore Popular States',
                    'items' => $exploreStates,
                ],

                'faqs' => [
                    'title'     => $setting->faq_title,
                    'sub_title' => $setting->faq_sub_title,
                    'items'     => $setting->faqs->map(fn ($f) => [
                        'question' => $f->question,
                        'answer'   => $f->answer,
                    ])->values(),
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
    // GET /api/v1/tourist-attractions/state/{slug}  — e.g. "Kerala Tourist Attractions"
    // ──────────────────────────────────────────────────────────────────────────

    public function statePage(string $stateSlug)
    {
        $state = State::get()->first(fn ($s) => Str::slug($s->name) === $stateSlug);
        if (!$state) {
            return invalidRequest();
        }

        $page = TouristAttractionPage::with(['bestTimes', 'faqs'])
            ->where('state_id', $state->id)->where('is_active', 1)->first();

        if (!$page) {
            return invalidRequest();
        }

        $attractions = TouristAttraction::with(['state', 'location'])
            ->where('state_id', $state->id)
            ->where('is_active', 1)->orderBy('sort_order')
            ->limit(capped_limit(request()))
            ->get();

        $cities = TouristAttractionPage::with('location')
            ->whereNotNull('location_id')
            ->whereHas('location', fn ($q) => $q->where('state_id', $state->id))
            ->where('is_active', 1)
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($p) => [
                'name'      => $p->location->name ?? null,
                'slug'      => $p->location ? Str::slug(preg_replace('/\s*\(.*?\)\s*/', '', $p->location->name)) : null,
                'image'     => $p->banner_image ? storage_link($p->banner_image) : null,
                'image_alt' => $p->banner_image_alt ?: ($p->location->name ?? null),
            ])->values();

        $data = $this->pageData($page, $attractions, $state->name);
        $ordered = [];
        foreach ($data as $key => $value) {
            $ordered[$key] = $value;
            if ($key === 'top_attractions') {
                $ordered['cities'] = $cities;
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Tourist Attraction State Page',
            'type'    => 'state',
            'data'    => $ordered,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // City-level: e.g. "Munnar Tourist Attractions"
    // GET /api/v1/tourist-attractions/{stateSlug}/{citySlug}
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

        $page = TouristAttractionPage::with(['bestTimes', 'faqs'])
            ->where('location_id', $location->id)->where('is_active', 1)->first();

        if (!$page) {
            return invalidRequest();
        }

        $attractions = TouristAttraction::with(['state', 'location'])
            ->where('location_id', $location->id)
            ->where('is_active', 1)->orderBy('sort_order')
            ->limit(capped_limit(request()))
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Tourist Attraction City Page',
            'type'    => 'city',
            'data'    => $this->pageData($page, $attractions, $location->name),
        ]);
    }

    private function pageData(TouristAttractionPage $page, $attractions, string $subjectName): array
    {
        return [
            'banner' => [
                'title'     => $page->title ?: ($subjectName . ' Tourist Attractions'),
                'image'     => $page->banner_image ? storage_link($page->banner_image) : null,
                'image_alt' => $page->banner_image_alt,
            ],

            'short_description' => $page->short_description,

            'top_attractions' => [
                'title' => 'Explore ' . $subjectName . ' Tourist Attractions',
                'items' => $attractions->map(fn ($a) => [
                    'name'      => $a->name,
                    'slug'      => $a->slug,
                    'image'     => $a->banner_image ? storage_link($a->banner_image) : null,
                    'image_alt' => $a->banner_image_alt,
                    'state'     => $a->state?->slug,
                    'city'      => $a->location?->slug,
                ])->values(),
            ],

            'best_time_to_visit' => [
                'title' => 'Best Time To Visit ' . $subjectName,
                'items' => $page->bestTimes->map(fn ($bt) => [
                    'period'      => $bt->period,
                    'description' => $bt->description,
                ])->values(),
            ],

            'faqs' => [
                'title'     => $page->faq_title,
                'sub_title' => $page->faq_sub_title,
                'items'     => $page->faqs->map(fn ($f) => [
                    'question' => $f->question,
                    'answer'   => $f->answer,
                ])->values(),
            ],

            'meta' => [
                'meta_title'       => $page->meta_title,
                'meta_description' => $page->meta_description,
                'meta_keywords'    => $page->meta_keywords,
                'h1_heading'       => $page->h1_heading,
                'meta_details'     => $page->meta_details,
            ],
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Attraction detail: e.g. "Tea Gardens in Munnar"
    // ──────────────────────────────────────────────────────────────────────────

    public function attractionDetail(string $slug)
    {
        $attraction = TouristAttraction::with(['state', 'location', 'highlights', 'activities', 'galleryImages', 'faqs'])
            ->where('slug', $slug)->where('is_active', 1)->first();

        if (!$attraction) {
            return invalidRequest();
        }

        $nearby = $attraction->nearby()->orderBy('sort_order')->limit(4)->get();
        $cleanCityName = preg_replace('/\s*\(.*?\)\s*/', '', $attraction->location->name ?? '');

        return response()->json([
            'status'  => 'success',
            'message' => 'Tourist Attraction Detail',
            'type'    => 'attraction',
            'data'    => [
                'banner' => [
                    'title'     => $attraction->name,
                    'tagline'   => $attraction->tagline,
                    'image'     => $attraction->banner_image ? storage_link($attraction->banner_image) : null,
                    'image_alt' => $attraction->banner_image_alt,
                ],

                'short_description' => $attraction->short_description,
                'state_name'         => $attraction->state->name ?? null,
                'city_name'          => $attraction->location->name ?? null,

                'quick_information' => [
                    'location' => $attraction->location_text,
                    'duration' => $attraction->duration_text,
                    'best_for' => $attraction->best_for,
                    'best_season' => $attraction->best_season,
                ],

                'why_visit' => [
                    'title'       => $attraction->why_visit_title,
                    'image'       => $attraction->why_visit_image ? storage_link($attraction->why_visit_image) : null,
                    'image_alt'   => $attraction->why_visit_image_alt,
                    'description' => $attraction->why_visit_description,
                    'highlights'  => $attraction->highlights->pluck('text')->values(),
                ],

                'things_to_do' => [
                    'title' => 'Things To Do At ' . $attraction->name,
                    'items' => $attraction->activities->map(fn ($a) => [
                        'title'       => $a->title,
                        'description' => $a->description,
                    ])->values(),
                ],

                'gallery' => $attraction->galleryImages->map(fn ($img) => [
                    'image'     => storage_link($img->image),
                    'image_alt' => $img->image_alt,
                ])->values(),

                'nearby_attractions' => [
                    'title' => 'Nearby Attractions',
                    'items' => $nearby->map(fn ($n) => [
                        'name'        => $n->name,
                        'slug'        => $n->slug,
                        'image'       => $n->banner_image ? storage_link($n->banner_image) : null,
                        'image_alt'   => $n->banner_image_alt,
                        'description' => $n->short_description,
                        'state'       => $attraction->state?->slug,
                        'city'        => $attraction->location?->slug,
                    ])->values(),
                ],

                'faqs' => [
                    'title'     => $attraction->faq_title,
                    'sub_title' => $attraction->faq_sub_title,
                    'items'     => $attraction->faqs->map(fn ($f) => [
                        'question' => $f->question,
                        'answer'   => $f->answer,
                    ])->values(),
                ],

                'explore_more' => [
                    'title' => 'Explore More Attractions in ' . $cleanCityName,
                    'items' => $nearby->map(fn ($n) => [
                        'name'  => $n->name,
                        'slug'  => $n->slug,
                        'image' => $n->banner_image ? storage_link($n->banner_image) : null,
                        'state' => $attraction->state?->slug,
                        'city'  => $attraction->location?->slug,
                    ])->values(),
                ],

                'meta' => [
                    'meta_title'       => $attraction->meta_title,
                    'meta_description' => $attraction->meta_description,
                    'meta_keywords'    => $attraction->meta_keywords,
                    'h1_heading'       => $attraction->h1_heading,
                    'meta_details'     => $attraction->meta_details,
                ],
            ],
        ]);
    }
}
