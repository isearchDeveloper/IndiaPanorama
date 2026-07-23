<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarRouteDetails extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'car_routes_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'route_id',
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
    public function route()
    {
        return $this->belongsTo(CarRoute::class, 'route_id', 'id');
    }
}
