<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarDestinationDetails extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'car_destination_details';

    protected $fillable = ['destination_id', 'car_id'];
}
