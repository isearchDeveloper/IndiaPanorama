<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegionDetails extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'regions_details';
    
    protected $fillable = [
        'region_id',
        'title',
        'sub_title',
        'banner_image',
        'banner_image_alt',
        'home_image',
        'home_image_alt',
        'about',
        'author_name'
    ];

    public function region(){
        return $this->belongsTo(Region::class);
    }
}
