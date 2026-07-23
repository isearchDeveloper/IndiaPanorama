<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class State extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'states';

    protected $fillable = [
        'country_id',
        'region_id',
        'name',
        'slug',
        'is_active',
        'faq_title',
        'best_time_title',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    /** State belongs to a Country */
    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /** State belongs to a Region (e.g. "North India") */
    public function region(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * Cities (Locations) that belong to this state.
     * Use ->cities() for semantic clarity; ->locations() for backward compat.
     */
    public function cities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Location::class, 'state_id');
    }

    /** Alias for backward compatibility */
    public function locations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->cities();
    }

    public function details(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StateDetails::class);
    }

    public function faqs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StateFaq::class);
    }

    public function bestTimes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StateBestTime::class);
    }

    public function meta(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StateMetaData::class);
    }

    /** Per-state "Festivals of {State}" landing page content. */
    public function festivalPage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FestivalStatePage::class);
    }

    /**
     * Clean slug for the City Guide URL hierarchy (e.g. "Tamil Nadu" -> "tamil-nadu"),
     * derived straight from the name — independent of the SEO `slug` column
     * (e.g. "tamil-nadu-tour-packages") used by the tour-package pages.
     */
    public function getCityGuideSlugAttribute(): string
    {
        return \Illuminate\Support\Str::slug($this->name);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /** Only active states */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Filter by country */
    public function scopeForCountry(Builder $query, int $countryId): Builder
    {
        return $query->where('country_id', $countryId);
    }

    /** Filter by region */
    public function scopeForRegion(Builder $query, int $regionId): Builder
    {
        return $query->where('region_id', $regionId);
    }
}
