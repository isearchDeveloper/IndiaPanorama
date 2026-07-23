<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristAttractionPageFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_attraction_page_faqs';

    protected $fillable = ['page_id', 'question', 'answer', 'sort_order'];
}
