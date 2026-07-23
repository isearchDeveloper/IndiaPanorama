<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ImageLicense;
use App\Models\Location;
use App\Models\ManageCity;
use App\Models\Region;
use App\Models\RegionDetails;
use App\Models\RegionFaq;
use App\Models\RegionMetaData;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegionsController extends Controller
{
    
    /**
     * Assign region_id to State records and Location (city) records from DB.
     * Replaces the former JSON-based mapping.
     * Traverses: Region → State.region_id, Location.region_id via state.
     */
    public function setLocationr(): array
    {
        $updatedStates  = 0;
        $updatedCities  = 0;
        $missing        = [];

        // Update state records: set region_id where missing
        $states = \App\Models\State::with('region')->whereNotNull('region_id')->get();
        foreach ($states as $state) {
            // Propagate state's region_id down to its location (city) records
            $affected = Location::where('state_id', $state->id)
                                 ->whereNull('region_id')
                                 ->update(['region_id' => $state->region_id]);
            $updatedCities += $affected;
            $updatedStates++;
        }

        // States that still have no region_id: match by name from regions
        $unregioned = \App\Models\State::whereNull('region_id')->get();
        foreach ($unregioned as $state) {
            $missing[] = "State has no region_id: {$state->name}";
        }

        // Locations with no state_id and no region_id: flag them
        $orphanCount = Location::whereNull('state_id')->whereNull('region_id')->count();
        if ($orphanCount > 0) {
            $missing[] = "{$orphanCount} location(s) have no state_id and no region_id";
        }

        return [
            'updated_states'  => $updatedStates,
            'updated_cities'  => $updatedCities,
            'missing_entries' => $missing,
        ];
    }
    
    public function index(Request $r)
    {
        // AJAX → fetch FAQs
        if ($r->exists('faqs')) {
            $region = Region::with('faqs')->findOrFail($r->id);

            return response()->json([
                'status' => 'success',
                'region' => $region
            ]);
        }

        return redirect()->route('admin.menus.builder');
    }

    public function store(Request $r)
    {
        $r->validate([
            'name' => 'required|string|max:255',
            
        ]);

        $slug = Str::slug($r->name);

        if (Region::where('slug',$slug)->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Region already exists'
            ]);
        }

        $region = Region::create([
            'name' => $r->name,
            'slug' => $slug,
        ]);

        ManageCity::create(['region_id' => $region->id, 'is_active' => true]);

        ActivityLog::log('created', 'Region', "Created region: {$region->name}", ['id' => $region->id]);

        return $region;
    }

    public function show(Region $region)
    {
        $region->load('details','meta');

        $data = $region->toArray();

        return response()->json($data);
    }

    private function licenseForJson(?ImageLicense $license): ?array
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

    public function togglePopular(Region $region)
    {
        $region->update(['is_popular' => !$region->is_popular]);

        HomeController::flushCache();

        ActivityLog::log('status-changed', 'Region', "Toggled popular status of region: {$region->name} to " . ($region->is_popular ? 'popular' : 'not popular'));

        return response()->json([
            'status'     => true,
            'is_popular' => $region->is_popular,
            'message'    => 'Popular status updated.',
        ]);
    }

    public function update(Request $r, Region $region)
    {
        /* ================= BASIC ================= */
        if (!$r->exists('page_setting') && !$r->exists('meta_setting')) {

            $r->validate([
                'name' => 'required|string|max:255'
            ]);

            $region->update([
                'name' => $r->name,
            ]);
        }

        /* ================= PAGE SETTINGS ================= */
        elseif ($r->exists('page_setting')) {

            $r->validate([
                'banner_image' => 'nullable|string|exists:media,path',
                'home_image'   => 'nullable|string|exists:media,path',
            ]);

            $bannerPath = $region->details->banner_image ?? '';
            if ($r->has('banner_image')) {
                $bannerPath = $r->input('banner_image');
            }

            $homePath = $region->details->home_image ?? '';
            if ($r->has('home_image')) {
                $homePath = $r->input('home_image');
            }

            RegionDetails::updateOrCreate(
                ['region_id' => $region->id],
                [
                    'title' => $r->title,
                    'sub_title' => $r->sub_title,
                    'banner_image' => $bannerPath,
                    'banner_image_alt' => $r->banner_image_alt,
                    'home_image' => $homePath,
                    'home_image_alt' => $r->home_image_alt,
                    'about' => $r->about,
                    'author_name' => auth()->user()->name
                ]
            );
        }

        /* ================= META SETTINGS ================= */
        elseif ($r->exists('meta_setting')) {

            RegionMetaData::updateOrCreate(
                ['region_id' => $region->id],
                [
                    'meta_title' => $r->meta_title,
                    'meta_description' => $r->meta_description,
                    'meta_keywords' => $r->meta_keywords,
                    'h1_heading' => $r->h1_heading,
                    'meta_details' => $r->meta_details,
                ]
            );
        }

        ActivityLog::log('updated', 'Region', "Updated region: {$region->name}");

        if ($r->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Region updated successfully',
            ]);
        }

        return back()->with('success', 'Region updated successfully');
    }

    public function updateFaq(Request $r, Region $region)
    {
        $region->faqs()->delete();

        if ($r->has('faqs')) {
            foreach ($r->faqs as $faq) {
                RegionFaq::create([
                    'region_id' => $region->id,
                    'question' => $faq['question'],
                    'answer' => $faq['answer'] ?? null
                ]);
            }
        }

        $region->faq_title = $r->faq_title;
        $region->save();

        ActivityLog::log('updated', 'Region', "Updated FAQs for region: {$region->name}");

        if ($r->ajax()) {
            return response()->json(['status' => true, 'message' => 'FAQs saved.']);
        }

        return back()->with('success', 'Region FAQs updated successfully');
    }

    public function destroy(Region $region)
    {
        $region->details()->delete();
        $region->faqs()->delete();
        $region->meta()->delete();
        ManageCity::where('region_id', $region->id)->delete();

        ActivityLog::log('deleted', 'Region', "Deleted region: {$region->name}");

        $region->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Region deleted successfully',
        ]);
    }
}
