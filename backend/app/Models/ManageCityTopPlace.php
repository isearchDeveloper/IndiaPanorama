<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManageCityTopPlace extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'manage_city_top_places';

    protected $fillable = ['manage_city_id', 'name', 'description', 'sort_order'];

    public function manageCity()
    {
        return $this->belongsTo(ManageCity::class, 'manage_city_id');
    }
}
