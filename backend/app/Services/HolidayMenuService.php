<?php

namespace App\Services;

use App\Models\HolidayMenuSetting;
use App\Models\Location;
use Illuminate\Support\Collection;

/**
 * HolidayMenuService
 * ──────────────────
 * Builds the auto-generated Holiday Packages menu tree.
 *
 * Tree hierarchy:  Region  →  State  →  Location (city)
 *
 * Rules:
 *  • Only locations with at least one published package appear.
 *  • Only active locations and states appear.
 *  • Sort order and visibility come from holiday_menu_settings;
 *    fallback to natural DB order when no override exists.
 *  • Two query pattern for packages: packages.location_id (primary)
 *    OR package_locations pivot (secondary / additional locations).
 *
 * All data is loaded in 2 DB hits (no N+1):
 *  1. locations with state & region eager-loaded (+ WHERE EXISTS filter)
 *  2. holiday_menu_settings rows (3 columns, tiny table)
 */
class HolidayMenuService
{
    // ──────────────────────────────────────────────────────────────
    // PUBLIC API
    // ──────────────────────────────────────────────────────────────

    /**
     * Build the full menu tree with admin overrides applied.
     *
     * @return Collection<int, object>  Sorted collection of region nodes.
     *         Each region node has ->states (Collection of state nodes).
     *         Each state node has ->locations (Collection of location nodes).
     */
    public function buildAutoTree(): Collection
    {
        // ── 1. Locations that have packages ──────────────────────
        $locations = $this->fetchActiveLocationsWithPackages();

        if ($locations->isEmpty()) {
            return collect();
        }

        // ── 2. Load all admin setting overrides (3 light queries) ─
        $regionSettings   = HolidayMenuSetting::mapForType(HolidayMenuSetting::TYPE_REGION);
        $stateSettings    = HolidayMenuSetting::mapForType(HolidayMenuSetting::TYPE_STATE);
        $locationSettings = HolidayMenuSetting::mapForType(HolidayMenuSetting::TYPE_LOCATION);

        // ── 3. Assemble tree in memory (O(n)) ────────────────────
        $regionMap = [];

        foreach ($locations as $loc) {
            $region = $loc->region;
            $state  = $loc->state;

            // Skip orphaned data
            if (!$region || !$state) {
                continue;
            }

            // ── Region node ───────────────────────────────────────
            if (!isset($regionMap[$region->id])) {
                $rSetting = $regionSettings->get($region->id);

                $regionMap[$region->id] = (object) [
                    'id'         => $region->id,
                    'name'       => $region->name,
                    'slug'       => $region->slug,
                    'sort_order' => $rSetting?->sort_order ?? ($region->order_seq ?? 999),
                    'is_visible' => $rSetting?->is_visible ?? 1,
                    'states'     => [],   // keyed by state_id while building
                ];
            }

            $regionNode = &$regionMap[$region->id];

            // ── State node ────────────────────────────────────────
            if (!isset($regionNode->states[$state->id])) {
                $sSetting = $stateSettings->get($state->id);

                $regionNode->states[$state->id] = (object) [
                    'id'         => $state->id,
                    'name'       => $state->name,
                    'slug'       => $state->slug,
                    'sort_order' => $sSetting?->sort_order ?? 999,
                    'is_visible' => $sSetting?->is_visible ?? 1,
                    'locations'  => [],  // keyed by location_id while building
                ];
            }

            $stateNode = &$regionNode->states[$state->id];

            // ── Location node ─────────────────────────────────────
            if (!isset($stateNode->locations[$loc->id])) {
                $lSetting = $locationSettings->get($loc->id);

                $stateNode->locations[$loc->id] = (object) [
                    'id'         => $loc->id,
                    'name'       => $loc->name,
                    'slug'       => $loc->slug,
                    'sort_order' => $lSetting?->sort_order ?? ($loc->sort_order ?? 999),
                    'is_visible' => $lSetting?->is_visible ?? 1,
                ];
            }
        }

        // ── 4. Sort & convert to Collection ──────────────────────
        return collect($regionMap)
            ->sortBy('sort_order')
            ->values()
            ->map(function (object $region): object {
                $region->states = collect($region->states)
                    ->sortBy('sort_order')
                    ->values()
                    ->map(function (object $state): object {
                        $state->locations = collect($state->locations)
                            ->sortBy('sort_order')
                            ->values();
                        return $state;
                    });
                return $region;
            });
    }

    /**
     * Compute dashboard stats from a pre-built tree.
     *
     * @param  Collection $tree  Output of buildAutoTree()
     * @return array{regions:int, states:int, locations:int, hidden_regions:int, hidden_states:int, hidden_locations:int}
     */
    public function getStats(Collection $tree): array
    {
        $stats = [
            'regions'          => 0,
            'states'           => 0,
            'locations'        => 0,
            'hidden_regions'   => 0,
            'hidden_states'    => 0,
            'hidden_locations' => 0,
        ];

        foreach ($tree as $region) {
            $stats['regions']++;
            if (!$region->is_visible) {
                $stats['hidden_regions']++;
            }

            foreach ($region->states as $state) {
                $stats['states']++;
                if (!$state->is_visible) {
                    $stats['hidden_states']++;
                }

                foreach ($state->locations as $loc) {
                    $stats['locations']++;
                    if (!$loc->is_visible) {
                        $stats['hidden_locations']++;
                    }
                }
            }
        }

        return $stats;
    }

    // ──────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────────

    /**
     * Single optimised query:
     *  • Active locations only (is_active = 1)
     *  • WHERE EXISTS at least one active, non-deleted package
     *    (checks both packages.location_id and the package_locations pivot)
     *  • Eager-loads state + region to avoid N+1
     */
    private function fetchActiveLocationsWithPackages(): Collection
    {
        return Location::query()
            ->where('is_active', true)
            ->where(function ($q) {
                // Primary: packages.location_id
                $q->whereHas('packages', fn ($p) =>
                    $p->where('is_active', true)
                )
                // Secondary: package_locations pivot
                ->orWhereHas('packagesLocation', fn ($p) =>
                    $p->where('is_active', true)
                );
            })
            ->with([
                'state:id,name,slug,region_id,is_active',
                'region:id,name,slug,order_seq',
            ])
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'state_id', 'region_id', 'sort_order']);
    }
}
