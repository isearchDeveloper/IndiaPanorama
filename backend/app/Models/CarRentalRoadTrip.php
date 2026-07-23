<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarRentalRoadTrip extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $fillable = [
        'state_id',
        'image',
        'image_alt',
        'rating',
        'route_text',
        'duration_days',
        'duration_nights',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating'    => 'float',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
