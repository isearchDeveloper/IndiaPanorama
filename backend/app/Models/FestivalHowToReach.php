<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalHowToReach extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'festival_how_to_reach';

    protected $fillable = ['festival_id', 'mode', 'description', 'sort_order'];

    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }
}
