<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\CmsPageMetaData;
use Illuminate\Http\Request;

class CmsPageController extends Controller
{
    private function licenseForJson(?\App\Models\ImageLicense $license): ?array
    {
        if (!$license) return null;

        return [
            'source_of_image'      => $license->source_of_image,
            'download_date'        => $license->download_date?->format('Y-m-d'),
            'account_id'           => $license->account_id,
            'license_key'          => $license->license_key,
            'license_key_file_url' => $license->license_key_file ? storage_link($license->license_key_file) : null,
        ];
    }

    public function index(Request $r)
    {
        $pages = CmsPage::orderBy(list_config()['order_by'], list_config()['direction'])
            ->paginate(25);
        return view('admin.cms.index', compact('pages'));
    }

    public function showMeta(CmsPage $cms_page)
    {
        $cms_page->load('meta');
        return response()->json($cms_page);
    }

    public function create()
    {
        return view('admin.cms.create');
    }

    public function create_page(Request $r)
    {
        $r->validate([
            'title'        => 'required',
            'slug'         => 'required',
            'sub_title'    => 'nullable',
            'description'  => 'nullable',
            'banner_image' => 'nullable|string|exists:media,path',
        ]);

        $path = null;
        if ($r->filled('banner_image')) {
            $path = $r->input('banner_image');
        }

        $cmsPage = CmsPage::create([
            'title'            => $r->title,
            'slug'             => $r->slug,
            'sub_title'        => $r->sub_title,
            'description'      => $r->description,
            'banner_image'     => $path,
            'banner_image_alt' => $r->banner_image_alt,
        ]);

        return redirect()->route('admin.cms-page.index')->with('success', 'Page created successfully');
    }

    public function edit(CmsPage $cms_page)
    {
        $page = $cms_page;
        return view('admin.cms.edit', compact('page'));
    }

    public function show(CmsPage $cms_page)
    {
        return view('admin.cms.show', compact('cms_page'));
    }

    public function update(Request $r, CmsPage $cms_page)
    {
        if ($r->exists('meta_setting')) {
            $r->validate([
                'meta_title'       => 'nullable|string',
                'meta_description' => 'nullable|string',
                'meta_keywords'    => 'nullable|string',
                'h1_heading'       => 'nullable|string',
                'meta_details'     => 'nullable|string',
            ]);

            if ($cms_page->meta) {
                $cms_page->meta->update([
                    'meta_title'       => $r->meta_title,
                    'meta_description' => $r->meta_description,
                    'meta_keywords'    => $r->meta_keywords,
                    'h1_heading'       => $r->h1_heading,
                    'meta_details'     => $r->meta_details,
                ]);
            } else {
                CmsPageMetaData::create([
                    'page_id'          => $cms_page->id,
                    'meta_title'       => $r->meta_title,
                    'meta_description' => $r->meta_description,
                    'meta_keywords'    => $r->meta_keywords,
                    'h1_heading'       => $r->h1_heading,
                    'meta_details'     => $r->meta_details,
                ]);
            }
        } else {
            $r->validate([
                'title'        => 'required',
                'sub_title'    => 'nullable',
                'description'  => 'nullable',
                'banner_image' => 'nullable|string|exists:media,path',
            ]);

            $imageChanged = $r->has('banner_image') && $r->input('banner_image') !== $cms_page->banner_image;

            $path = $cms_page->banner_image ?? '';

            if ($imageChanged) {
                $path = $r->input('banner_image');
            }

            $cms_page->update([
                'title'            => $r->title,
                'sub_title'        => $r->sub_title,
                'description'      => $r->description,
                'banner_image'     => $path,
                'banner_image_alt' => $r->banner_image_alt,
            ]);
        }

        return redirect()->route('admin.cms-page.index')->with('success', 'Page updated successfully');
    }

    public function uploadImage(Request $request)
    {
        try {
            $request->validate(['file' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048']);
            $img = $request->file('file');
            if ($img->isValid()) {
                $filename = unique_filename($img);
                $path     = $img->storeAs('cms_page', $filename, config('filesystems.upload_disk'));
                return response()->json(['location' => storage_link($path)]);
            }
            return response()->json(['error' => 'Invalid file upload.'], 400);
        } catch (\Throwable $e) {
            \Log::error('TinyMCE image upload failed: ' . $e->getMessage());
            return response()->json(['error' => 'Upload failed. Please try again.'], 500);
        }
    }
}
