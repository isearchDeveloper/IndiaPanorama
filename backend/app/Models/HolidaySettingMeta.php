<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidaySettingMeta extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'holiday_setting_meta';

    protected $fillable = [
        'holiday_setting_id',
        'meta_title', 'meta_description', 'meta_keywords',
        'h1_heading', 'meta_details',
    ];
}
