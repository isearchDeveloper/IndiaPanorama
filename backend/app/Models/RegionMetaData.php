<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegionMetaData extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'regions_meta_data';
    
    protected $fillable = [
        'region_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'h1_heading',
        'meta_details'
    ];

    public function region(){
        return $this->belongsTo(Region::class);
    }
}
