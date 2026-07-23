<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cars';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'primary_image',
        'primary_image_alt',
        'banner_image',
        'banner_image_alt',
        'description',
        'seats',
        'fuel_type',
        'vehicle_type',
        'transmission',
        'luggage_capacity',
        'mileage',
        'specs_title',
        'specs_description',
        'gallery_title',
        'gallery_description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];


    public function category()
    {
        return $this->belongsTo(CarCategory::class, 'category_id', 'id');
    }

    public function galleryImages()
    {
        return $this->hasMany(CarGalleryImage::class, 'car_id', 'id')->orderBy('sort_order');
    }

    public function highlightTags()
    {
        return $this->hasMany(CarHighlightTag::class, 'car_id', 'id')->orderBy('sort_order');
    }

    public function amenities()
    {
        return $this->hasMany(CarAmenity::class, 'car_id', 'id')->orderBy('sort_order');
    }

}
