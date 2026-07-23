<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalStatePage extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $fillable = [
        'state_id', 'featured_festival_id', 'title', 'banner_image', 'banner_image_alt', 'banner_text',
        'short_description', 'why_visit_title', 'why_visit_sub_title', 'faq_title', 'faq_sub_title',
        'meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function featuredFestival()
    {
        return $this->belongsTo(Festival::class, 'featured_festival_id');
    }

    public function whyVisits()
    {
        return $this->hasMany(FestivalStatePageWhyVisit::class, 'page_id')->orderBy('sort_order');
    }

    public function faqs()
    {
        return $this->hasMany(FestivalStatePageFaq::class, 'page_id')->orderBy('sort_order');
    }
}
