<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristAttractionPageBestTime extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_attraction_page_best_times';

    protected $fillable = ['page_id', 'period', 'description', 'sort_order'];
}
