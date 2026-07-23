<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceSubcategory extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $fillable = [
        'category_id', 'name', 'slug', 'image', 'image_alt', 'description', 'popular_tag',
        'banner_image', 'banner_description', 'meta_title', 'meta_description',
        'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ExperienceCategory::class, 'category_id');
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class, 'subcategory_id')->orderBy('sort_order');
    }
}
