<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\PackageLocation;
use App\Models\PackageSourceLocation;

class PackageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'title'         => $this->title,
            'slug'          => $this->slug,
            'price'          => $this->price,
            'parent_category'         => $this->parent_category,
            'parent_category_slug' => $this->parent_category_slug,
            'primary_image' => $this->primary_image ? storage_link($this->primary_image) : null,
            'primary_image_alt' => $this->primary_image_alt,
            'short_description' => $this->short_description,
            'long_description'  => $this->long_description,
            'rating' => round($this->reviews_avg_rating ?? 0, 1),
            'total_review' => $this->reviews_count,
            'package_mode' => $this->package_mode,

            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(fn($img) => [
                    'image_path' => $img->image_path ? storage_link($img->image_path) : null,
                    'image_alt' => $img->image_alt,
                ]);
            }),

            'itineraries' => $this->whenLoaded('itineraries', function () {
                return $this->itineraries->map(fn($itinerary) => [
                    'title'   => $itinerary->title,
                    'details' => $itinerary->details
                ]);
            }),

            'faq_title'  => $this->faq_title,
            'faqs' => $this->whenLoaded('faqs', function () {
                return $this->faqs->map(fn($faq) => [
                    'question'   => $faq->question,
                    'answer' => $faq->answer
                ]);
            }),

            'reviews' => $this->whenLoaded('reviews', function () {
                return $this->reviews->map(fn($review) => [
                    'customer_name'   => $review->customer_name,
                    'rating' => $review->rating,
                    'comment' => $review->comment
                ]);
            }),

            'category'    => new CategoryResource($this->whenLoaded('category')),
            'details'     => new PackageDetailResource($this->whenLoaded('details')),
            'location'    => new LocationResource($this->whenLoaded('location')),
            'meta' => $this->whenLoaded('meta', function () {
                return [
                    'meta_title'         => $this->meta->meta_title ?? null,
                    'meta_description'   => $this->meta->meta_description ?? null,
                    'meta_keywords'      => $this->meta->meta_keywords ?? null,
                    'h1_heading'         => $this->meta->h1_heading,
                    'meta_details'       => $this->meta->meta_details ?? null,
                ];
            }),

            // ✅ Extra destinations ("Destination Covered" — city + its highlights)
            'extra_destinations' => PackageLocation::with('location')
                ->where('package_id', $this->id)
                ->get()
                ->filter(fn ($item) => $item->location)
                ->map(function ($item) {
                    return [
                        'id' => $item->location->id,
                        'name' => $item->location->name,
                        'slug' => $item->location->slug,
                        'highlights' => $item->highlights,
                    ];
                })
                ->values(),

            // ✅ Extra sources
            'extra_sources' => PackageSourceLocation::with('location')
                ->where('package_id', $this->id)
                ->get()
                ->filter(fn ($item) => $item->location)
                ->map(function ($item) {
                    return [
                        'id' => $item->location->id,
                        'name' => $item->location->name,
                        'slug' => $item->location->slug,
                    ];
                })
                ->values(),

            'groupDepartures' => $this->whenLoaded('groupDepartures', function () {
                return $this->groupDepartures->map(function ($item) {
                    $availableSeats = max(0, (int) $item->total_seats - (int) ($item->booked_seats ?? 0));
                    return [
                        'id'              => $item->id,
                        'departure_date'  => $item->departure_date,
                        'price'           => (float) ($item->price ?? 0),
                        'total_seats'     => (int) $item->total_seats,
                        'booked_seats'    => (int) ($item->booked_seats ?? 0),
                        'available_seats' => $availableSeats,
                        'status'          => $item->computed_status,
                    ];
                });
            }),

            // ── Group-tour summary fields (only populated for group_tour mode)
            'id'            => $this->id,
            'package_id'    => $this->id,
            'is_group_tour' => $this->package_mode === 'group_tour',
        ];
    }
}
