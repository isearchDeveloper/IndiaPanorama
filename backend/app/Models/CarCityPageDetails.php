<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarCityPageDetails extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    use HasFactory;

    protected $table = 'car_city_page_details';

    protected $fillable = [
        'id',
        'city_id',
        'title',
        'description',
        'banner_image',
        'banner_image_alt',
        'gallery_title',
        'gallery_description',
    ];


    public function city()
    {
        return $this->belongsTo(Page::class, 'city_id', 'id');
    }
}
