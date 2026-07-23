<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Location extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    use HasFactory;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'locations';

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $fillable = [
        'country_id',
        'state_id',
        'name',
        'slug',
        'is_top_trending',
        'is_active',
        'faq_title',
        'best_time_title',
        'region_id',
        'sort_order',
        'author_name'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function details()
    {
        return $this->hasOne(LocationDetails::class, 'location_id', 'id');
    }

    public function packages()
    {
        return $this->hasMany(Package::class,'location_id','id');
    }

    public function packagesLocation()
    {
       return $this->belongsToMany(
        Package::class,
        'package_locations',
        'location_id',
        'package_id'
    )->distinct();
    }

    public function packagesSource()
    {
        return $this->belongsToMany(
        Package::class,
        'package_source_locations',
        'location_id',
        'package_id'
    )->distinct();
    }

    public function faqs()
    {
        return $this->hasMany(LocationFaq::class,'location_id','id');
    }

    public function bestTimes()
    {
        return $this->hasMany(LocationBestTime::class,'location_id','id');
    }

    public function meta()
    {
        return $this->hasOne(LocationMetaData::class,'location_id','id');
    }

    public function region()
    {
        return $this->belongsTo(\App\Models\Region::class, 'region_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    /**
     * Clean slug for the City Guide URL hierarchy (e.g. "Ooty" -> "ooty"),
     * derived straight from the name — independent of the SEO `slug` column
     * used by the tour-package pages.
     */
    public function getCityGuideSlugAttribute(): string
    {
        return \Illuminate\Support\Str::slug($this->name);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForState($query, int $stateId)
    {
        return $query->where('state_id', $stateId);
    }

    public function scopeForCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }
}
