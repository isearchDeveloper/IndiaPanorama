<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['experience_id', 'question', 'answer', 'sort_order'];

    public function experience()
    {
        return $this->belongsTo(Experience::class, 'experience_id');
    }
}
