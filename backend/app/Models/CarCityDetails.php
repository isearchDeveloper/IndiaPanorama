<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarCityDetails extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'car_city_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'city_id',
        'car_id',
    ];

    /**
     * Relationship: CarRouteDetails belongs to Car
     */
    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id', 'id');
    }

    /**
     * Relationship: CarRouteDetails belongs to CarRoute
     */
    public function city()
    {
        return $this->belongsTo(CarCity::class, 'city_id', 'id');
    }
}
