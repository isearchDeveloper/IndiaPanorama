<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalStat extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['festival_id', 'value', 'label', 'sort_order'];

    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }
}
