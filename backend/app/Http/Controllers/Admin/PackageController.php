<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Package;
use App\Models\PackageDetail;
use App\Models\PackageImage;
use App\Models\PackageItinerary;
use App\Models\PackageFaq;
use App\Models\PackageMetaData;
use App\Models\PackageCategory;
use App\Models\PackageLocation;
use App\Models\PackageSourceLocation;
use App\Models\PackageGroupDate;
use App\Models\Category;
use App\Models\Country;
use App\Models\Location;
use App\Models\State;
use App\Models\Festival;

class PackageController extends Controller
{
    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    /**
     * Build the canonical slug from title + days + nights.
     * Mirrors the JS createSlug() function so both always agree.
     */
    private function buildSlug(string $title, $days, $nights): string
    {
        $slug = Str::slug($title);

        $days   = (int) $days;
        $nights = (int) $nights;

        if ($days > 0) {
            $slug .= '-' . $days . '-' . ($days === 1 ? 'day' : 'days');
        }

        if ($nights > 0) {
            $slug .= '-' . $nights . '-' . ($nights === 1 ? 'night' : 'nights');
        }

        return $slug;
    }

    /**
     * Make a slug unique by appending an incrementing suffix when needed.
     * Optionally exclude a known package ID (for updates).
     */
    private function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug    = $base;
        $counter = 1;

        while (
            Package::where('slug', $slug)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists()
        ) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Sync group-tour departure rows using a smart upsert strategy:
     *
     * - Existing rows (matched by departure_date) → UPDATE price, total_seats, status
     *   but NEVER reduce total_seats below current booked_seats.
     * - New rows (no match) → INSERT with booked_seats = 0.
     * - Rows removed from the form → DELETE ONLY if booked_seats = 0.
     *   If booked_seats > 0, mark as 'cancelled' instead (preserve booking history).
     */
    private function syncGroupDepartures(int $packageId, array $departures): void
    {
        // Build a map of incoming dates → data
        $incoming = [];
        foreach ($departures as $dep) {
            if (empty($dep['date']) || !isset($dep['price']) || $dep['price'] === '') {
                continue;
            }
            $incoming[$dep['date']] = $dep;
        }

        // Fetch all existing rows for this package
        $existing = PackageGroupDate::where('package_id', $packageId)->get()->keyBy('departure_date');

        // ── 1. Handle incoming dates (upsert) ──
        foreach ($incoming as $date => $dep) {
            $totalSeats = (isset($dep['total_seats']) && $dep['total_seats'] !== '')
                ? (int) $dep['total_seats']
                : 20;

            if ($existing->has($date)) {
                // UPDATE existing row — preserve booked_seats, enforce floor
                $row = $existing->get($date);
                $bookedSeats = (int) $row->booked_seats;

                // total_seats can never go below what's already booked
                if ($totalSeats < $bookedSeats) {
                    $totalSeats = $bookedSeats;
                }

                $row->update([
                    'price'       => $dep['price'],
                    'total_seats' => $totalSeats,
                    'status'      => $dep['status'] ?? $row->status,
                    // booked_seats intentionally NOT touched
                ]);
            } else {
                // INSERT new row
                PackageGroupDate::create([
                    'package_id'     => $packageId,
                    'departure_date' => $date,
                    'price'          => $dep['price'],
                    'total_seats'    => $totalSeats,
                    'booked_seats'   => 0,
                    'status'         => $dep['status'] ?? 'available',
                ]);
            }
        }

        // ── 2. Handle removed dates ──
        foreach ($existing as $date => $row) {
            if (!array_key_exists($date, $incoming)) {
                if ((int) $row->booked_seats > 0) {
                    // Has bookings → cancel instead of delete (preserve history)
                    $row->update(['status' => 'cancelled']);
                } else {
                    // No bookings → safe to delete
                    $row->delete();
                }
            }
        }
    }

