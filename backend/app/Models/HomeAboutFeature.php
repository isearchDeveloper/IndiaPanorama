<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeAboutFeature extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table    = 'home_about_features';
    protected $fillable = ['text', 'icon_class', 'feature_description', 'sort_order', 'is_active'];
    protected $casts    = ['is_active' => 'boolean', 'sort_order' => 'integer'];

    public function scopeActive($q)  { return $q->where('is_active', true); }
    public function scopeOrdered($q) { return $q->orderBy('sort_order')->orderBy('id'); }
}
