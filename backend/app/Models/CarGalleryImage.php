<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarGalleryImage extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['car_id', 'image', 'image_alt', 'sort_order'];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
