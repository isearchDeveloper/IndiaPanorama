<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.page-settings.home');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'subtitle'         => 'nullable|string|max:255',
            'button_text'      => 'nullable|string|max:100',
            'url'              => 'nullable|string|max:500',
            'banner_image'     => 'required|string|exists:media,path',
            'banner_image_alt' => 'nullable|string|max:255',
        ]);

        $data = [
            'title'            => $request->title,
            'subtitle'         => $request->subtitle,
            'button_text'      => $request->button_text,
            'url'              => $request->url,
            'banner_image'     => $request->input('banner_image'),
            'banner_image_alt' => $request->banner_image_alt,
        ];

        // Static banner handling (used by other sections, not home CMS slider)
        if ($request->exists('is_static')) {
            Banner::where('is_static', 1)->update(['is_active' => 0]);
            $data['is_static'] = 1;
        }

        $banner = Banner::create($data);

        return response()->json(['success' => true]);
    }

    public function edit(Banner $banner)
    {
        $data = $banner->toArray();
        $data['banner_image_path'] = $banner->banner_image;
        $data['banner_image'] = $banner->banner_image ? storage_link($banner->banner_image) : null;

        return response()->json($data);
    }

    public function update(Request $request, Banner $banner)
    {
        // ── Status-only toggle (from the row toggle in the table) ────────
        if ($request->exists('status')) {
            if ($banner->is_static) {
                if ($request->status) {
                    Banner::where('is_static', 1)->update(['is_active' => 0]);
                    $banner->is_active = 1;
                } else {
                    $activeStatic = Banner::where('is_static', 1)
                        ->where('is_active', 1)
                        ->where('id', '!=', $banner->id)
                        ->exists();

                    if (!$activeStatic) {
                        $banner->is_active = 1;
                        return response()->json([
                            'status'  => 'error',
                            'message' => 'At least one banner needs to be active.',
                            'data'    => $banner,
                        ]);
                    }

                    $banner->is_active = 0;
                }
            } else {
                $banner->is_active = $request->boolean('status') ? 1 : 0;
            }

            $banner->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Banner status updated.',
                'data'    => $banner,
            ]);
        }

        // ── Full update ──────────────────────────────────────────────────
        $request->validate([
            'title'            => 'required|string|max:255',
            'subtitle'         => 'nullable|string|max:255',
            'button_text'      => 'nullable|string|max:100',
            'url'              => 'nullable|string|max:500',
            'banner_image'     => 'nullable|string|exists:media,path',
            'banner_image_alt' => 'nullable|string|max:255',
        ]);

        $data = [
            'title'            => $request->title,
            'subtitle'         => $request->subtitle,
            'button_text'      => $request->button_text,
            'url'              => $request->url,
            'banner_image_alt' => $request->banner_image_alt,
        ];

        if ($request->exists('is_static')) {
            $data['is_static'] = 1;
        }

        if ($request->has('banner_image')) {
            $data['banner_image'] = $request->input('banner_image');
        }

        $banner->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Banner updated successfully.',
            'data'    => $banner,
        ]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:banners,id',
        ]);

        foreach ($request->ids as $index => $id) {
            Banner::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Banner $banner)
    {
        // Note: the banner's image file is intentionally NOT deleted here — it now
        // lives in the shared Media Library and may still be referenced elsewhere.
        // Delete it from the Media Library directly (admin.media.destroy) if it's
        // truly no longer needed anywhere.
        $banner->delete();

        return response()->json(['success' => true]);
    }
}
