<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalWhyVisit extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['festival_id', 'title', 'description', 'sort_order'];

    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }
}
