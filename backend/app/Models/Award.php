<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Award extends Model {
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;


    protected $table = 'awards';

    protected $fillable = ['title','award_year','description','banner_image','is_active'];

}
