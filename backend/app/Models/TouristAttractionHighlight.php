<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristAttractionHighlight extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_attraction_highlights';

    protected $fillable = ['attraction_id', 'text', 'sort_order'];
}
