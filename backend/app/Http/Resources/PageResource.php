<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'banner_image' => $this->banner_image ? storage_link($this->banner_image) : null,
            'banner_image_alt' => $this->banner_image_alt,
            'description' => $this->description,
            'faq_title' => $this->faq_title,
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
                    'meta_body_details'  => $this->meta->meta_body_details ?? null,
                ];
            }),
        ];
    }
}
