<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ExperienceCategory;
use App\Models\ExperienceSubcategory;
use App\Services\ExperienceSlugRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExperienceSubcategoryController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->get('category_id');

        $subcategories = ExperienceSubcategory::with('category')
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderBy('sort_order')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $categories = ExperienceCategory::orderBy('name')->get(['id', 'name']);

        if ($request->ajax() || $request->boolean('ajax')) {
            return response()->json([
                'html' => view('admin.experiences.subcategories._table', compact('subcategories'))->render(),
            ]);
        }

        return view('admin.experiences.subcategories.index', compact('subcategories', 'categories', 'categoryId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:experience_categories,id',
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|string|exists:media,path',
            'image_alt'   => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'popular_tag' => 'nullable|string|max:255',
        ]);

        $slug = Str::slug($request->name);
        ExperienceSlugRules::assertUnique($slug);

        $path = null;
        if ($request->filled('image')) {
            $path = $request->input('image');
        }

        $maxOrder = ExperienceSubcategory::where('category_id', $request->category_id)->max('sort_order') ?? -1;

        $subcategory = ExperienceSubcategory::create([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'slug'        => $slug,
            'image'       => $path,
            'image_alt'   => $request->input('image_alt', ''),
            'description' => $request->description,
            'popular_tag' => $request->popular_tag,
            'sort_order'  => $maxOrder + 1,
        ]);

        ActivityLog::log('created', 'ExperienceSubcategory', "Created experience subcategory: {$subcategory->name}");

        return response()->json(['status' => true, 'message' => 'Subcategory added.', 'subcategory' => $subcategory]);
    }

    public function show(ExperienceSubcategory $subcategory)
    {
        return response()->json($subcategory->toArray());
    }

    public function update(Request $request, ExperienceSubcategory $subcategory)
    {
        $request->validate([
            'category_id' => 'required|exists:experience_categories,id',
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|string|exists:media,path',
            'image_alt'   => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'popular_tag' => 'nullable|string|max:255',
        ]);

        $slug = Str::slug($request->name);
        ExperienceSlugRules::assertUnique($slug, ignoreSubcategoryId: $subcategory->id);

        $data = $request->only([
            'category_id', 'image_alt', 'description', 'popular_tag',
        ]);
        $data['name'] = $request->name;
        $data['slug'] = $slug;

        $imageChanged = $request->has('image') && $request->input('image') !== $subcategory->image;

        if ($imageChanged) {
            $data['image'] = $request->input('image');
        }

        $subcategory->update($data);

        ActivityLog::log('updated', 'ExperienceSubcategory', "Updated experience subcategory: {$subcategory->name}");

        return response()->json(['status' => true, 'message' => 'Subcategory updated.', 'subcategory' => $subcategory]);
    }

    public function toggleStatus(ExperienceSubcategory $subcategory)
    {
        $subcategory->update(['is_active' => !$subcategory->is_active]);
        return response()->json(['status' => true, 'is_active' => $subcategory->is_active, 'message' => 'Status updated.']);
    }

    public function destroy(ExperienceSubcategory $subcategory)
    {
        $name = $subcategory->name;

        \Illuminate\Support\Facades\DB::transaction(function () use ($subcategory) {
            foreach ($subcategory->experiences as $experience) {
                foreach ($experience->galleryImages as $image) {
                    if ($image->image && Storage::disk(config('filesystems.upload_disk'))->exists($image->image)) {
                        Storage::disk(config('filesystems.upload_disk'))->delete($image->image);
                    }
                }
            }

            $subcategory->experiences()->delete();
            $subcategory->delete();
        });

        ActivityLog::log('deleted', 'ExperienceSubcategory', "Deleted experience subcategory: {$name}");

        return response()->json(['status' => true, 'message' => 'Subcategory deleted.']);
    }

    public function slugDuplicateCheck(Request $request)
    {
        $slug = Str::slug($request->name);
        return response()->json([
            'exists' => ExperienceSlugRules::exists($slug, ignoreSubcategoryId: $request->id),
        ]);
    }
}
