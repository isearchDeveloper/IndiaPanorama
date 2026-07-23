<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarRouteFaq extends Model{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    
    protected $table = 'car_routes_faqs';

    protected $fillable = ['route_id', 'question', 'answer'];

    public function route()
    {
        return $this->belongsTo(CarRoute::class, 'route_id', 'id');
    }
}
