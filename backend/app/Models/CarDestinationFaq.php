<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarDestinationFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'car_destination_faqs';

    protected $fillable = ['destination_id', 'question', 'answer', 'sort_order'];

    public function destination()
    {
        return $this->belongsTo(CarDestination::class, 'destination_id', 'id');
    }
}
