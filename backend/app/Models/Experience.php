<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $fillable = [
        'category_id', 'subcategory_id', 'state_id', 'location_id', 'title', 'slug', 'tagline', 'description',
        'banner_image_alt', 'best_time', 'duration', 'entry_fee', 'location_text',
        'meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details',
        'sort_order', 'is_active', 'is_popular',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'is_popular' => 'boolean',
    ];

    /** No dedicated banner_image column — the banner is always the gallery image with the lowest sort_order. */
    protected $appends = ['banner_image'];

    public function getBannerImageAttribute(): ?string
    {
        $first = $this->relationLoaded('galleryImages') ? $this->galleryImages->first() : $this->galleryImages()->first();

        return $first?->image;
    }

    /** Always present — every experience belongs to a category, with or without a subcategory. */
    public function category()
    {
        return $this->belongsTo(ExperienceCategory::class, 'category_id');
    }

    /** Optional — a finer-grained grouping within the category. */
    public function subcategory()
    {
        return $this->belongsTo(ExperienceSubcategory::class, 'subcategory_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function galleryImages()
    {
        return $this->hasMany(ExperienceGalleryImage::class, 'experience_id')->orderBy('sort_order');
    }

    public function highlights()
    {
        return $this->hasMany(ExperienceHighlight::class, 'experience_id')->orderBy('sort_order');
    }

    public function quickInfos()
    {
        return $this->hasMany(ExperienceQuickInfo::class, 'experience_id')->orderBy('sort_order');
    }

    public function faqs()
    {
        return $this->hasMany(ExperienceFaq::class, 'experience_id')->orderBy('sort_order');
    }
}
