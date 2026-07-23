<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'duration_days'     => $this->duration_days,
            'duration_nights'   => $this->duration_nights,
            'tour_highlights'   => $this->tour_highlights,
            'facilities'        => $this->facilities
        ];
    }
}


