<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManageCityQuickFact extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'manage_city_quick_facts';

    protected $fillable = ['manage_city_id', 'label', 'value', 'sort_order'];

    public function manageCity()
    {
        return $this->belongsTo(ManageCity::class, 'manage_city_id');
    }
}
