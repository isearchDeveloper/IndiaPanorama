<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['festival_id', 'question', 'answer', 'sort_order'];

    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }
}