    /**
     * Safely delete a file from S3 without throwing.
     */
    private function deleteFromS3(?string $path): void
    {
        if (!$path) return;

        try {
            $disk = config('filesystems.upload_disk');
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        } catch (\Exception $e) {
            Log::error('[S3 Delete] ' . $e->getMessage(), ['path' => $path]);
        }
    }

    /**
     * Validation rules shared by store and update for the "publish" save type.
     * Pass $isUpdate = true to relax primary_image to nullable.
     */
    private function publishValidationRules(bool $isUpdate = false): array
    {
        return [
            'title'               => 'required|string|max:255',
            'parent'              => $isUpdate ? 'nullable|exists:states,id' : 'required|exists:states,id',
            'package_mode'        => 'nullable|string',
            'price'               => 'nullable|numeric|min:0',

            'category_id'         => 'nullable|array',
            'category_id.*'       => 'exists:categories,id',
            'duration_days'       => 'required|integer|min:1',
            'duration_nights'     => 'required|integer|min:0',
            'country_id'          => 'nullable',
            'location_id'         => 'required|array|min:1',
            'location_id.*'       => 'exists:locations,id',
            'source_location_id'  => 'required|array|min:1',
            'source_location_id.*' => 'exists:locations,id',
            'tour_highlights'     => 'nullable|string',
            'primary_image'       => ($isUpdate ? 'nullable' : 'required') . '|string|exists:media,path',
            'gallery_images'         => 'nullable|array',
            'gallery_images.*.path'  => 'required|string|exists:media,path',
            'gallery_images.*.alt'   => 'nullable|string',
            // author_name set automatically from logged-in user
            'departures'               => 'nullable|array',
            'departures.*.date'        => 'nullable|date',
            'departures.*.price'       => 'nullable|numeric|min:0',
            'departures.*.total_seats' => 'nullable|integer|min:1',
            'departures.*.status'      => 'nullable|in:available,soldout,cancelled',
        ];
    }

    /**
     * Validate that no incoming departure sets total_seats below the
     * currently booked_seats for that date.
     *
     * Returns an array of error messages (empty = all good).
     * Pass $packageId = null for new packages (no existing rows to check).
     */
    private function validateDepartureSeats(?int $packageId, array $departures): array
    {
        if (!$packageId) {
            return []; // New package — no existing bookings possible
        }

        $errors = [];

        // Fetch only rows that have actual bookings
        $existing = PackageGroupDate::where('package_id', $packageId)
            ->where('booked_seats', '>', 0)
            ->get(['departure_date', 'booked_seats'])
            ->keyBy('departure_date');

        if ($existing->isEmpty()) {
            return []; // No bookings at all — nothing to protect
        }

        foreach ($departures as $index => $dep) {
            if (empty($dep['date'])) {
                continue;
            }

            $date = $dep['date'];

            if (!$existing->has($date)) {
                continue; // This date has no bookings — safe to set any value
            }

            $bookedSeats = (int) $existing->get($date)->booked_seats;
            $newTotal    = (isset($dep['total_seats']) && $dep['total_seats'] !== '')
                ? (int) $dep['total_seats']
                : 20;

            if ($newTotal < $bookedSeats) {
                $errors["departures.{$index}.total_seats"] =
                    "Departure {$date}: Total Seats ({$newTotal}) cannot be less than already booked seats ({$bookedSeats}). Minimum allowed: {$bookedSeats}.";
            }
        }

        return $errors;
    }

    // =========================================================

