<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\HeaderMenuController;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Services\MenuBuilderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * MenuManagerController
 *
 * Handles:
 *   - Builder page (show)
 *   - Menu CRUD  (store / destroy) — admins can create unlimited custom menus
 *   - AJAX endpoint for loading available link items (including menu_reference)
 *
 * Item CRUD (add / edit / delete / reorder / toggle) is in MenuItemController.
 */
class MenuManagerController extends Controller
{
    public function __construct(
        private readonly MenuBuilderService $service
    ) {}

    // ──────────────────────────────────────────────────────────────
    // INDEX — redirect to Header menu builder
    // ──────────────────────────────────────────────────────────────

    public function index(): \Illuminate\Http\RedirectResponse
    {
        $header = Menu::where('slug', 'header')->firstOrFail();
        return redirect()->route('admin.menus.show', $header);
    }

    // ──────────────────────────────────────────────────────────────
    // SHOW — Menu builder page
    // ──────────────────────────────────────────────────────────────

    public function show(Menu $menu): View
    {
        $menus = Menu::orderBy('sort_order')->get();

        $tree     = $this->service->buildTree($menu, activeOnly: false);
        $allItems = \App\Models\MenuItem::where('menu_id', $menu->id)->get();
        $urlMap   = $this->service->bulkResolveUrls($allItems);
        $stats    = $this->service->getStats($menu);

        return view('admin.menus.index', compact(
            'menu',
            'menus',
            'tree',
            'urlMap',
            'stats',
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // STORE  — Create a new custom menu
    // POST /admin/menus
    // ──────────────────────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        // Generate a unique slug from the name
        $base = Str::slug($validated['name']);
        $slug = $base;
        $n    = 1;
        while (Menu::where('slug', $slug)->exists()) {
            $slug = $base . '-' . (++$n);
        }

        try {
            $menu = Menu::create([
                'name'       => trim($validated['name']),
                'slug'       => $slug,
                'location'   => 'custom',
                'is_active'  => true,
                'sort_order' => (Menu::max('sort_order') ?? 0) + 1,
                // is_system defaults to false via DB default — not set here
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Rare race: two admins saved the same menu name in the same instant and both
            // passed the "slug not taken yet" check before either insert committed.
            return response()->json(['success' => false, 'message' => 'That menu name just got taken by another save — please try again.'], 409);
        }

        HeaderMenuController::flushCache();

        return response()->json([
            'success'  => true,
            'message'  => 'Menu "' . $menu->name . '" created.',
            'menu'     => [
                'id'   => $menu->id,
                'name' => $menu->name,
                'slug' => $menu->slug,
                'url'  => route('admin.menus.show', $menu),
            ],
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────
    // DESTROY  — Delete a custom menu (system menus are protected)
    // DELETE /admin/menus/{menu}
    // ──────────────────────────────────────────────────────────────

    public function destroy(Menu $menu): JsonResponse
    {
        if ($menu->is_system) {
            return response()->json([
                'success' => false,
                'message' => '"' . $menu->name . '" is a system menu and cannot be deleted.',
            ], 422);
        }

        $name = $menu->name;
        $menu->delete(); // menu_items cascade via FK

        HeaderMenuController::flushCache();

        return response()->json([
            'success' => true,
            'message' => 'Menu "' . $name . '" and all its items deleted.',
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // SETTINGS — GET / POST /admin/menus/{menu}/settings
    // ──────────────────────────────────────────────────────────────

    /**
     * GET /admin/menus/{menu}/settings
     * Returns current display settings as JSON.
     */
    public function settings(Menu $menu): JsonResponse
    {
        return response()->json([
            'success'          => true,
            'display_mode'     => $menu->display_mode ?? Menu::DISPLAY_MANUAL,
            'display_settings' => $menu->resolvedDisplaySettings(),
            'display_modes'    => Menu::DISPLAY_MODES,
        ]);
    }

    /**
     * POST /admin/menus/{menu}/settings
     * Saves display mode + filter settings.
     */
    public function updateSettings(Menu $menu, Request $request): JsonResponse
    {
        $data = $request->validate([
            'display_mode'              => ['required', 'string', 'in:' . implode(',', array_keys(Menu::DISPLAY_MODES))],
            'display_settings'          => ['nullable', 'array'],
            'display_settings.region_ids'   => ['nullable', 'array'],
            'display_settings.region_ids.*' => ['integer', 'min:1'],
            'display_settings.state_ids'    => ['nullable', 'array'],
            'display_settings.state_ids.*'  => ['integer', 'min:1'],
            'display_settings.active_only'  => ['nullable', 'boolean'],
            'display_settings.package_only' => ['nullable', 'boolean'],
            'display_settings.manage_city_only' => ['nullable', 'boolean'],
        ]);

        $menu->update([
            'display_mode'     => $data['display_mode'],
            'display_settings' => $data['display_settings'] ?? null,
        ]);

        HeaderMenuController::flushCache();

        return response()->json([
            'success' => true,
            'message' => 'Menu display settings saved.',
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // AVAILABLE ITEMS — AJAX for "link to" select dropdown
    // GET /admin/menu-items/available/{type}
    // ──────────────────────────────────────────────────────────────

    public function available(Request $request, string $type): JsonResponse
    {
        $allowed = array_keys(\App\Models\MenuItem::TYPES);

        if (! in_array($type, $allowed, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid type. Allowed: ' . implode(', ', $allowed),
            ], 422);
        }

        // For menu_reference, exclude the currently-open menu to prevent self-reference
        $rawExclude    = $type === 'menu_reference' ? (int) $request->query('exclude_menu', 0) : 0;
        $excludeMenuId = $rawExclude ?: null;

        $items = $this->service->getAvailableItems($type, $excludeMenuId);

        return response()->json([
            'success' => true,
            'type'    => $type,
            'count'   => count($items),
            'items'   => $items,
        ]);
    }
}
