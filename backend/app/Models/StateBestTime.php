<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateBestTime extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'state_best_times';

    protected $fillable = [
        'state_id',
        'month_range',
        'tagline',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
