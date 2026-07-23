<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristActivityPageExperience extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_activity_page_experiences';

    protected $fillable = ['page_id', 'icon', 'title', 'description', 'sort_order'];
}
