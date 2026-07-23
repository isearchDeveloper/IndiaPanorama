<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarGalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarDetailController extends Controller
{
    public function addGalleryImage(Request $r, Car $car)
    {
        $r->validate([
            'image'               => 'nullable|string|exists:media,path',
            'image_alt'           => 'nullable|string|max:255',
            'gallery_title'       => 'nullable|string|max:255',
            'gallery_description' => 'nullable|string',
        ]);

        $car->update($r->only(['gallery_title', 'gallery_description']));

        $image = null;
        if ($r->filled('image')) {
            $image = $car->galleryImages()->create([
                'image'      => $r->input('image'),
                'image_alt'  => $r->image_alt,
                'sort_order' => $car->galleryImages()->count(),
            ]);
        }

        if ($r->ajax()) {
            return response()->json(['status' => true, 'message' => 'Saved.', 'image' => $image]);
        }

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'car_list')
            ->with('success', 'Gallery image added.');
    }

    public function deleteGalleryImage(CarGalleryImage $gallery_image)
    {
        $gallery_image->delete();

        return response()->json(['status' => true, 'message' => 'Gallery image removed.']);
    }

    public function updateHighlightTags(Request $r, Car $car)
    {
        $r->validate([
            'items'             => 'nullable|array',
            'items.*'           => 'nullable|string|max:255',
            'highlight_icons.*' => 'nullable|string|exists:media,path',
        ]);

        $icons = $r->input('highlight_icons', []);
        $rows = [];

        foreach ($r->input('items', []) as $i => $text) {
            if (trim($text) === '') {
                continue;
            }
            $rows[] = ['text' => $text, 'icon' => $icons[$i] ?? null];
        }

        $car->highlightTags()->delete();

        foreach ($rows as $i => $row) {
            $car->highlightTags()->create([
                'text'       => $row['text'],
                'icon'       => $row['icon'],
                'sort_order' => $i,
            ]);
        }

        if ($r->ajax()) {
            return response()->json(['status' => true, 'message' => 'Highlight tags saved.']);
        }

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'car_list')
            ->with('success', 'Highlight tags saved.');
    }

    public function settings(Request $r)
    {
        $car = Car::with(['galleryImages', 'highlightTags'])->where('id', $r->id)->first();
        if ($r->ajax()) {
            return response()->json(['status' => 'success', 'car' => $car->toArray()]);
        }
    }

    public function amenities(Request $r)
    {
        $car = Car::with('amenities')->where('id', $r->id)->first();
        if ($r->ajax()) {
            return response()->json(['status' => 'success', 'car' => $car->toArray()]);
        }
    }

    public function updateAmenities(Request $r, Car $car)
    {
        $r->validate([
            'amenities.*.label'       => 'nullable|string|max:255',
            'amenities.*.description' => 'nullable|string|max:255',
            'amenities.*.icon'        => 'nullable|string|exists:media,path',
        ]);

        $rows = [];

        foreach ($r->input('amenities', []) as $i => $obj) {
            if (trim($obj['label'] ?? '') === '') {
                continue;
            }
            $rows[] = ['label' => $obj['label'], 'description' => $obj['description'] ?? null, 'icon' => $obj['icon'] ?? null];
        }

        $car->amenities()->delete();

        foreach ($rows as $i => $row) {
            $car->amenities()->create([
                'label'       => $row['label'],
                'description' => $row['description'],
                'icon'        => $row['icon'],
                'sort_order'  => $i,
            ]);
        }

        if ($r->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Features & Amenities saved.']);
        }

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'car_list')
            ->with('success', 'Features & Amenities saved.');
    }
}
