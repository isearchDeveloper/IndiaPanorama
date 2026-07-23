<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_super_admin',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
            'is_super_admin'    => 'boolean',
            'is_active'         => 'boolean',
        ];
    }

    private ?array $cachedPermissions = null;

    public function permissionNames(): array
    {
        if ($this->cachedPermissions === null) {
            $this->cachedPermissions = DB::table('admin_user_permissions')
                ->where('user_id', $this->id)
                ->pluck('permission_name')
                ->toArray();
        }
        return $this->cachedPermissions;
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->is_super_admin) return true;
        return in_array($permission, $this->permissionNames());
    }

    public function syncPermissions(array $names): void
    {
        DB::table('admin_user_permissions')->where('user_id', $this->id)->delete();
        $rows = array_map(fn($n) => [
            'user_id'         => $this->id,
            'permission_name' => $n,
            'created_at'      => now(),
            'updated_at'      => now(),
        ], array_unique($names));
        if ($rows) DB::table('admin_user_permissions')->insert($rows);
        $this->cachedPermissions = null;
    }
}
