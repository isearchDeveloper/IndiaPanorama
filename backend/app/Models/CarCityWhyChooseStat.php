<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarCityWhyChooseStat extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['city_id', 'label', 'sort_order'];
}
