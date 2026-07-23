<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarRentalChecklistItem extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['car_rental_content_id', 'text', 'sort_order'];
}
