<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageMetaData extends Model{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    
    protected $table = 'package_meta_data';

    protected $fillable = ['package_id','meta_title', 'meta_description', 'meta_keywords','h1_heading','meta_details'];

    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id', 'id');
    }
}
