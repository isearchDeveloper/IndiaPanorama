<?php

namespace App\Services;

use App\Models\Category;
use App\Models\HolidayMenuSetting;
use App\Models\Location;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Package;
use App\Models\Page;
use App\Models\Region;
use App\Models\State;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * MenuBuilderService — All menu business logic.
 *
 * Public API:
 *   buildTree(Menu, activeOnly)         → nested tree; menu_reference items are resolved inline
 *   assembleTree(Collection)            → flat → nested tree (no reference resolution)
 *   bulkResolveUrls(Collection)         → URL map for all items, no N+1
 *   getAvailableItems(string $type)     → [{id, label, slug}] for AJAX selects
 *   createItem(Menu, array)             → validated insert, returns MenuItem
 *   updateItem(MenuItem, array)         → update, returns fresh MenuItem
 *   deleteItem(MenuItem)                → delete (children cascade via FK)
 *   toggleStatus(MenuItem)              → flip status 0↔1
 *   reorder(Menu, array)                → bulk sort_order + parent_id in transaction
 *   getStats(Menu)                      → [total, active, root, nested]
 */
class MenuBuilderService
{
    // ──────────────────────────────────────────────────────────────
    // TREE BUILDER  (O(n) — single query per menu, in-memory assembly)
    // ──────────────────────────────────────────────────────────────

    /**
     * Build a fully nested tree for a menu.
     *
     * Returns a Collection of root MenuItem objects.
     * Each item has a `->_children` property (Collection<MenuItem>)
     * containing its direct children, which also have `->_children`.
     *
     * menu_reference items have their referenced menu's items
     * appended to `->_children` automatically (cycle-safe).
     *
     * @param  bool $activeOnly  true for public frontend, false for admin
     * @return Collection<MenuItem>
     */
    public function buildTree(Menu $menu, bool $activeOnly = false): Collection
    {
        $flat = MenuItem::where('menu_id', $menu->id)
                        ->orderBy('sort_order')
                        ->when($activeOnly, fn ($q) => $q->where('status', 1))
                        ->get();

        $tree = $this->assembleTree($flat);

        // Resolve menu_reference items — inject referenced menu's items as children
        $this->resolveMenuReferences($tree, [$menu->id], $activeOnly);

        return $tree;
    }

    /**
     * Assemble a flat Collection into a nested tree.
     * Each item receives a `->_children` Collection.
     * Does NOT resolve menu_reference items.
     */
    public function assembleTree(Collection $flat): Collection
    {
        /** @var Collection<int, MenuItem> $indexed */
        $indexed = $flat->keyBy('id');

        foreach ($indexed as $item) {
            $item->_children = new Collection();
        }

        $roots = new Collection();
        foreach ($indexed as $item) {
            if ($item->parent_id && $indexed->has($item->parent_id)) {
                $indexed[$item->parent_id]->_children->push($item);
            } else {
                $roots->push($item);
            }
        }

        return $roots;
    }

    /**
     * Resolve menu_reference items by inlining the referenced menu's tree
     * as the item's children. Cycle-safe via $visitedMenuIds guard.
     *
     * @param Collection $nodes         Current level of the tree
     * @param int[]      $visitedMenuIds Stack of menu IDs already on the call path
     * @param bool       $activeOnly
     */
    private function resolveMenuReferences(Collection $nodes, array $visitedMenuIds, bool $activeOnly): void
    {
        foreach ($nodes as $item) {
            if ($item->type === 'menu_reference' && $item->linked_id) {
                if (in_array($item->linked_id, $visitedMenuIds)) {
                    // Cycle detected — mark and skip
                    $item->_ref_cycle = true;
                    Log::warning("MenuBuilder: cycle detected — menu #{$item->linked_id} already in path " . implode('→', $visitedMenuIds));
                } else {
                    $refMenu = Menu::find($item->linked_id);
                    if ($refMenu) {
                        $refFlat = MenuItem::where('menu_id', $refMenu->id)
                                           ->orderBy('sort_order')
                                           ->when($activeOnly, fn ($q) => $q->where('status', 1))
                                           ->get();

                        $refTree = $this->assembleTree($refFlat);

                        // Recurse into the referenced menu (handles nested references)
                        $this->resolveMenuReferences(
                            $refTree,
                            array_merge($visitedMenuIds, [$refMenu->id]),
                            $activeOnly
                        );

                        // Append referenced items after any direct children
                        $item->_children = $item->_children->merge($refTree);
                        $item->_ref_menu_name = $refMenu->name; // display hint for admin
                    }
                }
            }

            // Recurse into existing children (non-reference or already resolved)
            if ($item->_children->isNotEmpty()) {
                $this->resolveMenuReferences($item->_children, $visitedMenuIds, $activeOnly);
            }
        }
    }

