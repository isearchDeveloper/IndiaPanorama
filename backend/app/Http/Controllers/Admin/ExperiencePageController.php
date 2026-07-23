<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExperiencePage;
use App\Models\ExperiencePageFaq;
use Illuminate\Http\Request;

class ExperiencePageController extends Controller
{
    /**
     * "Manage States" — a States tab and a Cities tab (all cities across every state,
     * not scoped to one state) on the same page. Read-only list + edit only — pages are
     * never created manually here (no "Add" button/route); a state's/city's page row is
     * only ever auto-created by Admin\ExperienceController::ensureExperiencePages() the
     * moment an Experience item references that state/city, via firstOrCreate() so it's
     * never duplicated.
     */
    public function index(Request $r)
    {
        if ($r->input('tab') === 'cities') {
            $query = ExperiencePage::whereNotNull('location_id')->with('location.state');

            if ($r->filled('search')) {
                $search = $r->search;
                $query->whereHas('location', fn ($l) => $l->where('name', 'like', "%{$search}%"));
            }

            $cityPages = $query->orderByDesc('id')->paginate(20)->withQueryString();

            if ($r->ajax() || $r->boolean('ajax')) {
                return response()->json([
                    'html' => view('admin.experiences.pages._cities_table', compact('cityPages'))->render(),
                ]);
            }

            return view('admin.experiences.pages.index', ['activeTab' => 'cities', 'cityPages' => $cityPages]);
        }

        $query = ExperiencePage::whereNull('location_id')->with('state.region');

        if ($r->filled('search')) {
            $search = $r->search;
            $query->whereHas('state', fn ($s) => $s->where('name', 'like', "%{$search}%"));
        }

        $pages = $query->orderByDesc('id')->paginate(20)->withQueryString();

        if ($r->ajax() || $r->boolean('ajax')) {
            return response()->json([
                'html' => view('admin.experiences.pages._table', compact('pages'))->render(),
            ]);
        }

        return view('admin.experiences.pages.index', ['activeTab' => 'states', 'pages' => $pages]);
    }

    public function show(ExperiencePage $experience_page)
    {
        $experience_page->load(['state', 'location', 'faqs', 'activities', 'highlights']);
        $data = $experience_page->toArray();

        return response()->json($data);
    }

    public function updateSection(Request $r, ExperiencePage $experience_page)
    {
        if ($r->section === 'banner_settings') {
            $r->validate([
                'title'             => 'nullable|string|max:255',
                'banner_image'      => 'nullable|string|exists:media,path',
                'banner_image_alt'  => 'nullable|string|max:255',
                'short_description' => 'nullable|string',
            ]);

            $imageChanged = $r->has('banner_image') && $r->input('banner_image') !== $experience_page->banner_image;

            $path = $experience_page->banner_image;
            if ($imageChanged) {
                $path = $r->input('banner_image');
            }

            $experience_page->update([
                'title'             => $r->title,
                'banner_image'      => $path,
                'banner_image_alt'  => $r->banner_image_alt,
                'short_description' => $r->short_description,
            ]);
        }

        if ($r->section === 'activities') {
            $experience_page->update(['activities_title' => $r->activities_title]);
            $experience_page->activities()->delete();
            foreach ($r->input('activities', []) as $i => $row) {
                if (trim($row['title'] ?? '') === '') continue;
                \App\Models\ExperiencePageActivity::create([
                    'page_id'          => $experience_page->id,
                    'title'            => $row['title'],
                    'description'      => $row['description'] ?? null,
                    'best_time'        => $row['best_time'] ?? null,
                    'best_for'         => $row['best_for'] ?? null,
                    'approximate_cost' => $row['approximate_cost'] ?? null,
                    'sort_order'       => $i,
                ]);
            }
        }

        if ($r->section === 'highlights') {
            $experience_page->update(['highlights_title' => $r->highlights_title]);
            $experience_page->highlights()->delete();
            foreach ($r->input('highlights', []) as $i => $row) {
                if (trim($row['title'] ?? '') === '') continue;
                \App\Models\ExperiencePageHighlight::create([
                    'page_id'     => $experience_page->id,
                    'title'       => $row['title'],
                    'description' => $row['description'] ?? null,
                    'sort_order'  => $i,
                ]);
            }
        }

        if ($r->section === 'meta') {
            $experience_page->update($r->only(['meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details']));
        }

        if ($r->section === 'faqs') {
            $experience_page->update(['faq_title' => $r->faq_title, 'faq_sub_title' => $r->faq_sub_title]);
            $experience_page->faqs()->delete();
            foreach ($r->input('faqs', []) as $i => $faq) {
                if (trim($faq['question'] ?? '') === '') continue;
                ExperiencePageFaq::create([
                    'page_id'    => $experience_page->id,
                    'question'   => $faq['question'],
                    'answer'     => $faq['answer'] ?? null,
                    'sort_order' => $i,
                ]);
            }
        }

        return response()->json(['status' => true, 'message' => 'Saved successfully.']);
    }

    public function toggleStatus(ExperiencePage $experience_page)
    {
        $experience_page->update(['is_active' => !$experience_page->is_active]);
        return response()->json(['status' => true, 'is_active' => $experience_page->is_active]);
    }

    public function toggleFeatured(ExperiencePage $experience_page)
    {
        $experience_page->update(['is_featured' => !$experience_page->is_featured]);
        return response()->json(['status' => true, 'is_featured' => $experience_page->is_featured]);
    }

    public function destroy(ExperiencePage $experience_page)
    {
        $experience_page->faqs()->delete();
        $experience_page->activities()->delete();
        $experience_page->highlights()->delete();
        $experience_page->delete();

        return response()->json(['status' => true, 'message' => 'Page deleted.']);
    }
}
