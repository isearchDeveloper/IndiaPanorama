<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Models\ExperienceCategory;
use App\Models\ExperiencePage;
use App\Models\ExperienceSetting;
use App\Models\ExperienceSubcategory;
use App\Models\Location;
use App\Models\State;
use App\Models\TouristActivity;
use App\Models\TouristAttraction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/page/settings/experiences  (also aliased as /experiences/settings)
    // Hub page — e.g. /experiences
    // ─────────────────────────────────────────────────────────────────────────
    public function index(): JsonResponse
    {
        $s = ExperienceSetting::current();
        $s->load(['faqs', 'bestTimes', 'whyChooseItems']);

        $categories = ExperienceCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($c) => $this->categoryBrief($c))
            ->values();

        return response()->json([
            'status'  => 'success',
            'message' => 'Experiences settings',
            'data'    => [
                'banner' => [
                    'title'       => $s->title,
                    'banner_text' => $s->banner_text,
                    'image'       => $s->banner_image ? storage_link($s->banner_image) : null,
                    'image_alt'   => $s->banner_image_alt,
                ],
                'short_description' => $s->short_description,
                'meta' => [
                    'meta_title'       => $s->meta_title,
                    'meta_description' => $s->meta_description,
                    'meta_keywords'    => $s->meta_keywords,
                    'h1_heading'       => $s->h1_heading,
                    'meta_details'     => $s->meta_details,
                ],
                'category'   => $categories,
                'best_time' => [
                    'title' => $s->best_time_title,
                    'list'  => $s->bestTimes->map(fn ($b) => [
                        'label' => $b->label,
                        'text'  => $b->text,
                    ])->values(),
                ],
                'states' => $this->statesWithExperiences(),
                'why_choose' => [
                    'title'       => $s->why_choose_title,
                    'description' => $s->why_choose_description,
                    'items'       => $s->whyChooseItems->pluck('label')->values(),
                ],
                'faqs' => [
                    'title'     => $s->faq_title,
                    'sub_title' => $s->faq_sub_title,
                    'list'      => $s->faqs->map(fn ($f) => [
                        'question' => $f->question,
                        'answer'   => $f->answer,
                    ])->values(),
                ],
            ],
        ]);
    }

    /** Every state that has at least one active Experience item — powers the hub's state list. */
    private function statesWithExperiences(): \Illuminate\Support\Collection
    {
        $counts = Experience::where('is_active', true)
            ->selectRaw('state_id, count(*) as experiences_count')
            ->groupBy('state_id')
            ->pluck('experiences_count', 'state_id');

        $pagesByStateId = ExperiencePage::whereNotNull('state_id')
            ->whereIn('state_id', $counts->keys())
            ->get()
            ->keyBy('state_id');

        return State::whereIn('id', $counts->keys())
            ->orderBy('name')
            ->get()
            ->map(function ($s) use ($counts, $pagesByStateId) {
                $page = $pagesByStateId->get($s->id);

                return [
                    'name'              => $s->name,
                    'slug'              => $s->city_guide_slug,
                    'image'             => $page?->banner_image ? storage_link($page->banner_image) : null,
                    'image_alt'         => $page?->banner_image_alt,
                    'experiences_count' => $counts[$s->id] ?? 0,
                ];
            })
            ->values();
    }

    private function categoryBrief(ExperienceCategory $category): array
    {
        return [
            'name'        => $category->name,
            'slug'        => $category->slug,
            'image'       => $category->image ? storage_link($category->image) : null,
            'image_alt'   => $category->image_alt,
            'description' => $category->description,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/experiences/category/{slug}  — Category page
    // ─────────────────────────────────────────────────────────────────────────
    public function category(string $slug): JsonResponse
    {
        $category = ExperienceCategory::where('slug', $slug)
            ->where('is_active', true)
            ->with(['quickInfos', 'perfectFors', 'faqs'])
            ->first();

        if (!$category) {
            abort(404);
        }

        $subcategories = $category->subcategories()
            ->where('is_active', true)
            ->get()
            ->map(fn ($sub) => [
                'name'       => $sub->name,
                'slug'       => $sub->slug,
                'image'      => $sub->image ? storage_link($sub->image) : null,
                'image_alt'  => $sub->image_alt,
                'description' => \Illuminate\Support\Str::limit(strip_tags($sub->description ?? ''), 120),
                'popular_experience' => $this->popularExperienceForSubcategory($sub->id),
            ])
            ->values();

        // Experiences filed directly under the category (no subcategory yet) — these
        // don't have a subcategory tile to live under, so they're surfaced here instead.
        $directExperiences = Experience::where('category_id', $category->id)
            ->whereNull('subcategory_id')
            ->where('is_active', true)
            ->with(['state', 'location', 'galleryImages'])
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($e) => $this->experienceBrief($e))
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $category->name,
                'slug' => $category->slug,
                'banner' => [
                    'image'     => $category->image ? storage_link($category->image) : null,
                    'image_alt' => $category->image_alt,
                ],
                'short_description' => $category->description,
                'intro_image'       => $category->intro_image ? storage_link($category->intro_image) : null,
                'quick_info' => $category->quickInfos->map(fn ($q) => [
                    'label' => $q->label,
                    'value' => $q->value,
                ])->values(),
                'subcategories' => $subcategories,
                'experiences'   => $directExperiences,
                'perfect_for' => $category->perfectFors->map(fn ($p) => [
                    'title'       => $p->title,
                    'description' => $p->description,
                    'icon'        => $p->icon ? storage_link($p->icon) : null,
                ])->values(),
                'faqs' => [
                    'title' => $category->faq_title,
                    'items' => $category->faqs->map(fn ($f) => [
                        'question' => $f->question,
                        'answer'   => $f->answer,
                    ])->values(),
                ],
                'meta' => $this->metaBlock($category),
            ],
        ]);
    }

    /** The one Experience flagged "Popular" (admin toggle) within this subcategory, if any. */
    private function popularExperienceForSubcategory(int $subcategoryId): ?array
    {
        $experience = Experience::where('subcategory_id', $subcategoryId)
            ->where('is_active', true)
            ->where('is_popular', true)
            ->with('galleryImages')
            ->first();

        if (!$experience) {
            return null;
        }

        return [
            'title' => $experience->title,
            'slug'  => $experience->slug,
            'image' => optional($experience->galleryImages->first())->image ? storage_link($experience->galleryImages->first()->image) : null,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/experiences/subcategory/{slug}?state={stateSlug}  — Listing page
    // ─────────────────────────────────────────────────────────────────────────
    public function subcategory(Request $request, string $slug): JsonResponse
    {
        $subcategory = ExperienceSubcategory::where('slug', $slug)
            ->where('is_active', true)
            ->with('category')
            ->first();

        if (!$subcategory) {
            abort(404);
        }

        $itemsQuery = Experience::where('subcategory_id', $subcategory->id)
            ->where('is_active', true)
            ->with(['state', 'location', 'galleryImages']);

        if (($stateSlug = $request->query('state')) && $stateSlug !== 'all') {
            $state = $this->resolveState($stateSlug);
            if (!$state) {
                abort(404);
            }
            $itemsQuery->where('state_id', $state->id);
        }

        $items = $itemsQuery->orderBy('sort_order')->limit(capped_limit($request))->get();

        $states = Experience::where('subcategory_id', $subcategory->id)
            ->where('is_active', true)
            ->with('state')
            ->get()
            ->pluck('state')
            ->filter()
            ->unique('id')
            ->values()
            ->map(fn ($s) => ['name' => $s->name, 'slug' => $s->city_guide_slug])
            ->values();

        $states = collect([['name' => 'All States', 'slug' => 'all']])->concat($states)->values();

        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $subcategory->name,
                'slug' => $subcategory->slug,
                'category' => [
                    'name' => $subcategory->category->name,
                    'slug' => $subcategory->category->slug,
                ],
                'banner' => [
                    'image'     => $subcategory->image ? storage_link($subcategory->image) : null,
                    'image_alt' => $subcategory->image_alt,
                ],
                'short_description' => $subcategory->description,
                'states' => $states,
                'items' => $items->map(fn ($e) => $this->experienceBrief($e))->values(),
            ],
        ]);
    }

    /** Brief experience card — title/slug/location/image/tagline — shared by category & subcategory listings. */
    private function experienceBrief(Experience $e): array
    {
        return [
            'title'      => $e->title,
            'slug'       => $e->slug,
            'state_slug' => $e->state?->city_guide_slug,
            'state_name' => $e->state?->name,
            'city_slug'  => $e->location?->city_guide_slug,
            'city_name'  => $e->location?->name,
            'image'      => optional($e->galleryImages->first())->image ? storage_link($e->galleryImages->first()->image) : null,
            'image_alt'  => optional($e->galleryImages->first())->image_alt,
            'tagline'    => $e->tagline,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/experiences/detail/{slug}  — Detail page (slug WITHOUT "-experience" suffix)
    // ─────────────────────────────────────────────────────────────────────────
    public function detail(string $slug): JsonResponse
    {
        $experience = Experience::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'subcategory', 'state', 'location', 'galleryImages', 'quickInfos', 'highlights', 'faqs'])
            ->first();

        if (!$experience) {
            abort(404);
        }

        // Related items: same subcategory when this experience has one, otherwise
        // fall back to sibling experiences filed directly under the same category.
        $related = Experience::where($experience->subcategory_id
                ? ['subcategory_id' => $experience->subcategory_id]
                : ['category_id' => $experience->category_id])
            ->where('id', '!=', $experience->id)
            ->where('is_active', true)
            ->with(['state', 'location', 'galleryImages'])
            ->limit(6)
            ->get();

        return response()->json([
            'status'  => 'success',
            'type'    => 'experience',
            'data' => [
                'title'      => $experience->title,
                'slug'       => $experience->slug,
                'state_slug' => $experience->state?->city_guide_slug,
                'state_name' => $experience->state?->name,
                'city_slug'  => $experience->location?->city_guide_slug,
                'city_name'  => $experience->location?->name,
                'category' => [
                    'name' => $experience->category->name,
                    'slug' => $experience->category->slug,
                ],
                'subcategory' => $experience->subcategory ? [
                    'name' => $experience->subcategory->name,
                    'slug' => $experience->subcategory->slug,
                ] : null,
                'tagline'     => $experience->tagline,
                'description' => $experience->description,
                'images'      => $experience->galleryImages->map(fn ($g) => storage_link($g->image))->values(),
                'highlights'  => $experience->highlights->pluck('text')->values(),
                'quick_info' => $experience->quickInfos->map(fn ($q) => [
                    'label' => $q->label,
                    'value' => $q->value,
                ])->values(),
                'faqs' => $experience->faqs->map(fn ($f) => [
                    'question' => $f->question,
                    'answer'   => $f->answer,
                ])->values(),
                'related' => $related->map(fn ($r) => [
                    'title'            => $r->title,
                    'slug'             => $r->slug,
                    'image'            => optional($r->galleryImages->first())->image ? storage_link($r->galleryImages->first()->image) : null,
                    'state_slug'       => $r->state?->city_guide_slug,
                    'city_slug'        => $r->location?->city_guide_slug,
                    'subcategory_slug' => $experience->subcategory?->slug,
                ])->values(),
                'meta' => $this->metaBlock($experience),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/experiences/state/{state}  — State hub — e.g. /kerala/experiences
    // ─────────────────────────────────────────────────────────────────────────
    public function statePage(string $stateSlug): JsonResponse
    {
        $state = $this->resolveState($stateSlug);
        if (!$state) {
            abort(404);
        }

        $page = ExperiencePage::where('state_id', $state->id)->with(['faqs', 'activities', 'highlights'])->first();

        return response()->json([
            'status' => 'success',
            'type'   => 'state',
            'data' => [
                'state_name' => $state->name,
                'state_slug' => $state->city_guide_slug,
                'banner' => [
                    'title'       => $page?->title,
                    'image'       => $page?->banner_image ? storage_link($page->banner_image) : null,
                    'image_alt'   => $page?->banner_image_alt,
                    'description' => $page?->short_description,
                ],
                'category' => $this->categoriesForState($state->id),
                'cities'   => $this->citiesForState($state->id),
                'attractions' => $this->attractionsForState($state->id),
                'activities' => [
                    'title' => $page?->activities_title ?: "Adventure Experiences in {$state->name}",
                    'list'  => $page ? $page->activities->map(fn ($a) => [
                        'title' => $a->title, 'description' => $a->description,
                        'best_time' => $a->best_time, 'best_for' => $a->best_for, 'approximate_cost' => $a->approximate_cost,
                    ])->values() : [],
                ],
                'highlights' => [
                    'title' => $page?->highlights_title ?: "What Makes {$state->name} Special?",
                    'list'  => $page ? $page->highlights->map(fn ($h) => ['title' => $h->title, 'description' => $h->description])->values() : [],
                ],
                'tourist_activities' => $this->touristActivitiesForState($state->id),
                'faqs' => [
                    'title'     => $page?->faq_title,
                    'sub_title' => $page?->faq_sub_title,
                    'list'      => $page ? $page->faqs->map(fn ($f) => ['question' => $f->question, 'answer' => $f->answer])->values() : [],
                ],
                'meta' => $page ? $this->metaBlock($page) : $this->emptyMetaBlock(),
            ],
        ]);
    }

    /** Experience categories that have at least one active Experience item in this state. */
    private function categoriesForState(int $stateId): \Illuminate\Support\Collection
    {
        return ExperienceCategory::where('is_active', true)
            ->where(fn ($q) => $q
                ->whereHas('experiences', fn ($qe) => $qe->where('state_id', $stateId)->where('is_active', true))
                ->orWhereHas('subcategories.experiences', fn ($qe) => $qe->where('state_id', $stateId)->where('is_active', true)))
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($c) => $this->categoryBrief($c))
            ->values();
    }

    /** Cities within this state that have at least one active Experience item. */
    private function citiesForState(int $stateId): \Illuminate\Support\Collection
    {
        $counts = Experience::where('state_id', $stateId)
            ->where('is_active', true)
            ->selectRaw('location_id, count(*) as experiences_count')
            ->groupBy('location_id')
            ->pluck('experiences_count', 'location_id');

        $pagesByLocationId = ExperiencePage::whereIn('location_id', $counts->keys())->get()->keyBy('location_id');

        return Location::whereIn('id', $counts->keys())
            ->with('state')
            ->orderBy('name')
            ->get()
            ->map(function ($loc) use ($counts, $pagesByLocationId) {
                $page = $pagesByLocationId->get($loc->id);

                return [
                    'name'              => $loc->name,
                    'state_slug'        => $loc->state?->city_guide_slug,
                    'city_slug'         => $loc->city_guide_slug,
                    'image'             => $page?->banner_image ? storage_link($page->banner_image) : null,
                    'image_alt'         => $page?->banner_image_alt,
                    'experiences_count' => $counts[$loc->id] ?? 0,
                ];
            })
            ->values();
    }

    /** Real Tourist Attraction records for this state (existing module, no separate data entry) — capped at 10. */
    private function attractionsForState(int $stateId): \Illuminate\Support\Collection
    {
        return TouristAttraction::with(['state', 'location'])
            ->where('state_id', $stateId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(10)
            ->get()
            ->map(fn ($a) => [
                'name'      => $a->name,
                'slug'      => $a->slug,
                'image'     => $a->banner_image ? storage_link($a->banner_image) : null,
                'image_alt' => $a->banner_image_alt,
                'state'     => $a->state?->city_guide_slug,
                'city'      => $a->location?->city_guide_slug,
            ])
            ->values();
    }

    /** Real Tourist Activity records for this state (existing module, no separate data entry) — capped at 10. */
    private function touristActivitiesForState(int $stateId): \Illuminate\Support\Collection
    {
        return TouristActivity::with('location')
            ->where('state_id', $stateId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(10)
            ->get()
            ->map(fn ($a) => [
                'name'        => $a->name,
                'slug'        => $a->slug,
                'image'       => $a->banner_image ? storage_link($a->banner_image) : null,
                'image_alt'   => $a->banner_image_alt,
                'description' => $a->short_description,
                'city_slug'   => $a->location?->city_guide_slug,
            ])
            ->values();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/experiences/{stateSlug}/{citySlug}  — City hub — e.g. /kerala/munnar/experiences
    // ─────────────────────────────────────────────────────────────────────────
    public function cityPage(string $stateSlug, string $citySlug): JsonResponse
    {
        $state = $this->resolveState($stateSlug);
        if (!$state) {
            abort(404);
        }

        $location = $this->resolveLocation($state->id, $citySlug);
        if (!$location) {
            abort(404);
        }

        $page = ExperiencePage::where('location_id', $location->id)->with(['faqs', 'activities', 'highlights'])->first();

        return response()->json([
            'status' => 'success',
            'type'   => 'city',
            'data' => [
                'state_name' => $state->name,
                'state_slug' => $state->city_guide_slug,
                'city_name'  => $location->name,
                'city_slug'  => $location->city_guide_slug,
                'banner' => [
                    'title'       => $page?->title,
                    'image'       => $page?->banner_image ? storage_link($page->banner_image) : null,
                    'image_alt'   => $page?->banner_image_alt,
                    'description' => $page?->short_description,
                ],
                'category'    => $this->categoriesForLocation($location->id),
                'attractions' => $this->attractionsForLocation($location->id),
                'activities' => [
                    'title' => $page?->activities_title ?: "Adventure Experiences in {$location->name}",
                    'list'  => $page ? $page->activities->map(fn ($a) => [
                        'title' => $a->title, 'description' => $a->description,
                        'best_time' => $a->best_time, 'best_for' => $a->best_for, 'approximate_cost' => $a->approximate_cost,
                    ])->values() : [],
                ],
                'highlights' => [
                    'title' => $page?->highlights_title ?: "What Makes {$location->name} Special?",
                    'list'  => $page ? $page->highlights->map(fn ($h) => ['title' => $h->title, 'description' => $h->description])->values() : [],
                ],
                'tourist_activities' => $this->touristActivitiesForLocation($location->id),
                'faqs' => [
                    'title'     => $page?->faq_title,
                    'sub_title' => $page?->faq_sub_title,
                    'list'      => $page ? $page->faqs->map(fn ($f) => ['question' => $f->question, 'answer' => $f->answer])->values() : [],
                ],
                'meta' => $page ? $this->metaBlock($page) : $this->emptyMetaBlock(),
            ],
        ]);
    }

    /** Experience categories that have at least one active Experience item in this city. */
    private function categoriesForLocation(int $locationId): \Illuminate\Support\Collection
    {
        return ExperienceCategory::where('is_active', true)
            ->whereHas('subcategories.experiences', fn ($q) => $q->where('location_id', $locationId)->where('is_active', true))
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($c) => $this->categoryBrief($c))
            ->values();
    }

    /** Real Tourist Attraction records for this city (existing module, no separate data entry) — capped at 10. */
    private function attractionsForLocation(int $locationId): \Illuminate\Support\Collection
    {
        return TouristAttraction::with(['state', 'location'])
            ->where('location_id', $locationId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(10)
            ->get()
            ->map(fn ($a) => [
                'name'      => $a->name,
                'slug'      => $a->slug,
                'image'     => $a->banner_image ? storage_link($a->banner_image) : null,
                'image_alt' => $a->banner_image_alt,
                'state'     => $a->state?->city_guide_slug,
                'city'      => $a->location?->city_guide_slug,
            ])
            ->values();
    }

    /** Real Tourist Activity records for this city (existing module, no separate data entry) — capped at 10. */
    private function touristActivitiesForLocation(int $locationId): \Illuminate\Support\Collection
    {
        return TouristActivity::with('location')
            ->where('location_id', $locationId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(10)
            ->get()
            ->map(fn ($a) => [
                'name'        => $a->name,
                'slug'        => $a->slug,
                'image'       => $a->banner_image ? storage_link($a->banner_image) : null,
                'image_alt'   => $a->banner_image_alt,
                'description' => $a->short_description,
                'city_slug'   => $a->location?->city_guide_slug,
            ])
            ->values();
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function metaBlock($model): array
    {
        return [
            'meta_title'       => $model->meta_title,
            'meta_description' => $model->meta_description,
            'meta_keywords'    => $model->meta_keywords,
            'h1_heading'       => $model->h1_heading,
            'meta_details'     => $model->meta_details,
        ];
    }

    private function emptyMetaBlock(): array
    {
        return [
            'meta_title' => null, 'meta_description' => null, 'meta_keywords' => null,
            'h1_heading' => null, 'meta_details' => null,
        ];
    }

    /** Resolve a State by its clean slug (Str::slug(name)), independent of the SEO `slug` column. */
    private function resolveState(string $slug): ?State
    {
        $match = State::active()->get(['id', 'name'])->first(
            fn ($s) => $s->city_guide_slug === $slug
        );

        return $match ? State::findOrFail($match->id) : null;
    }

    /** Resolve a Location (within a state) by its clean slug. */
    private function resolveLocation(int $stateId, string $slug): ?Location
    {
        $match = Location::active()->forState($stateId)->get(['id', 'name'])->first(
            fn ($l) => $l->city_guide_slug === $slug
        );

        return $match ? Location::findOrFail($match->id) : null;
    }
}
