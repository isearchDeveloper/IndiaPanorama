<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceSettingWhyChooseItem extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'experience_setting_why_choose_items';

    protected $fillable = ['setting_id', 'label', 'sort_order'];
}
