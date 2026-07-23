<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Festival;
use App\Models\FestivalStatePage;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FestivalController extends Controller
{
    /** Ensure the state has a "Festivals of {State}" page row — created blank if missing, left untouched if it already exists. */
    private function ensureStatePage(int $stateId): void
    {
        $page = FestivalStatePage::withTrashed()->firstOrCreate(
            ['state_id' => $stateId],
            ['is_active' => true]
        );
        if ($page->trashed()) $page->restore();
    }

    /** Build a unique slug from the name, appending -2, -3, ... on collision. */
    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;

        while (Festival::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');

        $allCount      = Festival::count();
        $activeCount   = Festival::where('is_active', 1)->count();
        $inactiveCount = Festival::where('is_active', 0)->count();

        $festivals = Festival::with('state')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($status === 'active', fn ($q) => $q->where('is_active', 1))
            ->when($status === 'inactive', fn ($q) => $q->where('is_active', 0))
            ->orderBy('sort_order')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        if ($request->ajax() || $request->boolean('ajax')) {
            return response()->json([
                'html' => view('admin.festivals._table', compact('festivals'))->render(),
            ]);
        }

        $states = State::active()->orderBy('name')->get(['id', 'name']);

        return view('admin.festivals.index', compact('festivals', 'states', 'allCount', 'activeCount', 'inactiveCount', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'state_id'           => 'required|exists:states,id',
            'name'               => 'required|string|max:255',
            'image'              => 'required|string|exists:media,path',
            'image_alt'          => 'nullable|string|max:255',
            'banner_subtitle'    => 'nullable|string|max:255',
            'banner_description' => 'nullable|string|max:255',
            'month'              => 'nullable|integer|min:1|max:12',
            'month_text'         => 'nullable|string|max:255',
            'location_text'      => 'nullable|string|max:255',
            'duration_text'      => 'nullable|string|max:255',
            'short_description'  => 'nullable|string',
            'intro_image'        => 'nullable|string|exists:media,path',
            'intro_image_alt'    => 'nullable|string|max:255',
        ]);

        $path = $request->input('image');

        $introPath = null;
        if ($request->filled('intro_image')) {
            $introPath = $request->input('intro_image');
        }

        $maxOrder = Festival::max('sort_order') ?? -1;

        try {
            $festival = Festival::create([
                'state_id'          => $request->state_id,
                'name'              => $request->name,
                'slug'              => $this->uniqueSlug($request->name),
                'image'             => $path,
                'image_alt'         => $request->input('image_alt', ''),
                'banner_subtitle'   => $request->banner_subtitle,
                'banner_description' => $request->banner_description,
                'sort_order'        => $maxOrder + 1,
                'month'             => $request->month,
                'month_text'        => $request->month_text,
                'location_text'     => $request->location_text,
                'duration_text'     => $request->duration_text,
                'short_description' => $request->short_description,
                'intro_image'       => $introPath,
                'intro_image_alt'   => $request->intro_image_alt,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Rare race: two admins saved the same festival name in the same instant and
            // both passed the "slug not taken yet" check before either insert committed.
            return response()->json(['status' => false, 'message' => 'That festival name just got taken by another save — please try again.'], 409);
        }

        $this->ensureStatePage($festival->state_id);

        ActivityLog::log('created', 'Festival', "Created festival: {$festival->name}");

        return response()->json(['status' => true, 'message' => 'Festival added.', 'festival' => $festival]);
    }

    /** JSON fetch for the Edit modal. */
    public function show(Festival $festival)
    {
        $data = $festival->toArray();
        return response()->json($data);
    }

    public function update(Request $request, Festival $festival)
    {
        $request->validate([
            'state_id'           => 'required|exists:states,id',
            'name'               => 'required|string|max:255',
            'image'              => 'nullable|string|exists:media,path',
            'image_alt'          => 'nullable|string|max:255',
            'banner_subtitle'    => 'nullable|string|max:255',
            'banner_description' => 'nullable|string|max:255',
            'month'              => 'nullable|integer|min:1|max:12',
            'month_text'         => 'nullable|string|max:255',
            'location_text'      => 'nullable|string|max:255',
            'duration_text'      => 'nullable|string|max:255',
            'short_description'  => 'nullable|string',
            'intro_image'        => 'nullable|string|exists:media,path',
            'intro_image_alt'    => 'nullable|string|max:255',
        ]);

        $imageChanged = $request->has('image') && $request->input('image') !== $festival->image;
        $introImageChanged = $request->has('intro_image') && $request->input('intro_image') !== $festival->intro_image;

        $data = $request->only(['state_id', 'name', 'image_alt', 'banner_subtitle', 'banner_description', 'month', 'month_text', 'location_text', 'duration_text', 'short_description', 'intro_image_alt']);

        if ($imageChanged) {
            $data['image'] = $request->input('image');
        }

        if ($introImageChanged) {
            $data['intro_image'] = $request->input('intro_image');
        }

        $festival->update($data);

        $this->ensureStatePage($festival->state_id);

        ActivityLog::log('updated', 'Festival', "Updated festival: {$festival->name}");

        return response()->json(['status' => true, 'message' => 'Festival updated.', 'festival' => $festival]);
    }

    public function toggleStatus(Festival $festival)
    {
        $festival->update(['is_active' => !$festival->is_active]);
        return response()->json(['status' => true, 'is_active' => $festival->is_active, 'message' => 'Status updated.']);
    }

    public function destroy(Festival $festival)
    {
        // Highlight/place/key-experience images are not deleted from storage —
        // they're shared Media Library assets that may still be referenced elsewhere.
        $name = $festival->name;

        \Illuminate\Support\Facades\DB::transaction(function () use ($festival) {
            $festival->stats()->delete();
            $festival->howToReach()->delete();
            $festival->whyVisits()->delete();
            $festival->faqs()->delete();
            $festival->meta()->delete();
            $festival->highlights()->delete();
            $festival->places()->delete();
            $festival->keyExperiences()->delete();
            $festival->delete();
        });

        ActivityLog::log('deleted', 'Festival', "Deleted festival: {$name}");

        return response()->json(['status' => true, 'message' => 'Festival deleted.']);
    }

    /** JSON fetch for the Setting / Key Experience / How to Reach / Why Visit / FAQs / Meta modals. */
    public function detail(Festival $festival)
    {
        $festival->load(['keyExperiences', 'howToReach', 'whyVisits', 'faqs', 'meta', 'stats', 'highlights', 'places']);

        return response()->json($festival->toArray());
    }

    /** "Setting" modal — long description shown on the detail page. */
    public function updateSetting(Request $request, Festival $festival)
    {
        $request->validate([
            'long_description' => 'nullable|string',
            'packages_title'   => 'nullable|string|max:255',
        ]);

        $festival->update($request->only(['long_description', 'packages_title']));

        return response()->json(['status' => true, 'message' => 'Setting saved.']);
    }

    /** "Key Experience" modal — section title + repeatable icon/label rows. */
    public function updateKeyExperiences(Request $request, Festival $festival)
    {
        $request->validate([
            'key_experience_title'   => 'nullable|string|max:255',
            'labels.*'               => 'nullable|string|max:255',
            'key_experience_icons.*' => 'nullable|string|exists:media,path',
        ]);

        $festival->update($request->only(['key_experience_title']));

        $icons = $request->input('key_experience_icons', []);
        $rows = [];

        foreach ($request->input('labels', []) as $i => $label) {
            if (trim($label) === '') {
                continue;
            }
            $rows[] = ['label' => $label, 'icon' => $icons[$i] ?? null];
        }

        $festival->keyExperiences()->delete();

        foreach ($rows as $i => $row) {
            $festival->keyExperiences()->create([
                'label'      => $row['label'],
                'icon'       => $row['icon'],
                'sort_order' => $i,
            ]);
        }

        return response()->json(['status' => true, 'message' => 'Key Experiences saved.']);
    }

    /** "Quick Stats" modal — repeatable value/label rows (e.g. "10" / "Days of Celebration"). */
    public function updateStats(Request $request, Festival $festival)
    {
        $request->validate([
            'values.*' => 'nullable|string|max:255',
            'labels.*' => 'nullable|string|max:255',
        ]);

        $festival->stats()->delete();

        $values = $request->input('values', []);
        $labels = $request->input('labels', []);

        foreach ($values as $i => $value) {
            if (trim($value) === '') {
                continue;
            }
            $festival->stats()->create([
                'value'      => $value,
                'label'      => $labels[$i] ?? null,
                'sort_order' => $i,
            ]);
        }

        return response()->json(['status' => true, 'message' => 'Stats saved.']);
    }

    /** "Festival Highlights" modal — section title + repeatable image/label rows. */
    public function updateHighlights(Request $request, Festival $festival)
    {
        $request->validate([
            'highlights_title'  => 'nullable|string|max:255',
            'labels.*'          => 'nullable|string|max:255',
            'image_alts.*'      => 'nullable|string|max:255',
            'highlight_images.*' => 'nullable|string|exists:media,path',
        ]);

        $festival->update($request->only(['highlights_title']));

        $images = $request->input('highlight_images', []);
        $rows = [];

        foreach ($request->input('labels', []) as $i => $label) {
            if (trim($label) === '') {
                continue;
            }
            $rows[] = ['label' => $label, 'image' => $images[$i] ?? null, 'image_alt' => $request->input("image_alts.$i")];
        }

        $festival->highlights()->delete();

        foreach ($rows as $i => $row) {
            $festival->highlights()->create([
                'label'      => $row['label'],
                'image'      => $row['image'],
                'image_alt'  => $row['image_alt'],
                'sort_order' => $i,
            ]);
        }

        return response()->json(['status' => true, 'message' => 'Festival Highlights saved.']);
    }

    /** "Popular Places" modal — section title + repeatable image/name rows. */
    public function updatePlaces(Request $request, Festival $festival)
    {
        $request->validate([
            'places_title'   => 'nullable|string|max:255',
            'names.*'        => 'nullable|string|max:255',
            'image_alts.*'   => 'nullable|string|max:255',
            'place_images.*' => 'nullable|string|exists:media,path',
        ]);

        $festival->update($request->only(['places_title']));

        $images = $request->input('place_images', []);
        $rows = [];

        foreach ($request->input('names', []) as $i => $name) {
            if (trim($name) === '') {
                continue;
            }
            $rows[] = ['name' => $name, 'image' => $images[$i] ?? null, 'image_alt' => $request->input("image_alts.$i")];
        }

        $festival->places()->delete();

        foreach ($rows as $i => $row) {
            $festival->places()->create([
                'name'       => $row['name'],
                'image'      => $row['image'],
                'image_alt'  => $row['image_alt'],
                'sort_order' => $i,
            ]);
        }

        return response()->json(['status' => true, 'message' => 'Popular Places saved.']);
    }

    /** "How to Reach" modal — repeatable mode/description rows. */
    public function updateHowToReach(Request $request, Festival $festival)
    {
        $request->validate([
            'modes.*'        => 'nullable|string|max:255',
            'descriptions.*' => 'nullable|string',
        ]);

        $festival->howToReach()->delete();

        $modes = $request->input('modes', []);
        $descriptions = $request->input('descriptions', []);

        foreach ($modes as $i => $mode) {
            if (trim($mode) === '') {
                continue;
            }
            $festival->howToReach()->create([
                'mode'        => $mode,
                'description' => $descriptions[$i] ?? null,
                'sort_order'  => $i,
            ]);
        }

        return response()->json(['status' => true, 'message' => 'How to Reach saved.']);
    }

    /** "Why Visit" modal — section title + repeatable title/description rows. */
    public function updateWhyVisits(Request $request, Festival $festival)
    {
        $request->validate([
            'why_visit_title' => 'nullable|string|max:255',
            'titles.*'         => 'nullable|string|max:255',
            'descriptions.*'   => 'nullable|string',
        ]);

        $festival->update($request->only(['why_visit_title']));

        $festival->whyVisits()->delete();

        $titles = $request->input('titles', []);
        $descriptions = $request->input('descriptions', []);

        foreach ($titles as $i => $title) {
            if (trim($title) === '') {
                continue;
            }
            $festival->whyVisits()->create([
                'title'       => $title,
                'description' => $descriptions[$i] ?? null,
                'sort_order'  => $i,
            ]);
        }

        return response()->json(['status' => true, 'message' => 'Why Visit saved.']);
    }

    /** "FAQs" modal — section title + repeatable question/answer rows. */
    public function updateFaqs(Request $request, Festival $festival)
    {
        $request->validate([
            'faq_title'     => 'nullable|string|max:255',
            'questions.*'   => 'nullable|string|max:255',
            'answers.*'     => 'nullable|string',
        ]);

        $festival->update($request->only(['faq_title']));

        $festival->faqs()->delete();

        $questions = $request->input('questions', []);
        $answers = $request->input('answers', []);

        foreach ($questions as $i => $question) {
            if (trim($question) === '') {
                continue;
            }
            $festival->faqs()->create([
                'question'   => $question,
                'answer'     => $answers[$i] ?? null,
                'sort_order' => $i,
            ]);
        }

        return response()->json(['status' => true, 'message' => 'FAQs saved.']);
    }

    /** "SEO Meta" modal. */
    public function updateMeta(Request $request, Festival $festival)
    {
        $request->validate([
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords'    => 'nullable|string',
            'h1_heading'       => 'nullable|string|max:255',
            'meta_details'     => 'nullable|string',
        ]);

        $data = $request->only(['meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details']);

        if ($festival->meta) {
            $festival->meta->update($data);
        } else {
            $festival->meta()->create($data);
        }

        return response()->json(['status' => true, 'message' => 'Meta saved.']);
    }
}
