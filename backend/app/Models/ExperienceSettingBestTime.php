<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceSettingBestTime extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'experience_setting_best_times';

    protected $fillable = ['setting_id', 'label', 'text', 'sort_order'];
}
