<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property float|null $budget
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Expense> $expenses
 */
class Project extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'location',
        'start_date',
        'end_date',
        'budget',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
    ];

    /**
     * @return HasMany<Expense, $this>
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * @return HasMany<ProjectMilestone, $this>
     */
    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class)->orderBy('order')->orderBy('start_date');
    }

    public function purchaseRequisitions(): HasMany
    {
        return $this->hasMany(PurchaseRequisition::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function goodsReceivingNotes(): HasMany
    {
        return $this->hasMany(GoodsReceivingNote::class);
    }

    public function clientCertificates(): HasMany
    {
        return $this->hasMany(ClientPaymentCertificate::class)->orderBy('ipc_sequence');
    }

    public function casualLaborers(): HasMany
    {
        return $this->hasMany(CasualLaborer::class);
    }

    public function musterRolls(): HasMany
    {
        return $this->hasMany(MusterRoll::class)->orderByDesc('date');
    }

    public function maintenanceWorkOrders(): HasMany
    {
        return $this->hasMany(MaintenanceWorkOrder::class)->orderByDesc('id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProjectDocument::class)->orderByDesc('created_at');
    }

    public function getTotalCasualLaborCostAttribute(): float
    {
        return (float) $this->musterRolls()
            ->whereIn('status', ['approved', 'paid'])
            ->sum('total_net_amount');
    }

    public function getTotalCertifiedRevenueAttribute(): float
    {
        return (float) $this->clientCertificates()
            ->whereIn('status', ['certified', 'partially_paid', 'paid'])
            ->sum('total_certified_amount');
    }

    public function getTotalRetentionWithheldAttribute(): float
    {
        return (float) $this->clientCertificates()
            ->whereIn('status', ['certified', 'partially_paid', 'paid'])
            ->sum('retention_deduction');
    }

    public function getTotalRevenueCollectedAttribute(): float
    {
        return (float) $this->clientCertificates()
            ->sum('amount_paid');
    }

    public function getTotalExpensesAttribute()
    {
        return $this->expenses()->where('status', 'approved')->sum('amount');
    }

    public function getBudgetUsagePercentageAttribute()
    {
        if ($this->budget <= 0) {
            return 0;
        }

        return round(($this->total_expenses / $this->budget) * 100, 2);
    }

    /**
     * Calculate aggregate physical completion based on WBS milestone weights or arithmetic mean.
     */
    public function getPhysicalProgressPercentageAttribute(): float
    {
        $milestones = $this->milestones;

        if ($milestones->isEmpty()) {
            return 0.0;
        }

        $totalWeight = $milestones->sum('weight_pct');

        if ($totalWeight > 0) {
            $weightedProgress = $milestones->reduce(function ($carry, $m) {
                return $carry + (($m->progress * (float) $m->weight_pct) / 100);
            }, 0.0);

            return round(min(100, ($weightedProgress / $totalWeight) * 100), 1);
        }

        // Default to arithmetic mean if no weights are defined
        return round($milestones->avg('progress') ?? 0.0, 1);
    }

    /**
     * Get breakdown counts of milestones by status.
     *
     * @return array<string, int>
     */
    public function getMilestoneSummaryAttribute(): array
    {
        $milestones = $this->milestones;

        return [
            'total' => $milestones->count(),
            'completed' => $milestones->where('status', 'completed')->count(),
            'in_progress' => $milestones->where('status', 'in_progress')->count(),
            'delayed' => $milestones->filter(fn ($m) => $m->status === 'delayed' || $m->is_overdue)->count(),
            'pending' => $milestones->where('status', 'pending')->count(),
        ];
    }
}
