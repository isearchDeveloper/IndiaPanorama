<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarRoute extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'car_routes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'from_location',
        'to_location',
        'is_active',
        'faq_title',
        'faq_sub_title',
        'is_popular',
        'display_label',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
    ];

    /**
     * Relationship: a route has many cars through the pivot `car_routes_details`
     */
    public function cars()
    {
        return $this->belongsToMany(Car::class, 'car_routes_details', 'route_id', 'car_id')
                    ->with(['category']) // ✅ optional: load related car category
                    ->withTimestamps();
    }

    /**
     * (Optional) Metadata relationship — adjust model if needed
     */
    public function meta()
    {
        return $this->hasOne(CarRouteMetaData::class, 'route_id', 'id');
    }

    public function details()
    {
        return $this->hasOne(CarRoutePageDetails::class, 'route_id', 'id');
    }

    public function faqs()
    {
        return $this->hasMany(CarRouteFaq::class,'route_id','id');
    }

    public function highlights()
    {
        return $this->hasMany(CarRouteHighlight::class, 'route_id', 'id')->orderBy('sort_order');
    }
}
