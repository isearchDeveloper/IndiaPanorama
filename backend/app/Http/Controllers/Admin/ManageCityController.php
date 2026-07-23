<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FestivalStatePage;
use App\Models\Location;
use App\Models\ManageCity;
use App\Models\ManageCityFaq;
use App\Models\ManageCityHowToReach;
use App\Models\ManageCityMeta;
use App\Models\ManageCityQuickFact;
use App\Models\ManageCityThingToDo;
use App\Models\ManageCityTopPlace;
use App\Models\State;
use Illuminate\Http\Request;

class ManageCityController extends Controller
{
    // ── List ─────────────────────────────────────────────────────────────────

    public function index(Request $r)
    {
        $search = $r->get('search');

        $regionQuery = ManageCity::with('region')
            ->whereNotNull('region_id')->whereHas('region')
            ->orderBy('sort_order')->orderBy('id');
        if ($search) {
            $regionQuery->whereHas('region', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }
        $regions = $regionQuery->paginate(20, ['*'], 'region_page')->withQueryString();

        $stateQuery = ManageCity::with('state')
            ->whereNotNull('state_id')->whereHas('state')
            ->orderBy('sort_order')->orderBy('id');
        if ($search) {
            $stateQuery->whereHas('state', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }
        $states = $stateQuery->paginate(20, ['*'], 'state_page')->withQueryString();

        $cityQuery = ManageCity::with('location.state')
            ->whereNotNull('location_id')->whereHas('location')
            ->orderBy('sort_order')->orderBy('id');
        if ($search) {
            $cityQuery->whereHas('location', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }
        $cities = $cityQuery->paginate(20, ['*'], 'city_page')->withQueryString();

        // States/cities that already exist in Location Setting but ended up without a
        // City Guide row (normally created automatically when the state/city itself is
        // added — this covers the cases where that step failed or was skipped).
        $missingStates = State::whereNotIn('id', ManageCity::whereNotNull('state_id')->pluck('state_id'))
            ->orderBy('name')->get(['id', 'name']);

        $missingLocations = Location::whereNotIn('id', ManageCity::whereNotNull('location_id')->pluck('location_id'))
            ->with('state:id,name')
            ->orderBy('name')->get(['id', 'name', 'state_id']);

        return view('admin.manage-cities.index', compact('regions', 'states', 'cities', 'missingStates', 'missingLocations'));
    }

    // ── Add missing State / City to the City Guide ─────────────────────────
    // Uses the exact same creation as the automatic step in StateController::store()
    // / LocationSettingController::storeCity() — this only ever runs for a state/city
    // that doesn't have a City Guide row yet, so it can't create a duplicate.

    public function storeState(Request $r)
    {
        $r->validate(['state_id' => 'required|exists:states,id']);

        if (ManageCity::where('state_id', $r->state_id)->exists()) {
            return response()->json(['success' => false, 'message' => 'This state already has a City Guide entry.'], 422);
        }

        ManageCity::create(['state_id' => $r->state_id, 'is_active' => true]);

        return response()->json(['success' => true, 'message' => 'State added to City Guide.']);
    }

    public function storeCity(Request $r)
    {
        $r->validate(['location_id' => 'required|exists:locations,id']);

        if (ManageCity::where('location_id', $r->location_id)->exists()) {
            return response()->json(['success' => false, 'message' => 'This city already has a City Guide entry.'], 422);
        }

        ManageCity::create(['location_id' => $r->location_id, 'is_active' => true]);

        return response()->json(['success' => true, 'message' => 'City added to City Guide.']);
    }

    // ── Edit Page (full multi-tab page) ──────────────────────────────────────

    public function edit(ManageCity $manageCity)
    {
        if ($manageCity->region_id) {
            return redirect()->route('admin.city-pages.index', ['tab' => 'regions'])
                ->with('error', 'Regions only have Settings, Quick Facts, FAQs and SEO — no guide content page.');
        }

        $manageCity->load(['region', 'state', 'location.state', 'howToReach', 'quickFacts', 'faqs', 'meta', 'topPlaces', 'thingsToDo']);

        $festivalStateId = $manageCity->state_id ?? $manageCity->location?->state_id;
        $festivalsIntro  = $festivalStateId
            ? (FestivalStatePage::where('state_id', $festivalStateId)->value('short_description') ?? '')
            : '';

        return view('admin.manage-cities.edit', compact('manageCity', 'festivalsIntro'));
    }

    // ── Settings modal (basic title/banner/about) ─────────────────────────

    public function show(ManageCity $manageCity)
    {
        $manageCity->load(['region', 'state', 'location']);
        return response()->json([
            'id'                => $manageCity->id,
            'display_name'      => $manageCity->display_name,
            'type'              => $manageCity->type,
            'title'             => $manageCity->title,
            'sub_title'         => $manageCity->sub_title,
            'banner_text'       => $manageCity->banner_text,
            'about'             => $manageCity->about,
            'short_description' => $manageCity->short_description,
            'banner_image_path' => $manageCity->banner_image,
            'banner_image'      => $manageCity->banner_image ? storage_link($manageCity->banner_image) : null,
            'banner_image_alt'  => $manageCity->banner_image_alt,
            'is_active'         => $manageCity->is_active,
        ]);
    }

    public function updateSettings(Request $r, ManageCity $manageCity)
    {
        $r->validate([
            'title'             => 'nullable|string|max:255',
            'sub_title'         => 'nullable|string|max:255',
            'banner_text'       => 'nullable|string',
            'about'             => 'nullable|string',
            'short_description' => 'nullable|string',
            'banner_image'      => 'nullable|string|exists:media,path',
            'banner_image_alt'  => 'nullable|string|max:255',
        ]);

        $imageChanged = $r->has('banner_image') && $r->input('banner_image') !== $manageCity->banner_image;

        // Only touch fields the submitting form actually sent, so the "Edit" modal
        // (title/banner/banner alt/banner text) and the "Settings" modal (short/long
        // description) don't wipe out each other's data when saved independently.
        $data = $r->only(['title', 'sub_title', 'banner_text', 'about', 'short_description', 'banner_image_alt']);

        if ($imageChanged) {
            // Note: the previous image is intentionally not deleted from storage here —
            // it now lives in the shared Media Library and may still be used elsewhere.
            $data['banner_image'] = $r->input('banner_image');
        }

        $manageCity->update($data);

        return response()->json(['success' => true, 'message' => 'Settings saved.']);
    }

    // ── Toggle Status ──────────────────────────────────────────────────────

    public function toggleStatus(ManageCity $manageCity)
    {
        $manageCity->update(['is_active' => ! $manageCity->is_active]);
        return response()->json(['success' => true, 'is_active' => $manageCity->is_active]);
    }

    // ── Toggle Popular ─────────────────────────────────────────────────────

    public function togglePopular(ManageCity $manageCity)
    {
        $manageCity->update(['is_popular' => ! $manageCity->is_popular]);
        return response()->json(['success' => true, 'is_popular' => $manageCity->is_popular]);
    }

    // ── How To Reach ──────────────────────────────────────────────────────

    public function saveHowToReach(Request $r, ManageCity $manageCity)
    {
        $r->validate([
            'rows'              => 'nullable|array',
            'rows.*.mode'       => 'nullable|string|max:100',
            'rows.*.description'=> 'nullable|string',
        ]);

        $manageCity->howToReach()->delete();

        foreach (($r->rows ?? []) as $i => $row) {
            if (empty($row['mode']) && empty($row['description'])) continue;
            ManageCityHowToReach::create([
                'manage_city_id' => $manageCity->id,
                'mode'           => $row['mode'] ?? null,
                'description'    => $row['description'] ?? null,
                'sort_order'     => $i,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'How To Reach saved.']);
    }

    // ── Top Tourist Places ────────────────────────────────────────────────

    public function saveTopPlaces(Request $r, ManageCity $manageCity)
    {
        $r->validate([
            'rows'             => 'nullable|array',
            'rows.*.name'      => 'nullable|string|max:255',
            'rows.*.description'=> 'nullable|string',
        ]);

        $manageCity->topPlaces()->delete();

        foreach (($r->rows ?? []) as $i => $row) {
            if (empty($row['name']) && empty($row['description'])) continue;
            ManageCityTopPlace::create([
                'manage_city_id' => $manageCity->id,
                'name'           => $row['name'] ?? null,
                'description'    => $row['description'] ?? null,
                'sort_order'     => $i,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Top Tourist Places saved.']);
    }

    // ── Things To Do ──────────────────────────────────────────────────────

    public function saveThingsToDo(Request $r, ManageCity $manageCity)
    {
        $r->validate([
            'rows'                => 'nullable|array',
            'rows.*.title'        => 'nullable|string|max:255',
            'rows.*.description'  => 'nullable|string',
            'rows.*.duration'     => 'nullable|string|max:100',
            'rows.*.best_for'     => 'nullable|string|max:255',
            'rows.*.approx_cost'  => 'nullable|string|max:100',
        ]);

        $manageCity->thingsToDo()->delete();

        foreach (($r->rows ?? []) as $i => $row) {
            if (empty($row['title']) && empty($row['description']) && empty($row['duration']) && empty($row['best_for']) && empty($row['approx_cost'])) continue;
            ManageCityThingToDo::create([
                'manage_city_id' => $manageCity->id,
                'title'          => $row['title'] ?? null,
                'description'    => $row['description'] ?? null,
                'duration'       => $row['duration'] ?? null,
                'best_for'       => $row['best_for'] ?? null,
                'approx_cost'    => $row['approx_cost'] ?? null,
                'sort_order'     => $i,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Things To Do saved.']);
    }

    // ── Travel Tips ───────────────────────────────────────────────────────

    public function saveTravelTips(Request $r, ManageCity $manageCity)
    {
        $manageCity->update(['travel_tips' => $r->travel_tips]);
        return response()->json(['success' => true, 'message' => 'Travel Tips saved.']);
    }

    // ── Things To Know ────────────────────────────────────────────────────

    public function saveThingsToKnow(Request $r, ManageCity $manageCity)
    {
        $manageCity->update(['things_to_know' => $r->things_to_know]);
        return response()->json(['success' => true, 'message' => 'Things To Know saved.']);
    }

    // ── Religious Tourism ─────────────────────────────────────────────────

    public function saveReligiousTourism(Request $r, ManageCity $manageCity)
    {
        $manageCity->update(['religious_tourism' => $r->religious_tourism]);
        return response()->json(['success' => true, 'message' => 'Religious Tourism saved.']);
    }

    // ── Souvenirs & Dishes ────────────────────────────────────────────────

    public function saveSouvenirs(Request $r, ManageCity $manageCity)
    {
        $manageCity->update([
            'souvenirs_to_shop' => $r->souvenirs_to_shop,
            'popular_dishes'    => $r->popular_dishes,
        ]);
        return response()->json(['success' => true, 'message' => 'Souvenirs & Dishes saved.']);
    }

    // ── Festivals Intro ───────────────────────────────────────────────────
    // Shared per state — this is the same intro shown on both the state page
    // and all of its cities' pages via the /api/v1/state* endpoints.

    public function saveFestivalsIntro(Request $r, ManageCity $manageCity)
    {
        $stateId = $manageCity->state_id ?? $manageCity->location?->state_id;

        if (! $stateId) {
            return response()->json(['success' => false, 'message' => 'No state associated with this page.'], 422);
        }

        $festivalStatePage = FestivalStatePage::withTrashed()->updateOrCreate(
            ['state_id' => $stateId],
            ['short_description' => $r->intro]
        );
        if ($festivalStatePage->trashed()) $festivalStatePage->restore();

        return response()->json(['success' => true, 'message' => 'Festivals intro saved.']);
    }

    // ── Quick Facts (modal) ────────────────────────────────────────────────

    public function getQuickFacts(ManageCity $manageCity)
    {
        return response()->json([
            'display_name' => $manageCity->display_name,
            'facts'        => $manageCity->quickFacts()->get(['id', 'label', 'value', 'sort_order']),
        ]);
    }

    public function saveQuickFacts(Request $r, ManageCity $manageCity)
    {
        $r->validate([
            'facts'          => 'nullable|array',
            'facts.*.label'  => 'nullable|string|max:150',
            'facts.*.value'  => 'nullable|string|max:255',
        ]);

        $manageCity->quickFacts()->delete();

        foreach (($r->facts ?? []) as $i => $fact) {
            if (empty($fact['label']) && empty($fact['value'])) continue;
            ManageCityQuickFact::create([
                'manage_city_id' => $manageCity->id,
                'label'          => $fact['label'] ?? null,
                'value'          => $fact['value'] ?? null,
                'sort_order'     => $i,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Quick Facts saved.']);
    }

    // ── FAQs (modal) ──────────────────────────────────────────────────────

    public function getFaqs(ManageCity $manageCity)
    {
        return response()->json([
            'display_name' => $manageCity->display_name,
            'faq_title'    => $manageCity->faq_title,
            'faqs'         => $manageCity->faqs()->get(['id', 'question', 'answer', 'sort_order']),
        ]);
    }

    public function saveFaqs(Request $r, ManageCity $manageCity)
    {
        $r->validate([
            'faq_title'         => 'nullable|string|max:255',
            'faqs'              => 'nullable|array',
            'faqs.*.question'   => 'nullable|string',
            'faqs.*.answer'     => 'nullable|string',
        ]);

        $manageCity->update(['faq_title' => $r->faq_title]);
        $manageCity->faqs()->delete();

        foreach (($r->faqs ?? []) as $i => $faq) {
            if (empty($faq['question']) && empty($faq['answer'])) continue;
            ManageCityFaq::create([
                'manage_city_id' => $manageCity->id,
                'question'       => $faq['question'] ?? null,
                'answer'         => $faq['answer'] ?? null,
                'sort_order'     => $i,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'FAQs saved.']);
    }

    // ── Meta (modal) ──────────────────────────────────────────────────────

    public function getMeta(ManageCity $manageCity)
    {
        $meta = $manageCity->meta ?? new ManageCityMeta();
        return response()->json([
            'display_name'     => $manageCity->display_name,
            'meta_title'       => $meta->meta_title,
            'meta_description' => $meta->meta_description,
            'meta_keywords'    => $meta->meta_keywords,
            'h1_heading'       => $meta->h1_heading,
            'meta_details'     => $meta->meta_details,
        ]);
    }

    public function saveMeta(Request $r, ManageCity $manageCity)
    {
        $r->validate([
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords'    => 'nullable|string',
            'h1_heading'       => 'nullable|string|max:255',
            'meta_details'     => 'nullable|string',
        ]);

        $manageCityMeta = ManageCityMeta::withTrashed()->updateOrCreate(
            ['manage_city_id' => $manageCity->id],
            $r->only(['meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details'])
        );
        if ($manageCityMeta->trashed()) $manageCityMeta->restore();

        return response()->json(['success' => true, 'message' => 'Meta saved.']);
    }
}
