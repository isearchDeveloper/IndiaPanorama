<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ExperienceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExperienceCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');

        $allCount      = ExperienceCategory::count();
        $activeCount   = ExperienceCategory::where('is_active', 1)->count();
        $inactiveCount = ExperienceCategory::where('is_active', 0)->count();

        $categories = ExperienceCategory::when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($status === 'active', fn ($q) => $q->where('is_active', 1))
            ->when($status === 'inactive', fn ($q) => $q->where('is_active', 0))
            ->orderBy('sort_order')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        if ($request->ajax() || $request->boolean('ajax')) {
            return response()->json([
                'html' => view('admin.experiences.categories._table', compact('categories'))->render(),
            ]);
        }

        $allStates = \App\Models\State::orderBy('name')->get(['id', 'name']);
        $allLocations = \App\Models\Location::orderBy('name')->get(['id', 'name', 'state_id']);

        return view('admin.experiences.categories.index', compact('categories', 'allCount', 'activeCount', 'inactiveCount', 'status', 'allStates', 'allLocations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'image'     => 'nullable|string|exists:media,path',
            'image_alt' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'intro_image' => 'nullable|string|exists:media,path',
        ]);

        $slug = Str::slug($request->name);
        \App\Services\ExperienceSlugRules::assertUnique($slug);

        $path = $request->filled('image') ? $request->input('image') : null;
        $introPath = $request->filled('intro_image') ? $request->input('intro_image') : null;

        $maxOrder = ExperienceCategory::max('sort_order') ?? -1;

        $category = ExperienceCategory::create([
            'name'        => $request->name,
            'slug'        => $slug,
            'image'       => $path,
            'image_alt'   => $request->input('image_alt', ''),
            'description' => $request->description,
            'intro_image' => $introPath,
            'sort_order'  => $maxOrder + 1,
        ]);

        ActivityLog::log('created', 'ExperienceCategory', "Created experience category: {$category->name}");

        return response()->json(['status' => true, 'message' => 'Category added.', 'category' => $category]);
    }

    /** JSON fetch for the Edit / Banner / Quick Info / Perfect For / Popular Cities / FAQs / Meta modals. */
    public function show(ExperienceCategory $category)
    {
        $category->load(['quickInfos', 'perfectFors', 'popularCities.state', 'popularCities.location', 'faqs']);

        $data = $category->toArray();

        return response()->json($data);
    }

    public function update(Request $request, ExperienceCategory $category)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'image'     => 'nullable|string|exists:media,path',
            'image_alt' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'intro_image' => 'nullable|string|exists:media,path',
        ]);

        $imageChanged = $request->has('image') && $request->input('image') !== $category->image;
        $introImageChanged = $request->has('intro_image') && $request->input('intro_image') !== $category->intro_image;

        $slug = Str::slug($request->name);
        \App\Services\ExperienceSlugRules::assertUnique($slug, ignoreCategoryId: $category->id);

        $data = $request->only(['image_alt', 'description']);
        $data['name'] = $request->name;
        $data['slug'] = $slug;

        if ($imageChanged) {
            $data['image'] = $request->input('image');
        }

        if ($introImageChanged) {
            $data['intro_image'] = $request->input('intro_image');
        }

        $category->update($data);

        ActivityLog::log('updated', 'ExperienceCategory', "Updated experience category: {$category->name}");

        return response()->json(['status' => true, 'message' => 'Category updated.', 'category' => $category]);
    }

    /**
     * Single endpoint for the Banner / Quick Info / Perfect For / Popular Cities / FAQs / SEO Meta modals —
     * dispatches on the `section` field, mirroring ThemeController::updateSection() / TouristActivityController's section updates.
     */
    public function updateSection(Request $request, ExperienceCategory $category)
    {
        switch ($request->input('section')) {

            case 'quick_info':
                $category->quickInfos()->delete();
                foreach ($request->input('quick_info', []) as $i => $row) {
                    if (!empty($row['label'])) {
                        $category->quickInfos()->create([
                            'label'      => $row['label'],
                            'value'      => $row['value'] ?? null,
                            'sort_order' => $i,
                        ]);
                    }
                }
                break;

            case 'perfect_for':
                $category->perfectFors()->delete();
                foreach ($request->input('perfect_for', []) as $i => $row) {
                    if (!empty($row['title'])) {
                        $category->perfectFors()->create([
                            'title'       => $row['title'],
                            'description' => $row['description'] ?? null,
                            'icon'        => $row['icon'] ?? null,
                            'sort_order'  => $i,
                        ]);
                    }
                }
                break;

            case 'popular_cities':
                $category->popularCities()->delete();
                foreach ($request->input('popular_cities', []) as $i => $row) {
                    if (!empty($row['title']) && !empty($row['state_id'])) {
                        $category->popularCities()->create([
                            'title'       => $row['title'],
                            'image'       => $row['image'] ?? null,
                            'image_alt'   => $row['image_alt'] ?? null,
                            'description' => $row['description'] ?? null,
                            'popular_tag' => $row['popular_tag'] ?? null,
                            'state_id'    => $row['state_id'],
                            'location_id' => $row['location_id'] ?? null,
                            'sort_order'  => $i,
                        ]);
                    }
                }
                break;

            case 'meta':
                $category->update($request->only([
                    'meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details',
                ]));
                break;

            case 'faqs':
                $category->update($request->only(['faq_title', 'faq_sub_title']));
                $category->faqs()->delete();
                foreach ($request->input('faqs', []) as $i => $row) {
                    if (!empty($row['question'])) {
                        $category->faqs()->create([
                            'question'   => $row['question'],
                            'answer'     => $row['answer'] ?? null,
                            'sort_order' => $i,
                        ]);
                    }
                }
                break;

            default:
                return response()->json(['status' => false, 'message' => 'Unknown section.'], 422);
        }

        ActivityLog::log('updated', 'ExperienceCategory', "Updated {$request->input('section')} for experience category: {$category->name}");

        return response()->json(['status' => true, 'message' => 'Saved successfully.']);
    }

    public function toggleStatus(ExperienceCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        return response()->json(['status' => true, 'is_active' => $category->is_active, 'message' => 'Status updated.']);
    }

    public function destroy(ExperienceCategory $category)
    {
        $category->subcategories()->delete();
        $category->quickInfos()->delete();
        $category->perfectFors()->delete();
        $category->popularCities()->delete();
        $category->faqs()->delete();

        $name = $category->name;
        $category->delete();

        ActivityLog::log('deleted', 'ExperienceCategory', "Deleted experience category: {$name}");

        return response()->json(['status' => true, 'message' => 'Category deleted.']);
    }

    public function slugDuplicateCheck(Request $request)
    {
        $slug = Str::slug($request->name);
        return response()->json([
            'exists' => \App\Services\ExperienceSlugRules::exists($slug, ignoreCategoryId: $request->id),
        ]);
    }
}
