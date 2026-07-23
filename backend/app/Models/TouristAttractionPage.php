<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TouristAttractionPage extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'tourist_attraction_pages';

    protected $fillable = [
        'state_id', 'location_id', 'title', 'banner_image', 'banner_image_alt', 'short_description',
        'faq_title', 'faq_sub_title',
        'meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details',
        'is_active', 'is_featured', 'is_popular', 'sort_order',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
        'is_popular'  => 'boolean',
    ];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function bestTimes()
    {
        return $this->hasMany(TouristAttractionPageBestTime::class, 'page_id', 'id')->orderBy('sort_order');
    }

    public function faqs()
    {
        return $this->hasMany(TouristAttractionPageFaq::class, 'page_id', 'id')->orderBy('sort_order');
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
