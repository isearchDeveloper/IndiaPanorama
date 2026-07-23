<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\MenuBuilderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Header Menu API
 * ───────────────
 * GET /api/header-menu        (primary — no token required for public nav)
 * GET /api/v1/header-menu     (legacy alias — same response)
 *
 * Single source of truth: the admin Menu Builder.
 * No hardcoded menus — only whatever exists in `menus` + `menu_items` tables.
 *
 * ── Response Shape ────────────────────────────────────────────────────────
 * {
 *   "success": true,
 *   "data": {
 *     "header": {
 *       "id": 1,
 *       "name": "Header Menu",
 *       "slug": "header",
 *       "display_mode": "manual",
 *       "items": [
 *         {
 *           "id": 5,
 *           "title": "Home",
 *           "url": "https://example.com/",
 *           "target": "_self",
 *           "type": "custom",
 *           "has_children": false,
 *           "children": []
 *         },
 *         {
 *           "id": 6,
 *           "title": "Holiday Packages",
 *           "url": "#",
 *           "target": "_self",
 *           "type": "menu_reference",
 *           "has_children": true,
 *           "children": [
 *             { "id": 1, "title": "South India", "url": "…/holidays/south-india",
 *               "target": "_self", "type": "region", "has_children": true,
 *               "children": [ … ] }
 *           ]
 *         }
 *       ]
 *     },
 *     "footer": { … },
 *     "holiday-packages": { … }
 *   }
 * }
 *
 * ── Keys ─────────────────────────────────────────────────────────────────
 * Top-level keys are each menu's `slug` exactly as stored in the DB.
 * Frontend reads:  data["header"]   data["footer"]   data["holiday-packages"]
 *
 * ── Display Modes ─────────────────────────────────────────────────────────
 * manual            → manual menu_items (active only)
 * region_state_city → Region → State → Cities tree from DB
 * region_state      → Region → State (no cities)
 * state_city        → State → Cities (no regions)
 * city_only         → flat city list
 * Filters (region_ids, state_ids, active_only, package_only) are respected.
 *
 * ── Menu References ───────────────────────────────────────────────────────
 * A menu_reference item inlines the referenced menu's items as children.
 * Cycle-safe via stack guard in MenuBuilderService.
 *
 * ── Cache ─────────────────────────────────────────────────────────────────
 * Key: api_header_menu_v6   TTL: 120 s
 * Flushed on every admin write (MenuItemController + MenuManagerController).
 */
class HeaderMenuController extends Controller
{
    /** Bump the suffix whenever the response shape changes. */
    public const CACHE_KEY = 'api_header_menu_v7';
    public const CACHE_TTL = 300; // 5 minutes

    public function __construct(
        private readonly MenuBuilderService $menuBuilder,
    ) {}

    // ──────────────────────────────────────────────────────────────
    // Endpoint
    // ──────────────────────────────────────────────────────────────

    public function index(): JsonResponse
    {
        try {
            $data = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, fn () => $this->build());

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('HeaderMenuController: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to load menu data.',
                'data'    => [],
            ], 500);
        }
    }

    /** Called after every admin write so the next request re-builds fresh. */
    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    // ──────────────────────────────────────────────────────────────
    // Core builder
    // ──────────────────────────────────────────────────────────────

    private function build(): array
    {
        // Only active menus, ordered as admin set them
        $menus = Menu::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $result = [];

        foreach ($menus as $menu) {
            try {
                $items = $this->buildMenuItems($menu);

                $result[$menu->slug] = [
                    'id'           => $menu->id,
                    'name'         => $menu->name,
                    'slug'         => $menu->slug,
                    'display_mode' => $menu->display_mode ?? Menu::DISPLAY_MANUAL,
                    'items'        => $items,
                ];
            } catch (\Throwable $e) {
                Log::error("HeaderMenuController: menu slug={$menu->slug} failed: " . $e->getMessage());

                $result[$menu->slug] = [
                    'id'           => $menu->id,
                    'name'         => $menu->name,
                    'slug'         => $menu->slug,
                    'display_mode' => $menu->display_mode ?? Menu::DISPLAY_MANUAL,
                    'items'        => [],
                ];
            }
        }

        return $result;
    }

    /**
     * Resolve a single menu's items according to its display_mode.
     */
    private function buildMenuItems(Menu $menu): array
    {
        if ($menu->isAutoDisplay()) {
            // Auto-generated location tree — Region/State/City hierarchy
            return $this->menuBuilder->buildAutoDisplayTree($menu);
        }

        // Manual mode: fetch only active items, resolve menu_references inline
        $tree   = $this->menuBuilder->buildTree($menu, activeOnly: true);
        $allItems = $this->flattenTree($tree);
        $urlMap = $this->menuBuilder->bulkResolveUrls($allItems);

        return $this->serializeTree($tree, $urlMap);
    }

    // ──────────────────────────────────────────────────────────────
    // Serialisation helpers
    // ──────────────────────────────────────────────────────────────

    /**
     * Recursively serialize a tree of MenuItem objects.
     * Produces consistent node shapes the frontend can rely on.
     *
     * Items with content_type = "mega_menu" get an extra `mega_menu` key
     * containing the full location tree + banner config.
     */
    private function serializeTree(Collection $nodes, array $urlMap): array
    {
        $out = [];

        foreach ($nodes as $item) {
            // Skip hidden items (activeOnly:true in buildTree catches most,
            // but menu_reference resolution may bring in items from other menus)
            if ($item->status !== 1) {
                continue;
            }

            $children     = $this->serializeTree($item->_children ?? collect(), $urlMap);
            $isMega       = $item instanceof MenuItem && $item->isMegaMenu();
            $contentType  = $isMega ? MenuItem::CONTENT_MEGA : MenuItem::CONTENT_NORMAL;

            $node = [
                'id'           => $item->id,
                'title'        => $item->title,
                'url'          => $urlMap[$item->id] ?? ($item->url ?: '#'),
                'target'       => $item->target ?? '_self',
                'type'         => $item->type,
                'content_type' => $contentType,
                'has_children' => $isMega || ! empty($children),
                'children'     => $children,
            ];

            // ── Mega menu — attach the full dropdown content ──────────
            if ($isMega) {
                try {
                    $node['mega_menu'] = $this->menuBuilder->buildMegaMenuContent($item);
                } catch (\Throwable $e) {
                    Log::error('HeaderMenuController: mega menu build failed for item #' . $item->id . ': ' . $e->getMessage());
                    $node['mega_menu'] = [
                        'display_source' => 'auto',
                        'display_mode'   => 'region_state_city',
                        'items'          => [],
                        'banner'         => [],
                    ];
                }
            }

            $out[] = $node;
        }

        return $out;
    }

    /**
     * Flatten any-depth tree into a single Collection.
     * Needed by bulkResolveUrls() to avoid N+1.
     */
    private function flattenTree(Collection $nodes): Collection
    {
        $flat = collect();

        foreach ($nodes as $node) {
            $flat->push($node);

            if (! empty($node->_children) && $node->_children->isNotEmpty()) {
                $flat = $flat->merge($this->flattenTree($node->_children));
            }
        }

        return $flat;
    }
}
