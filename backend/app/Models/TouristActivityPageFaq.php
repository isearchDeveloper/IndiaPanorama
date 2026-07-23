<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristActivityPageFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_activity_page_faqs';

    protected $fillable = ['page_id', 'question', 'answer', 'sort_order'];
}
