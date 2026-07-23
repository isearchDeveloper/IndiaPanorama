<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Models\ExperienceCategory;
use App\Models\ExperienceFaq;
use App\Models\ExperienceGalleryImage;
use App\Models\ExperiencePage;
use App\Models\ExperienceSubcategory;
use App\Models\Location;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ExperienceController extends Controller
{
    /**
     * Ensure both the state and the city have an Experience hub page row — created blank if
     * missing, left untouched if either already exists. Mirrors
     * TouristActivityController::ensureActivityPages(): pages are never created manually,
     * only once a real Experience item references that state/city. City is optional on an
     * Experience now, so its page is only ensured when a city was actually given.
     */
    private function ensureExperiencePages(int $stateId, ?int $locationId): void
    {
        $statePage = ExperiencePage::withTrashed()->firstOrCreate(['state_id' => $stateId], ['is_active' => true]);
        if ($statePage->trashed()) $statePage->restore();

        if (!$locationId) {
            return;
        }

        $locationPage = ExperiencePage::withTrashed()->firstOrCreate(['location_id' => $locationId], ['is_active' => true]);
        if ($locationPage->trashed()) $locationPage->restore();
    }

    public function index(Request $r)
    {
        $status = $r->get('status', 'all');

        $allCount      = Experience::count();
        $activeCount   = Experience::where('is_active', 1)->count();
        $inactiveCount = Experience::where('is_active', 0)->count();

        $query = Experience::with(['category', 'subcategory', 'state', 'location', 'galleryImages']);

        if ($r->filled('search')) {
            $search = $r->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($r->filled('subcategory_id')) {
            $query->where('subcategory_id', $r->subcategory_id);
        } elseif ($r->filled('category_id')) {
            $query->where('category_id', $r->category_id);
        }

        $query->when($status === 'active', fn ($q) => $q->where('is_active', 1))
              ->when($status === 'inactive', fn ($q) => $q->where('is_active', 0));

        $experiences = $query->orderByDesc('id')->paginate(20)->withQueryString();

        if ($r->ajax() || $r->boolean('ajax')) {
            return response()->json([
                'html' => view('admin.experiences.items._table', compact('experiences'))->render(),
            ]);
        }

        $subcategories = ExperienceSubcategory::with('category')->orderBy('name')->get(['id', 'name', 'category_id']);
        $categories = ExperienceCategory::orderBy('name')->get(['id', 'name']);
        $states = State::orderBy('name')->get(['id', 'name']);
        $locations = Location::orderBy('name')->get(['id', 'name', 'state_id']);

        return view('admin.experiences.items.index', compact('experiences', 'subcategories', 'categories', 'states', 'locations', 'allCount', 'activeCount', 'inactiveCount', 'status'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'category_id'            => 'required|exists:experience_categories,id',
            'subcategory_id'         => 'nullable|exists:experience_subcategories,id',
            'state_id'               => 'required|exists:states,id',
            'location_id'            => 'nullable|exists:locations,id',
            'title'                  => 'required|string|max:255',
            'tagline'                => 'nullable|string|max:255',
            'description'            => 'nullable|string',
            'gallery_images'         => 'nullable|array',
            'gallery_images.*.path'  => 'required|string|exists:media,path',
            'gallery_images.*.alt'   => 'nullable|string',
        ]);

        if ($r->filled('subcategory_id') &&
            !ExperienceSubcategory::where('id', $r->subcategory_id)->where('category_id', $r->category_id)->exists()) {
            throw ValidationException::withMessages(['subcategory_id' => 'That subcategory does not belong to the selected category.']);
        }

        $slug = Str::slug($r->title);
        if (Experience::where('slug', $slug)->exists()) {
            throw ValidationException::withMessages(['title' => 'An experience with this title already exists.']);
        }

        $experience = Experience::create([
            'category_id'    => $r->category_id,
            'subcategory_id' => $r->subcategory_id ?: null,
            'state_id'       => $r->state_id,
            'location_id'    => $r->location_id ?: null,
            'title'          => $r->title,
            'slug'           => $slug,
            'tagline'        => $r->tagline,
            'description'    => $r->description,
            'sort_order'     => (int) Experience::max('sort_order') + 1,
        ]);

        foreach ($r->input('gallery_images', []) as $i => $item) {
            if (empty($item['path'])) continue;
            $experience->galleryImages()->create([
                'image'      => $item['path'],
                'image_alt'  => $item['alt'] ?? null,
                'sort_order' => $i,
            ]);
        }

        $this->ensureExperiencePages($experience->state_id, $experience->location_id);

        return response()->json(['status' => true, 'message' => 'Experience created.']);
    }

    public function show(Experience $experience)
    {
        $experience->load(['category', 'subcategory', 'state', 'location', 'galleryImages', 'quickInfos', 'highlights', 'faqs']);

        return response()->json($experience->toArray());
    }

    public function update(Request $r, Experience $experience)
    {
        $r->validate([
            'category_id'      => 'required|exists:experience_categories,id',
            'subcategory_id'   => 'nullable|exists:experience_subcategories,id',
            'state_id'         => 'required|exists:states,id',
            'location_id'      => 'nullable|exists:locations,id',
            'title'            => 'required|string|max:255',
            'tagline'          => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'banner_image'     => 'nullable|string|exists:media,path',
            'banner_image_alt' => 'nullable|string|max:255',
        ]);

        if ($r->filled('subcategory_id') &&
            !ExperienceSubcategory::where('id', $r->subcategory_id)->where('category_id', $r->category_id)->exists()) {
            throw ValidationException::withMessages(['subcategory_id' => 'That subcategory does not belong to the selected category.']);
        }

        $experience->update([
            'category_id'    => $r->category_id,
            'subcategory_id' => $r->subcategory_id ?: null,
            'state_id'       => $r->state_id,
            'location_id'    => $r->location_id ?: null,
            'title'          => $r->title,
            'tagline'        => $r->tagline,
            'description'    => $r->description,
        ]);

        // Banner = the gallery image with the lowest sort_order (see ExperienceGalleryImage migration).
        // Replace/create that row instead of adding a competing "first" image; clearing it
        // (has('banner_image') but empty) removes the row rather than storing an empty path.
        if ($r->has('banner_image') || $r->filled('banner_image_alt')) {
            $banner = $experience->galleryImages()->orderBy('sort_order')->first();

            if ($r->has('banner_image') && !$r->filled('banner_image')) {
                $banner?->delete();
            } elseif ($banner) {
                $banner->update([
                    'image'     => $r->filled('banner_image') ? $r->input('banner_image') : $banner->image,
                    'image_alt' => $r->input('banner_image_alt', $banner->image_alt),
                ]);
            } elseif ($r->filled('banner_image')) {
                $experience->galleryImages()->create([
                    'image'      => $r->input('banner_image'),
                    'image_alt'  => $r->input('banner_image_alt'),
                    'sort_order' => 0,
                ]);
            }
        }

        $this->ensureExperiencePages($experience->state_id, $experience->location_id);

        return response()->json(['status' => true, 'message' => 'Experience updated.']);
    }

    public function updateSection(Request $r, Experience $experience)
    {
        if ($r->section === 'quick_info') {
            $experience->quickInfos()->delete();
            foreach ($r->input('quick_info', []) as $i => $row) {
                if (!empty($row['label'])) {
                    $experience->quickInfos()->create([
                        'label'      => $row['label'],
                        'value'      => $row['value'] ?? null,
                        'sort_order' => $i,
                    ]);
                }
            }
        }

        if ($r->section === 'highlights') {
            $experience->highlights()->delete();
            foreach ($r->input('highlights', []) as $i => $text) {
                if (trim($text) === '') continue;
                $experience->highlights()->create(['text' => $text, 'sort_order' => $i]);
            }
        }

        if ($r->section === 'faqs') {
            $experience->faqs()->delete();
            foreach ($r->input('faqs', []) as $i => $faq) {
                if (trim($faq['question'] ?? '') === '') continue;
                ExperienceFaq::create([
                    'experience_id' => $experience->id,
                    'question'      => $faq['question'],
                    'answer'        => $faq['answer'] ?? null,
                    'sort_order'    => $i,
                ]);
            }
        }

        if ($r->section === 'meta') {
            $experience->update($r->only(['meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details']));
        }

        return response()->json(['status' => true, 'message' => 'Saved successfully.']);
    }

    public function addGalleryImage(Request $r, Experience $experience)
    {
        $r->validate([
            'gallery_images'        => 'required|array|min:1',
            'gallery_images.*.path' => 'required|string|exists:media,path',
            'gallery_images.*.alt'  => 'nullable|string',
        ]);

        $sortOrder = $experience->galleryImages()->count();
        $images = [];

        foreach ($r->input('gallery_images', []) as $item) {
            if (empty($item['path'])) continue;

            $image = $experience->galleryImages()->create([
                'image'      => $item['path'],
                'image_alt'  => $item['alt'] ?? null,
                'sort_order' => $sortOrder++,
            ]);
            $images[] = $image;
        }

        if ($r->ajax()) {
            return response()->json(['status' => true, 'message' => 'Image(s) added.', 'images' => $images]);
        }

        return redirect()->route('admin.experiences.index')->with('success', 'Gallery image(s) added.');
    }

    public function updateGalleryImageAlt(Request $r, ExperienceGalleryImage $gallery_image)
    {
        $r->validate(['image_alt' => 'nullable|string|max:255']);
        $gallery_image->update(['image_alt' => $r->image_alt]);
        return response()->json(['status' => true, 'message' => 'Alt text saved.']);
    }

    public function deleteGalleryImage(ExperienceGalleryImage $gallery_image)
    {
        $gallery_image->delete();

        return response()->json(['status' => true, 'message' => 'Gallery image removed.']);
    }

    public function toggleStatus(Experience $experience)
    {
        $experience->update(['is_active' => !$experience->is_active]);
        return response()->json(['status' => true, 'is_active' => $experience->is_active]);
    }

    public function togglePopular(Experience $experience)
    {
        $experience->update(['is_popular' => !$experience->is_popular]);
        return response()->json(['status' => true, 'is_popular' => $experience->is_popular]);
    }

    public function destroy(Experience $experience)
    {
        // Gallery image files are not deleted from storage — they're shared
        // Media Library assets that may still be referenced elsewhere.
        $experience->galleryImages()->delete();
        $experience->quickInfos()->delete();
        $experience->highlights()->delete();
        $experience->faqs()->delete();
        $experience->delete();

        return response()->json(['status' => true, 'message' => 'Experience deleted.']);
    }

    public function slugDuplicateCheck(Request $r)
    {
        return response()->json([
            'exists' => Experience::where('slug', Str::slug($r->title))
                ->where('id', '!=', $r->id)
                ->exists(),
        ]);
    }
}
