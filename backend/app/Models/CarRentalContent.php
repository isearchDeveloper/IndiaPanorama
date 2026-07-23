<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarRentalContent extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $fillable = [
        'short_description',
        'checklist_title',
        'features_title',
        'benefits_title',
        'about_title',
        'about_description',
        'why_choose_title',
        'why_choose_description',
        'popular_locations_title',
        'popular_locations_description',
        'road_trip_title',
        'road_trip_subtitle',
        'amenities_title',
        'gallery_title',
        'gallery_description',
    ];

    /** Singleton accessor — there is only ever one Car Rental content row. */
    public static function current(): self
    {
        return static::firstOrCreate([]);
    }

    public function checklistItems()
    {
        return $this->hasMany(CarRentalChecklistItem::class)->orderBy('sort_order');
    }

    public function galleryImages()
    {
        return $this->hasMany(CarRentalGalleryImage::class)->orderBy('sort_order');
    }

    public function whyChooseStats()
    {
        return $this->hasMany(CarRentalWhyChooseStat::class)->orderBy('sort_order');
    }

    public function features()
    {
        return $this->hasMany(CarRentalFeature::class)->orderBy('sort_order');
    }

    public function benefits()
    {
        return $this->hasMany(CarRentalBenefit::class)->orderBy('sort_order');
    }

    public function amenities()
    {
        return $this->hasMany(CarRentalAmenity::class)->orderBy('sort_order');
    }
}
