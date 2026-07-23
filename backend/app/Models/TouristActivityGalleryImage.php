<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristActivityGalleryImage extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_activity_gallery_images';

    protected $fillable = ['activity_id', 'image', 'image_alt', 'sort_order'];

    public function activity()
    {
        return $this->belongsTo(TouristActivity::class, 'activity_id');
    }
}
