<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegionFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'region_faqs';
     
    protected $fillable = [
        'region_id',
        'question',
        'answer'
    ];

    public function region(){
        return $this->belongsTo(Region::class);
    }
}
