<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManageCityThingToDo extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'manage_city_things_to_do';

    protected $fillable = ['manage_city_id', 'title', 'description', 'duration', 'best_for', 'approx_cost', 'sort_order'];

    public function manageCity()
    {
        return $this->belongsTo(ManageCity::class, 'manage_city_id');
    }
}
