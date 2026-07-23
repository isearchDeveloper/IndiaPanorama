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
use App\Models\StateDetails;
use App\Models\StateBestTime;
use App\Models\StateFaq;
use App\Models\StateMetaData;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StateController extends Controller
{
    public function index(Request $request)
    {
        $query = State::with(['region', 'details'])
            ->where('country_id', 1) // India only
            ->orderBy('name');

        if ($search = $request->get('search')) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $states  = $query->paginate(15)->withQueryString();
        $regions = Region::orderBy('name')->get(['id', 'name']);

        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'html' => view('admin.location-setting.state-list', compact('states'))->render(),
            ]);
        }

        return redirect()->route('admin.location-setting.index', ['tab' => 'states']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
        ]);

        $slug = Str::slug($request->name);

        if (State::where('slug', $slug)->exists()) {
            throw ValidationException::withMessages(['name' => 'State already exists.']);
        }

        $state = State::create([
            'country_id' => 1,
            'region_id'  => $request->region_id,
            'name'       => $request->name,
            'slug'       => $slug,
            'is_active'  => true,
        ]);

        ManageCity::create(['state_id' => $state->id, 'is_active' => true]);

        ActivityLog::log('created', 'State', "Created state: {$state->name}", ['id' => $state->id]);

        return response()->json([
            'status'  => true,
            'message' => 'State added successfully.',
            'state'   => $state->load('region'),
        ]);
    }

    public function show(State $state)
    {
        $state->load('region', 'details', 'meta', 'faqs', 'bestTimes');
        $data = $state->toArray();
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

    public function update(Request $request, State $state)
    {
        /* ── PAGE SETTINGS ── */
        if ($request->exists('page_setting')) {
            $request->validate([
                'banner_image' => 'nullable|string|exists:media,path',
            ]);

            $path = $state->details->banner_image ?? '';

            if ($request->has('banner_image')) {
                $path = $request->input('banner_image');
            }

            StateDetails::updateOrCreate(
                ['state_id' => $state->id],
                [
                    'title'            => $request->title,
                    'sub_title'        => $request->sub_title,
                    'banner_image'     => $path,
                    'banner_image_alt' => $request->banner_image_alt,
                    'about'            => $request->about,
                    'author_name'      => auth()->user()->name,
                ]
            );

            ActivityLog::log('updated', 'State', "Updated page settings for state: {$state->name}");
            return response()->json(['status' => true, 'message' => 'Page settings saved.']);
        }

        /* ── META SETTINGS ── */
        if ($request->exists('meta_setting')) {
            StateMetaData::updateOrCreate(
                ['state_id' => $state->id],
                [
                    'meta_title'       => $request->meta_title,
                    'meta_description' => $request->meta_description,
                    'meta_keywords'    => $request->meta_keywords,
                    'h1_heading'       => $request->h1_heading,
                    'meta_details'     => $request->meta_details,
                ]
            );

            ActivityLog::log('updated', 'State', "Updated SEO meta for state: {$state->name}");
            return response()->json(['status' => true, 'message' => 'Meta saved.']);
        }

        /* ── BASIC UPDATE ── */
        $request->validate([
            'name'      => 'required|string|max:255',
            'region_id' => 'nullable|exists:regions,id',
        ]);

        $old = $state->only(['name', 'region_id', 'is_active']);

        $state->update([
            'region_id' => $request->region_id,
            'name'      => $request->name,
        ]);

        ActivityLog::log('updated', 'State', "Updated state: {$state->name}", [], $old, $state->fresh()->only(['name', 'region_id', 'is_active']));

        return response()->json([
            'status'  => true,
            'message' => 'State updated successfully.',
            'state'   => $state->load('region'),
        ]);
    }

    public function updateFaq(Request $request, State $state)
    {
        $state->faqs()->delete();

        if ($request->has('faqs')) {
            foreach ($request->faqs as $faq) {
                StateFaq::create([
                    'state_id' => $state->id,
                    'question' => $faq['question'],
                    'answer'   => $faq['answer'] ?? null,
                ]);
            }
        }

        $state->update(['faq_title' => $request->faq_title]);

        ActivityLog::log('updated', 'State', "Updated FAQs for state: {$state->name}");

        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'FAQs saved.']);
        }

        return back()->with('success', 'FAQs saved.');
    }

    public function updateBestTime(Request $request, State $state)
    {
        $state->bestTimes()->delete();

        if ($request->has('best_times')) {
            foreach ($request->best_times as $bestTime) {
                StateBestTime::create([
                    'state_id'    => $state->id,
                    'month_range' => $bestTime['month_range'],
                    'tagline'     => $bestTime['tagline'] ?? null,
                ]);
            }
        }

        $state->update(['best_time_title' => $request->best_time_title]);

        ActivityLog::log('updated', 'State', "Updated Best Time to Visit for state: {$state->name}");

        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Best Time to Visit saved.']);
        }

        return back()->with('success', 'Best Time to Visit saved.');
    }

    public function toggleStatus(Request $request, State $state)
    {
        $state->update(['is_active' => !$state->is_active]);

        ActivityLog::log('status-changed', 'State', "Toggled status of state: {$state->name} to " . ($state->is_active ? 'active' : 'inactive'));

        return response()->json([
            'status'    => true,
            'is_active' => $state->is_active,
            'message'   => 'Status updated.',
        ]);
    }

    public function destroy(State $state)
    {
        // Delete state-level ManageCity record
        ManageCity::where('state_id', $state->id)->whereNull('location_id')->delete();

        // Delete all cities (locations) in this state + their ManageCity records
        $locationIds = Location::where('state_id', $state->id)->pluck('id');
        if ($locationIds->isNotEmpty()) {
            ManageCity::whereIn('location_id', $locationIds)->delete();
            LocationFaq::whereIn('location_id', $locationIds)->delete();
            LocationBestTime::whereIn('location_id', $locationIds)->delete();
            LocationMetaData::whereIn('location_id', $locationIds)->delete();
            LocationDetails::whereIn('location_id', $locationIds)->delete();
            Location::whereIn('id', $locationIds)->delete();
        }

        // Delete the state's own detail/faq/meta/best-time rows
        $state->details()->delete();
        $state->faqs()->delete();
        $state->bestTimes()->delete();
        $state->meta()->delete();
        if ($festivalPage = $state->festivalPage) {
            $festivalPage->whyVisits()->delete();
            $festivalPage->faqs()->delete();
            $festivalPage->delete();
        }

        ActivityLog::log('deleted', 'State', "Deleted state: {$state->name}");

        $state->delete();

        return response()->json([
            'status'  => true,
            'message' => 'State deleted successfully.',
        ]);
    }
}
