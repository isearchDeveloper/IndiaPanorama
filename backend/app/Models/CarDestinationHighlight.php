<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarDestinationHighlight extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'car_destination_highlights';

    protected $fillable = ['destination_id', 'title', 'description', 'sort_order'];

    public function destination()
    {
        return $this->belongsTo(CarDestination::class, 'destination_id', 'id');
    }
}
