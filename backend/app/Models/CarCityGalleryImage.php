<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarCityGalleryImage extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'car_city_gallery_images';
    protected $fillable = ['city_id', 'image', 'image_alt', 'sort_order'];

    public function carCity()
    {
        return $this->belongsTo(CarCity::class, 'city_id');
    }
}
