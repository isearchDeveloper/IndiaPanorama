<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristActivitySettingSeason extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_activity_setting_seasons';

    protected $fillable = ['setting_id', 'season_label', 'period_text', 'activities_text', 'sort_order'];
}
