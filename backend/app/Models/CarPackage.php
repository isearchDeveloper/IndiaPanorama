<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarPackage extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'car_packages';

    protected $fillable = [
        'name', 'slug', 'banner_image', 'banner_image_alt', 'description',
        'about_title', 'about_image', 'about_image_alt', 'about_description',
        'duration_text', 'best_season', 'ideal_for',
        'faq_title', 'faq_sub_title',
        'meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details',
        'sort_order', 'is_active', 'is_popular',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
    ];

    public function cars()
    {
        return $this->belongsToMany(Car::class, 'car_package_details', 'package_id', 'car_id')
                    ->with(['category'])
                    ->withTimestamps();
    }

    public function stops()
    {
        return $this->hasMany(CarPackageStop::class, 'package_id', 'id')->orderBy('sort_order');
    }

    public function faqs()
    {
        return $this->hasMany(CarPackageFaq::class, 'package_id', 'id');
    }

    public function amenities()
    {
        return $this->hasMany(CarPackageAmenity::class, 'package_id', 'id')->orderBy('sort_order');
    }
}
