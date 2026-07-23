<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperiencePageActivity extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['page_id', 'title', 'description', 'best_time', 'best_for', 'approximate_cost', 'sort_order'];

    public function page()
    {
        return $this->belongsTo(ExperiencePage::class, 'page_id');
    }
}
