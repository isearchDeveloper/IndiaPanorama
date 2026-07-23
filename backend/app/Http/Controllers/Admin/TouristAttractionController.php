<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\State;
use App\Models\TouristAttraction;
use App\Models\TouristAttractionActivity;
use App\Models\TouristAttractionFaq;
use App\Models\TouristAttractionGalleryImage;
use App\Models\TouristAttractionHighlight;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TouristAttractionController extends Controller
{
    public function index(Request $r)
    {
        $status = $r->get('status', 'all');

        $allCount      = TouristAttraction::count();
        $activeCount   = TouristAttraction::where('is_active', 1)->count();
        $inactiveCount = TouristAttraction::where('is_active', 0)->count();

        $query = TouristAttraction::with(['state', 'location']);

        if ($r->filled('search')) {
            $search = $r->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $query->when($status === 'active', fn ($q) => $q->where('is_active', 1))
              ->when($status === 'inactive', fn ($q) => $q->where('is_active', 0));

        $attractions = $query->orderByDesc('id')->paginate(20)->withQueryString();

        if ($r->ajax() || $r->boolean('ajax')) {
            return response()->json([
                'html' => view('admin.tourist-attractions._table', compact('attractions'))->render(),
            ]);
        }

        $states = State::orderBy('name')->get(['id', 'name']);
        $locations = Location::orderBy('name')->get(['id', 'name', 'state_id']);

        return view('admin.tourist-attractions.index', compact('attractions', 'states', 'locations', 'allCount', 'activeCount', 'inactiveCount', 'status'));
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

        $slug = Str::slug($r->name . ' tourist-attractions');
        if (TouristAttraction::where('slug', $slug)->exists()) {
            throw ValidationException::withMessages(['name' => 'An attraction with this name already exists.']);
        }

        $path = $r->input('banner_image');

        $attraction = TouristAttraction::create([
            'name'              => $r->name,
            'slug'              => $slug,
            'state_id'          => $r->state_id,
            'location_id'       => $r->location_id,
            'banner_image'      => $path,
            'banner_image_alt'  => $r->banner_image_alt,
            'tagline'           => $r->tagline,
            'short_description' => $r->short_description,
            'sort_order'        => (int) TouristAttraction::max('sort_order') + 1,
        ]);

        return response()->json(['status' => true, 'message' => 'Attraction created.']);
    }

    public function show(TouristAttraction $tourist_attraction)
    {
        $tourist_attraction->load(['state', 'location', 'highlights', 'activities', 'galleryImages', 'faqs']);
        $data = $tourist_attraction->toArray();
        return response()->json($data);
    }

    public function update(Request $r, TouristAttraction $tourist_attraction)
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

        $imageChanged = $r->has('banner_image') && $r->input('banner_image') !== $tourist_attraction->banner_image;

        $path = $tourist_attraction->banner_image;
        if ($imageChanged) {
            $path = $r->input('banner_image');
        }

        $tourist_attraction->update([
            'name'              => $r->name,
            'state_id'          => $r->state_id,
            'location_id'       => $r->location_id,
            'banner_image'      => $path,
            'banner_image_alt'  => $r->banner_image_alt,
            'tagline'           => $r->tagline,
            'short_description' => $r->short_description,
        ]);

        return response()->json(['status' => true, 'message' => 'Attraction updated.']);
    }

    public function updateSection(Request $r, TouristAttraction $tourist_attraction)
    {
        if ($r->section === 'quick_info') {
            $tourist_attraction->update([
                'location_text' => $r->location_text,
                'duration_text' => $r->duration_text,
                'best_for'      => $r->best_for,
                'best_season'   => $r->best_season,
            ]);
        }

        if ($r->section === 'why_visit') {
            $r->validate(['why_visit_image' => 'nullable|string|exists:media,path']);

            $imageChanged = $r->has('why_visit_image') && $r->input('why_visit_image') !== $tourist_attraction->why_visit_image;

            $path = $tourist_attraction->why_visit_image;
            if ($imageChanged) {
                $path = $r->input('why_visit_image');
            }

            $tourist_attraction->update([
                'why_visit_title'       => $r->why_visit_title,
                'why_visit_image'       => $path,
                'why_visit_image_alt'   => $r->why_visit_image_alt,
                'why_visit_description' => $r->why_visit_description,
            ]);

            $tourist_attraction->highlights()->delete();
            foreach ($r->input('highlights', []) as $i => $text) {
                if (trim($text) === '') continue;
                TouristAttractionHighlight::create(['attraction_id' => $tourist_attraction->id, 'text' => $text, 'sort_order' => $i]);
            }
        }

        if ($r->section === 'activities') {
            $tourist_attraction->activities()->delete();
            foreach ($r->input('activities', []) as $i => $a) {
                if (trim($a['title'] ?? '') === '') continue;
                TouristAttractionActivity::create([
                    'attraction_id' => $tourist_attraction->id,
                    'title'         => $a['title'],
                    'description'   => $a['description'] ?? null,
                    'sort_order'    => $i,
                ]);
            }
        }

        if ($r->section === 'faqs') {
            $tourist_attraction->update(['faq_title' => $r->faq_title, 'faq_sub_title' => $r->faq_sub_title]);
            $tourist_attraction->faqs()->delete();
            foreach ($r->input('faqs', []) as $i => $faq) {
                if (trim($faq['question'] ?? '') === '') continue;
                TouristAttractionFaq::create([
                    'attraction_id' => $tourist_attraction->id,
                    'question'      => $faq['question'],
                    'answer'        => $faq['answer'] ?? null,
                    'sort_order'    => $i,
                ]);
            }
        }

        if ($r->section === 'meta') {
            $tourist_attraction->update($r->only(['meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details']));
        }

        return response()->json(['status' => true, 'message' => 'Saved successfully.']);
    }

    public function addGalleryImage(Request $r, TouristAttraction $tourist_attraction)
    {
        $r->validate([
            'gallery_images'        => 'required|array|min:1',
            'gallery_images.*.path' => 'required|string|exists:media,path',
            'gallery_images.*.alt'  => 'nullable|string|max:255',
        ]);

        $sortOrder = $tourist_attraction->galleryImages()->count();
        $images = [];

        foreach ($r->input('gallery_images', []) as $item) {
            $images[] = $tourist_attraction->galleryImages()->create([
                'image'      => $item['path'],
                'image_alt'  => $item['alt'] ?? null,
                'sort_order' => $sortOrder++,
            ]);
        }

        if ($r->ajax()) {
            return response()->json(['status' => true, 'message' => 'Image(s) added.', 'images' => $images]);
        }

        return redirect()->route('admin.tourist-attractions.index')->with('success', 'Gallery image(s) added.');
    }

    public function updateGalleryImageAlt(Request $r, TouristAttractionGalleryImage $gallery_image)
    {
        $r->validate(['image_alt' => 'nullable|string|max:255']);

        $gallery_image->update(['image_alt' => $r->image_alt]);

        return response()->json(['status' => true, 'message' => 'Alt text saved.']);
    }

    public function deleteGalleryImage(TouristAttractionGalleryImage $gallery_image)
    {
        $gallery_image->delete();

        return response()->json(['status' => true, 'message' => 'Gallery image removed.']);
    }

    public function toggleStatus(TouristAttraction $tourist_attraction)
    {
        $tourist_attraction->update(['is_active' => !$tourist_attraction->is_active]);
        return response()->json(['status' => true, 'is_active' => $tourist_attraction->is_active]);
    }

    public function togglePopular(TouristAttraction $tourist_attraction)
    {
        $tourist_attraction->update(['is_popular' => !$tourist_attraction->is_popular]);
        return response()->json(['status' => true, 'is_popular' => $tourist_attraction->is_popular]);
    }

    public function destroy(TouristAttraction $tourist_attraction)
    {
        // Note: banner_image/why_visit_image/gallery images are intentionally NOT deleted
        // from storage here — they live in the shared Media Library and may still be used elsewhere.
        $tourist_attraction->highlights()->delete();
        $tourist_attraction->activities()->delete();
        $tourist_attraction->galleryImages()->delete();
        $tourist_attraction->faqs()->delete();
        $tourist_attraction->delete();

        return response()->json(['status' => true, 'message' => 'Attraction deleted.']);
    }

    public function slugDuplicateCheck(Request $r)
    {
        return response()->json([
            'exists' => TouristAttraction::where('slug', Str::slug($r->name . ' tourist-attractions'))
                ->where('id', '!=', $r->id)
                ->exists(),
        ]);
    }
}
