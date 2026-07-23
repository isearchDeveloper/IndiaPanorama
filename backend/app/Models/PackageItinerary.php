<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PackageItinerary extends Model {
    use \Illuminate\Database\Eloquent\SoftDeletes;


    protected $table = 'package_itinerary';
    
    protected $fillable = ['package_id','title','details'];

    public function package(){ 
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }
}
