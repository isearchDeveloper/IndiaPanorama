<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TouristActivityPage;
use App\Models\TouristActivityPageExperience;
use App\Models\TouristActivityPageFaq;
use App\Models\TouristActivityPageThingToDo;
use App\Models\TouristActivityPageWaterfall;
use Illuminate\Http\Request;

class TouristActivityPageController extends Controller
{
    /**
     * Pages are never created manually here — they're auto-created (one per state, one per city)
     * the moment an Activity referencing that state/city is added, by
     * Admin\TouristActivityController::ensureActivityPages(). This avoids state/city pages
     * existing for places with no real activities yet.
     */
    public function index(Request $r)
    {
        $query = TouristActivityPage::with(['state.region', 'location.state']);

        if ($r->filled('search')) {
            $search = $r->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('state', fn ($s) => $s->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('location', fn ($l) => $l->where('name', 'like', "%{$search}%"));
            });
        }

        $pages = $query->orderByDesc('id')->paginate(20)->withQueryString();

        if ($r->ajax() || $r->boolean('ajax')) {
            return response()->json([
                'html' => view('admin.tourist-activities.pages._table', compact('pages'))->render(),
            ]);
        }

        return view('admin.tourist-activities.pages.index', compact('pages'));
    }

    public function show(TouristActivityPage $tourist_activity_page)
    {
        $tourist_activity_page->load(['state', 'location', 'faqs', 'experiences', 'waterfalls', 'thingsToDo']);

        return response()->json($tourist_activity_page->toArray());
    }

    public function updateSection(Request $r, TouristActivityPage $tourist_activity_page)
    {
        if ($r->section === 'banner_settings') {
            $r->validate([
                'title'             => 'nullable|string|max:255',
                'banner_image'      => 'nullable|string|exists:media,path',
                'banner_image_alt'  => 'nullable|string|max:255',
                'short_description' => 'nullable|string',
                'about_image'       => 'nullable|string|exists:media,path',
                'about_image_alt'   => 'nullable|string|max:255',
                'activities_in_city_sub_title' => 'nullable|string',
            ]);

            $bannerImageChanged = $r->has('banner_image') && $r->input('banner_image') !== $tourist_activity_page->banner_image;
            $aboutImageChanged = $r->has('about_image') && $r->input('about_image') !== $tourist_activity_page->about_image;

            $path = $tourist_activity_page->banner_image;
            if ($bannerImageChanged) {
                $path = $r->input('banner_image');
            }

            $aboutPath = $tourist_activity_page->about_image;
            if ($aboutImageChanged) {
                $aboutPath = $r->input('about_image');
            }

            $tourist_activity_page->update([
                'title'             => $r->title,
                'banner_image'      => $path,
                'banner_image_alt'  => $r->banner_image_alt,
                'short_description' => $r->short_description,
                'about_image'       => $aboutPath,
                'about_image_alt'   => $r->about_image_alt,
                'activities_in_city_sub_title' => $r->activities_in_city_sub_title,
            ]);
        }

        if ($r->section === 'meta') {
            $tourist_activity_page->update($r->only(['meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details']));
        }

        if ($r->section === 'experiences') {
            $r->validate([
                'experiences_title'    => 'nullable|string|max:255',
                'titles.*'             => 'nullable|string|max:255',
                'page_experience_icons.*' => 'nullable|string|exists:media,path',
            ]);

            $tourist_activity_page->update(['experiences_title' => $r->experiences_title]);

            $icons = $r->input('page_experience_icons', []);
            $rows = [];

            foreach ($r->input('titles', []) as $i => $title) {
                if (trim($title) === '') continue;
                $rows[] = ['title' => $title, 'description' => $r->input("descriptions.$i"), 'icon' => $icons[$i] ?? null];
            }

            $tourist_activity_page->experiences()->delete();

            foreach ($rows as $i => $row) {
                TouristActivityPageExperience::create($row + ['page_id' => $tourist_activity_page->id, 'sort_order' => $i]);
            }
        }

        if ($r->section === 'waterfalls') {
            $r->validate([
                'waterfalls_title'  => 'nullable|string|max:255',
                'labels.*'          => 'nullable|string|max:255',
                'waterfall_images.*' => 'nullable|string|exists:media,path',
            ]);

            $tourist_activity_page->update(['waterfalls_title' => $r->waterfalls_title]);

            $images = $r->input('waterfall_images', []);
            $rows = [];

            foreach ($r->input('labels', []) as $i => $label) {
                if (trim($label) === '') continue;
                $rows[] = ['label' => $label, 'image' => $images[$i] ?? null];
            }

            $tourist_activity_page->waterfalls()->delete();

            foreach ($rows as $i => $row) {
                TouristActivityPageWaterfall::create($row + ['page_id' => $tourist_activity_page->id, 'sort_order' => $i]);
            }
        }

        if ($r->section === 'things_to_do') {
            $tourist_activity_page->update(['things_to_do_title' => $r->things_to_do_title]);
            $tourist_activity_page->thingsToDo()->delete();
            foreach ($r->input('things_to_do', []) as $i => $row) {
                if (trim($row['title'] ?? '') === '') continue;
                TouristActivityPageThingToDo::create([
                    'page_id'           => $tourist_activity_page->id,
                    'title'             => $row['title'],
                    'description'       => $row['description'] ?? null,
                    'duration_timing'   => $row['duration_timing'] ?? null,
                    'best_for'          => $row['best_for'] ?? null,
                    'approximate_cost'  => $row['approximate_cost'] ?? null,
                    'sort_order'        => $i,
                ]);
            }
        }

        if ($r->section === 'faqs') {
            $tourist_activity_page->update(['faq_title' => $r->faq_title, 'faq_sub_title' => $r->faq_sub_title]);
            $tourist_activity_page->faqs()->delete();
            foreach ($r->input('faqs', []) as $i => $faq) {
                if (trim($faq['question'] ?? '') === '') continue;
                TouristActivityPageFaq::create([
                    'page_id'    => $tourist_activity_page->id,
                    'question'   => $faq['question'],
                    'answer'     => $faq['answer'] ?? null,
                    'sort_order' => $i,
                ]);
            }
        }

        return response()->json(['status' => true, 'message' => 'Saved successfully.']);
    }

    public function toggleStatus(TouristActivityPage $tourist_activity_page)
    {
        $tourist_activity_page->update(['is_active' => !$tourist_activity_page->is_active]);
        return response()->json(['status' => true, 'is_active' => $tourist_activity_page->is_active]);
    }

    public function toggleFeatured(TouristActivityPage $tourist_activity_page)
    {
        $tourist_activity_page->update(['is_featured' => !$tourist_activity_page->is_featured]);
        return response()->json(['status' => true, 'is_featured' => $tourist_activity_page->is_featured]);
    }

    public function destroy(TouristActivityPage $tourist_activity_page)
    {
        // Note: banner_image/about_image/experiences/waterfalls are intentionally NOT
        // deleted from storage here — they live in the shared Media Library and may
        // still be used elsewhere.
        $tourist_activity_page->faqs()->delete();
        $tourist_activity_page->experiences()->delete();
        $tourist_activity_page->waterfalls()->delete();
        $tourist_activity_page->thingsToDo()->delete();
        $tourist_activity_page->delete();
        return response()->json(['status' => true, 'message' => 'Page deleted.']);
    }
}
