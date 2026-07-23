<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationBestTime extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'location_best_times';

    protected $fillable = [
        'location_id',
        'month_range',
        'tagline',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
