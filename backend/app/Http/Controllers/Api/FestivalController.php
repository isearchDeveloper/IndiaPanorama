<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Festival;
use App\Models\FestivalStatePage;
use App\Models\Package;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class FestivalController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/page/settings/festivals/state/{slug}
    // Per-state "Festivals of {State}" landing page.
    // ─────────────────────────────────────────────────────────────────────────
    public function statePage(string $stateSlug): JsonResponse
    {
        $state = State::get()->first(fn ($s) => Str::slug($s->name) === $stateSlug);
        if (!$state) {
            return invalidRequest();
        }

        $page = FestivalStatePage::with(['whyVisits', 'faqs', 'featuredFestival'])
            ->where('state_id', $state->id)
            ->where('is_active', true)
            ->first();

        if (!$page) {
            return invalidRequest();
        }

        $popularFestivals = Festival::where('state_id', $state->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(capped_limit(request()))
            ->get();

        $f = $page->featuredFestival;

        return response()->json([
            'status'  => 'success',
            'message' => 'Festival State Page',
            'data'    => [
                'banner' => [
                    'title'     => $page->title ?: ('Festivals of ' . $state->name),
                    'banner_text' => $page->banner_text,
                    'image'     => $page->banner_image ? storage_link($page->banner_image) : null,
                    'image_alt' => $page->banner_image_alt,
                ],

                'short_description' => $page->short_description,

                'popular_festivals' => [
                    'title' => 'Popular Festivals in ' . $state->name,
                    'items' => $popularFestivals->map(fn ($pf) => [
                        'name'              => $pf->name,
                        'slug'              => $pf->slug,
                        'image'             => $pf->image ? storage_link($pf->image) : null,
                        'image_alt'         => $pf->image_alt,
                        'location_text'     => $pf->location_text,
                        'month_text'        => $pf->month_text,
                        'short_description' => $pf->short_description,
                    ])->values(),
                ],

                'explore_by_month' => Festival::groupedByMonth(),

                'featured_festival' => $f ? [
                    'name'              => $f->name,
                    'slug'              => $f->slug,
                    'image'             => $f->image ? storage_link($f->image) : null,
                    'image_alt'         => $f->image_alt,
                    'location_text'     => $f->location_text,
                    'month_text'        => $f->month_text,
                    'duration_text'     => $f->duration_text,
                    'short_description' => $f->short_description,
                ] : null,

                'why_visit' => [
                    'title'     => $page->why_visit_title,
                    'sub_title' => $page->why_visit_sub_title,
                    'items'     => $page->whyVisits->map(fn ($w) => [
                        'title'       => $w->title,
                        'description' => $w->description,
                    ])->values(),
                ],

                'faqs' => [
                    'title'     => $page->faq_title,
                    'sub_title' => $page->faq_sub_title,
                    'list'      => $page->faqs->map(fn ($fq) => [
                        'question' => $fq->question,
                        'answer'   => $fq->answer,
                    ])->values(),
                ],

                'state_packages' => [
                    'title' => $state->name . ' Tour Packages',
                    'items' => $this->statePackages($state->id),
                ],

                'meta' => [
                    'meta_title'       => $page->meta_title,
                    'meta_description' => $page->meta_description,
                    'meta_keywords'    => $page->meta_keywords,
                    'h1_heading'       => $page->h1_heading,
                    'meta_details'     => $page->meta_details,
                ],
            ],
        ]);
    }

    /** "{State} Tour Packages" — active packages whose destination (or an extra stop) falls in this state. */
    private function statePackages(int $stateId, int $limit = 8): \Illuminate\Support\Collection
    {
        return Package::where('is_active', 1)
            ->where(fn ($q) =>
                $q->whereHas('location', fn ($qq) => $qq->where('state_id', $stateId))
                  ->orWhereHas('extraDestinations.location', fn ($qq) => $qq->where('state_id', $stateId))
            )
            ->with(['details', 'location'])
            ->orderBy(list_config()['order_by'], list_config()['direction'])
            ->limit($limit)
            ->get()
            ->map(fn ($p) => [
                'title'           => $p->title,
                'slug'            => $p->slug,
                'image'           => $p->primary_image ? storage_link($p->primary_image) : null,
                'image_alt'       => $p->primary_image_alt,
                'short_description' => $p->short_description,
                'duration_days'   => $p->details?->duration_days,
                'duration_nights' => $p->details?->duration_nights,
                'location'        => $p->location?->name,
            ])
            ->values();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/page/settings/festivals/{slug}
    // Single festival detail page.
    // ─────────────────────────────────────────────────────────────────────────
    public function show(string $slug): JsonResponse
    {
        $festival = Festival::where('slug', $slug)
            ->where('is_active', true)
            ->with(['state', 'keyExperiences', 'howToReach', 'whyVisits', 'faqs', 'meta', 'stats', 'highlights', 'places'])
            ->first();

        if (!$festival) {
            return invalidRequest();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Festival Detail',
            'data'    => [
                // 1. Banner
                'banner' => [
                    'title'       => $festival->name,
                    'subtitle'    => $festival->banner_subtitle,
                    'description' => $festival->banner_description,
                    'image'       => $festival->image ? storage_link($festival->image) : null,
                    'image_alt'   => $festival->image_alt,
                ],

                // 2. Intro — short description + illustration shown beside it
                'short_description' => $festival->short_description,
                'intro_image'        => $festival->intro_image ? storage_link($festival->intro_image) : null,
                'intro_image_alt'    => $festival->intro_image_alt,

                'state' => [
                    'name' => $festival->state?->name,
                    'slug' => $festival->state?->city_guide_slug,
                ],
                'month' => $festival->month,

                // 3. Quick Stats — e.g. "10 Days of Celebration", "3000+ Years of Tradition"
                'stats' => $festival->stats->map(fn ($s) => [
                    'value' => $s->value,
                    'label' => $s->label,
                ])->values(),

                // 4. Festival Highlights gallery
                'highlights' => [
                    'title' => $festival->highlights_title,
                    'items' => $festival->highlights->map(fn ($h) => [
                        'image'     => $h->image ? storage_link($h->image) : null,
                        'image_alt' => $h->image_alt ?: $h->label,
                        'label'     => $h->label,
                    ])->values(),
                ],

                // 5. Key Experiences
                'key_experiences' => [
                    'title' => $festival->key_experience_title,
                    'items' => $festival->keyExperiences->map(fn ($k) => [
                        'icon'  => $k->icon ? storage_link($k->icon) : null,
                        'label' => $k->label,
                    ])->values(),
                ],

                // 6. Popular Places to Experience
                'popular_places' => [
                    'title' => $festival->places_title,
                    'items' => $festival->places->map(fn ($p) => [
                        'image'     => $p->image ? storage_link($p->image) : null,
                        'image_alt' => $p->image_alt,
                        'name'      => $p->name,
                    ])->values(),
                ],

                // 7. Long Description
                'long_description' => $festival->long_description,

                // 8. How to Reach
                'how_to_reach' => $festival->howToReach->map(fn ($h) => [
                    'mode'        => $h->mode,
                    'description' => $h->description,
                ])->values(),

                // 9. Popular Festival Packages — packages explicitly linked to this festival
                'festival_packages' => [
                    'title' => $festival->packages_title ?: ('Popular ' . $festival->name . ' Packages'),
                    'items' => $this->festivalPackages($festival->id),
                ],

                // 10. Why Visit
                'why_visit' => [
                    'title' => $festival->why_visit_title,
                    'items' => $festival->whyVisits->map(fn ($w) => [
                        'title'       => $w->title,
                        'description' => $w->description,
                    ])->values(),
                ],

                // 11. FAQs
                'faqs' => [
                    'title' => $festival->faq_title,
                    'list'  => $festival->faqs->map(fn ($f) => [
                        'question' => $f->question,
                        'answer'   => $f->answer,
                    ])->values(),
                ],

                // 12. Meta
                'meta' => [
                    'meta_title'       => $festival->meta?->meta_title,
                    'meta_description' => $festival->meta?->meta_description,
                    'meta_keywords'    => $festival->meta?->meta_keywords,
                    'h1_heading'       => $festival->meta?->h1_heading,
                    'meta_details'     => $festival->meta?->meta_details,
                ],
            ],
        ]);
    }

    /** Packages explicitly linked to a specific festival (via packages.festival_id). */
    private function festivalPackages(int $festivalId, int $limit = 8): \Illuminate\Support\Collection
    {
        return Package::where('is_active', 1)
            ->where('festival_id', $festivalId)
            ->with(['details', 'location'])
            ->orderBy(list_config()['order_by'], list_config()['direction'])
            ->limit($limit)
            ->get()
            ->map(fn ($p) => [
                'title'           => $p->title,
                'slug'            => $p->slug,
                'image'           => $p->primary_image ? storage_link($p->primary_image) : null,
                'image_alt'       => $p->primary_image_alt,
                'short_description' => $p->short_description,
                'duration_days'   => $p->details?->duration_days,
                'duration_nights' => $p->details?->duration_nights,
                'location'        => $p->location?->name,
            ])
            ->values();
    }

}
