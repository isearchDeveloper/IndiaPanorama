<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristActivitySettingFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_activity_setting_faqs';

    protected $fillable = ['setting_id', 'question', 'answer', 'sort_order'];
}
