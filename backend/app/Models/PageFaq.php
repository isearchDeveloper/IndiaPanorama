<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageFaq extends Model{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    
    protected $table = 'page_faqs';

    protected $fillable = ['page_id', 'question', 'answer'];

    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id', 'id');
    }
}
