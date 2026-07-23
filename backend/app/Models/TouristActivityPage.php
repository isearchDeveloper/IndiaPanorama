<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TouristActivityPage extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'tourist_activity_pages';

    protected $fillable = [
        'state_id', 'location_id', 'title', 'banner_image', 'banner_image_alt', 'short_description',
        'about_image', 'about_image_alt',
        'experiences_title',
        'waterfalls_title', 'things_to_do_title', 'activities_in_city_sub_title',
        'faq_title', 'faq_sub_title',
        'meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details',
        'is_active', 'is_featured', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function faqs()
    {
        return $this->hasMany(TouristActivityPageFaq::class, 'page_id', 'id')->orderBy('sort_order');
    }

    public function experiences()
    {
        return $this->hasMany(TouristActivityPageExperience::class, 'page_id', 'id')->orderBy('sort_order');
    }

    public function waterfalls()
    {
        return $this->hasMany(TouristActivityPageWaterfall::class, 'page_id', 'id')->orderBy('sort_order');
    }

    public function thingsToDo()
    {
        return $this->hasMany(TouristActivityPageThingToDo::class, 'page_id', 'id')->orderBy('sort_order');
    }

    public function getSubjectAttribute()
    {
        return $this->location ?: $this->state;
    }

    public function getSubjectTypeAttribute(): string
    {
        return $this->location_id ? 'City' : 'State';
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->subject?->name ?? '');
    }
}
