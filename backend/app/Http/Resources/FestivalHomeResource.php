<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FestivalHomeResource extends JsonResource
{
    public function toArray($request)
    {
        $s = $this['setting'];

        return [
            'banner' => [
                'title'       => $s->title,
                'banner_text' => $s->banner_text,
                'image'       => $s->banner_image ? storage_link($s->banner_image) : null,
                'image_alt'   => $s->banner_image_alt,
            ],
            'short_description' => $s->short_description,
            'stats' => [
                'title'      => $s->why_choose_title,
                'sub_title'  => $s->why_choose_sub_title,
                'highlights' => $s->highlights->map(fn ($h) => [
                    'stat'  => $h->stat,
                    'label' => $h->label,
                ])->values(),
            ],
            'festivals' => $this['festivals'],
            'explore_by_state' => $this['by_state'],
            'explore_by_month' => $this['by_month'],
            'upcoming_festivals' => $this['upcoming'],
            'why_experience' => [
                'title'     => $s->why_experience_title,
                'sub_title' => $s->why_experience_sub_title,
                'items'     => $s->whyExperiences->map(fn ($w) => [
                    'title'   => $w->title,
                    'tagline' => $w->tagline,
                ])->values(),
            ],
            'festival_packages' => $this['festival_packages'],
            'faqs' => [
                'title'     => $s->faq_title,
                'sub_title' => $s->faq_sub_title,
                'list'      => $s->faqs->map(fn ($f) => [
                    'question' => $f->question,
                    'answer'   => $f->answer,
                ])->values(),
            ],
            'meta' => [
                'meta_title'       => $s->meta_title,
                'meta_description' => $s->meta_description,
                'meta_keywords'    => $s->meta_keywords,
                'h1_heading'       => $s->h1_heading,
                'meta_details'     => $s->meta_details,
            ],
        ];
    }
}
