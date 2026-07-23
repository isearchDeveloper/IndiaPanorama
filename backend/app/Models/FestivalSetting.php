<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalSetting extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $fillable = [
        'title', 'banner_image', 'banner_image_alt', 'banner_text', 'short_description',
        'meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details',
        'is_active', 'faq_title', 'faq_sub_title', 'why_choose_title', 'why_choose_sub_title',
        'why_experience_title', 'why_experience_sub_title',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Singleton accessor — there is only ever one Festivals settings row. */
    public static function current(): self
    {
        return static::firstOrCreate([]);
    }

    public function faqs()
    {
        return $this->hasMany(FestivalSettingFaq::class)->orderBy('sort_order');
    }

    public function highlights()
    {
        return $this->hasMany(FestivalSettingHighlight::class)->orderBy('sort_order');
    }

    public function whyExperiences()
    {
        return $this->hasMany(FestivalSettingWhyExperience::class)->orderBy('sort_order');
    }
}
