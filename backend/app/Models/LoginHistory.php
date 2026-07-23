<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class LoginHistory extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'user_id', 'email', 'ip_address', 'user_agent',
        'status', 'failure_reason', 'logged_in_at',
        'session_id', 'device_type', 'device_name',
        'os_name', 'browser_name', 'browser_version',
        'country', 'state', 'city',
        'last_activity_at', 'logout_at',
    ];

    protected $casts = [
        'logged_in_at'     => 'datetime',
        'last_activity_at' => 'datetime',
        'logout_at'        => 'datetime',
    ];

    // Minutes of inactivity before treating session as offline
    const ONLINE_THRESHOLD = 10;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsOnlineAttribute(): bool
    {
        if ($this->logout_at || $this->status !== 'success') return false;
        $activity = $this->last_activity_at ?? $this->logged_in_at;
        return $activity && $activity->diffInMinutes(now()) <= self::ONLINE_THRESHOLD;
    }

    public function getSessionExpiresAtAttribute(): ?\Carbon\Carbon
    {
        $activity = $this->last_activity_at ?? $this->logged_in_at;
        return $activity?->addMinutes(config('session.lifetime', 120));
    }

    public function getLocationAttribute(): string
    {
        return implode(', ', array_filter([$this->city, $this->state, $this->country])) ?: '—';
    }

    public function getDeviceIconAttribute(): string
    {
        return match ($this->device_type) {
            'mobile' => 'fa-mobile-alt',
            'tablet' => 'fa-tablet-alt',
            default  => 'fa-desktop',
        };
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeOnline(Builder $q): Builder
    {
        $threshold = now()->subMinutes(self::ONLINE_THRESHOLD);
        return $q->where('status', 'success')
            ->whereNull('logout_at')
            ->where(function ($q) use ($threshold) {
                $q->where('last_activity_at', '>=', $threshold)
                  ->orWhere(function ($q) use ($threshold) {
                      $q->whereNull('last_activity_at')
                        ->where('logged_in_at', '>=', $threshold);
                  });
            });
    }
}