    // ──────────────────────────────────────────────────────────────
    // BULK URL RESOLUTION  (no N+1)
    // ──────────────────────────────────────────────────────────────

    /**
     * Resolve URLs for all items in ONE query per type.
     *
     * @param  Collection<MenuItem> $allItems  Flat (any depth)
     * @return array<int, string|null>          item_id → resolved URL
     */
    public function bulkResolveUrls(\Illuminate\Support\Collection $allItems): array
    {
        $slugMaps = [];

        // Fetch slugs — one query per type that needs a DB lookup
        foreach ($allItems->groupBy('type') as $type => $items) {
            if (in_array($type, ['custom', 'menu_reference'])) continue;

            $ids = $items->pluck('linked_id')->filter()->unique()->values();
            if ($ids->isEmpty()) continue;

            $slugMaps[$type] = match ($type) {
                'page'     => DB::table('pages')     ->whereIn('id', $ids)->pluck('slug', 'id'),
                'package'  => DB::table('packages')  ->whereIn('id', $ids)->pluck('slug', 'id'),
                'location' => DB::table('locations') ->whereIn('id', $ids)->pluck('slug', 'id'),
                'region'   => DB::table('regions')   ->whereIn('id', $ids)->pluck('slug', 'id'),
                'state'    => DB::table('states')    ->whereIn('id', $ids)->pluck('slug', 'id'),
                'category' => DB::table('categories')->whereIn('id', $ids)->pluck('slug', 'id'),
                default    => collect(),
            };
        }

        // Build result map
        $result = [];
        foreach ($allItems as $item) {
            $result[$item->id] = match (true) {
                $item->type === 'menu_reference'
                    => '#',   // container — no URL; children are the referenced menu's items

                $item->type === 'custom'
                    => $item->url ?: '#',

                isset($slugMaps[$item->type][$item->linked_id])
                    => $this->buildUrl($item->type, $slugMaps[$item->type][$item->linked_id]),

                default
                    => $item->url ?: '#',
            };
        }

        return $result;
    }

    public function buildUrl(string $type, string $slug): string
    {
        return match ($type) {
            'page'     => '/pages/'    . $slug,
            'package'  => '/packages/' . $slug,
            'category' => '/tours/'    . $slug,
            default    => '/'          . $slug,
        };
    }

    // ──────────────────────────────────────────────────────────────
    // MEGA MENU CONTENT BUILDER
    // ──────────────────────────────────────────────────────────────

    /**
     * Build the full mega-menu content for a MenuItem that has
     * content_type = "mega_menu" in its mega_settings.
     *
     * Returns the complete mega_menu payload ready for the API:
     * {
     *   display_source: "auto" | "custom_menu",
     *   display_mode:   "region_state_city" | ...,
     *   items:          [...tree nodes...],
     *   banner:         {...}
     * }
     */
    public function buildMegaMenuContent(MenuItem $item): array
    {
        $cfg    = $item->resolvedMegaSettings();
        $source = $cfg['display_source'] ?? MenuItem::MEGA_SOURCE_AUTO;
        $mode   = $cfg['display_mode']   ?? 'region_state_city';

        $items = match ($source) {
            MenuItem::MEGA_SOURCE_AUTO   => $this->buildMegaAutoTree($mode, $cfg),
            MenuItem::MEGA_SOURCE_CUSTOM => $this->buildMegaCustomMenuTree((int) ($cfg['linked_menu_id'] ?? 0)),
            default                      => [],
        };

        return [
            'display_source' => $source,
            'display_mode'   => $mode,
            'items'          => $items,
            'banner'         => $cfg['banner'] ?? [],
        ];
    }

