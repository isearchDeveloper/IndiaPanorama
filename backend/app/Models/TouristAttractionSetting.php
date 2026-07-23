<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristAttractionSetting extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'tourist_attraction_settings';

    protected $fillable = [
        'title', 'banner_image', 'banner_image_alt', 'banner_text', 'short_description',
        'faq_title', 'faq_sub_title',
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
        return $this->hasMany(TouristAttractionSettingFaq::class, 'setting_id', 'id')->orderBy('sort_order');
    }
}
