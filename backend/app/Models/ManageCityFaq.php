<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManageCityFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'manage_city_faqs';

    protected $fillable = ['manage_city_id', 'question', 'answer', 'sort_order'];

    public function manageCity()
    {
        return $this->belongsTo(ManageCity::class, 'manage_city_id');
    }
}
