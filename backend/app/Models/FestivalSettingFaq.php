<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalSettingFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['festival_setting_id', 'question', 'answer', 'sort_order'];

    public function festivalSetting()
    {
        return $this->belongsTo(FestivalSetting::class);
    }
}
