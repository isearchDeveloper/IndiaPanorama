<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarRouteHighlight extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'car_route_highlights';

    protected $fillable = ['route_id', 'title', 'description', 'sort_order'];

    public function route()
    {
        return $this->belongsTo(CarRoute::class, 'route_id', 'id');
    }
}
