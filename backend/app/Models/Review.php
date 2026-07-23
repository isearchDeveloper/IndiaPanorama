<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'reviews';

    protected $casts = [
        'is_approved' => 'boolean',
        'rating'      => 'integer',
    ];

    protected $fillable = [
        'package_id',
        'user_id',
        'customer_name',
        'rating',
        'comment',
        'is_approved',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
