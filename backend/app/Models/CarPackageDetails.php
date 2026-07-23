<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarPackageDetails extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'car_package_details';

    protected $fillable = ['package_id', 'car_id'];
}
