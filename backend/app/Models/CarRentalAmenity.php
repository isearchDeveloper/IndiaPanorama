<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarRentalAmenity extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'car_rental_amenities';

    protected $fillable = ['car_rental_content_id', 'icon', 'icon_alt', 'label', 'description', 'sort_order'];

    public function content()
    {
        return $this->belongsTo(CarRentalContent::class, 'car_rental_content_id', 'id');
    }
}
