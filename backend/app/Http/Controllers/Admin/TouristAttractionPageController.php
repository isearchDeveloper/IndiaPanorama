<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\State;
use App\Models\TouristAttractionPage;
use App\Models\TouristAttractionPageBestTime;
use App\Models\TouristAttractionPageFaq;
use Illuminate\Http\Request;

class TouristAttractionPageController extends Controller
{
    public function index(Request $r)
    {
        $query = TouristAttractionPage::with(['state.region', 'location.state']);

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
                'html' => view('admin.tourist-attractions.pages._table', compact('pages'))->render(),
            ]);
        }

        $usedStateIds = TouristAttractionPage::whereNotNull('state_id')->pluck('state_id');
        $usedLocationIds = TouristAttractionPage::whereNotNull('location_id')->pluck('location_id');

        $availableStates = State::whereNotIn('id', $usedStateIds)->orderBy('name')->get();
        $availableLocations = Location::with('state')->whereNotIn('id', $usedLocationIds)->orderBy('name')->get();

        return view('admin.tourist-attractions.pages.index', compact('pages', 'availableStates', 'availableLocations'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'subject_type' => 'required|in:state,location',
            'state_id'     => 'required_if:subject_type,state|nullable|exists:states,id',
            'location_id'  => 'required_if:subject_type,location|nullable|exists:locations,id',
        ]);

        $data = ['is_active' => true];
        if ($r->subject_type === 'state') {
            $data['state_id'] = $r->state_id;
        } else {
            $data['location_id'] = $r->location_id;
        }

        $page = TouristAttractionPage::create($data);

        return response()->json(['status' => true, 'message' => 'Page created.', 'page' => $page]);
    }

    public function show(TouristAttractionPage $tourist_attraction_page)
    {
        $tourist_attraction_page->load(['state', 'location', 'bestTimes', 'faqs']);
        $data = $tourist_attraction_page->toArray();
        return response()->json($data);
    }

    public function updateSection(Request $r, TouristAttractionPage $tourist_attraction_page)
    {
        if ($r->section === 'banner_settings') {
            $r->validate([
                'title'             => 'nullable|string|max:255',
                'banner_image'      => 'nullable|string|exists:media,path',
                'banner_image_alt'  => 'nullable|string|max:255',
                'short_description' => 'nullable|string',
            ]);

            $imageChanged = $r->has('banner_image') && $r->input('banner_image') !== $tourist_attraction_page->banner_image;

            $path = $tourist_attraction_page->banner_image;
            if ($imageChanged) {
                $path = $r->input('banner_image');
            }

            $tourist_attraction_page->update([
                'title'             => $r->title,
                'banner_image'      => $path,
                'banner_image_alt'  => $r->banner_image_alt,
                'short_description' => $r->short_description,
            ]);
        }

        if ($r->section === 'meta') {
            $tourist_attraction_page->update($r->only(['meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details']));
        }

        if ($r->section === 'best_times') {
            $tourist_attraction_page->bestTimes()->delete();
            foreach ($r->input('best_times', []) as $i => $bt) {
                if (trim($bt['period'] ?? '') === '') continue;
                TouristAttractionPageBestTime::create([
                    'page_id'     => $tourist_attraction_page->id,
                    'period'      => $bt['period'],
                    'description' => $bt['description'] ?? null,
                    'sort_order'  => $i,
                ]);
            }
        }

        if ($r->section === 'faqs') {
            $tourist_attraction_page->update(['faq_title' => $r->faq_title, 'faq_sub_title' => $r->faq_sub_title]);
            $tourist_attraction_page->faqs()->delete();
            foreach ($r->input('faqs', []) as $i => $faq) {
                if (trim($faq['question'] ?? '') === '') continue;
                TouristAttractionPageFaq::create([
                    'page_id'    => $tourist_attraction_page->id,
                    'question'   => $faq['question'],
                    'answer'     => $faq['answer'] ?? null,
                    'sort_order' => $i,
                ]);
            }
        }

        return response()->json(['status' => true, 'message' => 'Saved successfully.']);
    }

    public function toggleStatus(TouristAttractionPage $tourist_attraction_page)
    {
        $tourist_attraction_page->update(['is_active' => !$tourist_attraction_page->is_active]);
        return response()->json(['status' => true, 'is_active' => $tourist_attraction_page->is_active]);
    }

    public function toggleFeatured(TouristAttractionPage $tourist_attraction_page)
    {
        $tourist_attraction_page->update(['is_featured' => !$tourist_attraction_page->is_featured]);
        return response()->json(['status' => true, 'is_featured' => $tourist_attraction_page->is_featured]);
    }

    public function togglePopular(TouristAttractionPage $tourist_attraction_page)
    {
        $tourist_attraction_page->update(['is_popular' => !$tourist_attraction_page->is_popular]);
        return response()->json(['status' => true, 'is_popular' => $tourist_attraction_page->is_popular]);
    }

    public function destroy(TouristAttractionPage $tourist_attraction_page)
    {
        // Note: banner_image is intentionally NOT deleted from storage here —
        // it lives in the shared Media Library and may still be used elsewhere.
        $tourist_attraction_page->bestTimes()->delete();
        $tourist_attraction_page->faqs()->delete();
        $tourist_attraction_page->delete();
        return response()->json(['status' => true, 'message' => 'Page deleted.']);
    }
}
