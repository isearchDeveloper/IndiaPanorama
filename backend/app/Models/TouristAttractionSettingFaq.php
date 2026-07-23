<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristAttractionSettingFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_attraction_setting_faqs';

    protected $fillable = ['setting_id', 'question', 'answer', 'sort_order'];
}
