<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TourServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'title'        => $this->title,
            'link'         => $this->link,
            'banner_image' => $this->banner_image ? storage_link($this->banner_image) : null,
            'banner_image_alt' =>$this->banner_image_alt,
        ];
    }
}
