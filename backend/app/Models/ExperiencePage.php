<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExperiencePage extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $fillable = [
        'state_id', 'location_id', 'title', 'banner_image', 'banner_image_alt', 'short_description',
        'faq_title', 'faq_sub_title', 'activities_title', 'highlights_title',
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
        return $this->hasMany(ExperiencePageFaq::class, 'page_id', 'id')->orderBy('sort_order');
    }

    public function activities()
    {
        return $this->hasMany(ExperiencePageActivity::class, 'page_id', 'id')->orderBy('sort_order');
    }

    public function highlights()
    {
        return $this->hasMany(ExperiencePageHighlight::class, 'page_id', 'id')->orderBy('sort_order');
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
