<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalStatePageWhyVisit extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['page_id', 'title', 'description', 'sort_order'];

    public function page()
    {
        return $this->belongsTo(FestivalStatePage::class, 'page_id');
    }
}
