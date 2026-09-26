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
 * @property string $muster_roll_no
 * @property int $project_id
 * @property \Illuminate\Support\Carbon $date
 * @property string|null $title
 * @property int|null $supervisor_id
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property string $status
 * @property int $total_workers
 * @property float $total_regular_amount
 * @property float $total_overtime_amount
 * @property float $total_deductions
 * @property float $total_net_amount
 * @property string|null $payment_method
 * @property int|null $paid_by
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property string|null $payout_reference
 * @property int|null $expense_id
 * @property string|null $notes
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Project $project
 * @property-read User|null $supervisor
 * @property-read User|null $approvedBy
 * @property-read User|null $paidBy
 * @property-read Expense|null $expense
 * @property-read User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MusterRollItem> $items
 */
class MusterRoll extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'muster_roll_no',
        'project_id',
        'date',
        'title',
        'supervisor_id',
        'approved_by',
        'approved_at',
        'status',
        'total_workers',
        'total_regular_amount',
        'total_overtime_amount',
        'total_deductions',
        'total_net_amount',
        'payment_method',
        'paid_by',
        'paid_at',
        'payout_reference',
        'expense_id',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'total_regular_amount' => 'decimal:2',
        'total_overtime_amount' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_net_amount' => 'decimal:2',
        'total_workers' => 'integer',
    ];

    public static function generateMusterRollNo(): string
    {
        $year = date('Y');
        $prefix = "MR-{$year}-";

        $lastRecord = self::withTrashed()
            ->where('muster_roll_no', 'LIKE', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        if (! $lastRecord) {
            return "{$prefix}0001";
        }

        $lastNum = (int) substr($lastRecord->muster_roll_no, strlen($prefix));
        $nextNum = str_pad((string) ($lastNum + 1), 4, '0', STR_PAD_LEFT);

        return "{$prefix}{$nextNum}";
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MusterRollItem::class);
    }

    /**
     * Recalculate totals from items.
     */
    public function recalculateTotals(): void
    {
        $this->loadMissing('items');

        $activeItems = $this->items->filter(function (MusterRollItem $item) {
            return (float) $item->days_worked > 0 || (float) $item->overtime_hours > 0;
        });

        $this->total_workers = $activeItems->count();
        $this->total_regular_amount = (float) $this->items->sum('regular_amount');
        $this->total_overtime_amount = (float) $this->items->sum('overtime_amount');
        $this->total_deductions = (float) $this->items->sum('deduction_amount');
        $this->total_net_amount = max(0.00, (float) ($this->total_regular_amount + $this->total_overtime_amount - $this->total_deductions));
        $this->save();
    }

    /**
     * Approve muster roll.
     */
    public function approve(User $user): void
    {
        $this->status = self::STATUS_APPROVED;
        $this->approved_by = $user->id;
        $this->approved_at = now();
        $this->save();
    }

    /**
     * Disburse payout and automatically post directly to project expenses.
     */
    public function recordPayout(array $data, User $user): Expense
    {
        $this->status = self::STATUS_PAID;
        $this->payment_method = $data['payment_method'] ?? 'cash';
        $this->payout_reference = $data['payout_reference'] ?? null;
        $this->paid_by = $user->id;
        $this->paid_at = now();

        // 1. Automatically create or update project expense record
        $expense = $this->expense_id ? Expense::find($this->expense_id) : new Expense();
        if (! $expense) {
            $expense = new Expense();
        }

        $expenseDesc = "Site Casual Labor Payout: {$this->muster_roll_no} - {$this->total_workers} workers on {$this->date->format('d M Y')}";
        if ($this->title) {
            $expenseDesc .= " ({$this->title})";
        }

        $expense->fill([
            'project_id' => $this->project_id,
            'user_id' => $user->id,
            'amount' => $this->total_net_amount,
            'category' => 'Labor',
            'description' => $expenseDesc,
            'expense_date' => $this->date,
            'reference_no' => $this->muster_roll_no,
            'status' => 'approved',
            'approved_by' => $this->approved_by ?? $user->id,
        ]);
        $expense->save();

        $this->expense_id = $expense->id;
        $this->save();

        return $expense;
    }
}
