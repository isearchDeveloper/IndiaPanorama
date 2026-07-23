<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryDetails extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    use HasFactory;

    protected $table = 'country_details';

    protected $fillable = [
        'country_id',
        'title',
        'sub_title',
        'banner_image',
        'banner_image_alt',
        'about',
        'author_name'
    ];

    public function country(){ 
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
