<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristActivitySettingHighlight extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_activity_setting_highlights';

    protected $fillable = ['setting_id', 'stat', 'label', 'sort_order'];
}
