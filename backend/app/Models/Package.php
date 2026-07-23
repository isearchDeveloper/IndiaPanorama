<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Package extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'packages';

    protected $casts = [
        'is_active'         => 'boolean',
        'is_draft'          => 'boolean',
        'is_top_trending'   => 'boolean',
        'is_special_package'=> 'boolean',
        'is_customized'     => 'boolean',
        'is_festival_package' => 'boolean',
    ];

    protected $fillable = [
        'title',
        'slug',
        'source_url',
        'price',
        'parent_category',
        'parent_category_slug',
        'category_id',
        'country_id',
        'location_id',
        'source_location_id',
        'primary_image',
        'primary_image_alt',
        'source_of_image',
        'download_date',
        'account_id',
        'license_key',
        'license_key_file',
        'package_mode',          // 'normal' | 'group_tour'
        'is_top_trending',
        'is_special_package',
        'is_festival_package',
        'festival_id',
        'short_description',
        'long_description',
        'is_active',
        'is_draft',
        'is_customized',
        'faq_title',
        'author_name',
    ];

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', 1);
    }

    /** Only group-tour packages. */
    public function scopeGroupTour(Builder $query): Builder
    {
        return $query->where('package_mode', 'group_tour');
    }

    /** Only normal packages. */
    public function scopeNormal(Builder $query): Builder
    {
        return $query->where('package_mode', 'normal');
    }

    // =========================================================
    // HELPERS
    // =========================================================

    public function isGroupTour(): bool
    {
        return $this->package_mode === 'group_tour';
    }

    public function allowsDirectBooking(): bool
    {
        return $this->isGroupTour();
    }

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function source_location()
    {
        return $this->belongsTo(Location::class, 'source_location_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function festival()
    {
        return $this->belongsTo(Festival::class, 'festival_id', 'id');
    }

    public function details()
    {
        return $this->hasOne(PackageDetail::class, 'package_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(PackageImage::class, 'package_id', 'id');
    }

    public function itineraries()
    {
        return $this->hasMany(PackageItinerary::class, 'package_id', 'id')
                    ->orderBy('id');
    }

    public function faqs()
    {
        return $this->hasMany(PackageFaq::class, 'package_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'package_id', 'id');
    }

    public function meta()
    {
        return $this->hasOne(PackageMetaData::class, 'package_id', 'id');
    }

    public function extraDestinations()
    {
        return $this->hasMany(PackageLocation::class);
    }

    public function extraSources()
    {
        return $this->hasMany(PackageSourceLocation::class);
    }

    public function packageCategories()
    {
        return $this->hasMany(PackageCategory::class, 'package_id');
    }

    /** Group-tour departure dates. */
    public function groupDepartures()
    {
        return $this->hasMany(PackageGroupDate::class, 'package_id', 'id')
                    ->orderBy('departure_date');
    }

}