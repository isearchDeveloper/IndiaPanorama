<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarPackageFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'car_package_faqs';

    protected $fillable = ['package_id', 'question', 'answer', 'sort_order'];

    public function package()
    {
        return $this->belongsTo(CarPackage::class, 'package_id', 'id');
    }
}
