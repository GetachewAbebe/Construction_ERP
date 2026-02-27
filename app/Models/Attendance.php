<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // ---- SESSION STATUS CONSTANTS ----
    public const SESSION_PRESENT = 'present';

    public const SESSION_ABSENT = 'absent';

    public const SESSION_LEAVE = 'leave';

    public const SESSION_LATE = 'late';

    protected $fillable = [
        'employee_id',
        'date',
        'morning_status',
        'afternoon_status',
        'total_credit',
        'clock_in',
        'clock_out',
        'status', // Legacy fallback
        'note',
        'ip_address',
        'location_name',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeOpen($query)
    {
        return $query->whereNull('clock_out');
    }

    public function scopeClosed($query)
    {
        return $query->whereNotNull('clock_out');
    }

    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', Carbon::today());
    }

    public function scopeSessionStatus($query, string $session, string $status)
    {
        $column = $session === 'morning' ? 'morning_status' : 'afternoon_status';

        return $query->where($column, $status);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isOpen(): bool
    {
        return is_null($this->clock_out);
    }

    public function isClosed(): bool
    {
        return ! is_null($this->clock_out);
    }

    public function isPresent(): bool
    {
        return $this->status === self::STATUS_PRESENT;
    }

    public function isLate(): bool
    {
        return $this->status === self::STATUS_LATE;
    }

    public function isAbsent(): bool
    {
        return $this->status === self::STATUS_ABSENT;
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (Computed fields)
    |--------------------------------------------------------------------------
    */

    /**
     * Worked minutes between clock_in and clock_out.
     */
    public function getWorkedMinutesAttribute(): ?int
    {
        if (! $this->clock_in || ! $this->clock_out) {
            return null;
        }

        return $this->clock_in->diffInMinutes($this->clock_out);
    }

    /**
     * Worked hours (float), e.g. 7.5
     */
    public function getWorkedHoursAttribute(): ?float
    {
        if ($this->worked_minutes === null) {
            return null;
        }

        return round($this->worked_minutes / 60, 2);
    }

    /**
     * Calculate session-based "Payable Weight"
     * e.g. AM Present + PM Absent = 0.5 Credits
     */
    public function calculateCredits(): float
    {
        $credit = 0.0;
        if (in_array($this->morning_status, [self::SESSION_PRESENT, self::SESSION_LATE])) {
            $credit += 0.5;
        }
        if ($this->afternoon_status === self::SESSION_PRESENT) {
            $credit += 0.5;
        }

        return $credit;
    }
}
