<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateDetails extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'state_details';

    protected $fillable = [
        'state_id',
        'title',
        'sub_title',
        'banner_image',
        'banner_image_alt',
        'about',
        'author_name',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
