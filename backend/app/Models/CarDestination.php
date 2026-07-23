<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarDestination extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'car_destinations';

    protected $fillable = [
        'name', 'slug', 'state_id', 'location_id', 'banner_image', 'banner_image_alt', 'description',
        'about_title', 'about_image', 'about_image_alt', 'about_description',
        'distance_text', 'duration_text', 'ideal_for', 'best_season',
        'faq_title', 'faq_sub_title',
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

    public function cars()
    {
        return $this->belongsToMany(Car::class, 'car_destination_details', 'destination_id', 'car_id')
                    ->with(['category'])
                    ->withTimestamps();
    }

    public function highlights()
    {
        return $this->hasMany(CarDestinationHighlight::class, 'destination_id', 'id')->orderBy('sort_order');
    }

    public function faqs()
    {
        return $this->hasMany(CarDestinationFaq::class, 'destination_id', 'id');
    }
}
