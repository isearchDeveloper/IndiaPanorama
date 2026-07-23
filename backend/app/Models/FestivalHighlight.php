<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalHighlight extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['festival_id', 'image', 'image_alt', 'label', 'sort_order'];

    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }
}
