<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string|null $plate_number
 * @property string $type
 * @property int|null $project_id
 * @property string $status
 * @property float $operating_hours
 * @property float|null $next_service_hours
 * @property string $fuel_type
 * @property \Illuminate\Support\Carbon|null $purchase_date
 * @property float|null $purchase_cost
 * @property string|null $notes
 * @property-read Project|null $project
 * @property-read \Illuminate\Database\Eloquent\Collection<int, EquipmentLog> $logs
 */
class Equipment extends Model
{
    /** @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory<self>> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'equipment';

    protected $fillable = [
        'name',
        'plate_number',
        'type',
        'project_id',
        'status',
        'operating_hours',
        'next_service_hours',
        'fuel_type',
        'purchase_date',
        'purchase_cost',
        'notes',
    ];

    protected $casts = [
        'operating_hours' => 'decimal:2',
        'next_service_hours' => 'decimal:2',
        'purchase_cost' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return HasMany<EquipmentLog, $this>
     */
    public function logs(): HasMany
    {
        return $this->hasMany(EquipmentLog::class);
    }

    /**
     * @return HasMany<MaintenanceWorkOrder, $this>
     */
    public function workOrders(): HasMany
    {
        return $this->hasMany(MaintenanceWorkOrder::class)->orderByDesc('id');
    }

    public function getTotalMaintenanceCostAttribute(): float
    {
        return (float) $this->workOrders()->where('status', 'completed')->sum('total_cost');
    }

    protected $appends = [
        'status_badge',
        'fuel_efficiency',
        'service_status',
    ];

    /**
     * Check if machine is overdue for preventive maintenance.
     */
    public function isServiceOverdue(): bool
    {
        return $this->service_status === 'overdue';
    }

    /**
     * Compute fuel burn rate in Liters per Operating Hour (L/hr).
     */
    public function getFuelEfficiencyAttribute(): ?float
    {
        $totalFuel = (float) $this->logs()->whereNotNull('fuel_liters')->sum('fuel_liters');
        $hours = (float) $this->operating_hours;

        if ($totalFuel > 0 && $hours > 0) {
            return round($totalFuel / $hours, 2);
        }

        return null;
    }

    /**
     * Service due alert status: 'overdue', 'due_soon', 'ok'.
     */
    public function getServiceStatusAttribute(): string
    {
        if (! $this->next_service_hours || $this->next_service_hours <= 0) {
            return 'ok';
        }

        $current = (float) $this->operating_hours;
        $next = (float) $this->next_service_hours;

        if ($current >= $next) {
            return 'overdue';
        }

        if (($next - $current) <= 25.0) {
            return 'due_soon';
        }

        return 'ok';
    }

    /**
     * Get badge color class for status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'operational' => 'badge-success',
            'maintenance' => 'badge-warning',
            'breakdown' => 'badge-error',
            'idle' => 'badge-ghost',
            default => 'badge-info',
        };
    }
}
