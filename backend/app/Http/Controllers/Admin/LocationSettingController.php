<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Location;
use App\Models\LocationBestTime;
use App\Models\LocationDetails;
use App\Models\LocationFaq;
use App\Models\LocationMetaData;
use App\Models\ManageCity;
use App\Models\Region;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LocationSettingController extends Controller
{
    private function indiaCountryId(): int
    {
        return 1;
    }

    /**
     * A package can be linked to a location either via the direct `location_id` /
     * `source_location_id` FK columns on `packages` (used by the old-site import) or
     * via the `package_locations` / `package_source_locations` pivot tables (used by
     * the admin package form's multi-destination picker) — count distinct packages
     * across all four so neither source is missed or double-counted.
     */
    private function cityPackagesCountSql(): string
    {
        return "(
            SELECT COUNT(DISTINCT p.id) FROM packages p
            WHERE p.deleted_at IS NULL AND (
                p.location_id = locations.id
                OR p.source_location_id = locations.id
                OR EXISTS (SELECT 1 FROM package_locations pl WHERE pl.package_id = p.id AND pl.deleted_at IS NULL AND pl.location_id = locations.id)
                OR EXISTS (SELECT 1 FROM package_source_locations psl WHERE psl.package_id = p.id AND psl.deleted_at IS NULL AND psl.location_id = locations.id)
            )
        ) as packages_count";
    }

    /** Same as above, but rolled up to every city belonging to the state. */
    private function statePackagesCountSql(): string
    {
        return "(
            SELECT COUNT(DISTINCT p.id) FROM packages p
            WHERE p.deleted_at IS NULL AND (
                EXISTS (SELECT 1 FROM locations lc WHERE lc.id = p.location_id AND lc.state_id = states.id)
                OR EXISTS (SELECT 1 FROM locations lc WHERE lc.id = p.source_location_id AND lc.state_id = states.id)
                OR EXISTS (
                    SELECT 1 FROM package_locations pl
                    JOIN locations lc ON lc.id = pl.location_id
                    WHERE pl.package_id = p.id AND pl.deleted_at IS NULL AND lc.state_id = states.id
                )
                OR EXISTS (
                    SELECT 1 FROM package_source_locations psl
                    JOIN locations lc ON lc.id = psl.location_id
                    WHERE psl.package_id = p.id AND psl.deleted_at IS NULL AND lc.state_id = states.id
                )
            )
        ) as packages_count";
    }

    // ─── Main Page ────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        // AJAX: FAQs for a city
        if ($request->exists('faqs') && $request->id) {
            $loc = Location::with('faqs')->findOrFail($request->id);
            return response()->json(['status' => 'success', 'location' => $loc]);
        }

        // AJAX: Best Time to Visit for a city
        if ($request->exists('best_times') && $request->id) {
            $loc = Location::with('bestTimes')->findOrFail($request->id);
            return response()->json(['status' => 'success', 'location' => $loc]);
        }

        $regionQuery = Region::with('details')->withCount('states')->orderBy('name');
        $regions = $regionQuery->paginate(20, ['*'], 'region_page')->withQueryString();

        $stateQuery = State::with(['region', 'details'])
            ->select('states.*')
            ->withCount('cities')
            ->selectRaw($this->statePackagesCountSql())
            ->where('country_id', $this->indiaCountryId())
            ->orderBy('name');

        if ($search = $request->get('search')) {
            $stateQuery->where('name', 'LIKE', "%{$search}%");
        }
        $states = $stateQuery->paginate(15, ['*'], 'state_page')->withQueryString();

        $cityQuery = Location::with(['state', 'details'])
            ->select('locations.*')
            ->selectRaw($this->cityPackagesCountSql())
            ->where('country_id', $this->indiaCountryId())
            ->orderBy('name');

        if ($citySearch = $request->get('city')) {
            $cityQuery->where('name', 'LIKE', "%{$citySearch}%");
        }
        $locations = $cityQuery->paginate(15, ['*'], 'city_page')->withQueryString();

        if ($request->ajax() && $request->get('ajax')) {
            if ($request->has('city')) {
                return response()->json([
                    'html' => view('admin.location-setting.city-table', compact('locations'))->render(),
                ]);
            }
        }

        // All states (unpaginated) for dropdowns in modals
        $allStates = State::where('country_id', $this->indiaCountryId())
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        // All regions (unpaginated) for dropdowns in state modals
        $allRegions = Region::orderBy('name')->get(['id', 'name']);

        return view('admin.location-setting.index', compact(
            'regions', 'states', 'locations', 'allStates', 'allRegions', 'citySearch'
        ));
    }

    // ─── City (Location) CRUD ─────────────────────────────────────────────────

    public function storeCity(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
        ]);

        $slug = Str::slug($request->name);

        if (Location::where('slug', $slug)->exists()) {
            throw ValidationException::withMessages(['name' => 'City already exists.']);
        }

        $state = State::findOrFail($request->state_id);

        $location = Location::create([
            'country_id' => $this->indiaCountryId(),
            'state_id'   => $request->state_id,
            'region_id'  => $state->region_id,
            'name'       => $request->name,
            'slug'       => $slug,
            'is_active'  => true,
        ]);

        ManageCity::create(['location_id' => $location->id, 'is_active' => true]);

        ActivityLog::log('created', 'Location', "Created city: {$location->name}", ['id' => $location->id]);

        return response()->json([
            'status'   => true,
            'message'  => 'City added successfully.',
            'location' => $location,
        ]);
    }

    public function showCity(Location $location)
    {
        $location->load('state', 'details', 'meta', 'faqs');
        $data = $location->toArray();
        return response()->json($data);
    }

    private function licenseForJson(?\App\Models\ImageLicense $license): ?array
    {
        if (!$license) return null;

        return [
            'source_of_image'      => $license->source_of_image,
            'download_date'        => $license->download_date?->format('Y-m-d'),
            'account_id'           => $license->account_id,
            'license_key'          => $license->license_key,
            'license_key_file_url' => $license->license_key_file ? storage_link($license->license_key_file) : null,
        ];
    }

    public function showCityMeta(Location $location)
    {
        $location->load('meta');
        return response()->json($location);
    }

    public function updateCity(Request $request, Location $location)
    {
        if ($request->exists('page_setting')) {
            $request->validate([
                'banner_image' => 'nullable|string|exists:media,path',
            ]);

            $path = $location->details->banner_image ?? '';

            if ($request->has('banner_image')) {
                $path = $request->input('banner_image');
            }

            LocationDetails::updateOrCreate(
                ['location_id' => $location->id],
                [
                    'title'            => $request->title,
                    'sub_title'        => $request->sub_title,
                    'banner_image'     => $path,
                    'banner_image_alt' => $request->banner_image_alt,
                    'about'            => $request->about,
                ]
            );

            $location->update(['author_name' => auth()->user()->name]);
            ActivityLog::log('updated', 'Location', "Updated page settings for city: {$location->name}");

            return response()->json(['status' => true, 'message' => 'Page settings saved.']);
        }

        if ($request->exists('meta_setting')) {
            LocationMetaData::updateOrCreate(
                ['location_id' => $location->id],
                [
                    'meta_title'       => $request->meta_title,
                    'meta_description' => $request->meta_description,
                    'meta_keywords'    => $request->meta_keywords,
                    'h1_heading'       => $request->h1_heading,
                    'meta_details'     => $request->meta_details,
                ]
            );

            ActivityLog::log('updated', 'Location', "Updated SEO meta for city: {$location->name}");
            return response()->json(['status' => true, 'message' => 'Meta saved.']);
        }

        // Basic update
        $request->validate([
            'name'     => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
        ]);

        $state = State::find($request->state_id);

        $old = $location->only(['name', 'state_id']);
        $location->update([
            'name'      => $request->name,
            'state_id'  => $request->state_id,
            'region_id' => $state?->region_id ?? $location->region_id,
            'country_id'=> $this->indiaCountryId(),
        ]);

        ActivityLog::log('updated', 'Location', "Updated city: {$location->name}", [], $old, $location->fresh()->only(['name', 'state_id']));

        return response()->json(['status' => true, 'message' => 'City updated.', 'location' => $location->load('state')]);
    }

    public function updateCityFaq(Request $request, Location $location)
    {
        $location->faqs()->delete();

        if ($request->has('faqs')) {
            foreach ($request->faqs as $faq) {
                LocationFaq::create([
                    'location_id' => $location->id,
                    'question'    => $faq['question'],
                    'answer'      => $faq['answer'] ?? null,
                ]);
            }
        }

        $location->update(['faq_title' => $request->faq_title]);

        ActivityLog::log('updated', 'Location', "Updated FAQs for city: {$location->name}");

        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'FAQs saved.']);
        }

        return back()->with('success', 'FAQs saved.');
    }

    public function updateCityBestTime(Request $request, Location $location)
    {
        $location->bestTimes()->delete();

        if ($request->has('best_times')) {
            foreach ($request->best_times as $bestTime) {
                LocationBestTime::create([
                    'location_id' => $location->id,
                    'month_range' => $bestTime['month_range'],
                    'tagline'     => $bestTime['tagline'] ?? null,
                ]);
            }
        }

        $location->update(['best_time_title' => $request->best_time_title]);

        ActivityLog::log('updated', 'Location', "Updated Best Time to Visit for city: {$location->name}");

        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Best Time to Visit saved.']);
        }

        return back()->with('success', 'Best Time to Visit saved.');
    }

    public function toggleCityStatus(Request $request, Location $location)
    {
        $location->update(['is_active' => !$location->is_active]);

        ActivityLog::log('status-changed', 'Location', "Toggled status of city: {$location->name} to " . ($location->is_active ? 'active' : 'inactive'));

        return response()->json([
            'status'    => true,
            'is_active' => $location->is_active,
            'message'   => 'Status updated.',
        ]);
    }

    public function destroyCity(Location $location)
    {
        ManageCity::where('location_id', $location->id)->delete();

        $location->faqs()->delete();
        $location->bestTimes()->delete();
        $location->meta()->delete();
        $location->details()->delete();

        ActivityLog::log('deleted', 'Location', "Deleted city: {$location->name}");
        $location->delete();

        return response()->json(['status' => true, 'message' => 'City deleted.']);
    }
}
