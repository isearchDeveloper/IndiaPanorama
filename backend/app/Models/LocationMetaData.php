<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationMetaData extends Model{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    
    protected $table = 'location_meta_data';

    protected $fillable = ['location_id','meta_title', 'meta_description', 'meta_keywords','h1_heading','meta_details'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
