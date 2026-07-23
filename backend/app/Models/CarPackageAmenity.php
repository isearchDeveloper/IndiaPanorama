<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarPackageAmenity extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['package_id', 'label', 'icon', 'icon_alt', 'description', 'sort_order'];
}
