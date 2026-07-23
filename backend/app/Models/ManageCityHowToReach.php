<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManageCityHowToReach extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'manage_city_how_to_reach';

    protected $fillable = ['manage_city_id', 'mode', 'description', 'sort_order'];

    public function manageCity()
    {
        return $this->belongsTo(ManageCity::class, 'manage_city_id');
    }
}
