<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristActivity extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'tourist_activities';

    protected $fillable = [
        'name', 'slug', 'state_id', 'location_id', 'banner_image', 'banner_image_alt', 'tagline', 'short_description',
        'faq_title', 'faq_sub_title',
        'experiences_title', 'things_to_do_title', 'itinerary_title',
        'meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details',
        'sort_order', 'is_active', 'is_popular',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
    ];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function itinerarySteps()
    {
        return $this->hasMany(TouristActivityItineraryStep::class, 'activity_id', 'id')->orderBy('sort_order');
    }

    public function experiences()
    {
        return $this->hasMany(TouristActivityExperience::class, 'activity_id', 'id')->orderBy('sort_order');
    }

    public function thingsToDo()
    {
        return $this->hasMany(TouristActivityThingToDo::class, 'activity_id', 'id')->orderBy('sort_order');
    }

    public function galleryImages()
    {
        return $this->hasMany(TouristActivityGalleryImage::class, 'activity_id', 'id')->orderBy('sort_order');
    }

    public function faqs()
    {
        return $this->hasMany(TouristActivityFaq::class, 'activity_id', 'id')->orderBy('sort_order');
    }
}