    public function index(Request $r)
    {
        // --- Single package lookup (AJAX) ---
        if ($r->exists('id') && !$r->exists('faqs')) {
            $package = Package::where('id', $r->id)
                ->with([
                    'category',
                    'details',
                    'location.country',
                    'source_location.country',
                    'images',
                    'itineraries',
                    'faqs',
                ])
                ->first();

            if ($package) {
                $package->primary_image = storage_link($package->primary_image);
                $package->images->each(function ($img) {
                    $img->image_path = storage_link($img->image_path);
                });
            }

            if ($r->ajax()) {
                return response()->json(['status' => 'success', 'package' => $package]);
            }
        }

        // --- FAQ-only lookup (AJAX) ---
        if ($r->exists('id') && $r->exists('faqs')) {
            $package = Package::with('faqs')->find($r->id);

            if ($r->ajax()) {
                return response()->json(['status' => 'success', 'package' => $package]);
            }
        }

        // --- List ---
        $allCount      = Package::count();
        $activeCount   = Package::where('is_active', 1)->count();
        $inactiveCount = Package::where('is_active', 0)->count();

        $query = Package::with(['category', 'details', 'location.country'])
            ->orderBy(list_config()['order_by'], list_config()['direction']);

        $searchTerm    = '';
        $currentStatus = '';

        if ($search = $r->get('package')) {
            $searchTerm = $search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('author_name', 'LIKE', "%{$search}%");
            });
        }

        if ($status = $r->get('status')) {
            $currentStatus = $status;
            if ($status === 'active')   $query->where('is_active', 1);
            if ($status === 'inactive') $query->where('is_active', 0);
        }

        $packages = $query->paginate(25)->withQueryString();

        if ($r->ajax()) {
            $html = view('admin.packages.list', compact('packages'))->render();
            return response($html, 200)->header('Content-Type', 'text/html');
        }

        return view('admin.packages.index', compact(
            'packages',
            'allCount',
            'activeCount',
            'inactiveCount',
            'searchTerm',
            'currentStatus'
        ));
    }

    // =========================================================
    // CREATE
    // =========================================================

    public function create()
    {
        $categories = Category::where('is_active', 1)
            ->orderBy('id', 'desc')->get();
        $festivals = Festival::where('is_active', 1)->orderBy('name')->get(['id', 'name']);

        return view('admin.packages.create', compact(
            'categories', 'festivals'
        ));
    }

    // =========================================================
    // STORE
    // =========================================================

    public function store(Request $r)
    {
        $saveType = $r->input('save_type', 'publish');

        // ---- Validation ----
        if ($saveType === 'publish') {
            $r->validate($this->publishValidationRules(false));

            // Extra rule: group tour must have at least one departure
            if ($r->input('package_mode') === 'group_tour') {
                $departures = array_filter(
                    $r->input('departures', []),
                    fn($d) =>
                    !empty($d['date']) && isset($d['price']) && $d['price'] !== ''
                );
                if (empty($departures)) {
                    return back()
                        ->withInput()
                        ->withErrors(['departures' => 'At least one valid departure date is required for a Group Tour.']);
                }
                // New package: no existing bookings to check, skip seat validation
            }
        } else {
            // Draft: only title required
            $r->validate(['title' => 'required|string|max:255']);
        }

        try {
            DB::beginTransaction();

            // ---- Resolve first IDs ----
            $destinationLocations = $r->input('location_id', []);
            $sourceLocations      = $r->input('source_location_id', []);
            $categoryIds          = $r->input('category_id', []);

            $firstDestination = $destinationLocations[0] ?? null;
            $firstSource      = $sourceLocations[0]      ?? null;
            $firstCategory    = $categoryIds[0]          ?? null;

            // ---- Parent state (for parent_category fields) ----
            $parentLocation = State::findOrFail($r->input('parent'));

            // ---- Build slug (unique) ----
            $baseSlug = $this->buildSlug(
                $r->input('title'),
                $r->input('duration_days'),
                $r->input('duration_nights')
            );
            $slug = $this->uniqueSlug($baseSlug);

            // ---- Primary image (picked/uploaded via the Media Library — its
            // license was already captured at upload time, not per-usage) ----
            $primaryImagePath = $r->input('primary_image');

            // ---- Build package data ----
            $pkgData = [
                'title'               => $r->input('title'),
                'slug'                => $slug,
                'price'               => ($r->input('package_mode') === 'group_tour')
                    ? $r->input('price')
                    : null,
                'parent_category'     => $parentLocation->name,
                'parent_category_slug' => $parentLocation->slug,
                'category_id'         => $firstCategory,
                'country_id'          => 1,
                'location_id'         => $firstDestination,
                'source_location_id'  => $firstSource,
                'primary_image'       => $primaryImagePath,
                'primary_image_alt'   => $r->input('primary_image_alt'),
                'short_description'   => null,
                'long_description'    => null,
                'package_mode'        => in_array($r->input('package_mode'), ['normal', 'group_tour'])
                                            ? $r->input('package_mode')
                                            : 'normal',
                'is_top_trending'     => $r->boolean('is_top_trending'),
                'is_special_package'  => $r->boolean('is_special_package'),
                'is_festival_package' => $r->boolean('is_festival_package'),
                'festival_id'         => $r->input('festival_id') ?: null,
                'is_customized'       => $r->boolean('is_customized'),
                'author_name'         => Auth::user()?->name,
                'is_draft'            => ($saveType === 'draft') ? 1 : 0,
                'is_active'           => ($saveType === 'draft') ? 0 : 1,
            ];

            $package = Package::create($pkgData);

            // ---- Package details ----
            PackageDetail::create([
                'package_id'         => $package->id,
                'duration_days'      => $r->input('duration_days'),
                'duration_nights'    => ((int) $r->input('duration_nights') === 0) ? null : $r->input('duration_nights'),
                'tour_highlights'    => $r->input('tour_highlights'),
                'destination_covered_description' => strip_figma_paste_junk($r->input('destination_covered_description')),
                'facilities'         => null,
            ]);

            // ---- Group departures ----
            if ($r->input('package_mode') === 'group_tour') {
                $this->syncGroupDepartures($package->id, $r->input('departures', []));
            }

            // ---- Gallery images (picked/uploaded via the Media Library) ----
            foreach ($r->input('gallery_images', []) as $index => $item) {
                if (empty($item['path'])) continue;
                PackageImage::create([
                    'package_id' => $package->id,
                    'image_path' => $item['path'],
                    'image_alt'  => $item['alt'] ?? null,
                    'sort_order' => $index,
                ]);
            }

            // ---- Itineraries ----
            if ($r->has('itineraries')) {
                foreach ($r->input('itineraries') as $obj) {
                    if (empty($obj['title'])) continue;
                    PackageItinerary::create([
                        'package_id' => $package->id,
                        'title'      => strip_figma_paste_junk($obj['title']),
                        'details'    => strip_figma_paste_junk($obj['details'] ?? null),
                    ]);
                }
            }

            // ---- Pivot: destination locations (with per-city highlights) ----
            $destinationHighlights = $r->input('location_highlights', []);
            foreach ($destinationLocations as $locId) {
                DB::table('package_locations')->insert([
                    'package_id'  => $package->id,
                    'location_id' => $locId,
                    'highlights'  => $destinationHighlights[$locId] ?? null,
                ]);
            }

            // ---- Pivot: source locations ----
            foreach ($sourceLocations as $locId) {
                DB::table('package_source_locations')->insert([
                    'package_id'  => $package->id,
                    'location_id' => $locId,
                ]);
            }

            // ---- Pivot: categories ----
            foreach ($categoryIds as $catId) {
                PackageCategory::create([
                    'package_id'  => $package->id,
                    'category_id' => $catId,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[Package Store] ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Something went wrong while saving the package. Please try again.']);
        }

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package created successfully.');
    }

    // =========================================================
    // EDIT
    // =========================================================

    public function edit(Package $package)
    {
        $categories       = Category::where('is_active', 1)
            ->orderBy('id', 'desc')->get();
        $locations        = Location::where('country_id', 1)->get();
        $source_locations = Location::where('country_id', 1)->get();
        $festivals        = Festival::where('is_active', 1)->orderBy('name')->get(['id', 'name']);
        $package->load([
            'details',
            'images',
            'itineraries',
            'extraDestinations.location',
            'extraSources.location',
            'packageCategories',
            'groupDepartures',
            'location',
            'source_location',
        ]);

        $selectedSourceStateId      = $package->source_location->state_id ?? null;
        $selectedDestinationStateId = $package->location->state_id ?? null;

        // ---- Selected IDs for form pre-population ----
        $selectedCategoryIds = $package->packageCategories
            ->pluck('category_id')
            ->push($package->category_id)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $selectedDestinationIds = $package->extraDestinations
            ->pluck('location_id')
            ->push($package->location_id)
            ->filter()->unique()->values()->toArray();

        $selectedSourceIds = $package->extraSources
            ->pluck('location_id')
            ->push($package->source_location_id)
            ->filter()->unique()->values()->toArray();

        $destinationHighlights = $package->extraDestinations
            ->pluck('highlights', 'location_id')
            ->filter()
            ->toArray();

        $groupDepartures = $package->groupDepartures->toArray();

        return view('admin.packages.edit', compact(
            'package',
            'categories',
            'locations',
            'source_locations',
            'festivals',
            'selectedCategoryIds',
            'selectedDestinationIds',
            'selectedSourceIds',
            'selectedSourceStateId',
            'selectedDestinationStateId',
            'destinationHighlights',
            'groupDepartures'
        ));
    }

    // =========================================================
    // UPDATE
    // =========================================================

    public function update(Request $r, Package $package)
    {
        // ----------------------------------------------------------
        // Branch A: Quick status toggle  (AJAX only)
        // ----------------------------------------------------------
        if ($r->exists('status') && !$r->exists('meta_setting')) {
            $r->validate(['status' => 'required|boolean']);
            $package->is_active = (bool) $r->input('status');
            $package->save();

            if ($r->ajax()) {
                return response()->json(['status' => 'success', 'message' => 'Status updated.']);
            }
            return redirect()->back()->with('success', 'Status updated.');
        }

        // ----------------------------------------------------------
        // Branch B: Meta settings only
        // ----------------------------------------------------------
        if ($r->exists('meta_setting')) {
            $r->validate([
                'meta_title'       => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords'    => 'nullable|string',
                'h1_heading'       => 'nullable|string|max:255',
                'meta_details'     => 'nullable|string',
            ]);

            $metaData = $r->only([
                'meta_title',
                'meta_description',
                'meta_keywords',
                'h1_heading',
                'meta_details',
            ]);
            $metaData['meta_details'] = strip_figma_paste_junk($metaData['meta_details'] ?? null);

            if ($package->meta) {
                $package->meta->update($metaData);
            } else {
                PackageMetaData::create(array_merge($metaData, ['package_id' => $package->id]));
            }

            if ($r->ajax()) {
                return response()->json(['status' => 'success', 'message' => 'Meta settings updated.']);
            }
            return redirect()->back()->with('success', 'Meta settings updated.');
        }

        // ----------------------------------------------------------
        // Branch C: Full package update
        // ----------------------------------------------------------
        $saveType = $r->input('save_type', 'publish');

        if ($saveType === 'publish') {
            $r->validate($this->publishValidationRules(true));

            if ($r->input('package_mode') === 'group_tour') {
                $departures = array_filter(
                    $r->input('departures', []),
                    fn($d) =>
                    !empty($d['date']) && isset($d['price']) && $d['price'] !== ''
                );
                if (empty($departures)) {
                    return back()
                        ->withInput()
                        ->withErrors(['departures' => 'At least one valid departure date is required for a Group Tour.']);
                }

                // ── Seat protection: total_seats must not go below booked_seats ──
                $seatErrors = $this->validateDepartureSeats($package->id, array_values($departures));
                if (!empty($seatErrors)) {
                    return back()
                        ->withInput()
                        ->withErrors($seatErrors);
                }
            }
        } else {
            $r->validate(['title' => 'required|string|max:255']);
        }

        try {
            DB::beginTransaction();

            $destinationLocations = $r->input('location_id', []);
            $sourceLocations      = $r->input('source_location_id', []);
            $categoryIds          = $r->input('category_id', []);

            $firstDestination = $destinationLocations[0] ?? null;
            $firstSource      = $sourceLocations[0]      ?? null;
            $firstCategory    = $categoryIds[0]          ?? null;

            // ---- Parent state ----
            $parentLocation = State::find($r->input('parent'));

            // ---- Primary image (replace if a different one was picked) ----
            // Note: the previous image is intentionally not deleted from storage here —
            // it now lives in the shared Media Library and may still be used elsewhere.
            $primaryImagePath = $r->has('primary_image') ? $r->input('primary_image') : $package->primary_image;

            // ---- Build update payload ----
            $pkgData = [
                'title'               => $r->input('title'),
                'price'               => ($r->input('package_mode', $package->package_mode) === 'group_tour')
                    ? $r->input('price')
                    : null,
                'category_id'         => $firstCategory,
                'country_id'          => 1,
                'location_id'         => $firstDestination,
                'source_location_id'  => $firstSource,
                'primary_image'       => $primaryImagePath,
                'primary_image_alt'   => $r->input('primary_image_alt', $package->primary_image_alt),
                'short_description'   => null,
                'long_description'    => null,
                'package_mode'        => in_array($r->input('package_mode'), ['normal', 'group_tour'])
                                            ? $r->input('package_mode')
                                            : ($package->package_mode ?: 'normal'),
                'is_top_trending'     => $r->boolean('is_top_trending'),
                'is_special_package'  => $r->boolean('is_special_package'),
                'is_festival_package' => $r->boolean('is_festival_package'),
                'festival_id'         => $r->input('festival_id') ?: null,
                'is_customized'       => $r->boolean('is_customized'),
                'author_name'         => Auth::user()?->name,
                'is_draft'            => ($saveType === 'draft') ? 1 : 0,
                'is_active'           => ($saveType === 'draft') ? 0 : 1,
            ];

            if ($parentLocation) {
                $pkgData['parent_category']      = $parentLocation->name;
                $pkgData['parent_category_slug'] = $parentLocation->slug;
            }

            $package->update($pkgData);

            // ---- Group departures ----
            if ($pkgData['package_mode'] === 'group_tour') {
                $this->syncGroupDepartures($package->id, $r->input('departures', []));
            } else {
                // If mode switched back to normal, purge old departures
                PackageGroupDate::where('package_id', $package->id)->delete();
            }

            // ---- Clear & re-sync pivot tables ----
            // forceDelete (not soft) because the same request immediately re-inserts
            // identical (package_id, category_id/location_id) pairs below, which would
            // collide with the unique index if a soft-deleted row were left behind.
            PackageCategory::where('package_id', $package->id)->forceDelete();
            PackageLocation::where('package_id', $package->id)->forceDelete();
            PackageSourceLocation::where('package_id', $package->id)->forceDelete();

            foreach ($categoryIds as $catId) {
                PackageCategory::create(['package_id' => $package->id, 'category_id' => $catId]);
            }

            $destinationHighlights = $r->input('location_highlights', []);
            foreach ($destinationLocations as $locId) {
                DB::table('package_locations')->insert([
                    'package_id'  => $package->id,
                    'location_id' => $locId,
                    'highlights'  => $destinationHighlights[$locId] ?? null,
                ]);
            }

            foreach ($sourceLocations as $locId) {
                DB::table('package_source_locations')->insert(['package_id' => $package->id, 'location_id' => $locId]);
            }

            // ---- Package details (upsert) ----
            $detailData = [
                'duration_days'      => $r->input('duration_days', 1),
                'duration_nights'    => ((int) $r->input('duration_nights') === 0) ? null : $r->input('duration_nights'),
                'tour_highlights'    => $r->input('tour_highlights'),
                'destination_covered_description' => strip_figma_paste_junk($r->input('destination_covered_description')),
                'facilities'         => null,
            ];

            if ($package->details) {
                $package->details->update($detailData);
            } else {
                PackageDetail::create(array_merge($detailData, ['package_id' => $package->id]));
            }

            // ---- Gallery images (picked/uploaded via the Media Library) ----
            // The submitted list is the complete desired final state, so we
            // replace all rows rather than diffing (same pattern used for
            // itineraries/FAQs elsewhere in this controller). S3 files are
            // never deleted here — they belong to the shared Media Library.
            PackageImage::where('package_id', $package->id)->delete();
            foreach ($r->input('gallery_images', []) as $index => $item) {
                if (empty($item['path'])) continue;
                PackageImage::create([
                    'package_id' => $package->id,
                    'image_path' => $item['path'],
                    'image_alt'  => $item['alt'] ?? null,
                    'sort_order' => $index,
                ]);
            }

            // ---- Itineraries (replace all) ----
            if ($r->has('itineraries')) {
                $package->itineraries()->delete();
                foreach ($r->input('itineraries') as $obj) {
                    if (empty($obj['title'])) continue;
                    PackageItinerary::create([
                        'package_id' => $package->id,
                        'title'      => strip_figma_paste_junk($obj['title']),
                        'details'    => strip_figma_paste_junk($obj['details'] ?? null),
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[Package Update] ' . $e->getMessage(), [
                'package_id' => $package->id,
                'trace'      => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Something went wrong while updating the package. Please try again.']);
        }

        if ($r->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Package updated successfully.',
                'data'    => $package->fresh(),
            ]);
        }

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package updated successfully.');
    }

    // =========================================================
    // UPDATE FAQ
    // =========================================================

    public function updateFaq(Request $r, Package $package)
    {
        $r->validate([
            'faq_title'       => 'nullable|string|max:255',
            'faqs'            => 'nullable|array',
            'faqs.*.question' => 'required|string',
            'faqs.*.answer'   => 'nullable|string',
        ]);

        DB::transaction(function () use ($r, $package) {
            $package->faqs()->delete();

            foreach ($r->input('faqs', []) as $obj) {
                if (empty($obj['question'])) continue;
                PackageFaq::create([
                    'package_id' => $package->id,
                    'question'   => strip_figma_paste_junk($obj['question']),
                    'answer'     => strip_figma_paste_junk($obj['answer'] ?? null),
                ]);
            }

            $package->faq_title = $r->input('faq_title');
            $package->save();
        });

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package FAQ updated successfully.');
    }

    // =========================================================
    // DESTROY (soft delete)
    // =========================================================

    public function destroy(Package $package)
    {
        try {
            DB::transaction(function () use ($package) {
                PackageSourceLocation::where('package_id', $package->id)->delete();
                PackageLocation::where('package_id', $package->id)->delete();
                PackageCategory::where('package_id', $package->id)->delete();
                PackageGroupDate::where('package_id', $package->id)->delete();
                PackageImage::where('package_id', $package->id)->get()->each(function ($img) {
                    $this->deleteFromS3($img->image_path);
                });
                PackageImage::where('package_id', $package->id)->delete();

                $package->details()->delete();
                $package->faqs()->delete();
                $package->meta()->delete();
                $package->itineraries()->delete();
                $package->reviews()->delete();

                $package->delete();
            });
        } catch (\Throwable $e) {
            Log::error('[Package Destroy] ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Delete failed.'], 500);
        }

        return response()->json(['success' => true]);
    }

    // =========================================================
    // DELETE GALLERY IMAGE
    // =========================================================

    public function deleteImage(Request $r)
    {
        $r->validate([
            'id'         => 'required|integer',
            'package_id' => 'required|integer',
        ]);

        // Scope to the owning package — prevents IDOR (deleting other packages' images)
        $img = PackageImage::where('id', $r->input('id'))
                           ->where('package_id', $r->input('package_id'))
                           ->first();

        if (!$img) {
            return response()->json(['success' => false, 'message' => 'Image not found.'], 404);
        }

        $this->deleteFromS3($img->image_path);
        $img->delete();

        return response()->json(['success' => true]);
    }

    // =========================================================
    // SLUG DUPLICATE CHECK (AJAX)
    // =========================================================

    public function slugDuplicateCheck(Request $r)
    {
        $r->validate([
            'title'           => 'required|string',
            'duration_days'   => 'nullable|integer',
            'duration_nights' => 'nullable|integer',
        ]);

        $baseSlug = $this->buildSlug(
            $r->input('title'),
            $r->input('duration_days'),
            $r->input('duration_nights')
        );

        // ── UPDATE path ──────────────────────────────────────────────────────────
        // Simulate what uniqueSlug() would actually assign for this package.
        // If the resolved slug equals the package's current stored slug, the save
        // would simply keep the same slug — there is no real conflict.
        // This prevents a false-positive when a package owns a suffixed slug
        // (e.g. "tour-india-5-days-1") while another package owns the base
        // ("tour-india-5-days"), which is the common cause of this error on group tours.
        if ($r->filled('id')) {
            $packageId = (int) $r->input('id');
            $package   = Package::where('id', $packageId)->first();

            if ($package) {
                $resolvedSlug = $this->uniqueSlug($baseSlug, $packageId);

                if ($resolvedSlug === $package->slug) {
                    return response()->json(['exists' => false, 'slug' => $resolvedSlug]);
                }
            }
        }

        // ── CREATE path (or title/duration genuinely changed) ────────────────────
        $exists = Package::where('slug', $baseSlug)
            ->when($r->filled('id'), fn($q) => $q->where('id', '!=', (int) $r->input('id')))
            ->exists();

        return response()->json(['exists' => $exists, 'slug' => $baseSlug]);
    }

    // =========================================================
    // PARENT BY TYPE (AJAX)
    // =========================================================

    public function getParentByType(Request $r)
    {
        $type    = (int) $r->input('type');
        $parents = match ($type) {
            1       => State::where('is_active', 1)->orderBy('name')->get(),
            default => Location::where('country_id', '!=', 1)
                ->whereNull('state_id')
                ->where('is_active', 1)
                ->get(),
        };

        return response()->json(['status' => true, 'data' => $parents]);
    }

    // =========================================================
    // SHOW META (AJAX)
    // =========================================================

    public function showMeta(Package $package)
    {
        $package->load('meta');
        return response()->json($package);
    }

    // =========================================================
    // GET DEPARTURE SEATS (AJAX) — used by admin edit form
    // Returns booked_seats for each departure of a package so the
    // UI can enforce the minimum total_seats constraint in real-time.
    // =========================================================

    public function getDepartureSeats(Package $package)
    {
        $departures = PackageGroupDate::where('package_id', $package->id)
            ->get(['id', 'departure_date', 'total_seats', 'booked_seats', 'status'])
            ->map(fn($d) => [
                'id'             => $d->id,
                'departure_date' => $d->departure_date,
                'total_seats'    => (int) $d->total_seats,
                'booked_seats'   => (int) $d->booked_seats,
                'available_seats' => max(0, (int) $d->total_seats - (int) $d->booked_seats),
                'status'         => $d->status,
                'computed_status' => $d->computed_status,
            ]);

        return response()->json([
            'success'    => true,
            'package_id' => $package->id,
            'departures' => $departures,
        ]);
    }
}
