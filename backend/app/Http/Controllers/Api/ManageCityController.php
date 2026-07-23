<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Festival;
use App\Models\FestivalStatePage;
use App\Models\Location;
use App\Models\ManageCity;
use App\Models\Package;
use App\Models\State;
use App\Models\TouristAttraction;
use Illuminate\Support\Str;

class ManageCityController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/state/{state}
    // ──────────────────────────────────────────────────────────────────────────

    public function statePage(string $stateSlug)
    {
        $state = State::get()->first(fn ($s) => Str::slug($s->name) === $stateSlug)
            ?? State::where('slug', $stateSlug)->first();

        if (! $state) {
            return response()->json(['status' => 'error', 'message' => 'State not found.'], 404);
        }

        $city = ManageCity::with(['howToReach', 'quickFacts', 'faqs', 'meta', 'topPlaces', 'thingsToDo'])
            ->where('state_id', $state->id)
            ->whereNull('location_id')
            ->first();

        if (! $city) {
            return response()->json(['status' => 'error', 'message' => 'City page not found.'], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'City guide details',
            'data'    => $this->buildStateResponse($city, $state),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/state/city/{state}/{city}
    // ──────────────────────────────────────────────────────────────────────────

    public function cityPage(string $stateSlug, string $citySlug)
    {
        $state = State::get()->first(fn ($s) => Str::slug($s->name) === $stateSlug)
            ?? State::where('slug', $stateSlug)->first();

        if (! $state) {
            return response()->json(['status' => 'error', 'message' => 'State not found.'], 404);
        }

        $location = Location::where('state_id', $state->id)
            ->get()
            ->first(fn ($l) => Str::slug($l->name) === $citySlug || $l->slug === $citySlug);

        if (! $location) {
            return response()->json(['status' => 'error', 'message' => 'City not found.'], 404);
        }

        $city = ManageCity::with(['howToReach', 'quickFacts', 'faqs', 'meta', 'topPlaces', 'thingsToDo'])
            ->where('location_id', $location->id)
            ->first();

        if (! $city) {
            return response()->json(['status' => 'error', 'message' => 'City page not found.'], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'City guide details',
            'data'    => $this->buildCityResponse($city, $location, $state),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // State response builder
    // ──────────────────────────────────────────────────────────────────────────

    private function buildStateResponse(ManageCity $city, State $state): array
    {
        // Packages in this state
        $packages = Package::where('is_active', 1)
            ->where(fn ($q) =>
                $q->whereHas('location', fn ($qq) => $qq->where('state_id', $state->id))
                  ->orWhereHas('extraDestinations.location', fn ($qq) => $qq->where('state_id', $state->id))
            )
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($p) => [
                'title'             => $p->title,
                'slug'              => $p->slug,
                'image'             => $p->primary_image ? storage_link($p->primary_image) : null,
                'image_alt'         => $p->primary_image_alt,
                'price'             => $p->price,
                'short_description' => $p->short_description ?? null,
            ])->values();

        // Tourist attractions for this state
        $attractions = TouristAttraction::with(['state:id,slug', 'location:id,slug'])
            ->where('state_id', $state->id)
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($a) => [
                'image' => $a->banner_image ? storage_link($a->banner_image) : null,
                'alt'   => $a->banner_image_alt,
                'title' => $a->name,
                'slug'  => $a->slug,
                'state' => $a->state?->slug,
                'city'  => $a->location?->slug,
            ])->values();

        // Top tourist places (from this state page's own data)
        $topPlaces = $city->topPlaces->map(fn ($p) => [
            'name'        => $p->name,
            'description' => $p->description ?? '',
        ])->values();

        // Things to do (from this state page's own data)
        $thingsToDo = $city->thingsToDo->map(fn ($a) => [
            'title'       => $a->title,
            'description' => $a->description ?? '',
            'duration'    => $a->duration,
            'best_for'    => $a->best_for,
            'approx_cost' => $a->approx_cost,
        ])->values();

        // Destination cities (locations) in the state
        $destinations = Location::with('details')
            ->where('state_id', $state->id)
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($l) => [
                'name'      => $l->name,
                'slug'      => Str::slug($l->name),
                'image'     => $l->details?->banner_image ? storage_link($l->details->banner_image) : null,
                'image_alt' => $l->details?->banner_image_alt,
            ])->values();

        // Cities that have a ManageCity page (location-level)
        $cityPages = ManageCity::with('location')
            ->whereHas('location', fn ($q) => $q->where('state_id', $state->id))
            ->whereNotNull('location_id')
            ->where('is_active', 1)
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($c) => [
                'name'      => $c->location?->name ?? '',
                'slug'      => Str::slug($c->location?->name ?? ''),
                'image'     => $c->banner_image ? storage_link($c->banner_image) : null,
                'image_alt' => $c->banner_image_alt,
            ])->values();

        // Festivals for this state
        $festivalPage  = FestivalStatePage::where('state_id', $state->id)->where('is_active', 1)->first();
        $festivalList  = Festival::where('state_id', $state->id)
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($f) => [
                'image' => $f->image ? storage_link($f->image) : null,
                'alt'   => $f->image_alt,
                'title' => $f->name,
            ])->values();

        return [
            'banner' => [
                'title'       => $city->title,
                'tagline'     => $city->sub_title,
                'banner_text' => $city->banner_text,
                'image'       => $city->banner_image ? storage_link($city->banner_image) : null,
                'image_alt'   => $city->banner_image_alt,
            ],

            'short_description' => $city->about,

            'meta' => $city->meta ? [
                'meta_title'       => $city->meta->meta_title,
                'meta_description' => $city->meta->meta_description,
                'meta_keywords'    => $city->meta->meta_keywords,
                'h1_heading'       => $city->meta->h1_heading,
                'meta_details'     => $city->meta->meta_details,
            ] : null,

            'quick_facts' => $city->quickFacts->map(fn ($f) => [
                'label' => $f->label,
                'text'  => $f->value,
            ])->values(),

            'packages'           => $packages,
            'top_tourist_places' => $topPlaces,
            'destinations'       => $destinations,
            'cities'             => $cityPages,
            'things_to_do'       => $thingsToDo,
            'attractions'        => $attractions,

            'how_to_reach' => $city->howToReach->map(fn ($r) => [
                'mode'        => $r->mode,
                'description' => $r->description,
            ])->values(),

            'travel_tips' => $city->travel_tips,

            'festivals' => [
                'intro' => $festivalPage?->short_description ?? '',
                'list'  => $festivalList,
            ],

            'things_to_know' => $city->things_to_know,

            'religious_tourism' => [
                'intro'   => $city->religious_tourism,
                'temples' => [],
            ],

            'souvenirs'     => $city->souvenirs_to_shop,
            'popular_dishes'=> $city->popular_dishes,

            'faqs' => [
                'title' => $city->faq_title,
                'list'  => $city->faqs->map(fn ($f) => [
                    'question' => $f->question,
                    'answer'   => $f->answer,
                ])->values(),
            ],
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // City response builder
    // ──────────────────────────────────────────────────────────────────────────

    private function buildCityResponse(ManageCity $city, Location $location, State $state): array
    {
        // Packages for this city
        $packages = Package::where('is_active', 1)
            ->where(fn ($q) =>
                $q->whereHas('location', fn ($qq) => $qq->where('id', $location->id))
                  ->orWhereHas('extraDestinations.location', fn ($qq) => $qq->where('id', $location->id))
            )
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($p) => [
                'title'             => $p->title,
                'slug'              => $p->slug,
                'image'             => $p->primary_image ? storage_link($p->primary_image) : null,
                'image_alt'         => $p->primary_image_alt,
                'price'             => $p->price,
                'short_description' => $p->short_description ?? null,
            ])->values();

        // Top tourist places (from this city page's own data)
        $topPlaces = $city->topPlaces->map(fn ($p) => [
            'name'        => $p->name,
            'description' => $p->description ?? '',
        ])->values();

        // Things to do (from this city page's own data)
        $thingsToDo = $city->thingsToDo->map(fn ($a) => [
            'title'       => $a->title,
            'description' => $a->description ?? '',
            'duration'    => $a->duration,
            'best_for'    => $a->best_for,
            'approx_cost' => $a->approx_cost,
        ])->values();

        // Attractions (tourist attractions for this city)
        $attractions = TouristAttraction::with(['state:id,slug', 'location:id,slug'])
            ->where('location_id', $location->id)
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($a) => [
                'image' => $a->banner_image ? storage_link($a->banner_image) : null,
                'alt'   => $a->banner_image_alt,
                'title' => $a->name,
                'slug'  => $a->slug,
                'state' => $a->state?->slug,
                'city'  => $a->location?->slug,
            ])->values();

        // Festivals for this specific city (matched by location_text, not the whole state)
        $festivalList = Festival::where('state_id', $state->id)
            ->where('is_active', 1)
            ->whereRaw('LOWER(TRIM(location_text)) = LOWER(TRIM(?))', [$location->name])
            ->orderBy('sort_order')
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn ($f) => [
                'image' => $f->image ? storage_link($f->image) : null,
                'alt'   => $f->image_alt,
                'title' => $f->name,
            ])->values();

        $festivalPage = FestivalStatePage::where('state_id', $state->id)->where('is_active', 1)->first();

        return [
            'banner' => [
                'title'       => $city->title,
                'tagline'     => $city->sub_title,
                'banner_text' => $city->banner_text,
                'image'       => $city->banner_image ? storage_link($city->banner_image) : null,
                'image_alt'   => $city->banner_image_alt,
            ],

            'short_description' => $city->about,

            'meta' => $city->meta ? [
                'meta_title'       => $city->meta->meta_title,
                'meta_description' => $city->meta->meta_description,
                'meta_keywords'    => $city->meta->meta_keywords,
                'h1_heading'       => $city->meta->h1_heading,
                'meta_details'     => $city->meta->meta_details,
            ] : null,

            'quick_facts' => $city->quickFacts->map(fn ($f) => [
                'label' => $f->label,
                'text'  => $f->value,
            ])->values(),

            'packages'           => $packages,
            'top_tourist_places' => $topPlaces,
            'destinations'       => [],
            'things_to_do'       => $thingsToDo,
            'attractions'        => $attractions,

            'how_to_reach' => $city->howToReach->map(fn ($r) => [
                'mode'        => $r->mode,
                'description' => $r->description,
            ])->values(),

            'travel_tips' => $city->travel_tips,

            'festivals' => [
                'intro' => $festivalPage?->short_description ?? '',
                'list'  => $festivalList,
            ],

            'things_to_know'    => $city->things_to_know,
            'religious_tourism' => [
                'intro'   => $city->religious_tourism,
                'temples' => [],
            ],
            'souvenirs'      => $city->souvenirs_to_shop,
            'popular_dishes' => $city->popular_dishes,

            'faqs' => [
                'title' => $city->faq_title,
                'list'  => $city->faqs->map(fn ($f) => [
                    'question' => $f->question,
                    'answer'   => $f->answer,
                ])->values(),
            ],
        ];
    }
}
