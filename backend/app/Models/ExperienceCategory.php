<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceCategory extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $fillable = [
        'name', 'slug', 'image', 'image_alt', 'description',
        'banner_title', 'banner_tagline', 'banner_image', 'banner_image_alt',
        'intro_image',
        'meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details',
        'faq_title', 'faq_sub_title',
        'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subcategories()
    {
        return $this->hasMany(ExperienceSubcategory::class, 'category_id')->orderBy('sort_order');
    }

    /** Experiences filed directly under this category (no subcategory). */
    public function experiences()
    {
        return $this->hasMany(Experience::class, 'category_id');
    }

    public function quickInfos()
    {
        return $this->hasMany(ExperienceCategoryQuickInfo::class, 'category_id')->orderBy('sort_order');
    }

    public function perfectFors()
    {
        return $this->hasMany(ExperienceCategoryPerfectFor::class, 'category_id')->orderBy('sort_order');
    }

    public function popularCities()
    {
        return $this->hasMany(ExperienceCategoryPopularCity::class, 'category_id')->orderBy('sort_order');
    }

    public function faqs()
    {
        return $this->hasMany(ExperienceCategoryFaq::class, 'category_id')->orderBy('sort_order');
    }
}
