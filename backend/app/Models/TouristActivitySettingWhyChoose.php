<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristActivitySettingWhyChoose extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_activity_setting_why_chooses';

    protected $fillable = ['setting_id', 'title', 'tagline', 'sort_order'];
}
