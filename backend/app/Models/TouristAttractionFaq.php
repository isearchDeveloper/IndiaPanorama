<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristAttractionFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_attraction_faqs';

    protected $fillable = ['attraction_id', 'question', 'answer', 'sort_order'];
}
