<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarRoutePageDetails extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    use HasFactory;

    protected $table = 'car_routes_page_details';

    protected $fillable = [
        'id',
        'route_id',
        'title',
        'description',
        'banner_image',
        'banner_image_alt',
        'about_title',
        'about_image',
        'about_image_alt',
        'about_description',
        'distance_text',
        'duration_text',
        'route_number',
        'best_season',
    ];


    public function route()
    {
        return $this->belongsTo(Page::class, 'route_id', 'id');
    }
}