    /**
     * Build an auto location tree (Region/State/City) for a mega menu item.
     * Reuses the same private builders as buildAutoDisplayTree().
     */
    private function buildMegaAutoTree(string $mode, array $cfg): array
    {
        return match ($mode) {
            'region_state_city' => $this->autoRegionStateCityTree($cfg),
            'region_state'      => $this->autoRegionStateTree($cfg),
            'state_city'        => $this->autoStateCityTree($cfg),
            'city_only'         => $this->autoCityOnlyTree($cfg),
            default             => [],
        };
    }

    /**
     * Build a serialised tree from a referenced menu's active items.
     * Used when mega display_source = "custom_menu".
     */
    private function buildMegaCustomMenuTree(int $menuId): array
    {
        if (! $menuId) {
            return [];
        }

        $menu = Menu::find($menuId);
        if (! $menu) {
            return [];
        }

        $flat = MenuItem::where('menu_id', $menu->id)
                        ->where('status', 1)
                        ->orderBy('sort_order')
                        ->get();

        $tree   = $this->assembleTree($flat);
        $urlMap = $this->bulkResolveUrls($flat);

        return $this->megaSerializeTree($tree, $urlMap);
    }

    /**
     * Generic tree serialiser for mega menu content.
     * Returns plain arrays (not MenuItem objects) for the API.
     */
    private function megaSerializeTree(Collection $nodes, array $urlMap): array
    {
        $out = [];

        foreach ($nodes as $item) {
            if ($item->status !== 1) {
                continue;
            }

            $children = $this->megaSerializeTree($item->_children ?? collect(), $urlMap);

            $out[] = [
                'id'           => $item->id,
                'title'        => $item->title,
                'url'          => $urlMap[$item->id] ?? ($item->url ?: '#'),
                'target'       => $item->target ?? '_self',
                'type'         => $item->type,
                'has_children' => ! empty($children),
                'children'     => $children,
            ];
        }

        return $out;
    }

    // ──────────────────────────────────────────────────────────────
    // AVAILABLE ITEMS  (for AJAX select dropdowns)
    // ──────────────────────────────────────────────────────────────

    /**
     * Return linkable records for a given type.
     *
     * @param  int|null  $excludeMenuId  When type=menu_reference, exclude this menu
     *                                   (prevents a menu referencing itself)
     * @return array<int, array{id:int, label:string, slug:string}>
     */
    public function getAvailableItems(string $type, ?int $excludeMenuId = null): array
    {
        return match ($type) {
            'page'           => $this->fetchPages(),
            'package'        => $this->fetchPackages(),
            'location'       => $this->fetchLocations(),
            'region'         => $this->fetchRegions(),
            'state'          => $this->fetchStates(),
            'category'       => $this->fetchCategories(),
            'menu_reference' => $this->fetchMenus($excludeMenuId),
            default          => [],
        };
    }

    private function fetchPages(): array
    {
        return Page::select('id', 'title', 'slug')
                   ->orderBy('title')
                   ->get()
                   ->map(fn ($r) => ['id' => $r->id, 'label' => $r->title, 'slug' => $r->slug])
                   ->values()->toArray();
    }

    private function fetchPackages(): array
    {
        return Package::select('id', 'title', 'slug')
                      ->where('is_active', true)
                      ->orderBy('title')
                      ->get()
                      ->map(fn ($r) => ['id' => $r->id, 'label' => $r->title, 'slug' => $r->slug])
                      ->values()->toArray();
    }

    private function fetchLocations(): array
    {
        return Location::select('id', 'name', 'slug')
                       ->where('is_active', true)
                       ->orderBy('name')
                       ->get()
                       ->map(fn ($r) => ['id' => $r->id, 'label' => $r->name, 'slug' => $r->slug])
                       ->values()->toArray();
    }

