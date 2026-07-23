<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarCategory extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    use HasFactory;

    protected $table = 'car_categories';

    protected $fillable = ['name', 'slug', 'icon', 'icon_alt', 'is_active', 'is_homepage'];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_homepage' => 'boolean',
    ];

    public function car(){

        return $this->hasMany(Car::class, 'category_id', 'id');
    }
}
