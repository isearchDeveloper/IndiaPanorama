<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceQuickInfo extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['experience_id', 'label', 'value', 'sort_order'];

    public function experience()
    {
        return $this->belongsTo(Experience::class, 'experience_id');
    }
}
