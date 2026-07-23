<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Award;
use App\Models\CmsPage;
use App\Models\CmsPageMetaData;
use App\Models\Department;
use App\Models\Team;
use App\Services\CmsPageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsBuilderController extends Controller
{
    public function __construct(private CmsPageService $service) {}

    // ── List ──────────────────────────────────────────────────────────────

    public function index()
    {
        $pages = CmsPage::withCount('sections')
            ->whereNotNull('template')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.cms-builder.index', compact('pages'));
    }

    // ── Create ────────────────────────────────────────────────────────────

    public function create()
    {
        // Restore sections if validation redirected back
        $existingSections = [];
        if ($old = old('sections_json')) {
            $existingSections = json_decode($old, true) ?? [];
        }

        return view('admin.cms-builder.edit', [
            'page'            => new CmsPage(),
            'existingSections'=> $existingSections,
            'sectionTypes'    => $this->sectionTypes(),
            'teamMembers'     => Team::where('is_active', true)->orderBy('name')->get(['id', 'name', 'dep_id']),
            'departments'     => Department::orderBy('id')->get(['id', 'name']),
            'awards'          => Award::where('is_active', true)->orderByDesc('award_year')->get(['id', 'title', 'award_year']),
        ]);
    }

    // ── Store ─────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $this->validatePage($request);

        $sections = json_decode($request->input('sections_json', '[]'), true) ?? [];

        try {
            $page = CmsPage::create($validated);
        } catch (\Illuminate\Database\QueryException $e) {
            // Rare race: two admins saved the same slug in the same instant and both
            // passed the "slug not taken yet" validation before either insert committed.
            return back()->withInput()->with('error', 'That page slug just got taken by another save — please try again.');
        }

        $this->service->saveSections($page, $sections);
        $this->service->flushCache($page);

        return redirect()
            ->route('admin.cms-builder.edit', $page)
            ->with('success', 'Page created successfully.');
    }

    // ── Edit ──────────────────────────────────────────────────────────────

    public function edit(CmsPage $cmsBuilder)
    {
        $cmsBuilder->load('sections');

        // Restore sections from the flashed input if we just redirected back
        // here after a validation failure, so in-progress edits aren't lost.
        if ($old = old('sections_json')) {
            $existingSections = json_decode($old, true) ?? [];
        } else {
            $existingSections = $cmsBuilder->sections->map(fn($s) => [
                'id'       => $s->id,
                'type'     => $s->type,
                'label'    => $s->label ?? '',
                'is_active'=> $s->is_active,
                'content'  => $s->content ?? [],
            ])->values()->toArray();
        }

        return view('admin.cms-builder.edit', [
            'page'            => $cmsBuilder,
            'existingSections'=> $existingSections,
            'sectionTypes'    => $this->sectionTypes(),
            'teamMembers'     => Team::where('is_active', true)->orderBy('name')->get(['id', 'name', 'dep_id']),
            'departments'     => Department::orderBy('id')->get(['id', 'name']),
            'awards'          => Award::where('is_active', true)->orderByDesc('award_year')->get(['id', 'title', 'award_year']),
        ]);
    }

    // ── Update ────────────────────────────────────────────────────────────

    public function update(Request $request, CmsPage $cmsBuilder)
    {
        $validated = $this->validatePage($request, $cmsBuilder->id);

        $sections = json_decode($request->input('sections_json', '[]'), true) ?? [];

        $cmsBuilder->update($validated);

        $this->service->saveSections($cmsBuilder, $sections);
        $this->service->flushCache($cmsBuilder);

        return redirect()
            ->route('admin.cms-builder.edit', $cmsBuilder)
            ->with('success', 'Page saved successfully.');
    }

    // ── Toggle publish ────────────────────────────────────────────────────

    // ── SEO update (AJAX from index modal) ───────────────────────────────────

    public function updateSeo(Request $request, CmsPage $cmsBuilder)
    {
        $request->validate([
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords'    => 'nullable|string',
            'h1_heading'       => 'nullable|string',
            'meta_details'     => 'nullable|string',
        ]);

        CmsPageMetaData::updateOrCreate(
            ['page_id' => $cmsBuilder->id],
            [
                'meta_title'       => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords'    => $request->meta_keywords,
                'h1_heading'       => $request->h1_heading,
                'meta_details'     => $request->meta_details,
            ]
        );

        return response()->json(['success' => true, 'message' => 'SEO settings saved.']);
    }

    public function togglePublish(CmsPage $cmsBuilder)
    {
        $cmsBuilder->update(['is_published' => !$cmsBuilder->is_published]);
        $this->service->flushCache($cmsBuilder);

        $status = $cmsBuilder->is_published ? 'Published' : 'Unpublished';
        return back()->with('success', "Page {$status}.");
    }

    // ── Destroy ───────────────────────────────────────────────────────────

    public function destroy(CmsPage $cmsBuilder)
    {
        $this->service->flushCache($cmsBuilder);

        \Illuminate\Support\Facades\DB::transaction(function () use ($cmsBuilder) {
            if ($cmsBuilder->banner_image) {
                $disk = config('filesystems.upload_disk');
                rescue(function () use ($disk, $cmsBuilder) {
                    if (\Storage::disk($disk)->exists($cmsBuilder->banner_image)) {
                        \Storage::disk($disk)->delete($cmsBuilder->banner_image);
                    }
                }, function (\Throwable $e) use ($cmsBuilder) {
                    \Illuminate\Support\Facades\Log::error('[CmsPage Delete] Failed to delete banner image from disk', [
                        'banner_image' => $cmsBuilder->banner_image,
                        'error'        => $e->getMessage(),
                    ]);
                }, report: false);
            }

            $cmsBuilder->sections()->delete();
            $cmsBuilder->meta()->delete();
            $cmsBuilder->delete();
        });

        return redirect()
            ->route('admin.cms-builder.index')
            ->with('success', 'Page deleted.');
    }

    // ── Slug generator (AJAX) ─────────────────────────────────────────────

    public function generateSlug(Request $request)
    {
        $slug = Str::slug($request->input('title', ''));
        $original = $slug;
        $i = 2;

        $excludeId = $request->input('exclude_id');
        while (
            CmsPage::where('slug', $slug)
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return response()->json(['slug' => $slug]);
    }

    // ── Inline team member creation from CMS section ─────────────────────────

    public function quickAddTeamMember(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'dep_id'        => 'required|integer',
            'description'   => 'required|string|max:255',
            'about'         => 'nullable|string',
            'profile_image' => 'nullable|string|exists:media,path',
        ]);

        $member = Team::create([
            'name'          => $request->name,
            'dep_id'        => $request->dep_id,
            'description'   => $request->description,
            'about'         => $request->about ?? '',
            'profile_image' => $request->input('profile_image', ''),
            'is_active'     => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Team member "' . $member->name . '" created successfully.',
            'member'  => ['id' => $member->id, 'name' => $member->name],
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function validatePage(Request $request, ?int $excludeId = null): array
    {
        $rules = [
            'title'        => 'required|string|max:255',
            'template'     => 'nullable|string|max:50',
            'is_published' => 'nullable|boolean',
        ];

        // Slug is only ever set at creation — locked thereafter.
        if (! $excludeId) {
            $rules['slug'] = 'required|string|max:255|unique:cms_pages,slug';
        }

        $data = $request->validate($rules);

        $data['is_published'] = (bool) $request->input('is_published', false);
        $data['template']     = $request->input('template', 'default');

        return $data;
    }

    public static function sectionTypes(): array
    {
        return [
            'hero'       => ['label' => 'Hero Banner',        'icon' => 'fa-image',         'color' => 'primary'],
            'text'       => ['label' => 'Text / Rich Content', 'icon' => 'fa-align-left',    'color' => 'secondary'],
            'image_text' => ['label' => 'Image + Content',    'icon' => 'fa-columns',        'color' => 'info'],
            'team'       => ['label' => 'Team Section',       'icon' => 'fa-users',          'color' => 'success'],
            'awards'     => ['label' => 'Awards Section',     'icon' => 'fa-trophy',         'color' => 'warning'],
            'faq'        => ['label' => 'FAQ',                'icon' => 'fa-question-circle','color' => 'danger'],
            'cta'        => ['label' => 'CTA Banner',         'icon' => 'fa-bullhorn',       'color' => 'dark'],
            'cards'      => ['label' => 'Feature Cards',      'icon' => 'fa-th-large',       'color' => 'info'],
            'experience' => ['label' => 'Experience',         'icon' => 'fa-star',           'color' => 'warning'],
        ];
    }
}
