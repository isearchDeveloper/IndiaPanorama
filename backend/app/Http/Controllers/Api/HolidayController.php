<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HolidaySetting;
use App\Models\State;
use Illuminate\Support\Facades\DB;

class HolidayController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/page/settings/holidays
    // Returns all active holiday CMS pages + states with package counts
    // ─────────────────────────────────────────────────────────────────────────
    public function index()
    {
        $settings = HolidaySetting::where('is_active', true)
            ->with(['details', 'meta', 'faqs'])
            ->get()
            ->map(fn ($h) => $this->formatHoliday($h))
            ->values();

        $states = $this->statesWithCounts();

        return response()->json([
            'status'  => 'success',
            'message' => 'Holiday settings',
            'data'    => [
                'settings' => $settings,
                'states'   => $states,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/page/settings/holidays/{slug}
    // Returns a specific holiday CMS page + states with package counts
    // ─────────────────────────────────────────────────────────────────────────
    public function show(string $slug)
    {
        $holiday = HolidaySetting::where('slug', $slug)
            ->where('is_active', true)
            ->with(['details', 'meta', 'faqs'])
            ->firstOrFail();

        return response()->json([
            'status'  => 'success',
            'message' => 'Holiday details',
            'data'    => [
                'holiday' => $this->formatHoliday($holiday),
                'states'  => $this->statesWithCounts(),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Shared: states ordered by package count desc
    // ─────────────────────────────────────────────────────────────────────────
    private function statesWithCounts(): \Illuminate\Support\Collection
    {
        // Count active packages per state via packages.location_id → locations.state_id
        $counts = DB::table('packages')
            ->select('locations.state_id', DB::raw('COUNT(DISTINCT packages.id) as cnt'))
            ->join('locations', 'locations.id', '=', 'packages.location_id')
            ->where('packages.is_active', true)
            ->whereNull('packages.deleted_at')
            ->whereNotNull('locations.state_id')
            ->groupBy('locations.state_id')
            ->pluck('cnt', 'state_id');

        return State::active()
            ->with('details')
            ->get()
            ->filter(fn ($s) => ($counts[$s->id] ?? 0) > 0)
            ->map(fn ($s) => [
                'name'             => $s->name,
                'slug'             => $s->slug,
                'banner_image'     => $s->details?->banner_image
                                      ? storage_link($s->details->banner_image)
                                      : null,
                'banner_image_alt' => $s->details?->banner_image_alt ?? $s->name,
                'packages_count'   => (int) ($counts[$s->id] ?? 0),
            ])
            ->sortByDesc('packages_count')
            ->values();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Format a HolidaySetting model into the API response shape
    // ─────────────────────────────────────────────────────────────────────────
    private function formatHoliday(HolidaySetting $h): array
    {
        $d = $h->details;

        return [
            'details' => $d ? [
                'banner_image'                 => $d->banner_image ? storage_link($d->banner_image) : null,
                'banner_image_alt'             => $d->banner_image_alt,
                'banner_title'                 => $d->banner_title,
                'banner_description'           => $d->banner_description,
                'short_description'            => $d->short_description,
                'long_description'             => $d->long_description,
                'popular_packages_heading'     => $d->popular_packages_heading,
                'popular_packages_description' => $d->popular_packages_description,
            ] : null,

            'meta' => $h->meta ? [
                'meta_title'       => $h->meta->meta_title,
                'meta_description' => $h->meta->meta_description,
                'meta_keywords'    => $h->meta->meta_keywords,
                'h1_heading'       => $h->meta->h1_heading,
                'meta_details'     => $h->meta->meta_details,
            ] : null,

            'faqs' => [
                'faq_title'     => $h->faq_title,
                'faq_image'     => $h->faq_image ? storage_link($h->faq_image) : null,
                'faq_image_alt' => $h->faq_image_alt,
                'list'          => $h->faqs->map(fn ($f) => [
                    'question' => $f->question,
                    'answer'   => $f->answer,
                ])->values(),
            ],
        ];
    }
}
