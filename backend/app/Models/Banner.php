<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model {
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;


    protected $table = 'banners';

    protected $fillable = ['url','title','subtitle','button_text','banner_image','banner_image_alt','is_active','is_static','sort_order'];
}
