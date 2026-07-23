<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarHighlightTag extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['car_id', 'text', 'icon', 'sort_order'];
}
