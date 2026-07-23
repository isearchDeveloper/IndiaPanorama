<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceGalleryImage extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['experience_id', 'image', 'image_alt', 'sort_order'];

    public function experience()
    {
        return $this->belongsTo(Experience::class, 'experience_id');
    }
}
