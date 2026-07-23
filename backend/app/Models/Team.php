<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'teams';

    protected $fillable = ['name', 'dep_id', 'description', 'profile_image', 'about','is_active'];

    // Each team belongs to one department
    public function department()
    {
        return $this->belongsTo(Department::class, 'dep_id', 'id');
    }
}
