<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarRentalGalleryImage extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['car_rental_content_id', 'image', 'image_alt', 'sort_order'];
}