    private function fetchRegions(): array
    {
        return Region::select('id', 'name', 'slug')
                     ->orderBy('name')
                     ->get()
                     ->map(fn ($r) => ['id' => $r->id, 'label' => $r->name, 'slug' => $r->slug])
                     ->values()->toArray();
    }

    private function fetchStates(): array
    {
        return State::select('id', 'name', 'slug')
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get()
                    ->map(fn ($r) => ['id' => $r->id, 'label' => $r->name, 'slug' => $r->slug])
                    ->values()->toArray();
    }

    private function fetchCategories(): array
    {
        return Category::select('id', 'name', 'slug')
                       ->orderBy('name')
                       ->get()
                       ->map(fn ($r) => ['id' => $r->id, 'label' => $r->name, 'slug' => $r->slug])
                       ->values()->toArray();
    }

    /**
     * Return all menus as selectable items (for Menu Reference picker).
     * Excludes $excludeMenuId to prevent a menu referencing itself.
     */
    private function fetchMenus(?int $excludeMenuId = null): array
    {
        return Menu::select('id', 'name', 'slug')
                   ->where('is_active', true)
                   ->when($excludeMenuId, fn ($q) => $q->where('id', '!=', $excludeMenuId))
                   ->orderBy('sort_order')
                   ->get()
                   ->map(fn ($r) => ['id' => $r->id, 'label' => $r->name, 'slug' => $r->slug])
                   ->values()->toArray();
    }

    // ──────────────────────────────────────────────────────────────
    // CRUD
    // ──────────────────────────────────────────────────────────────

    /**
     * Create a menu item, auto-assigning sort_order = max sibling + 1.
     */
    public function createItem(Menu $menu, array $data): MenuItem
    {
        $parentId = isset($data['parent_id']) && $data['parent_id'] ? (int) $data['parent_id'] : null;

        $maxOrder = MenuItem::where('menu_id', $menu->id)
                            ->where('parent_id', $parentId)
                            ->max('sort_order') ?? -1;

        $payload = [
            'menu_id'       => $menu->id,
            'parent_id'     => $parentId,
            'title'         => trim($data['title']),
            'type'          => $data['type'],
            'linked_id'     => $this->resolveLinkedId($data),
            'url'           => $data['type'] === 'custom' ? ($data['url'] ?? null) : null,
            'target'        => $data['target'] ?? '_self',
            'status'        => isset($data['status']) ? (int) $data['status'] : 1,
            'sort_order'    => $maxOrder + 1,
            'mega_settings' => $this->cleanMegaSettings($data['mega_settings'] ?? null),
        ];

        $item = MenuItem::create($payload);

        Log::info('MenuBuilder: item created', [
            'id'      => $item->id,
            'menu_id' => $menu->id,
            'type'    => $item->type,
        ]);

        return $item;
    }

    /**
     * Update a menu item.
     */
    public function updateItem(MenuItem $item, array $data): MenuItem
    {
        $payload = [
            'title'         => trim($data['title']),
            'type'          => $data['type'],
            'linked_id'     => $this->resolveLinkedId($data),
            'url'           => $data['type'] === 'custom' ? ($data['url'] ?? null) : null,
            'target'        => $data['target'] ?? '_self',
            'status'        => isset($data['status']) ? (int) $data['status'] : $item->status,
            'mega_settings' => $this->cleanMegaSettings($data['mega_settings'] ?? null),
        ];

        $item->update($payload);

        Log::info('MenuBuilder: item updated', ['id' => $item->id]);

        return $item->fresh();
    }

    /**
     * Toggle item status 1 ↔ 0.
     */
    public function toggleStatus(MenuItem $item): MenuItem
    {
        $item->update(['status' => $item->status === 1 ? 0 : 1]);
        return $item->fresh();
    }

    /**
     * Delete an item. Children cascade via DB FK.
     */
    public function deleteItem(MenuItem $item): void
    {
        $id = $item->id;
        $item->delete();
        Log::info('MenuBuilder: item deleted', ['id' => $id]);
    }

