<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\HeaderMenuController;
use App\Http\Requests\StoreMenuItemRequest;
use App\Http\Requests\UpdateMenuItemRequest;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\MenuBuilderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * MenuItemController
 *
 * All item-level CRUD + reorder, returning JSON.
 * Every response follows the same envelope:
 * {
 *   "success": true|false,
 *   "message": "...",
 *   "item":    {...}   // present on store/update/toggle
 *   "html":    "..."   // blade partial, present on store (for DOM insert)
 * }
 */
class MenuItemController extends Controller
{
    public function __construct(
        private readonly MenuBuilderService $service
    ) {}

    // ──────────────────────────────────────────────────────────────
    // STORE  — Create a new item
    // POST /admin/menus/{menu}/items
    // ──────────────────────────────────────────────────────────────

    public function store(StoreMenuItemRequest $request, Menu $menu): JsonResponse
    {
        $item = $this->service->createItem($menu, $request->validated());

        // Bust public API cache so frontend sees the new item immediately
        HeaderMenuController::flushCache();

        // Re-attach _children bag so the partial can render
        $item->_children = collect();

        // Compute depth from parent chain
        $depth = $this->resolveDepth($item);

        // Render Blade partial for instant DOM insertion (no page reload)
        $html = view('admin.menus.partials.item-row', [
            'item'   => $item,
            'urlMap' => [$item->id => $item->resolveUrl()],
            'depth'  => $depth,
        ])->render();

        return response()->json([
            'success' => true,
            'message' => 'Item "' . $item->title . '" added successfully.',
            'item'    => $this->formatItem($item),
            'html'    => $html,
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────
    // SHOW  — Get single item for Edit modal prefill
    // GET /admin/menu-items/{item}
    // ──────────────────────────────────────────────────────────────

    public function show(MenuItem $item): JsonResponse
    {
        return response()->json([
            'success' => true,
            'item'    => $this->formatItem($item),
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // UPDATE  — Edit an existing item
    // PUT /admin/menu-items/{item}
    // ──────────────────────────────────────────────────────────────

    public function update(UpdateMenuItemRequest $request, MenuItem $item): JsonResponse
    {
        $item = $this->service->updateItem($item, $request->validated());

        HeaderMenuController::flushCache();

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully.',
            'item'    => $this->formatItem($item),
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // DESTROY  — Delete item (children cascade via FK)
    // DELETE /admin/menu-items/{item}
    // ──────────────────────────────────────────────────────────────

    public function destroy(MenuItem $item): JsonResponse
    {
        $title    = $item->title;
        $children = $item->children()->count();

        $this->service->deleteItem($item);

        HeaderMenuController::flushCache();

        $msg = 'Item "' . $title . '" deleted.';
        if ($children > 0) {
            $msg .= ' ' . $children . ' child item(s) also removed.';
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // TOGGLE  — Flip status 1 ↔ 0
    // POST /admin/menu-items/{item}/toggle
    // ──────────────────────────────────────────────────────────────

    public function toggle(MenuItem $item): JsonResponse
    {
        $item = $this->service->toggleStatus($item);

        HeaderMenuController::flushCache();

        return response()->json([
            'success' => true,
            'status'  => $item->status,
            'message' => 'Item ' . ($item->status ? 'enabled' : 'hidden') . '.',
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // REORDER  — Drag-drop save
    // POST /admin/menus/{menu}/items/reorder
    // ──────────────────────────────────────────────────────────────

    public function reorder(Request $request, Menu $menu): JsonResponse
    {
        $validated = $request->validate([
            'items'              => ['required', 'array', 'min:1'],
            'items.*.id'         => ['required', 'integer', 'exists:menu_items,id'],
            'items.*.parent_id'  => ['nullable', 'integer'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $this->service->reorder($menu, $validated['items']);

        HeaderMenuController::flushCache();

        return response()->json([
            'success' => true,
            'message' => 'Order saved.',
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────────

    /**
     * Consistent JSON shape returned for every item operation.
     */
    private function formatItem(MenuItem $item): array
    {
        return [
            'id'            => $item->id,
            'menu_id'       => $item->menu_id,
            'parent_id'     => $item->parent_id,
            'title'         => $item->title,
            'type'          => $item->type,
            'type_label'    => $item->typeLabel(),
            'type_badge'    => $item->typeBadgeClass(),
            'type_icon'     => $item->typeIcon(),
            'linked_id'     => $item->linked_id,
            'url'           => $item->url,
            'resolved_url'  => $item->resolveUrl(),
            'target'        => $item->target,
            'status'        => $item->status,
            'sort_order'    => $item->sort_order,
            'is_active'     => $item->isActive(),
            'is_root'       => $item->isRoot(),
            'is_mega_menu'  => $item->isMegaMenu(),
            'mega_settings' => $item->resolvedMegaSettings(),
        ];
    }

    /**
     * Compute depth (0/1/2) by walking parent chain.
     * Used only on fresh inserts — small overhead is acceptable.
     */
    private function resolveDepth(MenuItem $item): int
    {
        if (! $item->parent_id) return 0;

        $parent = MenuItem::find($item->parent_id);
        if (! $parent || ! $parent->parent_id) return 1;

        return 2;
    }
}
