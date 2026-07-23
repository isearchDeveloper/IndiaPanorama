<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceHighlight extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['experience_id', 'text', 'sort_order'];

    public function experience()
    {
        return $this->belongsTo(Experience::class, 'experience_id');
    }
}
