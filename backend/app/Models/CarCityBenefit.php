<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarCityBenefit extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'car_city_benefits';
    protected $fillable = ['city_id', 'text', 'sort_order'];
}
