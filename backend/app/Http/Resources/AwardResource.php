<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AwardResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'award_year' => $this->award_year,
            'banner_image' => $this->banner_image ? storage_link($this->banner_image): null,
            'banner_image_alt' =>$this->title,
            'description' => $this->description,
        ];
    }
}

