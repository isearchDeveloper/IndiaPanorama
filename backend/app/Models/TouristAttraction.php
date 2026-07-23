<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristAttraction extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'tourist_attractions';

    protected $fillable = [
        'name', 'slug', 'state_id', 'location_id', 'banner_image', 'banner_image_alt', 'tagline', 'short_description',
        'location_text', 'duration_text', 'best_for', 'best_season',
        'why_visit_title', 'why_visit_image', 'why_visit_image_alt', 'why_visit_description',
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

    public function highlights()
    {
        return $this->hasMany(TouristAttractionHighlight::class, 'attraction_id', 'id')->orderBy('sort_order');
    }

    public function activities()
    {
        return $this->hasMany(TouristAttractionActivity::class, 'attraction_id', 'id')->orderBy('sort_order');
    }

    public function galleryImages()
    {
        return $this->hasMany(TouristAttractionGalleryImage::class, 'attraction_id', 'id')->orderBy('sort_order');
    }

    public function faqs()
    {
        return $this->hasMany(TouristAttractionFaq::class, 'attraction_id', 'id')->orderBy('sort_order');
    }

    /** Other attractions in the same city, excluding this one. */
    public function nearby()
    {
        return self::where('location_id', $this->location_id)
            ->where('id', '!=', $this->id)
            ->where('is_active', 1);
    }
}
