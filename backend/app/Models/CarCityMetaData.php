<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarCityMetaData extends Model{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    
    protected $table = 'car_city_meta_data';

    protected $fillable = ['city_id','meta_title', 'meta_description', 'meta_keywords','h1_heading','meta_details'];

    public function city()
    {
        return $this->belongsTo(CarCity::class);
    }
}
