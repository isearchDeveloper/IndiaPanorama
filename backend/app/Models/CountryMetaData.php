<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryMetaData extends Model{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    
    protected $table = 'country_meta_data';

    protected $fillable = ['country_id','meta_title', 'meta_description', 'meta_keywords','h1_heading','meta_details'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
