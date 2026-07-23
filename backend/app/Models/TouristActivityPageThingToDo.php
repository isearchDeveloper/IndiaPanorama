<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristActivityPageThingToDo extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_activity_page_things_to_do';

    protected $fillable = ['page_id', 'title', 'description', 'duration_timing', 'best_for', 'approximate_cost', 'sort_order'];
}
