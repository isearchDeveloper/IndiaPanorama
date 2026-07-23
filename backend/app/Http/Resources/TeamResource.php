<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'profile_image' => $this->profile_image ? storage_link($this->profile_image): null,
            'description' => $this->description,
            'about' => $this->about,
            'department' => $this->whenLoaded('department', function () {
                return [
                    'id'         => $this->department->id ?? null,
                    'name'   => $this->department->name ?? null,
                ];
            }),
        ];
    }
}

