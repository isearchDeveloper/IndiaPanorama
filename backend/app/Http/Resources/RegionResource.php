<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'faq_title' => $this->faq_title,

            'details' => $this->whenLoaded('details', function () {
                return [
                    'title'     => $this->details->title ?? null,
                    'sub_title' => $this->details->sub_title ?? null,
                    'about'     => $this->details->about ?? null,

                    // ✅ SAME AS LOCATION (IMPORTANT)
                    'banner_image' => $this->details->banner_image
                        ? storage_link($this->details->banner_image)
                        : null,

                    'banner_image_alt' => $this->details->banner_image_alt ?? null,
                ];
            }),

            'faqs' => $this->whenLoaded('faqs', function () {
                return $this->faqs->map(fn ($faq) => [
                    'question' => $faq->question,
                    'answer'   => $faq->answer,
                ]);
            }),

            'meta' => $this->whenLoaded('meta', function () {
                return [
                    'meta_title'       => $this->meta->meta_title ?? null,
                    'meta_description' => $this->meta->meta_description ?? null,
                    'meta_keywords'    => $this->meta->meta_keywords ?? null,
                    'h1_heading'       => $this->meta->h1_heading ?? null,
                    'meta_details'     => $this->meta->meta_details ?? null,
                ];
            }),
        ];
    }
}
