<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristActivityFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'tourist_activity_faqs';

    protected $fillable = ['activity_id', 'question', 'answer', 'sort_order'];
}
