<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryFaq extends Model{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    
    protected $table = 'country_faqs';

    protected $fillable = ['country_id', 'question', 'answer'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
