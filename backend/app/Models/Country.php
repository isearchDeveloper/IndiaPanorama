<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    use HasFactory;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'countries';

    protected $fillable = [
        'slug',
        'name',
        'code',
        'faq_title'
    ];

    public function locations()
    {
        return $this->hasMany(Location::class, 'country_id', 'id');
    }

    /** States / UTs belonging to this country */
    public function states()
    {
        return $this->hasMany(State::class, 'country_id', 'id');
    }
    
    public function packages()
    { 
        return $this->hasMany(Package::class,'country_id','id'); 
    }

    public function faqs()
    {
        return $this->hasMany(CountryFaq::class,'country_id','id');
    }

    public function details(){
        return $this->hasOne(CountryDetails::class, 'country_id', 'id');
    }

    public function meta(){
        return $this->hasOne(CountryMetaData::class, 'country_id', 'id');
    }
}
