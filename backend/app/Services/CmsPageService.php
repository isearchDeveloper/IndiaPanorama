<?php

namespace App\Services;

use App\Models\Award;
use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\Team;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CmsPageService
{
    // ── Section save ──────────────────────────────────────────────────────

    public function saveSections(CmsPage $page, array $sections): void
    {
        DB::transaction(function () use ($page, $sections) {
            $keep = [];

            foreach ($sections as $i => $data) {
                $attributes = [
                    'type'              => $data['type'],
                    'label'             => $data['label'] ?? null,
                    'content'           => $data['content'] ?? [],
                    'sort_order'        => $i,
                    'is_active'         => (bool) ($data['is_active'] ?? true),
                ];

                $id = $data['id'] ?? null;

                if ($id) {
                    $section = CmsSection::where('id', $id)->where('cms_page_id', $page->id)->first();
                    if ($section) {
                        $section->update($attributes);
                        $keep[] = $section->id;
                        continue;
                    }
                }

                $section = $page->sections()->create($attributes);
                $keep[] = $section->id;
            }

            // Delete sections removed by the admin
            $page->sections()->whereNotIn('id', $keep)->delete();
        });
    }

    // ── Frontend rendering (with DB hydration) ────────────────────────────

    public function renderPage(CmsPage $page): array
    {
        return Cache::remember("cms_page_{$page->id}_sections", 60, function () use ($page) {
            return $page
                ->activeSections()
                ->get()
                ->map(fn(CmsSection $s) => $this->hydrateSection($s))
                ->all();
        });
    }

    public function flushCache(CmsPage $page): void
    {
        Cache::forget("cms_page_{$page->id}_sections");
    }

    // ── Hydration ─────────────────────────────────────────────────────────

    private function hydrateSection(CmsSection $section): array
    {
        $content = $section->resolved_content;

        return match ($section->type) {
            'team'   => $this->hydrateTeam($content, $section->type),
            'awards' => $this->hydrateAwards($content, $section->type),
            default  => ['type' => $section->type, 'content' => $content],
        };
    }

    private function hydrateTeam(array $content, string $type): array
    {
        $query = Team::where('is_active', true)->orderBy('name');

        match ($content['filter'] ?? 'all') {
            'selected' => $query->whereIn('id', $content['member_ids'] ?? []),
            default    => null,
        };

        $limit = (int) ($content['limit'] ?? 12);
        if ($limit > 0) {
            $query->limit($limit);
        }

        $content['members'] = $query->get();

        return ['type' => $type, 'content' => $content];
    }

    private function hydrateAwards(array $content, string $type): array
    {
        $query = Award::where('is_active', true);

        match ($content['filter'] ?? 'all') {
            'latest'   => $query->orderByDesc('award_year'),
            'selected' => $query->whereIn('id', $content['award_ids'] ?? []),
            default    => $query->orderByDesc('award_year'),
        };

        $limit = (int) ($content['limit'] ?? 0);
        if ($limit > 0) {
            $query->limit($limit);
        }

        $content['awards'] = $query->get();

        return ['type' => $type, 'content' => $content];
    }
}
