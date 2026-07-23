<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Festival extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $fillable = [
        'state_id', 'name', 'slug', 'image', 'image_alt', 'sort_order', 'is_active', 'month',
        'banner_subtitle', 'banner_description',
        'short_description', 'intro_image', 'intro_image_alt', 'long_description',
        'key_experience_title', 'why_visit_title', 'faq_title',
        'highlights_title', 'places_title', 'packages_title',
        'location_text', 'month_text', 'duration_text',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function keyExperiences()
    {
        return $this->hasMany(FestivalKeyExperience::class)->orderBy('sort_order');
    }

    public function stats()
    {
        return $this->hasMany(FestivalStat::class)->orderBy('sort_order');
    }

    public function highlights()
    {
        return $this->hasMany(FestivalHighlight::class)->orderBy('sort_order');
    }

    public function places()
    {
        return $this->hasMany(FestivalPlace::class)->orderBy('sort_order');
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function howToReach()
    {
        return $this->hasMany(FestivalHowToReach::class)->orderBy('sort_order');
    }

    public function whyVisits()
    {
        return $this->hasMany(FestivalWhyVisit::class)->orderBy('sort_order');
    }

    public function faqs()
    {
        return $this->hasMany(FestivalFaq::class)->orderBy('sort_order');
    }

    public function meta()
    {
        return $this->hasOne(FestivalMetaData::class);
    }

    /** "Explore Festivals by Month" tabs — active festivals that have a month assigned, grouped 1-12. */
    public static function groupedByMonth(): Collection
    {
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $byMonth = static::where('is_active', true)
            ->whereNotNull('month')
            ->with('state')
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($f) => $f->state)
            ->groupBy('month');

        return collect(range(1, 12))->map(fn ($month) => [
            'month'      => $month,
            'month_name' => $monthNames[$month - 1],
            'festivals'  => ($byMonth->get($month) ?? collect())->map(fn ($f) => [
                'name'       => $f->name,
                'image'      => $f->image ? storage_link($f->image) : null,
                'image_alt'  => $f->image_alt,
                'state_slug' => $f->state->city_guide_slug,
            ])->values(),
        ]);
    }
}
