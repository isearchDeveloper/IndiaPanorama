<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarRentalAmenity;
use App\Models\CarRentalContent;
use App\Models\CarRentalGalleryImage;
use Illuminate\Http\Request;

class CarRentalContentController extends Controller
{
    public function updateText(Request $r)
    {
        $r->validate([
            'short_description'       => 'nullable|string',
            'checklist_title'         => 'nullable|string|max:255',
            'features_title'          => 'nullable|string|max:255',
            'benefits_title'          => 'nullable|string|max:255',
            'about_title'             => 'nullable|string|max:255',
            'about_description'      => 'nullable|string',
            'popular_locations_title'       => 'nullable|string|max:255',
            'popular_locations_description' => 'nullable|string',
            'road_trip_title'         => 'nullable|string|max:255',
            'road_trip_subtitle'      => 'nullable|string|max:255',
            'amenities_title'         => 'nullable|string|max:255',
            'gallery_title'           => 'nullable|string|max:255',
            'gallery_description'     => 'nullable|string',
        ]);

        $data = $r->only([
            'short_description', 'checklist_title', 'features_title', 'benefits_title',
            'about_title', 'about_description',
            'popular_locations_title', 'popular_locations_description', 'road_trip_title', 'road_trip_subtitle',
            'amenities_title', 'gallery_title', 'gallery_description',
        ]);

        foreach (['short_description', 'about_description', 'popular_locations_description'] as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = strip_figma_paste_junk($data[$field]);
            }
        }

        $content = CarRentalContent::current();
        $content->update($data);

        if ($r->hasAny(['popular_locations_title', 'popular_locations_description'])) {
            $activeTab = 'global_setting';
        } elseif ($r->hasAny(['road_trip_title', 'road_trip_subtitle'])) {
            $activeTab = 'road_trip_list';
        } else {
            $activeTab = 'car_content';
        }

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', $activeTab)
            ->with('success', 'Car rental page content saved.');
    }

    public function updateChecklist(Request $r)
    {
        $content = CarRentalContent::current();
        $content->checklistItems()->delete();

        if ($r->has('items')) {
            foreach ($r->items as $i => $text) {
                if (trim($text) === '') {
                    continue;
                }
                $content->checklistItems()->create([
                    'text'       => $text,
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'car_content')
            ->with('success', 'Checklist saved.');
    }

    public function updateFeatures(Request $r)
    {
        $content = CarRentalContent::current();
        $content->features()->delete();

        if ($r->has('items')) {
            foreach ($r->items as $i => $text) {
                if (trim($text) === '') {
                    continue;
                }
                $content->features()->create(['text' => $text, 'sort_order' => $i]);
            }
        }

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'car_content')
            ->with('success', 'Features saved.');
    }

    public function updateBenefits(Request $r)
    {
        $content = CarRentalContent::current();
        $content->benefits()->delete();

        if ($r->has('items')) {
            foreach ($r->items as $i => $text) {
                if (trim($text) === '') {
                    continue;
                }
                $content->benefits()->create(['text' => $text, 'sort_order' => $i]);
            }
        }

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'car_content')
            ->with('success', 'Benefits saved.');
    }

    public function addGalleryImage(Request $r)
    {
        $r->validate([
            'gallery_images'         => 'nullable|array',
            'gallery_images.*.path'  => 'required|string|exists:media,path',
            'gallery_images.*.alt'   => 'nullable|string|max:255',
            'gallery_title'          => 'nullable|string|max:255',
            'gallery_description'    => 'nullable|string',
        ]);

        $content = CarRentalContent::current();
        $content->update($r->only(['gallery_title', 'gallery_description']));

        $sortOrder = $content->galleryImages()->count();
        $images = [];

        foreach ($r->input('gallery_images', []) as $item) {
            if (empty($item['path'])) continue;
            $image = $content->galleryImages()->create([
                'image'      => $item['path'],
                'image_alt'  => $item['alt'] ?? null,
                'sort_order' => $sortOrder++,
            ]);
            $images[] = $image;
        }

        if ($r->ajax()) {
            return response()->json(['status' => true, 'message' => 'Images added.', 'images' => $images]);
        }

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'car_content')
            ->with('open_gallery_modal', true)
            ->with('success', 'Gallery images added.');
    }

    public function deleteGalleryImage(CarRentalGalleryImage $gallery_image)
    {
        $gallery_image->delete();

        return response()->json(['status' => true, 'message' => 'Gallery image removed.']);
    }

    public function updateWhyChooseStats(Request $r)
    {
        $r->validate([
            'why_choose_title'       => 'nullable|string|max:255',
            'why_choose_description' => 'nullable|string',
            'labels'                 => 'nullable|array',
            'labels.*'               => 'nullable|string|max:255',
            'why_choose_icons.*'     => 'nullable|string|exists:media,path',
        ]);

        $content = CarRentalContent::current();
        $whyChooseData = $r->only(['why_choose_title', 'why_choose_description']);
        if (array_key_exists('why_choose_description', $whyChooseData)) {
            $whyChooseData['why_choose_description'] = strip_figma_paste_junk($whyChooseData['why_choose_description']);
        }
        $content->update($whyChooseData);

        $icons = $r->input('why_choose_icons', []);
        $rows = [];

        foreach ($r->input('labels', []) as $i => $label) {
            if (trim($label) === '') {
                continue;
            }
            $rows[] = ['label' => $label, 'icon' => $icons[$i] ?? null];
        }

        $content->whyChooseStats()->delete();

        foreach ($rows as $i => $row) {
            $content->whyChooseStats()->create([
                'label'      => $row['label'],
                'icon'       => $row['icon'],
                'sort_order' => $i,
            ]);
        }

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'global_setting')
            ->with('success', 'Why Choose Us stats saved.');
    }

    public function addAmenity(Request $r)
    {
        $r->validate([
            'icon'        => 'nullable|string|exists:media,path',
            'icon_alt'    => 'nullable|string|max:255',
            'label'       => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $content = CarRentalContent::current();

        $amenity = $content->amenities()->create([
            'icon'        => $r->input('icon'),
            'icon_alt'    => $r->icon_alt,
            'label'       => $r->label,
            'description' => $r->description,
            'sort_order'  => $content->amenities()->count(),
        ]);

        if ($r->ajax()) {
            return response()->json(['status' => true, 'message' => 'Amenity added.', 'amenity' => $amenity]);
        }

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'car_content')
            ->with('success', 'Amenity added.');
    }

    public function deleteAmenity(CarRentalAmenity $amenity)
    {
        $amenity->delete();

        return response()->json(['status' => true, 'message' => 'Amenity removed.']);
    }
}
