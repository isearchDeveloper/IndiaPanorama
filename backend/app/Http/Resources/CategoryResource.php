<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name'  => $this->name,
            'slug'  => $this->slug,
            'title' => $this->title,
            'sub_title' => $this->sub_title,
            'banner_image' => $this->banner_image ? storage_link($this->banner_image): null,
            'banner_image_alt' =>$this->name,
            'description' => $this->description,
        ];
    }
}

