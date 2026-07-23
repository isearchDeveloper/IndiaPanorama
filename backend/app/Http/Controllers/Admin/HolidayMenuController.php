<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\HeaderMenuController;
use App\Models\HolidayMenuSetting;
use App\Services\HolidayMenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * HolidayMenuController
 * ──────────────────────
 * Manages the auto-generated "Holiday Packages" menu.
 *
 * Admins CAN:
 *   • View the auto-built tree (read-only structure)
 *   • Drag-drop to reorder regions, states, and cities
 *   • Toggle visibility of any node
 *
 * Admins CANNOT:
 *   • Add / remove locations manually
 *   • Rename any items (names come from source tables)
 *   • Delete nodes (removing a package from a location removes it from the tree automatically)
 */
class HolidayMenuController extends Controller
{
    public function __construct(protected HolidayMenuService $service) {}

    // ──────────────────────────────────────────────────────────────
    // SHOW — main builder page
    // ──────────────────────────────────────────────────────────────

    public function show(): View
    {
        $tree  = $this->service->buildAutoTree();
        $stats = $this->service->getStats($tree);
        $menus = \App\Models\Menu::orderBy('sort_order')->get();

        return view('admin.holiday-menu.index', compact('tree', 'stats', 'menus'));
    }

    // ──────────────────────────────────────────────────────────────
    // REORDER — drag-drop sort update (AJAX)
    // ──────────────────────────────────────────────────────────────

    /**
     * POST /admin/holiday-menu/reorder
     *
     * Body: { type: "region"|"state"|"location", ids: [1,4,2,…] }
     * Saves the new sort_order for each ID at its array position.
     */
    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type'   => ['required', 'in:region,state,location'],
            'ids'    => ['required', 'array', 'min:1'],
            'ids.*'  => ['required', 'integer', 'min:1'],
        ]);

        HolidayMenuSetting::bulkReorder($data['type'], $data['ids']);

        HeaderMenuController::flushCache();

        return response()->json(['success' => true, 'message' => 'Order saved.']);
    }

    // ──────────────────────────────────────────────────────────────
    // TOGGLE — show / hide a node (AJAX)
    // ──────────────────────────────────────────────────────────────

    /**
     * POST /admin/holiday-menu/toggle
     *
     * Body: { type: "region"|"state"|"location", id: 7 }
     * Flips is_visible and returns the new value.
     */
    public function toggle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:region,state,location'],
            'id'   => ['required', 'integer', 'min:1'],
        ]);

        $newVisible = HolidayMenuSetting::toggleVisibility($data['type'], $data['id']);

        HeaderMenuController::flushCache();

        return response()->json([
            'success'    => true,
            'is_visible' => $newVisible,
            'message'    => $newVisible ? 'Item is now visible.' : 'Item is now hidden.',
        ]);
    }
}
