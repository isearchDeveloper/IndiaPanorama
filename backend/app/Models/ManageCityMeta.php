<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManageCityMeta extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'manage_city_meta';

    protected $fillable = ['manage_city_id', 'meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details'];

    public function manageCity()
    {
        return $this->belongsTo(ManageCity::class, 'manage_city_id');
    }
}
