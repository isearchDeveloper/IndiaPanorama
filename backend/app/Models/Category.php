<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;


    protected $table = 'categories';

    protected $fillable = ['name','slug','description','title','sub_title','banner_image'];

    public function packages(){ 
        return $this->hasMany(Package::class, 'category_id','id'); 
    }

    public function allPackages()
    {
        return Package::where(function ($q) {
            $q->where('packages.category_id', $this->id)
              ->orWhereHas('packageCategories', function ($p) {
                  $p->where('category_id', $this->id);
              });

        });
    }
}
