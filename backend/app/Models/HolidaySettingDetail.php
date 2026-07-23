<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidaySettingDetail extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $fillable = [
        'holiday_setting_id',
        'banner_image', 'banner_image_alt', 'banner_title', 'banner_description',
        'main_heading', 'short_description', 'long_description',
        'tour_packages_heading', 'tour_packages_description',
        'popular_packages_heading', 'popular_packages_description',
        'enquiry_title', 'enquiry_subtitle',
        'luxury_title', 'luxury_description',
        'popular_tour_title', 'popular_tour_description',
        'additional_content',
    ];
}
