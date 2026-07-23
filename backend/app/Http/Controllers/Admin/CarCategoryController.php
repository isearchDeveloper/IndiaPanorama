<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\CarCategory;

class CarCategoryController extends Controller
{
    /**
     * Show all categories
     */
    public function index(Request $r = null)
    {
        if ($r && $r->ajax()) {
            return response()->json(CarCategory::all());
        }

        return redirect()->route('admin.page-settings.car');
    }

    /**
     * Store (Add) new category
     */
    public function store(Request $r)
    {
        $r->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|exists:media,path',
        ]);

        $slug = Str::slug($r->name);

        if (CarCategory::where('slug', $slug)->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Category already exists.',
            ]);
        }

        $iconPath = $r->input('icon');

        try {
            $category = CarCategory::create([
                'name'     => $r->name,
                'slug'     => $slug,
                'icon'     => $iconPath,
                'icon_alt' => $r->icon_alt,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Rare race: two admins saved the same category name in the same instant
            // and both passed the "not taken yet" check above before either insert committed.
            throw ValidationException::withMessages([
                'name' => 'Category already exists.',
            ]);
        }

        return redirect()->route('admin.page-settings.car')
        ->with('active_tab','category_list')
        ->with('success','Category created');
    }

    /**
     * Show single category
     */
    public function show($id)
    {
        $category = CarCategory::findOrFail($id);
        $data = $category->toArray();
        return response()->json($data);
    }

    /**
     * Update category
     */
    public function update(Request $r, CarCategory $car_category)
    {
        if ($r->has('status')) {
            $car_category->is_active = $r->status;
            $car_category->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status'  => $car_category->is_active
            ]);
        }

        if ($r->has('is_homepage')) {
            $car_category->is_homepage = (bool) $r->is_homepage;
            $car_category->save();

            return response()->json([
                'success'     => true,
                'message'     => 'Homepage visibility updated.',
                'is_homepage' => $car_category->is_homepage,
            ]);
        }
        $r->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|exists:media,path',
        ]);

        $iconChanged = $r->has('icon') && $r->input('icon') !== $car_category->icon;

        $iconPath = $car_category->icon;
        if ($iconChanged) {
            $iconPath = $r->input('icon');
        }

        $car_category->update([
            'name'     => $r->name,
            'icon'     => $iconPath,
            'icon_alt' => $r->icon_alt,
        ]);

        return redirect()->route('admin.page-settings.car')
        ->with('active_tab','category_list')
        ->with('success','Category updated successfully');
    }

    /**
     * Delete category
     */

    public function destroy(CarCategory $car_category)
    {
        // Note: icon is intentionally NOT deleted from storage here — it lives in
        // the shared Media Library and may still be used elsewhere.
        $car_category->delete();

        return response()->json(['success'=>true,'message' => 'Category deleted successfully']);
    }

    public function slugDuplicateCheck(Request $r){ 
        return response()->json([
            'exists' => CarCategory::where('slug', Str::slug($r->name))
            ->where('id', '!=', $r->id)
            ->exists()
        ]);

    }
}
