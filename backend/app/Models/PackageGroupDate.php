<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class PackageGroupDate extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'packages_group_dates';

    protected $fillable = [
        'package_id',
        'departure_date',
        'price',
        'total_seats',
        'booked_seats',
        'status',
    ];

    protected $casts = [
        'total_seats'  => 'integer',
        'booked_seats' => 'integer',
        'price'        => 'float',
    ];

    // -------------------------------------------------------
    // Boot — model-level guard
    // -------------------------------------------------------

    protected static function boot(): void
    {
        parent::boot();

        /**
         * Before any INSERT or UPDATE, ensure total_seats >= booked_seats.
         * This is the last line of defence — catches any code path that
         * bypasses the controller-level validation.
         */
        static::saving(function (PackageGroupDate $date) {
            $booked = (int) ($date->booked_seats ?? 0);
            $total  = (int) ($date->total_seats  ?? 0);

            if ($total < $booked) {
                throw ValidationException::withMessages([
                    'total_seats' => "Total Seats ({$total}) cannot be less than already booked seats ({$booked}) for departure {$date->departure_date}.",
                ]);
            }
        });
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    // -------------------------------------------------------
    // Dynamic / computed status accessor
    // -------------------------------------------------------

    public function getComputedStatusAttribute(): string
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }
        if ($this->booked_seats >= $this->total_seats) {
            return 'soldout';
        }
        if (($this->total_seats - $this->booked_seats) <= 5) {
            return 'few_seats';
        }
        return 'available';
    }

    // -------------------------------------------------------
    // Scopes
    // -------------------------------------------------------

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
            ->whereColumn('booked_seats', '<', 'total_seats');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('departure_date', '>=', now()->toDateString());
    }
}
