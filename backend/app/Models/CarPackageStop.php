<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarPackageStop extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'car_package_stops';

    protected $fillable = ['package_id', 'state_id', 'location_id', 'name', 'description', 'attractions', 'sort_order'];

    protected $casts = [
        'attractions' => 'array',
    ];

    public function package()
    {
        return $this->belongsTo(CarPackage::class, 'package_id', 'id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }
}
