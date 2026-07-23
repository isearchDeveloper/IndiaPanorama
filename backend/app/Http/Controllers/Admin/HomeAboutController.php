<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeAboutFeature;
use Illuminate\Http\Request;

class HomeAboutController extends Controller
{
    public function featuresIndex()
    {
        return response()->json([
            'success' => true,
            'data'    => HomeAboutFeature::ordered()->get(),
        ]);
    }

    public function featuresStore(Request $request)
    {
        $data = $request->validate([
            'text'                => 'required|string|max:255',
            'icon_class'          => 'nullable|string|max:100',
            'feature_description' => 'nullable|string|max:2000',
            'sort_order'          => 'nullable|integer|min:0|max:99',
        ]);

        $feature = HomeAboutFeature::create([
            'text'                => $data['text'],
            'icon_class'          => $data['icon_class'] ?? 'fas fa-check-circle',
            'feature_description' => $data['feature_description'] ?? null,
            'sort_order'          => $data['sort_order'] ?? ((HomeAboutFeature::max('sort_order') ?? 0) + 1),
            'is_active'           => true,
        ]);

        return response()->json(['success' => true, 'data' => $feature, 'message' => 'Feature added.']);
    }

    public function featuresUpdate(Request $request, HomeAboutFeature $feature)
    {
        $data = $request->validate([
            'text'                => 'required|string|max:255',
            'icon_class'          => 'nullable|string|max:100',
            'feature_description' => 'nullable|string|max:2000',
            'sort_order'          => 'nullable|integer|min:0|max:99',
            'is_active'           => 'nullable|boolean',
        ]);

        $feature->update($data);

        return response()->json(['success' => true, 'data' => $feature, 'message' => 'Feature updated.']);
    }

    public function featuresToggle(HomeAboutFeature $feature)
    {
        $feature->update(['is_active' => !$feature->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $feature->is_active,
            'message'   => 'Feature ' . ($feature->is_active ? 'enabled' : 'disabled') . '.',
        ]);
    }

    public function featuresDestroy(HomeAboutFeature $feature)
    {
        $feature->delete();
        return response()->json(['success' => true, 'message' => 'Feature deleted.']);
    }

    public function featuresReorder(Request $request)
    {
        $request->validate([
            'order'              => 'required|array',
            'order.*.id'         => 'required|integer',
            'order.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->order as $item) {
            HomeAboutFeature::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Order saved.']);
    }
}
