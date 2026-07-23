<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristActivityThingToDo extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_activity_things_to_do';

    protected $fillable = ['activity_id', 'title', 'description', 'sort_order'];
}
