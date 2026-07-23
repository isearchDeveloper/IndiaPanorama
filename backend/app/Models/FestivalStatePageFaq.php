<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalStatePageFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['page_id', 'question', 'answer', 'sort_order'];

    public function page()
    {
        return $this->belongsTo(FestivalStatePage::class, 'page_id');
    }
}
