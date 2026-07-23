<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalPlace extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['festival_id', 'image', 'image_alt', 'name', 'sort_order'];

    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }
}
