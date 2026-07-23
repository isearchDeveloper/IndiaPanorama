<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristAttractionGalleryImage extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_attraction_gallery_images';

    protected $fillable = ['attraction_id', 'image', 'image_alt', 'sort_order'];

    public function attraction()
    {
        return $this->belongsTo(TouristAttraction::class, 'attraction_id');
    }
}
