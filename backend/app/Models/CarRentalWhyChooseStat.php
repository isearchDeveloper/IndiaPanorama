<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarRentalWhyChooseStat extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['car_rental_content_id', 'icon', 'icon_alt', 'label', 'sort_order'];
}
