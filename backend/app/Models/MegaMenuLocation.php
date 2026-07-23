<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MegaMenuLocation extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'mega_menu_locations';

    protected $fillable = ['type', 'location_id','is_active'];


    public function related()
    {
        return $this->morphTo(__FUNCTION__, 'type', 'location_id');
    }

}

