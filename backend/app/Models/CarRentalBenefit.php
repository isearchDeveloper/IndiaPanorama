<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarRentalBenefit extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['car_rental_content_id', 'text', 'sort_order'];
}
