<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'title'              => $this->title,
            'slug'               => $this->slug,
            'type'               => $this->type,
            'seats'              => $this->seats,
            'primary_image'      => $this->primary_image ? storage_link($this->primary_image) : null,
            'primary_image_alt'  => $this->primary_image_alt,
            //'is_active'          => $this->is_active,
            'category'           => new CarCategoryResource($this->whenLoaded('category')),
        ];
    }
}
