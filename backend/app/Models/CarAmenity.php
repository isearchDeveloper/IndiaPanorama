<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarAmenity extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['car_id', 'icon', 'icon_alt', 'label', 'description', 'sort_order'];
}
