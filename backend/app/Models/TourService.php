<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TourService extends Model {
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;


    protected $table = 'tour_services';

    protected $fillable = ['title','link','banner_image','banner_image_alt','is_active'];

}
