<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SpecialPackage extends Model {
    use \Illuminate\Database\Eloquent\SoftDeletes;


    protected $table = 'special_package';

    protected $fillable = ['package_id','title','banner_image','banner_image_alt','is_active'];

    public function package(){
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }
}
