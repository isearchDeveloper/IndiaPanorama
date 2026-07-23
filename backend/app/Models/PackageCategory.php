<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageCategory extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'packages_categories';

    protected $fillable = [
        'package_id',
        'category_id'
    ];

    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
}
