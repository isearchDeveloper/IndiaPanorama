<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidaySettingFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['holiday_setting_id', 'question', 'answer'];
}
