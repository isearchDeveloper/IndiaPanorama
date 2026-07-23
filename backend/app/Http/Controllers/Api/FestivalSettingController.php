<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FestivalHomeResource;
use App\Models\Festival;
use App\Models\FestivalSetting;
use App\Models\FestivalStatePage;
use App\Models\Package;
use App\Models\State;
use Illuminate\Http\JsonResponse;

class FestivalSettingController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/page/settings/festivals
    // Single overview/hub landing page for the Festivals section.
    // ─────────────────────────────────────────────────────────────────────────
    public function show(): JsonResponse
    {
        $s = FestivalSetting::current();
        $s->load(['faqs', 'highlights', 'whyExperiences']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Festivals settings',
            'data'    => new FestivalHomeResource([
                'setting'           => $s,
                'festivals'         => $this->festivals(),
                'by_state'          => $this->festivalsByState(),
                'by_month'          => $this->festivalsByMonth(),
                'upcoming'          => $this->upcomingFestivals(),
                'festival_packages' => $this->festivalPackages(),
            ]),
        ]);
    }

    /** "All Cities Festivals" grid — active Festival entries from Manage Festival. */
    private function festivals(): \Illuminate\Support\Collection
    {
        return Festival::where('is_active', true)
            ->with('state')
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($f) => $f->state)
            ->map(fn ($f) => [
                'name'      => $f->name,
                'slug'      => $f->slug,
                'image'     => $f->image ? storage_link($f->image) : null,
                'image_alt' => $f->image_alt,
            ])
            ->values();
    }

    /** "Explore Festivals by State" — only states with an active Festival State Page (Admin → Festivals By State), with its cities & popular festivals. */
    private function festivalsByState(): \Illuminate\Support\Collection
    {
        return FestivalStatePage::where('is_active', true)
            ->whereHas('state')
            ->with(['state.cities'])
            ->get()
            ->sortBy(fn ($page) => $page->state->name)
            ->map(fn ($page) => [
                'state_name'        => $page->state->name,
                'state_slug'        => $page->state->city_guide_slug,
                'image'             => $page->banner_image ? storage_link($page->banner_image) : null,
                'image_alt'         => $page->banner_image_alt,
                'cities'            => $page->state->cities->pluck('name')->take(5)->values(),
                'popular_festivals' => Festival::where('state_id', $page->state_id)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->pluck('name')
                    ->values(),
            ])
            ->values();
    }

    /** "Explore Festivals by Month" tabs — active festivals that have a month assigned, grouped 1-12. */
    private function festivalsByMonth(): \Illuminate\Support\Collection
    {
        return Festival::groupedByMonth();
    }

    /**
     * "Upcoming Festivals" carousel — automatic. Looks at the current month and works
     * forward (next month, the one after, wrapping back around) so it never includes
     * the current month itself. Each entry's "days left" counts down to the end of
     * its own month (next year's occurrence if that month has already passed this year).
     */
    private function upcomingFestivals(): \Illuminate\Support\Collection
    {
        $todayStart   = now()->startOfDay();
        $currentMonth = $todayStart->month;

        return Festival::where('is_active', true)
            ->whereNotNull('month')
            ->where('month', '!=', $currentMonth)
            ->orderBy('sort_order')
            ->get()
            ->sortBy(fn ($f) => ($f->month - $currentMonth + 12) % 12)
            ->map(function ($f) use ($todayStart, $currentMonth) {
                $year = $todayStart->year + ($f->month < $currentMonth ? 1 : 0);
                $endOfMonth = \Illuminate\Support\Carbon::create($year, $f->month, 1)->endOfMonth()->startOfDay();

                return [
                    'name'               => $f->name,
                    'slug'               => $f->slug,
                    'image'              => $f->image ? storage_link($f->image) : null,
                    'image_alt'          => $f->image_alt,
                    'short_desc'         => $f->short_description,
                    'month'              => $f->month,
                    'days_left_in_month' => $todayStart->diffInDays($endOfMonth),
                ];
            })
            ->values();
    }

    /** "Festival Tour Packages" — Packages explicitly flagged is_festival_package. */
    private function festivalPackages(): \Illuminate\Support\Collection
    {
        return Package::where('is_active', 1)
            ->where('is_festival_package', 1)
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
            ])
            ->values();
    }
}
