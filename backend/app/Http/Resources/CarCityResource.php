<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarCityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug'                 => $this->slug,
            'location'             => $this->location ?? '',

            'details' => $this->whenLoaded('details', function () {
                return [
                    'title'         => $this->details->title ?? null,
                    'banner_image' =>$this->details->banner_image ? storage_link($this->details->banner_image): null,
                    'banner_image_alt' =>$this->details->banner_image_alt,
                    'description' => $this->details->description,
                ];
            }),
            'faq_title'  => $this->faq_title,
            'faqs' => $this->whenLoaded('faqs', function () {
                return $this->faqs->map(fn($faq) => [
                    'question'   => $faq->question,
                    'answer' => $faq->answer
                ]);
            }),

            'meta' => $this->whenLoaded('meta', function () {
                return [
                    'meta_title'         => $this->meta->meta_title ?? null,
                    'meta_description'   => $this->meta->meta_description ?? null,
                    'meta_keywords'      => $this->meta->meta_keywords ?? null,
                    'h1_heading'         => $this->meta->h1_heading,
                    'meta_details'       => $this->meta->meta_details ?? null,
                ];
            }),
        ];
    }
}
