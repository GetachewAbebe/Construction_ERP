<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $muster_roll_id
 * @property int $casual_laborer_id
 * @property string $trade
 * @property string $attendance_status
 * @property float $days_worked
 * @property float $daily_rate
 * @property float $regular_amount
 * @property float $overtime_hours
 * @property float $overtime_rate
 * @property float $overtime_amount
 * @property float $deduction_amount
 * @property float $net_amount
 * @property string|null $task_assigned
 * @property bool $signed
 * @property string|null $remarks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read MusterRoll $musterRoll
 * @property-read CasualLaborer $casualLaborer
 */
class MusterRollItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'muster_roll_id',
        'casual_laborer_id',
        'trade',
        'attendance_status',
        'days_worked',
        'daily_rate',
        'regular_amount',
        'overtime_hours',
        'overtime_rate',
        'overtime_amount',
        'deduction_amount',
        'net_amount',
        'task_assigned',
        'signed',
        'remarks',
    ];

    protected $casts = [
        'days_worked' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'regular_amount' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'deduction_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'signed' => 'boolean',
    ];

    public function musterRoll(): BelongsTo
    {
        return $this->belongsTo(MusterRoll::class);
    }

    public function casualLaborer(): BelongsTo
    {
        return $this->belongsTo(CasualLaborer::class);
    }

    /**
     * Compute calculated amounts based on days, rates, and overtime.
     */
    public function computeAmounts(): void
    {
        $days = (float) $this->days_worked;
        $dailyRate = (float) $this->daily_rate;
        $otHours = (float) $this->overtime_hours;
        $otRate = (float) $this->overtime_rate;
        $deduction = (float) $this->deduction_amount;

        $this->regular_amount = round($days * $dailyRate, 2);
        $this->overtime_amount = round($otHours * $otRate, 2);
        $this->net_amount = max(0.00, round($this->regular_amount + $this->overtime_amount - $deduction, 2));
    }
}
