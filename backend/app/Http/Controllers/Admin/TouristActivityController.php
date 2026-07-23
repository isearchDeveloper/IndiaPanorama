<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\State;
use App\Models\TouristActivity;
use App\Models\TouristActivityExperience;
use App\Models\TouristActivityFaq;
use App\Models\TouristActivityGalleryImage;
use App\Models\TouristActivityItineraryStep;
use App\Models\TouristActivityPage;
use App\Models\TouristActivityThingToDo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TouristActivityController extends Controller
{
    /**
     * Ensure both the state and the city have a "Activities of {Place}" page row — created blank
     * if missing, left untouched if either already exists. Pages are never created manually
     * (no "Add Page" option on /admin/tourist-activity-pages) so a state/city only ever gets a
     * page once a real Activity references it.
     */
    private function ensureActivityPages(int $stateId, int $locationId): void
    {
        $statePage = TouristActivityPage::withTrashed()->firstOrCreate(['state_id' => $stateId], ['is_active' => true]);
        if ($statePage->trashed()) $statePage->restore();

        $locationPage = TouristActivityPage::withTrashed()->firstOrCreate(['location_id' => $locationId], ['is_active' => true]);
        if ($locationPage->trashed()) $locationPage->restore();
    }

    public function index(Request $r)
    {
        $status = $r->get('status', 'all');

        $allCount      = TouristActivity::count();
        $activeCount   = TouristActivity::where('is_active', 1)->count();
        $inactiveCount = TouristActivity::where('is_active', 0)->count();

        $query = TouristActivity::with(['state', 'location']);

        if ($r->filled('search')) {
            $search = $r->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $query->when($status === 'active', fn ($q) => $q->where('is_active', 1))
              ->when($status === 'inactive', fn ($q) => $q->where('is_active', 0));

        $activities = $query->orderByDesc('id')->paginate(20)->withQueryString();

        if ($r->ajax() || $r->boolean('ajax')) {
            return response()->json([
                'html' => view('admin.tourist-activities._table', compact('activities'))->render(),
            ]);
        }

        $states = State::orderBy('name')->get(['id', 'name']);
        $locations = Location::orderBy('name')->get(['id', 'name', 'state_id']);

        return view('admin.tourist-activities.index', compact('activities', 'states', 'locations', 'allCount', 'activeCount', 'inactiveCount', 'status'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'name'              => 'required|string|max:255',
            'state_id'          => 'required|exists:states,id',
            'location_id'       => 'required|exists:locations,id',
            'banner_image'      => 'required|string|exists:media,path',
            'banner_image_alt'  => 'nullable|string|max:255',
            'tagline'           => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
        ]);

        $slug = Str::slug($r->name . ' activity');
        if (TouristActivity::where('slug', $slug)->exists()) {
            throw ValidationException::withMessages(['name' => 'An activity with this name already exists.']);
        }

        $path = $r->input('banner_image');

        $activity = TouristActivity::create([
            'name'              => $r->name,
            'slug'              => $slug,
            'state_id'          => $r->state_id,
            'location_id'       => $r->location_id,
            'banner_image'      => $path,
            'banner_image_alt'  => $r->banner_image_alt,
            'tagline'           => $r->tagline,
            'short_description' => $r->short_description,
            'sort_order'        => (int) TouristActivity::max('sort_order') + 1,
        ]);

        $this->ensureActivityPages($activity->state_id, $activity->location_id);

        return response()->json(['status' => true, 'message' => 'Activity created.']);
    }

    public function show(TouristActivity $tourist_activity)
    {
        $tourist_activity->load(['state', 'location', 'itinerarySteps', 'experiences', 'thingsToDo', 'galleryImages', 'faqs']);

        return response()->json($tourist_activity->toArray());
    }

    public function update(Request $r, TouristActivity $tourist_activity)
    {
        $r->validate([
            'name'              => 'required|string|max:255',
            'state_id'          => 'required|exists:states,id',
            'location_id'       => 'required|exists:locations,id',
            'banner_image'      => 'nullable|string|exists:media,path',
            'banner_image_alt'  => 'nullable|string|max:255',
            'tagline'           => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
        ]);

        $imageChanged = $r->has('banner_image') && $r->input('banner_image') !== $tourist_activity->banner_image;

        $path = $tourist_activity->banner_image;
        if ($imageChanged) {
            $path = $r->input('banner_image');
        }

        $tourist_activity->update([
            'name'              => $r->name,
            'state_id'          => $r->state_id,
            'location_id'       => $r->location_id,
            'banner_image'      => $path,
            'banner_image_alt'  => $r->banner_image_alt,
            'tagline'           => $r->tagline,
            'short_description' => $r->short_description,
        ]);

        $this->ensureActivityPages($tourist_activity->state_id, $tourist_activity->location_id);

        return response()->json(['status' => true, 'message' => 'Activity updated.']);
    }

    public function updateSection(Request $r, TouristActivity $tourist_activity)
    {
        if ($r->section === 'itinerary') {
            $tourist_activity->update(['itinerary_title' => $r->itinerary_title]);
            $tourist_activity->itinerarySteps()->delete();
            foreach ($r->input('itinerary', []) as $i => $step) {
                if (trim($step['title'] ?? '') === '') continue;
                TouristActivityItineraryStep::create([
                    'activity_id' => $tourist_activity->id,
                    'title'       => $step['title'],
                    'description' => $step['description'] ?? null,
                    'sort_order'  => $i,
                ]);
            }
        }

        if ($r->section === 'experiences') {
            $r->validate([
                'experiences_title'  => 'nullable|string|max:255',
                'titles.*'           => 'nullable|string|max:255',
                'experience_images.*' => 'nullable|string|exists:media,path',
            ]);

            $tourist_activity->update(['experiences_title' => $r->experiences_title]);

            $images = $r->input('experience_images', []);
            $rows = [];

            foreach ($r->input('titles', []) as $i => $title) {
                if (trim($title) === '') continue;
                $rows[] = ['title' => $title, 'description' => $r->input("descriptions.$i"), 'image' => $images[$i] ?? null];
            }

            $tourist_activity->experiences()->delete();

            foreach ($rows as $i => $row) {
                $tourist_activity->experiences()->create([
                    'title'       => $row['title'],
                    'description' => $row['description'],
                    'image'       => $row['image'],
                    'sort_order'  => $i,
                ]);
            }
        }

        if ($r->section === 'things_to_do') {
            $tourist_activity->update(['things_to_do_title' => $r->things_to_do_title]);
            $tourist_activity->thingsToDo()->delete();
            foreach ($r->input('things_to_do', []) as $i => $item) {
                if (trim($item['title'] ?? '') === '') continue;
                TouristActivityThingToDo::create([
                    'activity_id' => $tourist_activity->id,
                    'title'       => $item['title'],
                    'description' => $item['description'] ?? null,
                    'sort_order'  => $i,
                ]);
            }
        }

        if ($r->section === 'faqs') {
            $tourist_activity->update(['faq_title' => $r->faq_title, 'faq_sub_title' => $r->faq_sub_title]);
            $tourist_activity->faqs()->delete();
            foreach ($r->input('faqs', []) as $i => $faq) {
                if (trim($faq['question'] ?? '') === '') continue;
                TouristActivityFaq::create([
                    'activity_id' => $tourist_activity->id,
                    'question'    => $faq['question'],
                    'answer'      => $faq['answer'] ?? null,
                    'sort_order'  => $i,
                ]);
            }
        }

        if ($r->section === 'meta') {
            $tourist_activity->update($r->only(['meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details']));
        }

        return response()->json(['status' => true, 'message' => 'Saved successfully.']);
    }

    public function addGalleryImage(Request $r, TouristActivity $tourist_activity)
    {
        $r->validate([
            'gallery_images'         => 'required|array|min:1',
            'gallery_images.*.path'  => 'required|string|exists:media,path',
            'gallery_images.*.alt'   => 'nullable|string|max:255',
        ]);

        $sortOrder = $tourist_activity->galleryImages()->count();
        $images = [];

        foreach ($r->input('gallery_images', []) as $item) {
            $images[] = $tourist_activity->galleryImages()->create([
                'image'      => $item['path'],
                'image_alt'  => $item['alt'] ?? null,
                'sort_order' => $sortOrder++,
            ]);
        }

        if ($r->ajax()) {
            return response()->json(['status' => true, 'message' => 'Image(s) added.', 'images' => $images]);
        }

        return redirect()->route('admin.tourist-activities.index')->with('success', 'Gallery image(s) added.');
    }

    public function updateGalleryImageAlt(Request $r, TouristActivityGalleryImage $gallery_image)
    {
        $r->validate(['image_alt' => 'nullable|string|max:255']);

        $gallery_image->update(['image_alt' => $r->image_alt]);

        return response()->json(['status' => true, 'message' => 'Alt text saved.']);
    }

    public function deleteGalleryImage(TouristActivityGalleryImage $gallery_image)
    {
        // Note: the underlying file is NOT deleted from storage here — it lives in the
        // shared Media Library and may still be used elsewhere. Only unlink this row.
        $gallery_image->delete();

        return response()->json(['status' => true, 'message' => 'Gallery image removed.']);
    }

    public function toggleStatus(TouristActivity $tourist_activity)
    {
        $tourist_activity->update(['is_active' => !$tourist_activity->is_active]);
        return response()->json(['status' => true, 'is_active' => $tourist_activity->is_active]);
    }

    public function togglePopular(TouristActivity $tourist_activity)
    {
        $tourist_activity->update(['is_popular' => !$tourist_activity->is_popular]);
        return response()->json(['status' => true, 'is_popular' => $tourist_activity->is_popular]);
    }

    public function destroy(TouristActivity $tourist_activity)
    {
        // Note: banner_image/experiences/galleryImages are intentionally NOT deleted
        // from storage here — they live in the shared Media Library and may still be
        // used elsewhere.
        $tourist_activity->itinerarySteps()->delete();
        $tourist_activity->experiences()->delete();
        $tourist_activity->thingsToDo()->delete();
        $tourist_activity->galleryImages()->delete();
        $tourist_activity->faqs()->delete();
        $tourist_activity->delete();

        return response()->json(['status' => true, 'message' => 'Activity deleted.']);
    }

    public function slugDuplicateCheck(Request $r)
    {
        return response()->json([
            'exists' => TouristActivity::where('slug', Str::slug($r->name . ' activity'))
                ->where('id', '!=', $r->id)
                ->exists(),
        ]);
    }
}
