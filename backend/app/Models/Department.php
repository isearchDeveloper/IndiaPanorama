<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Department extends Model {
    use \Illuminate\Database\Eloquent\SoftDeletes;


    protected $table = 'departments';

    protected $fillable = ['name'];

    public function teams()
    {
        return $this->hasMany(Team::class, 'dep_id', 'id');
    }

}
