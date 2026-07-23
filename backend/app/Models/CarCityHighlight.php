<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarCityHighlight extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['city_id', 'title', 'description', 'sort_order'];

    public function city()
    {
        return $this->belongsTo(CarCity::class, 'city_id', 'id');
    }
}
