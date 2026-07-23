<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarCityFaq extends Model{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    
    protected $table = 'car_city_faqs';

    protected $fillable = ['city_id', 'question', 'answer'];

    public function city()
    {
        return $this->belongsTo(CarCity::class, 'city_id', 'id');
    }
}
