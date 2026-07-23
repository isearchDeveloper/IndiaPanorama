<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalSettingHighlight extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['festival_setting_id', 'icon', 'stat', 'label', 'sort_order'];

    public function festivalSetting()
    {
        return $this->belongsTo(FestivalSetting::class);
    }
}
