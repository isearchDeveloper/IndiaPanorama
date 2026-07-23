<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristActivitySettingPerfectFor extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_activity_setting_perfect_fors';

    protected $fillable = ['setting_id', 'title', 'icon', 'sort_order'];
}
