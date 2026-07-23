<?php

namespace App\Models;

/**
 * City model — semantic alias for the `locations` table.
 *
 * Cities and tourist destinations are stored in the `locations` table.
 * This model provides a clean "City" interface while reusing all
 * Location relationships and scopes.
 *
 * Usage:  City::where('state_id', $id)->active()->get()
 */
class City extends Location
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    // Inherits: table = 'locations', all fillable, all relationships, all scopes

    // ─── Additional scopes (city-specific) ───────────────────────────────────

    /**
     * Cities belonging to a given state.
     */
    public function scopeInState($query, int $stateId)
    {
        return $query->where('state_id', $stateId);
    }

    /**
     * Cities belonging to a given country.
     */
    public function scopeInCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Top-trending cities only.
     */
    public function scopeTopTrending($query)
    {
        return $query->where('is_top_trending', true);
    }

    /**
     * Active cities only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
