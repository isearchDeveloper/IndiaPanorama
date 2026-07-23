<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarCity extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'car_city';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'location',
        'thumbnail_image',
        'thumbnail_alt',
        'is_active',
        'faq_title',
        'faq_sub_title',
        'is_popular',
        'display_label',
        'features_title',
        'benefits_title',
        'why_choose_title',
        'why_choose_subtitle',
        'why_choose_enabled',
        'popular_locations_title',
        'popular_locations_subtitle',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'why_choose_enabled' => 'boolean',
    ];

    /**
     * Relationship: a route has many cars through the pivot `car_city_details`
     */
    public function cars()
    {
        return $this->belongsToMany(Car::class, 'car_city_details', 'city_id', 'car_id')
                    ->with(['category']) // ✅ optional: load related car category
                    ->withTimestamps();
    }

    /**
     * (Optional) Metadata relationship — adjust model if needed
     */
    public function meta()
    {
        return $this->hasOne(CarCityMetaData::class, 'city_id', 'id');
    }

    public function details()
    {
        return $this->hasOne(CarCityPageDetails::class, 'city_id', 'id');
    }

    public function faqs()
    {
        return $this->hasMany(CarCityFaq::class,'city_id','id');
    }

    public function features()
    {
        return $this->hasMany(CarCityFeature::class, 'city_id', 'id')->orderBy('sort_order');
    }

    public function benefits()
    {
        return $this->hasMany(CarCityBenefit::class, 'city_id', 'id')->orderBy('sort_order');
    }

    public function galleryImages()
    {
        return $this->hasMany(CarCityGalleryImage::class, 'city_id', 'id')->orderBy('sort_order');
    }

    public function whyChooseStats()
    {
        return $this->hasMany(CarCityWhyChooseStat::class, 'city_id', 'id')->orderBy('sort_order');
    }

    public function highlights()
    {
        return $this->hasMany(CarCityHighlight::class, 'city_id', 'id')->orderBy('sort_order');
    }
}
