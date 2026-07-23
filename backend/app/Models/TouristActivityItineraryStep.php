<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristActivityItineraryStep extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_activity_itinerary_steps';

    protected $fillable = ['activity_id', 'title', 'description', 'sort_order'];
}
