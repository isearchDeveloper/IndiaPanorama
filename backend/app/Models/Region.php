<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'regions';
    
    protected $fillable = [
        'name',
        'slug',
        'faq_title',
        'order_seq',
        'is_popular',
    ];

    protected $casts = [
        'is_popular' => 'boolean',
    ];
    
    public function locations() {
        return $this->hasMany(Location::class);
    }


   public function states() {
        return $this->hasMany(State::class);
    }
    
    public function details(){
        return $this->hasOne(RegionDetails::class);
    }

    public function faqs(){
        return $this->hasMany(RegionFaq::class);
    }

    public function meta(){
        return $this->hasOne(RegionMetaData::class);
    }

   public function packages()
    {
        return $this->hasManyThrough(
            Package::class,
            Location::class,
            'region_id',   
            'location_id',
            'id',
            'id'
        );
}
}
