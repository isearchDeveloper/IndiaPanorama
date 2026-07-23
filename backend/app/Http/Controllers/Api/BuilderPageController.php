<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Award;
use App\Models\Branch;
use App\Models\CmsPage;
use App\Models\Department;
use App\Models\Team;
use Illuminate\Http\JsonResponse;

class BuilderPageController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        $page = CmsPage::with(['activeSections', 'meta'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (!$page) {
            return response()->json(['status' => 'error', 'message' => 'Page not found.'], 404);
        }

        $sections = $page->activeSections->map(fn($s) => $this->formatSection($s));

        if ($slug === 'contact-us') {
            return response()->json([
                'status'  => 'success',
                'message' => 'Page data',
                'data'    => $this->buildContactUsResponse($page, $sections),
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Page data',
            'data'    => [
                'title'    => $page->title,
                'slug'     => $page->slug,
                'seo'      => $this->formatSeo($page->meta),
                'sections' => $sections,
            ],
        ]);
    }

    // ── Contact Us — custom flattened shape (banner + branches{heading,body,items}) ──

    private function buildContactUsResponse(CmsPage $page, \Illuminate\Support\Collection $sections): array
    {
        $hero = $sections->firstWhere('type', 'hero');
        $text = $sections->firstWhere('type', 'text');

        return [
            'title'   => $page->title,
            'slug'    => $page->slug,
            'seo'     => $this->formatSeo($page->meta),
            'banner'   => $hero['content'] ?? [],
            'branches' => [
                'heading' => $text['content']['heading'] ?? '',
                'body'    => $text['content']['body'] ?? '',
                // Branch list always comes from /admin/branches, independent of CMS sections
                'items'   => $this->branches([]),
            ],
        ];
    }

    // ── Section formatter ─────────────────────────────────────────────────────

    private function formatSection(\App\Models\CmsSection $section): array
    {
        $content = $section->resolved_content;
        $content = $this->normalizeImages($section->type, $content);

        $data = [
            'id'         => $section->id,
            'type'       => $section->type,
            'label'      => $section->label,
            'sort_order' => $section->sort_order,
            'content'    => $content,
        ];

        // Enrich sections that reference other models
        if ($section->type === 'team') {
            $data['members']     = $this->teamMembers($content);
            $data['departments'] = Department::orderBy('id')->get(['id', 'name']);
        }

        if ($section->type === 'awards') {
            $data['awards'] = $this->awards($content);
        }

        if ($section->type === 'branches') {
            $data['branches'] = $this->branches($content);
        }

        return $data;
    }

    // ── Image URL normalisation ───────────────────────────────────────────────

    private function normalizeImages(string $type, array $content): array
    {
        // Normalize image URL fields; alt tags are plain strings — pass through as-is
        foreach (['banner_image', 'image', 'bg_image'] as $key) {
            if (!empty($content[$key])) {
                $content[$key] = storage_link($content[$key]);
            }
        }

        if ($type === 'cards' && !empty($content['cards'])) {
            $content['cards'] = array_map(function ($card) {
                if (!empty($card['image'])) $card['image'] = storage_link($card['image']);
                // image_alt passes through unchanged
                return $card;
            }, $content['cards']);
        }

        return $content;
    }

    // ── Team members ──────────────────────────────────────────────────────────

    private function teamMembers(array $content): array
    {
        $filter = $content['filter'] ?? 'all';
        $limit  = (int) ($content['limit'] ?? 0);

        $q = Team::with('department')->where('is_active', true)->orderBy('id');

        if ($filter === 'selected' && !empty($content['member_ids'])) {
            $q->whereIn('id', $content['member_ids']);
        }

        if ($limit > 0) $q->limit($limit);

        return $q->get()->map(fn($m) => [
            'id'            => $m->id,
            'name'          => $m->name,
            'description'   => $m->description,
            'about'         => $m->about,
            'profile_image' => $m->profile_image ? storage_link($m->profile_image) : null,
            'department'    => $m->department
                ? ['id' => $m->department->id, 'name' => $m->department->name]
                : null,
        ])->toArray();
    }

    // ── Awards ────────────────────────────────────────────────────────────────

    private function awards(array $content): array
    {
        $filter = $content['filter'] ?? 'all';

        $q = Award::where('is_active', true)->orderByDesc('award_year');

        if ($filter === 'selected' && !empty($content['award_ids'])) {
            $q->whereIn('id', $content['award_ids']);
        }

        return $q->get()->map(fn($a) => [
            'id'           => $a->id,
            'title'        => $a->title,
            'award_year'   => $a->award_year,
            'description'  => $a->description,
            'banner_image' => $a->banner_image ? storage_link($a->banner_image) : null,
        ])->toArray();
    }

    // ── Branches ──────────────────────────────────────────────────────────────

    private function branches(array $content): array
    {
        return Branch::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn($b) => [
                'id'      => $b->id,
                'name'    => $b->name,
                'address' => $b->address,
                'phones'  => $b->phones ?? [],
            ])->toArray();
    }

    // ── SEO ───────────────────────────────────────────────────────────────────

    private function formatSeo(?\App\Models\CmsPageMetaData $meta): ?array
    {
        if (!$meta) return null;

        return [
            'meta_title'       => $meta->meta_title,
            'meta_description' => $meta->meta_description,
            'meta_keywords'    => $meta->meta_keywords,
            'h1_heading'       => $meta->h1_heading,
            'meta_details'     => $meta->meta_details,
        ];
    }
}
