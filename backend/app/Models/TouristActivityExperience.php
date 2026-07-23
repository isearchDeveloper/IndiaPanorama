<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristActivityExperience extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_activity_experiences';

    protected $fillable = ['activity_id', 'image', 'image_alt', 'title', 'description', 'sort_order'];
}
