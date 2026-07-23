<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageMetaData extends Model{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    
    protected $table = 'page_meta_data';

    protected $fillable = ['page_id','meta_title', 'meta_description', 'meta_keywords','h1_heading','meta_details','meta_body_details'];

    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id', 'id');
    }
}
