<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PackageImage extends Model {
    use \Illuminate\Database\Eloquent\SoftDeletes;


    protected $table = 'package_images';

    protected $fillable = ['package_id','image_path','image_alt','sort_order','source_of_image','download_date','account_id','license_key','license_key_file'];

    public function package(){
        return $this->belongsTo(Package::class,'package_id','id');
    }
}
