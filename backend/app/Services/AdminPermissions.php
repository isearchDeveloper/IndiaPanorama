<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Auto-discovers admin modules from registered routes.
 *
 * HOW IT WORKS:
 *   1. Scans all admin.* routes at runtime — no hardcoded route lists.
 *   2. Extracts the "module" from the route name's 2nd segment (admin.{module}.action).
 *   3. Resolves aliases so sub-resources merge into their parent module.
 *   4. Checks config/_modules for display labels & groups (optional — auto-generated if absent).
 *
 * TO ADD A NEW SECTION:
 *   Just add new routes with ->name('admin.your-section.*').
 *   Done. It appears in Admin Management automatically.
 *   If you want a custom label/group, add one line to config/_modules.
 */
class AdminPermissions
{
    /**
     * Resolve the required permission for a given route.
     * Returns null → route is public to all admins (no check needed).
     */
    public static function resolveForRoute(string $routeName): ?string
    {
        $cfg = config('admin_permissions', []);

        // 1. Fixed permissions take priority (enquiries.view, admin.manage, sitemap.manage)
        foreach ($cfg['_fixed'] ?? [] as $permission => $def) {
            foreach ($def['routes'] ?? [] as $prefix) {
                if ($routeName === $prefix || str_starts_with($routeName, $prefix . '.')) {
                    return $permission;
                }
            }
        }

        // 2. Public routes — no permission required (login, dashboard, upload utils, etc.)
        foreach ($cfg['_public'] ?? [] as $pub) {
            if ($routeName === $pub || str_starts_with($routeName, $pub . '.')) {
                return null;
            }
        }

        // 3. Auto-discover: extract module from 2nd route segment, resolve aliases
        $module = static::moduleFor($routeName, $cfg);
        if (!$module) return null;

        return $module . '.' . static::actionFor($routeName);
    }

    /**
     * Resolve the module slug for a route name.
     */
    public static function moduleFor(string $routeName, ?array $cfg = null): ?string
    {
        $cfg ??= config('admin_permissions', []);

        $overrides = $cfg['_route_overrides'] ?? [];
        if (isset($overrides[$routeName])) {
            return $overrides[$routeName];
        }

        $parts   = explode('.', $routeName);
        $segment = $parts[1] ?? null;
        if (!$segment) return null;

        $aliases = $cfg['_aliases'] ?? [];
        return $aliases[$segment] ?? $segment;
    }

    /**
     * Determine action type (view/create/edit/delete) from route name suffix.
     */
    public static function actionFor(string $routeName): string
    {
        $suffix = last(explode('.', $routeName));

        if (in_array($suffix, ['index', 'show', 'data', 'table', 'list', 'search', 'ajax',
                                'export', 'download', 'print', 'available', 'faq', 'faqs',
                                'highlights', 'meta', 'icons', 'pages', 'setting', 'seats'])) {
            return 'view';
        }

        if (in_array($suffix, ['create', 'store', 'import', 'clone', 'duplicate', 'copy',
                                'quick-add', 'generate', 'slug', 'check',
                                'duplicate_check', 'add', 'page'])) {
            return 'create';
        }

        if (in_array($suffix, ['destroy', 'delete', 'bulkDelete', 'bulk-delete',
                                'forceDelete', 'force-delete', 'deleteImage', 'delete-image'])) {
            return 'delete';
        }

        // Everything else: update, toggle, upload, sort, reorder, faq, etc.
        return 'edit';
    }

    /**
     * All valid permission names — used for validation in store/update.
     */
    public static function allNames(): array
    {
        $cfg   = config('admin_permissions', []);
        $names = [];

        // Fixed (non-granular)
        foreach ($cfg['_fixed'] ?? [] as $key => $def) {
            $names[] = $key;
        }

        // Granular: module × 4 actions
        foreach (static::discoverModules($cfg) as $module => $def) {
            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                $names[] = $module . '.' . $action;
            }
        }

        return array_unique($names);
    }

    /**
     * Permission items grouped by group — for the Admin Management create/edit UI.
     */
    public static function forView(): Collection
    {
        $cfg        = config('admin_permissions', []);
        $groupOrder = $cfg['_group_order'] ?? [];
        $items      = collect();

        // Granular modules (auto-discovered)
        foreach (static::discoverModules($cfg) as $module => $def) {
            foreach (['view' => 'View', 'create' => 'Add', 'edit' => 'Edit', 'delete' => 'Delete'] as $action => $label) {
                $items->push((object)[
                    'name'         => $module . '.' . $action,
                    'label'        => $label,
                    'action'       => $action,
                    'group'        => $def['group'],
                    'module_key'   => $module,
                    'module_label' => $def['label'],
                ]);
            }
        }

        // Fixed (non-granular) permissions
        foreach ($cfg['_fixed'] ?? [] as $permName => $def) {
            $items->push((object)[
                'name'         => $permName,
                'label'        => $def['label'],
                'action'       => null,
                'group'        => $def['group'],
                'module_key'   => null,
                'module_label' => null,
            ]);
        }

        // Group and apply custom display order
        $grouped = $items->groupBy('group');

        if ($groupOrder) {
            $ordered = collect();
            foreach ($groupOrder as $g) {
                if ($grouped->has($g)) $ordered[$g] = $grouped[$g];
            }
            // Append any groups not in the order list (new ones land here automatically)
            foreach ($grouped as $g => $gItems) {
                if (!$ordered->has($g)) $ordered[$g] = $gItems;
            }
            return $ordered;
        }

        return $grouped;
    }

    /**
     * Discover all unique module slugs from registered routes, with metadata.
     * Returns: ['module-slug' => ['label' => '...', 'group' => '...']]
     */
    private static function discoverModules(array $cfg): array
    {
        $meta          = $cfg['_modules'] ?? [];
        $publicList    = $cfg['_public']  ?? [];
        $fixedRoutes   = collect($cfg['_fixed'] ?? [])
                            ->flatMap(fn($d) => $d['routes'] ?? [])
                            ->all();

        // Modules explicitly sorted via _group_order + _modules should come first
        $modules = collect(Route::getRoutes()->getRoutesByName())
            ->keys()
            ->filter(fn($name) => str_starts_with($name, 'admin.'))
            ->filter(function ($name) use ($publicList, $fixedRoutes) {
                foreach ($publicList as $pub) {
                    if ($name === $pub || str_starts_with($name, $pub . '.')) return false;
                }
                foreach ($fixedRoutes as $prefix) {
                    if ($name === $prefix || str_starts_with($name, $prefix . '.')) return false;
                }
                return true;
            })
            ->map(fn($name) => static::moduleFor($name, $cfg))
            ->filter()
            ->unique()
            ->values();

        $result = [];
        foreach ($modules as $module) {
            // Skip entries that are pure alias definitions (they have 'alias_of' key)
            if (isset($meta[$module]['alias_of'])) continue;

            $def = $meta[$module] ?? [];
            $result[$module] = [
                'label' => $def['label'] ?? Str::title(str_replace(['-', '_'], ' ', $module)),
                'group' => $def['group'] ?? 'General',
            ];
        }

        return $result;
    }
}
