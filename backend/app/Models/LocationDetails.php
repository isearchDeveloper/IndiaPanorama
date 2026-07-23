<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationDetails extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    use HasFactory;

    protected $table = 'location_details';

    protected $fillable = [
        'location_id',
        'title',
        'sub_title',
        'banner_image',
        'banner_image_alt',
        'about'
    ];

    public function location(){ 
        return $this->belongsTo(Location::class, 'location_id', 'id'); 
    }
    
}
