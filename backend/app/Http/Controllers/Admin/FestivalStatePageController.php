<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Festival;
use App\Models\FestivalStatePage;
use App\Models\State;
use Illuminate\Http\Request;

class FestivalStatePageController extends Controller
{
    public function index(Request $r)
    {
        $query = FestivalStatePage::with('state');

        if ($r->filled('search')) {
            $search = $r->search;
            $query->whereHas('state', fn ($s) => $s->where('name', 'like', "%{$search}%"));
        }

        $pages = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        if ($r->ajax() || $r->boolean('ajax')) {
            return response()->json([
                'html' => view('admin.festivals.by-state._table', compact('pages'))->render(),
            ]);
        }

        $usedStateIds = FestivalStatePage::pluck('state_id');
        $availableStates = State::whereNotIn('id', $usedStateIds)->orderBy('name')->get();

        return view('admin.festivals.by-state.index', compact('pages', 'availableStates'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'state_id' => 'required|exists:states,id|unique:festival_state_pages,state_id',
        ]);

        $page = FestivalStatePage::create([
            'state_id'  => $r->state_id,
            'is_active' => true,
        ]);

        ActivityLog::log('created', 'FestivalStatePage', "Created festival state page for state #{$page->state_id}");

        return response()->json(['status' => true, 'message' => 'Page created.', 'page' => $page]);
    }

    /** JSON fetch for the Banner / Featured / Why Visit / FAQ / Meta modals. */
    public function show(FestivalStatePage $festival_state_page)
    {
        $festival_state_page->load(['whyVisits', 'faqs', 'featuredFestival']);

        $stateFestivals = Festival::where('state_id', $festival_state_page->state_id)
            ->orderBy('sort_order')
            ->get(['id', 'name']);

        $data = $festival_state_page->toArray();
        $data['state_festivals'] = $stateFestivals;

        return response()->json($data);
    }

    public function updateSection(Request $r, FestivalStatePage $festival_state_page)
    {
        switch ($r->input('section')) {

            case 'banner_settings':
                $r->validate([
                    'banner_image' => 'nullable|string|exists:media,path',
                ]);

                $imageChanged = $r->has('banner_image') && $r->input('banner_image') !== $festival_state_page->banner_image;

                $data = $r->only(['title', 'banner_text', 'banner_image_alt', 'short_description']);

                if ($imageChanged) {
                    $data['banner_image'] = $r->input('banner_image');
                }

                $festival_state_page->update($data);
                break;

            case 'featured':
                $r->validate([
                    'featured_festival_id' => 'nullable|exists:festivals,id',
                ]);

                if ($r->featured_festival_id) {
                    $belongsToState = Festival::where('id', $r->featured_festival_id)
                        ->where('state_id', $festival_state_page->state_id)
                        ->exists();

                    if (!$belongsToState) {
                        return response()->json(['status' => false, 'message' => 'That festival does not belong to this state.'], 422);
                    }
                }

                $festival_state_page->update(['featured_festival_id' => $r->featured_festival_id]);
                break;

            case 'why_visit':
                $festival_state_page->update($r->only(['why_visit_title', 'why_visit_sub_title']));
                $festival_state_page->whyVisits()->delete();
                foreach ($r->input('why_visits', []) as $i => $row) {
                    if (!empty($row['title'])) {
                        $festival_state_page->whyVisits()->create([
                            'title'       => $row['title'],
                            'description' => $row['description'] ?? null,
                            'sort_order'  => $i,
                        ]);
                    }
                }
                break;

            case 'faqs':
                $festival_state_page->update($r->only(['faq_title', 'faq_sub_title']));
                $festival_state_page->faqs()->delete();
                foreach ($r->input('faqs', []) as $i => $row) {
                    if (!empty($row['question'])) {
                        $festival_state_page->faqs()->create([
                            'question'   => $row['question'],
                            'answer'     => $row['answer'] ?? null,
                            'sort_order' => $i,
                        ]);
                    }
                }
                break;

            case 'meta':
                $festival_state_page->update($r->only(['meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details']));
                break;

            default:
                return response()->json(['status' => false, 'message' => 'Unknown section.'], 422);
        }

        ActivityLog::log('updated', 'FestivalStatePage', "Updated festival state page #{$festival_state_page->id}");

        return response()->json(['status' => true, 'message' => 'Saved successfully.']);
    }

    public function toggleStatus(FestivalStatePage $festival_state_page)
    {
        $festival_state_page->update(['is_active' => !$festival_state_page->is_active]);
        return response()->json(['status' => true, 'is_active' => $festival_state_page->is_active]);
    }
}
