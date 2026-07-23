<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PackageDetail extends Model {
    use \Illuminate\Database\Eloquent\SoftDeletes;


    protected $table = 'package_details';

    protected $casts = [
        'facilities' => 'array',
    ];

    protected $fillable = ['package_id','duration_days','duration_nights','tour_highlights','destination_covered_description','facilities'];

    public function package(){
        return $this->belongsTo(Package::class,'package_id','id');
    }
}
