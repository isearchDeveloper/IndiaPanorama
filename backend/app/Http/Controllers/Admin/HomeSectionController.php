<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Admin — Homepage Section CMS
 *
 * Manages the visibility, heading copy, and per-section settings
 * for every named section on the public homepage.
 *
 * Routes:
 *   GET    admin/home-sections              → index()   (JSON list)
 *   PUT    admin/home-sections/{key}        → update()  (update a section)
 *   PATCH  admin/home-sections/{key}/toggle → toggle()  (flip is_visible)
 *   POST   admin/home-sections/reorder      → reorder() (drag-to-sort)
 */
class HomeSectionController extends Controller
{
    // ── List all sections ─────────────────────────────────────────────

    public function index()
    {
        $sections = HomeSection::ordered()->get();

        return response()->json([
            'success' => true,
            'data'    => $sections,
        ]);
    }

    // ── Update a section ──────────────────────────────────────────────

    public function update(Request $request, string $key)
    {
        $section = HomeSection::where('section_key', $key)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'title'            => 'nullable|string|max:255',
            'subtitle'         => 'nullable|string|max:500',
            'description'      => 'nullable|string',
            'button_text'      => 'nullable|string|max:100',
            'button_url'       => 'nullable|string|max:500',
            'image_alt'        => 'nullable|string|max:255',
            'is_visible'       => 'nullable|boolean',
            'sort_order'       => 'nullable|integer|min:0|max:99',
            'image'            => 'nullable|string|exists:media,path',
            'extra_data'       => 'nullable|json',
            'right_side_text'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $imageChanged = $request->has('image') && $request->input('image') !== $section->image;

        $data = $request->only([
            'title',
            'subtitle',
            'description',
            'button_text',
            'button_url',
            'image_alt',
            'is_visible',
            'sort_order',
        ]);

        // Handle image change — the picker already stored the file in the
        // Media Library; we just persist the chosen path here.
        if ($imageChanged) {
            $data['image'] = $request->input('image');
        }

        // Handle extra_data — merge so other keys are preserved
        $extra = $section->extra_data ?? [];
        if ($request->filled('extra_data')) {
            $extra = array_merge($extra, json_decode($request->extra_data, true));
        }
        if ($request->has('right_side_text')) {
            $extra['right_side_text'] = $request->input('right_side_text');
        }
        if (!empty($extra)) {
            $data['extra_data'] = $extra;
        }

        $section->update($data);
        HomeSection::flushCache();

        return response()->json([
            'success' => true,
            'message' => 'Section updated successfully.',
            'data'    => $section->fresh(),
        ]);
    }

    // ── Toggle visibility ─────────────────────────────────────────────

    public function toggle(string $key)
    {
        $section = HomeSection::where('section_key', $key)->firstOrFail();

        $section->update(['is_visible' => !$section->is_visible]);
        HomeSection::flushCache();

        return response()->json([
            'success'    => true,
            'is_visible' => $section->is_visible,
            'message'    => 'Section ' . ($section->is_visible ? 'shown' : 'hidden') . ' on homepage.',
        ]);
    }

    // ── Bulk toggle customized packages ──────────────────────────────

    public function bulkCustomized(Request $request)
    {
        $request->validate([
            'ids'   => 'present|array',
            'ids.*' => 'integer|min:1',
        ]);

        $ids = array_map('intval', $request->input('ids', []));

        // Persist as extra_data.package_ids on the customized_tours section row.
        // This keeps the data in the CMS layer; Package rows are not touched.
        $section = HomeSection::where('section_key', 'customized_tours')->firstOrFail();

        $extra             = $section->extra_data ?? [];
        $extra['package_ids'] = $ids;

        $section->update(['extra_data' => $extra]);
        HomeSection::flushCache();

        return response()->json([
            'success' => true,
            'message' => 'Customized packages updated (' . count($ids) . ' selected).',
        ]);
    }

    // ── Delete section image ──────────────────────────────────────────

    public function deleteImage(string $key)
    {
        $section = HomeSection::where('section_key', $key)->firstOrFail();

        // Note: the image file is intentionally NOT deleted here — it now lives
        // in the shared Media Library and may still be referenced elsewhere.
        // Delete it from the Media Library directly (admin.media.destroy) if it's
        // truly no longer needed anywhere.
        if ($section->image) {
            $section->update(['image' => null, 'image_alt' => null]);
            HomeSection::flushCache();
        }

        return response()->json([
            'success' => true,
            'message' => 'Banner image removed.',
        ]);
    }

    // ── Reorder sections ──────────────────────────────────────────────

    public function reorder(Request $request)
    {
        $request->validate([
            'order'              => 'required|array|min:1',
            'order.*.key'        => 'required|string',
            'order.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->order as $item) {
            HomeSection::where('section_key', $item['key'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        HomeSection::flushCache();

        return response()->json([
            'success' => true,
            'message' => 'Section order updated.',
        ]);
    }
}
