<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomepageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'slider_banners'  => BannerResource::collection($this['extra']['slider_banners']),
            'single_banner'   => $this['extra']['single_banner'] ? new BannerResource($this['extra']['single_banner']) : null,
            'special_package' => $this['extra']['special_package'] ? new BannerResource($this['extra']['special_package']) : null,
            'tour_services'   => TourServiceResource::collection($this['extra']['tour_services']),
            'why_choose'      => (function () {
                $sec = $this['extra']['why_choose'] ?? null;
                return [
                    'title'     => $sec?->title,
                    'subtitle'  => $sec?->subtitle,
                    'image'     => $sec?->image ? storage_link($sec->image) : null,
                    'image_alt' => $sec?->image_alt,
                ];
            })(),

            // Now meta comes from the page model
            'meta' => $this['page']->meta ? [
                'meta_title'       => $this['page']->meta->meta_title ?? null,
                'meta_description' => $this['page']->meta->meta_description ?? null,
                'meta_keywords'    => $this['page']->meta->meta_keywords ?? null,
                'h1_heading'       => $this['page']->meta->h1_heading ?? null,
                'meta_details'     => $this['page']->meta->meta_details ?? null,
                'meta_body_details'=> $this['page']->meta->meta_body_details ?? null,
            ] : null,
        ];
    }

}

