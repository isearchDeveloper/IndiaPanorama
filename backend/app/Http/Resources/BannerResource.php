<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'title'            => $this->title,
            'banner_image'     => $this->banner_image ? storage_link($this->banner_image) : null,
            'banner_image_alt' => $this->banner_image_alt,
            'sort_order'       => $this->sort_order,
            'url'              => $this->url,
            'link'             => $this->url, // alias — frontend banner button ko is pe redirect karo
        ];
    }
}
