<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageLocation extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'package_locations';
    public $timestamps = false;

    protected $fillable = [
        'package_id',
        'location_id',
        'highlights'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }
}
