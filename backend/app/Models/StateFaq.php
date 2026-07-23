<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'state_faqs';

    protected $fillable = [
        'state_id',
        'question',
        'answer',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