    // ──────────────────────────────────────────────────────────────
    // REORDER  (drag-drop, full tree snapshot)
    // ──────────────────────────────────────────────────────────────

    /**
     * Persist drag-drop order. Expects payload:
     * [
     *   ['id' => 5, 'parent_id' => null, 'sort_order' => 0],
     *   ['id' => 8, 'parent_id' => 5,    'sort_order' => 0],
     * ]
     *
     * @throws \Throwable  Rolls back on failure
     */
    public function reorder(Menu $menu, array $items): void
    {
        DB::transaction(function () use ($menu, $items) {
            foreach ($items as $row) {
                MenuItem::where('id', (int) $row['id'])
                        ->where('menu_id', $menu->id)   // security scope
                        ->update([
                            'parent_id'  => isset($row['parent_id']) && $row['parent_id'] ? (int) $row['parent_id'] : null,
                            'sort_order' => (int) ($row['sort_order'] ?? 0),
                        ]);
            }
        });
    }

    // ──────────────────────────────────────────────────────────────
    // STATS
    // ──────────────────────────────────────────────────────────────

    /**
     * Return counts for the builder header stats bar.
     */
    public function getStats(Menu $menu): array
    {
        $items = MenuItem::where('menu_id', $menu->id)->get();

        return [
            'total'  => $items->count(),
            'active' => $items->where('status', 1)->count(),
            'root'   => $items->whereNull('parent_id')->count(),
            'nested' => $items->whereNotNull('parent_id')->count(),
        ];
    }

    // ──────────────────────────────────────────────────────────────
    // AUTO DISPLAY TREE  (Region / State / City hierarchy)
    // ──────────────────────────────────────────────────────────────

    /**
     * Build an auto-generated location tree for a menu according to its
     * display_mode and display_settings.
     *
     * Returns an array of plain objects with shape:
     *   { id, title, url, target, type, status, children[] }
     *
     * The structure mirrors the serialized MenuItem tree used by the API.
     *
     * @param  Menu $menu
     * @return array
     */
    public function buildAutoDisplayTree(Menu $menu): array
    {
        $mode     = $menu->display_mode ?? Menu::DISPLAY_MANUAL;
        $settings = $menu->resolvedDisplaySettings();

        return match ($mode) {
            Menu::DISPLAY_REGION_STATE_CITY => $this->autoRegionStateCityTree($settings),
            Menu::DISPLAY_REGION_STATE      => $this->autoRegionStateTree($settings),
            Menu::DISPLAY_STATE_CITY        => $this->autoStateCityTree($settings),
            Menu::DISPLAY_CITY_ONLY         => $this->autoCityOnlyTree($settings),
            default                         => [],   // 'manual' — caller handles it
        };
    }

    // ── Private builders ─────────────────────────────────────────

    /**
     * Region → State → Cities (full 3-level tree).
     */
    private function autoRegionStateCityTree(array $cfg): array
    {
        $regions = $this->queryRegions($cfg);
        $result  = [];

        foreach ($regions as $region) {
            $states = $this->queryStatesForRegion($region->id, $cfg);
            if ($states->isEmpty()) continue;

            $stateNodes = [];
            foreach ($states as $state) {
                $cities = $this->queryCitiesForState($state->id, $cfg, $state->name);
                if ($cities->isEmpty()) continue;

                $stateNodes[] = $this->node(
                    id:       $state->id,
                    title:    $state->name,
                    url:      $this->buildUrl('state', $state->slug),
                    type:     'state',
                    children: $this->cityNodes($cities),
                );
            }

            if (empty($stateNodes)) continue;

            $result[] = $this->node(
                id:       $region->id,
                title:    $region->name,
                url:      $this->buildUrl('region', $region->slug),
                type:     'region',
                children: $stateNodes,
            );
        }

        return $result;
    }

