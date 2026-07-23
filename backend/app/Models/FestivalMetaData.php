<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalMetaData extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'festival_meta_data';

    protected $fillable = [
        'festival_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'h1_heading',
        'meta_details',
    ];

    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }
}
