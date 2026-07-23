<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Country;
use App\Models\Location;
use App\Models\ManageCity;
use App\Models\Region;
use App\Models\LocationDetails;
use App\Models\LocationFaq;
use App\Models\LocationMetaData;
use App\Models\MegaMenuLocation;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class LocationController extends Controller{

    public function index(Request $r){
        // AJAX: return cities for a country (used by package/hotel/car forms)
        if ($r->exists('country_id') && !$r->exists('faqs')) {
            $query = Location::where('country_id', $r->country_id);
            if ($r->filled('state_id')) {
                $query->where('state_id', $r->state_id);
            }
            return response()->json(['cities' => $query->get()]);
        }

        // AJAX: return FAQs for a location
        if (!$r->exists('country_id') && $r->exists('faqs')) {
            $location = Location::with('faqs')->where('id',$r->id)->first();
            if ($r->ajax()) {
                return response()->json([
                    'status'   => 'success',
                    'location' => $location
                ]);
            }
        }

        // Regular page visit → redirect to the new Location Setting page
        return redirect()->route('admin.location-setting.index');
    }
    
    /**
     * Detect region name for a given city/state/region input string.
     * Uses DB State → Region relationship (no static JSON).
     */
    private function detectRegionFromHelper(string $input): ?string
    {
        $needle = strtolower(trim($input));

        // 1. Direct region match
        $region = DB::table('regions')
            ->whereRaw('LOWER(name) = ?', [$needle])
            ->value('name');
        if ($region) {
            return $region;
        }

        // 2. State match → return its region
        $state = State::with('region')
            ->whereRaw('LOWER(name) = ?', [$needle])
            ->first();
        if ($state?->region) {
            return $state->region->name;
        }

        // 3. City (location) match → return state's region
        $location = Location::with('state.region')
            ->whereRaw('LOWER(name) = ?', [$needle])
            ->first();
        if ($location?->state?->region) {
            return $location->state->region->name;
        }

        // 4. Fallback: city has direct region_id
        if ($location?->region_id) {
            return DB::table('regions')->where('id', $location->region_id)->value('name');
        }

        return null;
    }



    public function store(Request $request){
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255'
        ]);

        $slug = Str::slug($request->name);

        $regionName = $this->detectRegionFromHelper($request->name);
        
        $regionId = null;
        if ($regionName) {
            $regionId = Region::where('name', $regionName)->value('id');
        }
        

        if (Location::where('slug', $slug)->exists()) {
            throw ValidationException::withMessages([
                'name' => 'City already exists.',
            ]);
        }
        $location = Location::create([
            'country_id'      => $request->country_id,
            'state_id'        => $request->state_id,
            'name'            => $request->name,
            'slug'            => $slug,
            'region_id'       => $regionId,
            'is_top_trending' => $request->is_top_trending ? 1 : 0,
            'author_name'     => $request->author_name,
        ]);

        ManageCity::create(['location_id' => $location->id, 'is_active' => true]);

        ActivityLog::log('created', 'Location', "Created city: {$location->name}", ['id' => $location->id]);

        return response()->json($location->load('country'));
    }
    

    public function show(Location $location){

        $location->load('country','details');
        $data = $location->toArray();
        $data['banner_license'] = $this->licenseForJson($location->imageLicense('banner'));
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

    public function showMeta(Location $location){
        $location->load('meta');
        return response()->json($location);
    }

    

    public function update(Request $r, Location $location){
        // echo '<pre>';
        // print_r($r->toArray());die;
        if(!$r->exists('page_setting') && !$r->exists('meta_setting')){
            //echo 'hjfghjb';die;
            $r->validate([
                'country_id' => 'required|exists:countries,id',
                'name' => 'required|string|max:255'
            ]);

            $location->update([
                'name'        => $r->name,
                'country_id' => $r->country_id,
                'state_id'    => $r->state_id,
              
                'is_top_trending' => $r->is_top_trending ? 1 : 0,
                'author_name' => $r->author_name
            ]);
        } elseif($r->exists('page_setting')) {
            $r->validate(array_merge([
                'banner_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
                'author_name'  => 'required|string|max:255',
            ], \App\Services\ImageLicenseManager::rules('banner_license')));

            if ($r->hasFile('banner_image')) {
                $licenseErrors = \App\Services\ImageLicenseManager::validationErrors(
                    $r, 'banner_license', 'the City Banner Image', $location->imageLicense('banner')
                );
                if ($licenseErrors) {
                    return back()->withInput()->withErrors($licenseErrors);
                }
            }

            $location->update([
                'author_name' => $r->author_name
            ]);
            $path = $location->details?->banner_image ?? '';
            if ($r->hasFile('banner_image')) {
                $img = $r->file('banner_image');
                if ($img->isValid()) {
                    $oldBannerImage = $location->details?->banner_image;
                    $filename = unique_filename($img);

                    // store in S3 under "location/"
                    try {
                        $path = $img->storeAs('location', $filename, config('filesystems.upload_disk'));
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error('[Location Update] storeAs failed', ['error' => $e->getMessage()]);
                        return back()->withInput()->with('error', 'Banner image upload failed. Please try again.');
                    }

                    if ($oldBannerImage && $oldBannerImage != 0 && Storage::disk(config('filesystems.upload_disk'))->exists($oldBannerImage)) {
                        Storage::disk(config('filesystems.upload_disk'))->delete($oldBannerImage);
                    }
                }

                \App\Services\ImageLicenseManager::save($location, $r, 'banner_license', 'banner');
            }

            if ($location->details) {
                $location->details->update([
                    'title' => $r->title,
                    'sub_title' => $r->sub_title,
                    'banner_image' => $path != '0' ? $path : '',
                    'banner_image_alt'=>$r->banner_image_alt,
                    'about' => $r->about
                ]);
            } else {
                LocationDetails::create([
                    'location_id' => $location->id,
                    'title' => $r->title,
                    'sub_title' => $r->sub_title,
                    'banner_image' => $path != '0' ? $path : '',
                    'banner_image_alt'=>$r->banner_image_alt,
                    'about' => $r->about
                ]);
            }
        } elseif($r->exists('meta_setting')) {
            
            $r->validate([
                'meta_title'       => 'nullable|string',
                'meta_description' => 'nullable|string',
                'meta_keywords'    => 'nullable|string',
                'h1_heading'       => 'nullable|string',
                'meta_details'     => 'nullable|string',
            ]);
            if ($location->meta) {
                //print_r($r->toArray());die;
                $location->meta->update([
                    'meta_title' => $r->meta_title,
                    'meta_description' => $r->meta_description,
                    'meta_keywords' => $r->meta_keywords,
                    'h1_heading' => $r->h1_heading,
                    'meta_details' => $r->meta_details,
                ]);
            } else {
                LocationMetaData::create([
                    'location_id' => $location->id,
                    'meta_title' => $r->meta_title,
                    'meta_description' => $r->meta_description,
                    'meta_keywords' => $r->meta_keywords,
                    'h1_heading' => $r->h1_heading,
                    'meta_details' => $r->meta_details,
                ]);
            }
        }
        // If AJAX request, return JSON response
        if ($r->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Location updated successfully',
                'data'    => $location
            ]);
        }
        return redirect()->route('admin.locations.index')->with('success', 'Location updated successfully');
    }

    public function updateFaq(Request $r, Location $location){
        \Illuminate\Support\Facades\DB::transaction(function () use ($r, $location) {
            $location->faqs()->delete();
            if ($r->has('faqs')) {
                foreach ($r->faqs as $obj) {
                    LocationFaq::create([
                        'location_id'=>$location->id,
                        'question' => $obj['question'],
                        'answer'   => $obj['answer'] ?? null,
                    ]);
                }
            }
            $location->faq_title = $r->faq_title;
            $location->save();
        });
        return redirect()->route('admin.locations.index')->with('success', 'Location faq updated successfully');
    }

    public function searchCountry(Request $request)
    {
        $countries = Country::query()
            ->orderBy('id');

        if ($search = $request->get('keyword')) {
            $countries->where('name', 'LIKE', "%{$search}%");
        }

        // ✅ Assign paginated results
        $countries = $countries->paginate(list_config()['limit'], ['*'], 'country_page')->appends(['keyword' => $request->keyword]);


        $html = view('admin.locations.country-list', compact('countries'))->render();
        return response($html, 200)->header('Content-Type', 'text/html');
    }

    public function searchCity(Request $request)
    {
        $countries = Country::query() 
            ->orderBy('id');

        if ($search = $request->get('keyword')) {
            $countries->where('name', 'LIKE', "%{$search}%");
        }

        // ✅ Assign paginated results
        $countries = $countries->paginate(1, ['*'], 'country_page');

        $html = view('admin.locations.country-list', compact('countries'))->render();
        return response($html, 200)->header('Content-Type', 'text/html');
    }

    public function addMegaMenu(Request $request){
        MegaMenuLocation::create([
            'type'        => 'App\\Models\\' . ucfirst($request->type),
            'location_id' => $request->location_id ?: $request->country_id,
            'is_active'   => 0,
        ]);

        return redirect()->route('admin.locations.index')->with('success', 'Menu Location added successfully');
    }

    public function updateMegaMenu(Request $r, MegaMenuLocation $menu_location){
        $menu_location->is_active = $r->status;
        $menu_location->save();
        if ($r->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Location status updated successfully',
                'data'    => $menu_location
            ]);
        }
    }


    public function destroy(Request $r,MegaMenuLocation $menu_location){
        $menu_location->delete();
        if ($r->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Location deleted successfully',
            ]);
        }
    }
    /**
     * GET /admin/get-states?country_id={id}
     * Returns states for a country (used by Add/Edit City modals).
     */
    public function getStates(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['country_id' => 'required|integer']);

        $states = State::where('country_id', $request->country_id)
                       ->where('is_active', true)
                       ->orderBy('name')
                       ->get(['id', 'name', 'slug']);

        return response()->json([
            'status' => true,
            'states' => $states,
        ]);
    }

    /**
     * GET /admin/get-cities?state_id={id}
     * Returns cities for a state (used by cascading dropdowns).
     */
    public function getCities(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['state_id' => 'required|integer|exists:states,id']);

        $cities = Location::where('state_id', $request->state_id)
                          ->where('is_active', true)
                          ->orderBy('sort_order')
                          ->orderBy('name')
                          ->get(['id', 'name', 'slug']);

        return response()->json([
            'status' => true,
            'cities' => $cities,
        ]);
    }
    public function destroyLocation(Location $location)
    {
        $location->load('details');
        $bannerImage = $location->details?->banner_image;
        $name = $location->name;

        \Illuminate\Support\Facades\DB::transaction(function () use ($location, $bannerImage) {
            if ($bannerImage) {
                $disk = config('filesystems.upload_disk');
                rescue(function () use ($disk, $bannerImage) {
                    if (Storage::disk($disk)->exists($bannerImage)) {
                        Storage::disk($disk)->delete($bannerImage);
                    }
                }, function (\Throwable $e) use ($bannerImage) {
                    \Illuminate\Support\Facades\Log::error('[Location Delete] Failed to delete banner image from disk', [
                        'banner_image' => $bannerImage,
                        'error'        => $e->getMessage(),
                    ]);
                }, report: false);
            }

            $location->faqs()->delete();
            $location->bestTimes()->delete();
            $location->meta()->delete();
            $location->details()->delete();
            ManageCity::where('location_id', $location->id)->delete();
            $location->delete();
        });

        ActivityLog::log('deleted', 'Location', "Deleted city: {$name}");

        return response()->json([
            'status'  => true,
            'message' => 'City deleted successfully',
        ]);
    }

}
