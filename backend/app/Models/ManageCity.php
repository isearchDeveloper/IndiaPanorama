<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManageCity extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'manage_cities';

    protected $fillable = [
        'region_id', 'state_id', 'location_id',
        'title', 'sub_title', 'banner_text', 'about', 'short_description',
        'banner_image', 'banner_image_alt',
        'travel_tips', 'things_to_know', 'religious_tourism',
        'souvenirs_to_shop', 'popular_dishes',
        'is_active', 'is_popular', 'sort_order', 'faq_title',
    ];

    protected $casts = ['is_active' => 'boolean', 'is_popular' => 'boolean'];

    public function region()   { return $this->belongsTo(Region::class,   'region_id'); }
    public function state()    { return $this->belongsTo(State::class,    'state_id'); }
    public function location() { return $this->belongsTo(Location::class, 'location_id'); }

    public function howToReach()  { return $this->hasMany(ManageCityHowToReach::class, 'manage_city_id')->orderBy('sort_order'); }
    public function quickFacts()  { return $this->hasMany(ManageCityQuickFact::class,  'manage_city_id')->orderBy('sort_order'); }
    public function faqs()        { return $this->hasMany(ManageCityFaq::class,         'manage_city_id')->orderBy('sort_order'); }
    public function meta()        { return $this->hasOne(ManageCityMeta::class,          'manage_city_id'); }
    public function topPlaces()   { return $this->hasMany(ManageCityTopPlace::class,    'manage_city_id')->orderBy('sort_order'); }
    public function thingsToDo()  { return $this->hasMany(ManageCityThingToDo::class,   'manage_city_id')->orderBy('sort_order'); }

    public function getDisplayNameAttribute(): string
    {
        return $this->region?->name ?? $this->state?->name ?? $this->location?->name ?? '—';
    }

    public function getParentNameAttribute(): ?string
    {
        return $this->location?->state?->name ?? null;
    }

    public function getTypeAttribute(): string
    {
        if ($this->region_id) return 'Region';
        return $this->state_id ? 'State' : 'City';
    }
}
