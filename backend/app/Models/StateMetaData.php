<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateMetaData extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'state_meta_data';

    protected $fillable = [
        'state_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'h1_heading',
        'meta_details',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
