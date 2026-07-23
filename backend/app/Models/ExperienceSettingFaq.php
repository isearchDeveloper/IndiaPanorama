<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceSettingFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['experience_setting_id', 'question', 'answer', 'sort_order'];

    public function setting()
    {
        return $this->belongsTo(ExperienceSetting::class, 'experience_setting_id');
    }
}
