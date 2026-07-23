<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperiencePageFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['page_id', 'question', 'answer', 'sort_order'];

    public function page()
    {
        return $this->belongsTo(ExperiencePage::class, 'page_id');
    }
}
