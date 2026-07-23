<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category', 'enquiry_type', 'name', 'email', 'phone', 'message', 'status', 'source_url', 'ip_address',
        'country', 'budget', 'no_of_persons', 'travel_date', 'duration', 'arrival_city', 'departure_city',
    ];

    protected $casts = [
        'travel_date' => 'date:Y-m-d',
    ];

    /** Categories shown as tabs in the admin CRM — mirror the site's main modules. */
    public const CATEGORIES = ['holidays', 'experiences', 'destination', 'activities', 'car_rental', 'general'];

    public const STATUSES = ['new', 'contacted', 'closed'];

    /**
     * Frontend enquiry-type label -> admin category, used by the API to auto-file an
     * enquiry when the caller doesn't pass `category` explicitly (e.g. the site-wide
     * "Enquiries" dropdown). Types with no dedicated module fall back to "general".
     */
    public const TYPE_CATEGORY_MAP = [
        'Tour Booking'             => 'holidays',
        'Customized Tour Booking'  => 'holidays',
        'Car & Bus Booking'        => 'car_rental',
        'Train Booking'            => 'general',
        'Hotel Booking'            => 'general',
        'Plan Trip Enquiries'      => 'general',
        'General Enquiries'        => 'general',
        'Go Exploring Enquiries'   => 'general',
        'Health Tourism Enquiries' => 'general',
    ];

    public static function categoryForType(string $enquiryType): string
    {
        return self::TYPE_CATEGORY_MAP[$enquiryType] ?? 'general';
    }

    public function scopeInCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }
}
