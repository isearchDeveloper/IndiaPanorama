<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceCategoryPopularCity extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'category_id', 'title', 'image', 'image_alt', 'description', 'popular_tag',
        'state_id', 'location_id', 'sort_order',
    ];

    public function category()
    {
        return $this->belongsTo(ExperienceCategory::class, 'category_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
