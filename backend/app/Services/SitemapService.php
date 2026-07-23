<?php

namespace App\Services;

class SitemapService
{
    private string $base;
    private string $today;
    private string $defaultFreq;

    public function __construct()
    {
        $cfg               = config('sitemap', []);
        $this->base        = rtrim($cfg['base_url'] ?? 'https://www.indianpanorama.in', '/');
        $this->today       = now()->toDateString();
        $this->defaultFreq = $cfg['changefreq_default'] ?? 'weekly';
    }

    /**
     * Generate the full URL list for the sitemap.
     *
     * @return array<array{loc:string,lastmod:string,priority:string,changefreq:string}>
     */
    public function generate(): array
    {
        $cfg  = config('sitemap', []);
        $urls = [];

        // 1. Static pages
        foreach ($cfg['static'] ?? [] as $page) {
            $urls[] = $this->entry(
                $this->base . $page['path'],
                $page['priority']   ?? '0.8',
                $page['changefreq'] ?? ($page['path'] === '/' ? 'daily' : $this->defaultFreq)
            );
        }

        // 2. Dynamic sections (each reads from DB)
        foreach ($cfg['sections'] ?? [] as $section) {
            array_push($urls, ...$this->urlsForSection($section));
        }

        return $urls;
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function urlsForSection(array $cfg, ?object $parent = null): array
    {
        $model = $cfg['model'] ?? null;
        if (!$model || !class_exists($model)) return [];

        $query = $model::query();

        // Simple WHERE conditions: ['column', 'value']
        foreach ($cfg['where'] ?? [] as [$col, $val]) {
            $query->where($col, $val);
        }

        // Column is NULL (e.g. state-level pages have no location_id)
        foreach ($cfg['where_null'] ?? [] as $col) {
            $query->whereNull($col);
        }

        // Column is NOT NULL (e.g. city-level pages have a location_id)
        foreach ($cfg['where_not_null'] ?? [] as $col) {
            $query->whereNotNull($col);
        }

        // Filter by relation existence: ['relation' => [['col', 'val'], ...]]
        foreach ($cfg['where_has'] ?? [] as $relation => $conditions) {
            $query->whereHas($relation, function ($q) use ($conditions) {
                foreach ($conditions as [$col, $val]) {
                    $q->where($col, $val);
                }
            });
        }

        // Filter by relation non-existence
        foreach ($cfg['where_doesnt_have'] ?? [] as $relation) {
            $query->doesntHave($relation);
        }

        // Eager-load relations needed for URL building AND children
        $eagerLoad = $cfg['with'] ?? [];
        $childCfg  = $cfg['children'] ?? null;

        if ($childCfg) {
            $rel         = $childCfg['relation'];
            $childWhere  = $childCfg['where'] ?? [];
            $eagerLoad[] = [$rel => function ($q) use ($childWhere) {
                foreach ($childWhere as [$col, $val]) {
                    $q->where($col, $val);
                }
            }];
        }

        if ($eagerLoad) {
            $query->with($eagerLoad);
        }

        $priority = $cfg['priority']   ?? '0.8';
        $freq     = $cfg['changefreq'] ?? $this->defaultFreq;
        $urls     = [];
        $seenLocs = []; // for unique_url deduplication

        foreach ($query->get() as $record) {
            $loc = $this->resolveUrl($cfg['url'], $record, $parent);

            // unique_url: skip duplicate URLs (e.g. multiple cities → same state URL)
            if (!empty($cfg['unique_url'])) {
                if (isset($seenLocs[$loc])) continue;
                $seenLocs[$loc] = true;
            }

            $urls[] = $this->entry($loc, $priority, $freq);

            // Nested children (e.g. train → tours)
            if ($childCfg) {
                foreach ($record->{$childCfg['relation']} ?? [] as $child) {
                    $urls[] = $this->entry(
                        $this->resolveUrl($childCfg['url'], $child, $record),
                        $childCfg['priority']   ?? '0.7',
                        $childCfg['changefreq'] ?? $this->defaultFreq
                    );
                }
            }
        }

        return $urls;
    }

    /**
     * Replace URL placeholders with real values.
     *
     * Supported placeholders:
     *   {slug}                           → $record->slug
     *   {field}                          → $record->field  (any column or accessor)
     *   {relation.field}                 → $record->relation->field
     *   {relation.nested.field}          → $record->relation->nested->field  (any depth)
     *   {parent}                         → $parent->slug  (for child URL patterns)
     *   {parent.field}                   → $parent->field
     */
    private function resolveUrl(string $pattern, object $record, ?object $parent = null): string
    {
        $url = preg_replace_callback('/\{([\w.]+)\}/', function ($m) use ($record, $parent) {
            $parts = explode('.', $m[1]);

            // {parent} or {parent.field} → start from $parent object
            if ($parts[0] === 'parent') {
                $obj = $parent;
                $chain = array_slice($parts, 1);
                if (empty($chain)) return $obj?->slug ?? '';
            } else {
                $obj = $record;
                $chain = $parts;
            }

            // Walk the chain: relation → nested → field
            foreach ($chain as $step) {
                if ($obj === null) return '';
                $obj = $obj->{$step} ?? null;
            }

            return (string) ($obj ?? '');
        }, $pattern);

        // Remove double slashes created by empty segments (e.g. when state is null)
        $url = preg_replace('#/{2,}#', '/', $url);

        return $this->base . $url;
    }

    private function entry(string $loc, string $priority, string $changefreq): array
    {
        return [
            'loc'        => $loc,
            'lastmod'    => $this->today,
            'priority'   => $priority,
            'changefreq' => $changefreq,
        ];
    }
}
