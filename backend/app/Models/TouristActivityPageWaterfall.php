<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristActivityPageWaterfall extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_activity_page_waterfalls';

    protected $fillable = ['page_id', 'image', 'label', 'sort_order'];
}
