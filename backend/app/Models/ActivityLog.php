<?php

namespace App\Models;

use App\Services\GeoLocation;
use App\Services\UserAgentParser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'user_id', 'user_name', 'role', 'action', 'module',
        'description', 'ip_address', 'user_agent', 'properties',
        'old_value', 'new_value',
        'device_type', 'os_name', 'browser_name', 'country',
    ];

    protected $casts = [
        'properties' => 'array',
        'old_value'  => 'array',
        'new_value'  => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record an activity.
     *
     * @param  string       $action      created|updated|deleted|viewed|login|logout|status-changed …
     * @param  string       $module      Dashboard|Package|Booking|Permission …
     * @param  string       $description Human-readable sentence
     * @param  array        $properties  Misc key/value pairs
     * @param  mixed        $oldValue    Snapshot before change
     * @param  mixed        $newValue    Snapshot after change
     */
    public static function log(
        string $action,
        string $module,
        string $description = '',
        array  $properties  = [],
        mixed  $oldValue    = null,
        mixed  $newValue    = null
    ): void {
        $user = Auth::user();
        $ua   = Request::userAgent();
        $ip   = Request::ip();
        $device = UserAgentParser::parse($ua);
        $geo    = GeoLocation::lookup($ip);

        static::create([
            'user_id'     => $user?->id,
            'user_name'   => $user?->name ?? 'System',
            'role'        => $user?->is_super_admin ? 'Super Admin' : ($user?->role ?? null),
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
            'ip_address'  => $ip,
            'user_agent'  => $ua,
            'properties'  => $properties ?: null,
            'old_value'   => $oldValue !== null ? (is_array($oldValue) ? $oldValue : ['value' => $oldValue]) : null,
            'new_value'   => $newValue !== null ? (is_array($newValue) ? $newValue : ['value' => $newValue]) : null,
            'device_type' => $device['device_type'],
            'os_name'     => $device['os_name'],
            'browser_name'=> $device['browser_name'],
            'country'     => $geo['country'],
        ]);
    }
}
