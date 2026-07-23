<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarRouteMetaData extends Model{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    
    protected $table = 'car_routes_meta_data';

    protected $fillable = ['route_id','meta_title', 'meta_description', 'meta_keywords','h1_heading','meta_details'];

    public function route()
    {
        return $this->belongsTo(CarRoute::class);
    }
}
