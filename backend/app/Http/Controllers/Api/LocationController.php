<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Cascade Dropdown API Controller
 *
 * Endpoints:
 *   GET /api/v1/countries                        → All countries
 *   GET /api/v1/states?country_id={id}           → States for a country
 *   GET /api/v1/cities?state_id={id}             → Cities for a state
 *   GET /api/v1/cities?country_id={id}           → All cities for a country (flat)
 */
class LocationController extends Controller
{
    private function success(mixed $data, string $message = 'Data fetched successfully'): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    private function error(string $message, int $code = 422): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'data'    => [],
        ], $code);
    }

    // ─── Countries ────────────────────────────────────────────────────────────

    /**
     * GET /api/v1/countries
     * Returns all countries ordered by name.
     */
    public function countries(): JsonResponse
    {
        $countries = Cache::remember('api_countries', 60, function () {
            return Country::orderBy('name')
                          ->get(['id', 'name', 'code', 'slug']);
        });

        return $this->success($countries);
    }

    // ─── States ───────────────────────────────────────────────────────────────

    /**
     * GET /api/v1/states?country_id={id}
     * Returns active states/UTs for the given country, ordered by name.
     */
    public function states(Request $request): JsonResponse
    {
        $request->validate([
            'country_id' => 'required|integer|exists:countries,id',
        ]);

        $countryId = (int) $request->country_id;

        $states = Cache::remember("api_states_country_{$countryId}", 60, function () use ($countryId) {
            return State::where('country_id', $countryId)
                        ->where('is_active', true)
                        ->with('region:id,name')
                        ->orderBy('name')
                        ->get(['id', 'country_id', 'region_id', 'name', 'slug']);
        });

        return $this->success($states);
    }

    // ─── Cities ───────────────────────────────────────────────────────────────

    /**
     * GET /api/v1/cities?state_id={id}
     * Returns active cities for the given state, ordered by sort_order then name.
     *
     * GET /api/v1/cities?country_id={id}
     * Returns all active cities for a country (flat list — use for non-India countries).
     */
    public function cities(Request $request): JsonResponse
    {
        // Validate: at least one of state_id or country_id is required
        if (!$request->hasAny(['state_id', 'country_id'])) {
            return $this->error('Provide state_id or country_id parameter.');
        }

        $request->validate([
            'state_id'   => 'nullable|integer|exists:states,id',
            'country_id' => 'nullable|integer|exists:countries,id',
        ]);

        if ($request->filled('state_id')) {
            $stateId = (int) $request->state_id;

            $cities = Cache::remember("api_cities_state_{$stateId}", 60, function () use ($stateId) {
                return Location::where('state_id', $stateId)
                               ->where('is_active', true)
                               ->orderBy('sort_order')
                               ->orderBy('name')
                               ->get(['id', 'country_id', 'state_id', 'name', 'slug']);
            });

            return $this->success($cities);
        }

        // Flat list for a country (no state breakdown)
        $countryId = (int) $request->country_id;

        $cities = Cache::remember("api_cities_country_{$countryId}", 60, function () use ($countryId) {
            return Location::where('country_id', $countryId)
                           ->where('is_active', true)
                           ->orderBy('sort_order')
                           ->orderBy('name')
                           ->get(['id', 'country_id', 'state_id', 'name', 'slug']);
        });

        return $this->success($cities);
    }

    // ─── Grouped (State → Cities) ─────────────────────────────────────────────

    /**
     * GET /api/v1/states-with-cities?country_id={id}
     * Returns states with their nested cities for a country.
     * Useful for rendering a grouped dropdown or accordion.
     */
    public function statesWithCities(Request $request): JsonResponse
    {
        $request->validate([
            'country_id' => 'required|integer|exists:countries,id',
        ]);

        $countryId = (int) $request->country_id;

        $data = Cache::remember("api_states_cities_country_{$countryId}", 60, function () use ($countryId) {
            return State::where('country_id', $countryId)
                        ->where('is_active', true)
                        ->with(['cities' => function ($q) {
                            $q->where('is_active', true)
                              ->orderBy('sort_order')
                              ->orderBy('name')
                              ->select('id', 'country_id', 'state_id', 'name', 'slug');
                        }])
                        ->orderBy('name')
                        ->get(['id', 'country_id', 'region_id', 'name', 'slug']);
        });

        return $this->success($data);
    }
}
