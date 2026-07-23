<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristActivitySetting extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'tourist_activity_settings';

    protected $fillable = [
        'title', 'banner_image', 'banner_image_alt', 'banner_text', 'short_description',
        'why_choose_title', 'why_choose_sub_title',
        'faq_title', 'faq_sub_title',
        'seasons_title',
        'stats_image', 'stats_image_alt',
        'meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public static function current(): self
    {
        return static::firstOrCreate([]);
    }

    public function faqs()
    {
        return $this->hasMany(TouristActivitySettingFaq::class, 'setting_id', 'id')->orderBy('sort_order');
    }

    public function whyChooses()
    {
        return $this->hasMany(TouristActivitySettingWhyChoose::class, 'setting_id', 'id')->orderBy('sort_order');
    }

    public function highlights()
    {
        return $this->hasMany(TouristActivitySettingHighlight::class, 'setting_id', 'id')->orderBy('sort_order');
    }

    public function perfectFors()
    {
        return $this->hasMany(TouristActivitySettingPerfectFor::class, 'setting_id', 'id')->orderBy('sort_order');
    }

    public function seasons()
    {
        return $this->hasMany(TouristActivitySettingSeason::class, 'setting_id', 'id')->orderBy('sort_order');
    }
}
