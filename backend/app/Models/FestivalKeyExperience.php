<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalKeyExperience extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['festival_id', 'icon', 'label', 'sort_order'];

    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }
}
