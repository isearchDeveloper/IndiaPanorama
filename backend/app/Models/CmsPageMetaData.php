<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPageMetaData extends Model{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    
    protected $table = 'cms_page_meta_data';

    protected $fillable = ['page_id','meta_title', 'meta_description', 'meta_keywords','h1_heading','meta_details'];

    public function page()
    {
        return $this->belongsTo(CmsPage::class, 'page_id', 'id');
    }
}
