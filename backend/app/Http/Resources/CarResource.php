<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'title'              => $this->title,
            'slug'               => $this->slug,
            'seats'              => $this->seats,
            'fuel_type'          => $this->fuel_type,
            'primary_image'      => $this->primary_image ? storage_link($this->primary_image) : null,
            'primary_image_alt'  => $this->primary_image_alt,
            //'is_active'          => $this->is_active,
            'category'           => new CarCategoryResource($this->whenLoaded('category')),
        ];
    }
}