    /**
     * Region → State (2-level; no cities).
     */
    private function autoRegionStateTree(array $cfg): array
    {
        $regions = $this->queryRegions($cfg);
        $result  = [];

        foreach ($regions as $region) {
            $states = $this->queryStatesForRegion($region->id, $cfg);
            if ($states->isEmpty()) continue;

            $stateNodes = [];
            foreach ($states as $state) {
                $stateNodes[] = $this->node(
                    id:    $state->id,
                    title: $state->name,
                    url:   $this->buildUrl('state', $state->slug),
                    type:  'state',
                );
            }

            $result[] = $this->node(
                id:       $region->id,
                title:    $region->name,
                url:      $this->buildUrl('region', $region->slug),
                type:     'region',
                children: $stateNodes,
            );
        }

        return $result;
    }

    /**
     * State → Cities (2-level; no regions).
     */
    private function autoStateCityTree(array $cfg): array
    {
        $states = $this->queryAllStates($cfg);
        $result = [];

        foreach ($states as $state) {
            $cities = $this->queryCitiesForState($state->id, $cfg, $state->name);
            if ($cities->isEmpty()) continue;

            $result[] = $this->node(
                id:       $state->id,
                title:    $state->name,
                url:      $this->buildUrl('state', $state->slug),
                type:     'state',
                children: $this->cityNodes($cities),
            );
        }

        return $result;
    }

    /**
     * Cities only (flat list).
     */
    private function autoCityOnlyTree(array $cfg): array
    {
        $cities = $this->queryAllCities($cfg);
        return $this->cityNodes($cities);
    }

    // ── DB query helpers ─────────────────────────────────────────

    private function queryRegions(array $cfg): \Illuminate\Support\Collection
    {
        $q = DB::table('regions')->select('id', 'name', 'slug')->orderBy('order_seq')->orderBy('name');

        if (! empty($cfg['region_ids'])) {
            $q->whereIn('id', $cfg['region_ids']);
        }

        return $this->applyHolidayMenuOverrides($q->get(), HolidayMenuSetting::TYPE_REGION);
    }

