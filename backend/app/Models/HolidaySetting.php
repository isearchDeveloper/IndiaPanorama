<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidaySetting extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $fillable = ['name', 'slug', 'is_active', 'faq_title', 'faq_image', 'faq_image_alt'];

    protected $casts = ['is_active' => 'boolean'];

    public function details()
    {
        return $this->hasOne(HolidaySettingDetail::class);
    }

    public function faqs()
    {
        return $this->hasMany(HolidaySettingFaq::class);
    }

    public function meta()
    {
        return $this->hasOne(HolidaySettingMeta::class);
    }
}
