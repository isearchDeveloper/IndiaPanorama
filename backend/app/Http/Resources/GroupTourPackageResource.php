<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupTourPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Fields are aligned with the group-tour listing card shown on the frontend:
     *   image, badges, title, short_description, rating, features/facilities,
     *   cities, dates_count, days, nights, starting_price, currency, slug.
     */
    public function toArray($request)
    {
        $details    = $this->details;
        $departures = $this->whenLoaded('groupDepartures');

        // ── Facilities stored in package_details.facilities (array of icon strings)
        $storedFacilities = [];
        if ($details && !empty($details->facilities)) {
            $storedFacilities = is_array($details->facilities)
                ? $details->facilities
                : json_decode($details->facilities, true) ?? [];
        }

        // ── Lowest price from upcoming available departures
        $startingPrice = null;
        if ($departures && $departures->count()) {
            $startingPrice = $departures
                ->where('status', 'available')
                ->where('departure_date', '>=', now()->toDateString())
                ->min('price');
        }

        // ── Number of future available departure dates
        $datesCount = 0;
        if ($departures && $departures->count()) {
            $datesCount = $departures
                ->where('status', 'available')
                ->where('departure_date', '>=', now()->toDateString())
                ->count();
        }

        // ── Cities count from destination locations
        $citiesCount = 0;
        if ($this->relationLoaded('extraDestinations')) {
            $locationIds = $this->extraDestinations->pluck('location_id')->toArray();
            if ($this->location_id) {
                $locationIds[] = $this->location_id;
            }
            $citiesCount = count(array_unique($locationIds));
        }

        // ── Gallery images
        $galleryImages = [];
        if ($this->relationLoaded('images') && $this->images->count()) {
            $galleryImages = $this->images->map(function ($img) {
                return [
                    'id'  => $img->id,
                    'url' => $img->image_path ? storage_link($img->image_path) : null,
                    'alt' => $img->image_alt,
                ];
            })->toArray();
        }

        // ── Location info
        $locationName  = null;
        $countryName   = null;
        if ($this->relationLoaded('location') && $this->location) {
            $locationName = $this->location->name;
            if ($this->location->relationLoaded('country') && $this->location->country) {
                $countryName = $this->location->country->name;
            }
        }

        // ── Departure dates detail list
        $departuresList = [];
        if ($departures && $departures->count()) {
            $departuresList = $departures->map(function ($dep) {
                return [
                    'id'             => $dep->id,
                    'departure_date' => $dep->departure_date,
                    'price'          => (float) $dep->price,
                    'total_seats'    => (int) $dep->total_seats,
                    'booked_seats'   => (int) ($dep->booked_seats ?? 0),
                    'available_seats' => max(0, (int) $dep->total_seats - (int) ($dep->booked_seats ?? 0)),
                    'status'         => $dep->status,
                ];
            })->toArray();
        }

        return [
            'id'                => $this->id,
            'package_id'        => $this->id,
            'title'             => $this->title,
            'slug'              => $this->slug,
            'short_description' => $this->short_description,
            'long_description'  => $this->long_description,
            'package_mode'      => $this->package_mode,

            // ── Image
            'primary_image'     => $this->primary_image ? storage_link($this->primary_image) : null,
            'primary_image_alt' => $this->primary_image_alt,
            'images'    => $galleryImages,

            // ── Badges
            'badges'            => array_values(array_filter([
                'GROUP TOUR',
                $this->whenLoaded('category', fn() => $this->category->name ?? null),
            ])),

            // ── Rating
            'rating'            => round($this->reviews_avg_rating ?? 0, 1),
            'reviews_count'     => $this->whenLoaded('reviews', fn() => $this->reviews->count(), 0),

            // ── Facilities / features (from DB, not hardcoded)
            'facilities'        => $storedFacilities,

            // ── Tour highlights
            'tour_highlights'   => $details->tour_highlights ?? null,

            // ── Duration & cities
            'cities_count'      => $citiesCount,
            'dates_count'       => $datesCount,
            'duration_days'     => (int) ($details->duration_days ?? 0),
            'duration_nights'   => (int) ($details->duration_nights ?? 0),

            // ── Pricing
            'starting_price'    => $startingPrice !== null ? (float) $startingPrice : null,
            'currency'          => 'INR',

            // ── Location
            'location'          => $locationName,
            'country'           => $countryName,

            // ── Departure dates (full detail)
            'departures'        => $departuresList,

            // ── Flags
            'is_top_trending'   => (bool) $this->is_top_trending,
            'is_special_package' => (bool) $this->is_special_package,

            // ── Author
            'author_name'       => $this->author_name,
        ];
    }
}
