<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceSetting extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $fillable = [
        'title', 'banner_image', 'banner_image_alt', 'banner_text', 'short_description',
        'meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details',
        'is_active', 'faq_title', 'faq_sub_title',
        'best_time_title', 'why_choose_title', 'why_choose_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Singleton accessor — there is only ever one Experiences hub settings row. */
    public static function current(): self
    {
        return static::firstOrCreate([]);
    }

    public function faqs()
    {
        return $this->hasMany(ExperienceSettingFaq::class)->orderBy('sort_order');
    }

    public function bestTimes()
    {
        return $this->hasMany(ExperienceSettingBestTime::class, 'setting_id')->orderBy('sort_order');
    }

    public function whyChooseItems()
    {
        return $this->hasMany(ExperienceSettingWhyChooseItem::class, 'setting_id')->orderBy('sort_order');
    }
}
