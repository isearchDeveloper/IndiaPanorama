<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristAttractionActivity extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_attraction_activities';

    protected $fillable = ['attraction_id', 'title', 'description', 'sort_order'];
}
