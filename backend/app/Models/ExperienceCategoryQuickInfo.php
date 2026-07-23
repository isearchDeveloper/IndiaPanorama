<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceCategoryQuickInfo extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['category_id', 'label', 'value', 'sort_order'];

    public function category()
    {
        return $this->belongsTo(ExperienceCategory::class, 'category_id');
    }
}