    private function queryAllStates(array $cfg): \Illuminate\Support\Collection
    {
        $q = DB::table('states')->select('id', 'name', 'slug', 'region_id')->orderBy('name');

        if (! empty($cfg['state_ids'])) {
            $q->whereIn('id', $cfg['state_ids']);
        }

        if ($cfg['active_only'] ?? true) {
            $q->where('is_active', true);
        }

        if ($cfg['package_only'] ?? true) {
            $q->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('packages')
                    ->join('locations', 'locations.id', '=', 'packages.location_id')
                    ->whereColumn('locations.state_id', 'states.id')
                    ->where('packages.is_active', true)
                    ->whereNull('packages.deleted_at');
            });
        }

        if ($cfg['manage_city_only'] ?? false) {
            $this->applyManageCityStateFilter($q);
        }

        return $this->applyHolidayMenuOverrides($q->get(), HolidayMenuSetting::TYPE_STATE);
    }

    private function queryStatesForRegion(int $regionId, array $cfg): \Illuminate\Support\Collection
    {
        $q = DB::table('states')
               ->select('id', 'name', 'slug')
               ->where('region_id', $regionId)
               ->orderBy('name');

        if (! empty($cfg['state_ids'])) {
            $q->whereIn('id', $cfg['state_ids']);
        }

        if ($cfg['active_only'] ?? true) {
            $q->where('is_active', true);
        }

        if ($cfg['package_only'] ?? true) {
            $q->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('packages')
                    ->join('locations', 'locations.id', '=', 'packages.location_id')
                    ->whereColumn('locations.state_id', 'states.id')
                    ->where('packages.is_active', true)
                    ->whereNull('packages.deleted_at');
            });
        }

        if ($cfg['manage_city_only'] ?? false) {
            $this->applyManageCityStateFilter($q);
        }

        return $this->applyHolidayMenuOverrides($q->get(), HolidayMenuSetting::TYPE_STATE);
    }

    /**
     * Restrict a `states` query to states that have a "City & State Page"
     * (ManageCity) of their own, OR at least one city within them that does.
     */
    private function applyManageCityStateFilter(\Illuminate\Database\Query\Builder $q): void
    {
        $q->where(function ($outer) {
            $outer->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('manage_cities')
                    ->whereColumn('manage_cities.state_id', 'states.id')
                    ->where('manage_cities.is_active', true);
            })->orWhereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('manage_cities')
                    ->join('locations', 'locations.id', '=', 'manage_cities.location_id')
                    ->whereColumn('locations.state_id', 'states.id')
                    ->where('manage_cities.is_active', true);
            });
        });
    }

    private function queryCitiesForState(int $stateId, array $cfg, ?string $stateName = null): \Illuminate\Support\Collection
    {
        $q = DB::table('locations')
               ->select('id', 'name', 'slug')
               ->where('state_id', $stateId)
               ->orderBy('name');

        if ($cfg['active_only'] ?? true) {
            $q->where('is_active', true);
        }

        if ($cfg['package_only'] ?? true) {
            // Only cities that have at least one published package
            $q->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('packages')
                    ->whereColumn('packages.location_id', 'locations.id')
                    ->where('packages.is_active', true)
                    ->whereNull('packages.deleted_at');
            });
        }

        if ($cfg['manage_city_only'] ?? false) {
            $this->applyManageCityLocationFilter($q);
        }

        $cities = $q->get();

        // A location sharing its parent state's exact name is usually a placeholder,
        // not a real distinct city — drop it UNLESS it's the only city under the
        // state (e.g. Delhi, Chandigarh — city-states/UTs where the city and state
        // legitimately share a name). Dropping it there would empty the list and
        // hide the entire state from the menu, which is worse than showing the
        // "duplicate".
        if ($stateName !== null) {
            $withoutPlaceholder = $cities->reject(
                fn ($city) => strtolower(trim($city->name)) === strtolower(trim($stateName))
            );

            if ($withoutPlaceholder->isNotEmpty()) {
                $cities = $withoutPlaceholder->values();
            }
        }

        return $this->applyHolidayMenuOverrides($cities, HolidayMenuSetting::TYPE_LOCATION);
    }

    private function queryAllCities(array $cfg): \Illuminate\Support\Collection
    {
        $q = DB::table('locations')
               ->select('id', 'name', 'slug', 'state_id')
               ->orderBy('name');

        if ($cfg['active_only'] ?? true) {
            $q->where('is_active', true);
        }

        if ($cfg['package_only'] ?? true) {
            $q->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('packages')
                    ->whereColumn('packages.location_id', 'locations.id')
                    ->where('packages.is_active', true)
                    ->whereNull('packages.deleted_at');
            });
        }

        if ($cfg['manage_city_only'] ?? false) {
            $this->applyManageCityLocationFilter($q);
        }

        return $this->applyHolidayMenuOverrides($q->get(), HolidayMenuSetting::TYPE_LOCATION);
    }

    /**
     * Restrict a `locations` query to cities that have their own
     * "City & State Page" (ManageCity) row.
     */
    private function applyManageCityLocationFilter(\Illuminate\Database\Query\Builder $q): void
    {
        $q->whereExists(function ($sub) {
            $sub->select(DB::raw(1))
                ->from('manage_cities')
                ->whereColumn('manage_cities.location_id', 'locations.id')
                ->where('manage_cities.is_active', true);
        });
    }

    // ── Holiday Packages menu overrides (sort_order + is_visible) ──
    //
    // The admin "Holiday Packages" builder (/admin/holiday-menu) stores
    // drag-drop order and show/hide state in `holiday_menu_settings`.
    // These auto-tree queries must apply the same overrides so that what
    // the admin sees matches what the public API/header menu serves —
    // otherwise reordering/hiding in the admin never takes effect live.

    private ?\Illuminate\Support\Collection $regionSettingsCache = null;
    private ?\Illuminate\Support\Collection $stateSettingsCache = null;
    private ?\Illuminate\Support\Collection $locationSettingsCache = null;

    private function holidayMenuSettings(string $type): \Illuminate\Support\Collection
    {
        return match ($type) {
            HolidayMenuSetting::TYPE_REGION   => $this->regionSettingsCache   ??= HolidayMenuSetting::mapForType(HolidayMenuSetting::TYPE_REGION),
            HolidayMenuSetting::TYPE_STATE    => $this->stateSettingsCache    ??= HolidayMenuSetting::mapForType(HolidayMenuSetting::TYPE_STATE),
            HolidayMenuSetting::TYPE_LOCATION => $this->locationSettingsCache ??= HolidayMenuSetting::mapForType(HolidayMenuSetting::TYPE_LOCATION),
        };
    }

    /**
     * Remove rows hidden via admin toggle and re-sort by admin drag-drop
     * order (falling back to the incoming DB order for rows with no
     * override yet).
     */
    private function applyHolidayMenuOverrides(\Illuminate\Support\Collection $rows, string $type): \Illuminate\Support\Collection
    {
        $settings = $this->holidayMenuSettings($type);

        return $rows
            ->reject(fn ($row) => ($settings->get($row->id)?->is_visible ?? 1) === 0)
            ->values()
            ->sortBy(fn ($row, $index) => $settings->get($row->id)?->sort_order ?? (900000 + $index))
            ->values();
    }

    // ── Node factory ─────────────────────────────────────────────

    /**
     * Build a serialised node for the API response.
     * IDs use the real DB primary key so the frontend can deep-link items.
     * linked_type distinguishes auto-generated nodes from manual menu_items.
     */
    private function node(
        int    $id,
        string $title,
        string $url,
        string $type,
        array  $children = [],
    ): array {
        return [
            'id'       => $id,
            'title'    => $title,
            'url'      => $url,
            'target'   => '_self',
            'type'     => $type,
            'status'   => 1,
            'children' => $children,
        ];
    }

    private function cityNodes(\Illuminate\Support\Collection $cities): array
    {
        return $cities->map(fn($c) => $this->node(
            id:    $c->id,
            title: $c->name,
            url:   $this->buildUrl('location', $c->slug),
            type:  'location',
        ))->values()->toArray();
    }

    // ──────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────────

    /**
     * Normalise and sanitise mega_settings before DB write.
     * Returns null when content_type is "normal" (nothing to store).
     */
    private function cleanMegaSettings(?array $settings): ?array
    {
        if (empty($settings) || ($settings['content_type'] ?? 'normal') === 'normal') {
            return null;
        }

        return [
            'content_type'   => $settings['content_type'],
            'display_source' => $settings['display_source'] ?? 'auto',
            'display_mode'   => $settings['display_mode']   ?? 'region_state_city',
            'linked_menu_id' => isset($settings['linked_menu_id']) ? (int) $settings['linked_menu_id'] : null,
            'region_ids'     => array_values(array_filter(array_map('intval', $settings['region_ids']  ?? []))),
            'state_ids'      => array_values(array_filter(array_map('intval', $settings['state_ids']   ?? []))),
            'active_only'    => (bool) ($settings['active_only']  ?? true),
            'package_only'   => (bool) ($settings['package_only'] ?? false),
            'manage_city_only' => (bool) ($settings['manage_city_only'] ?? false),
            'banner'         => [
                'image'       => trim($settings['banner']['image']       ?? ''),
                'alt'         => trim($settings['banner']['alt']         ?? ''),
                'title'       => trim($settings['banner']['title']       ?? ''),
                'description' => trim($settings['banner']['description'] ?? ''),
                'cta_text'    => trim($settings['banner']['cta_text']    ?? ''),
                'cta_url'     => trim($settings['banner']['cta_url']     ?? ''),
            ],
        ];
    }

    /**
     * Extract linked_id from validated data.
     * Returns null for custom type or when not provided.
     * For menu_reference, linked_id holds a menus.id.
     */
    private function resolveLinkedId(array $data): ?int
    {
        if ($data['type'] === 'custom') {
            return null;
        }

        return isset($data['linked_id']) && $data['linked_id']
            ? (int) $data['linked_id']
            : null;
    }

}
