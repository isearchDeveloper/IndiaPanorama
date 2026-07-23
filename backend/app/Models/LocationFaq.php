<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationFaq extends Model{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    
    protected $table = 'location_faqs';

    protected $fillable = ['location_id', 'question', 'answer'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
