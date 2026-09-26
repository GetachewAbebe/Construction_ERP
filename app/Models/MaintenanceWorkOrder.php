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
 * @property string $work_order_no
 * @property int $equipment_id
 * @property int|null $project_id
 * @property string $order_type
 * @property string $priority
 * @property string $status
 * @property string $title
 * @property float $operating_hours
 * @property string|null $fault_description
 * @property string|null $work_performed
 * @property int|null $assigned_mechanic_id
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $completed_date
 * @property float $downtime_hours
 * @property float $parts_cost
 * @property float $labor_cost
 * @property float $external_cost
 * @property float $total_cost
 * @property int|null $expense_id
 * @property string|null $notes
 * @property int|null $created_by
 * @property int|null $completed_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Equipment $equipment
 * @property-read Project|null $project
 * @property-read User|null $assignedMechanic
 * @property-read User|null $creator
 * @property-read User|null $completer
 * @property-read Expense|null $expense
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MaintenanceWorkOrderPart> $parts
 */
class MaintenanceWorkOrder extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const TYPE_PREVENTIVE = 'preventive';
    public const TYPE_BREAKDOWN = 'breakdown';
    public const TYPE_INSPECTION = 'inspection';
    public const TYPE_TIRE_TRACKS = 'tire_tracks';

    public const PRIORITY_ROUTINE = 'routine';
    public const PRIORITY_URGENT = 'urgent';
    public const PRIORITY_CRITICAL = 'critical_downtime';

    protected $fillable = [
        'work_order_no',
        'equipment_id',
        'project_id',
        'order_type',
        'priority',
        'status',
        'title',
        'operating_hours',
        'fault_description',
        'work_performed',
        'assigned_mechanic_id',
        'start_date',
        'completed_date',
        'downtime_hours',
        'parts_cost',
        'labor_cost',
        'external_cost',
        'total_cost',
        'expense_id',
        'notes',
        'created_by',
        'completed_by',
    ];

    protected $casts = [
        'operating_hours' => 'decimal:2',
        'downtime_hours' => 'decimal:2',
        'parts_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'external_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'start_date' => 'datetime',
        'completed_date' => 'datetime',
    ];

    public static function generateWorkOrderNo(): string
    {
        $year = date('Y');
        $prefix = "WO-{$year}-";

        $last = self::withTrashed()
            ->where('work_order_no', 'LIKE', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        if (! $last) {
            return "{$prefix}0001";
        }

        $lastNum = (int) substr($last->work_order_no, strlen($prefix));
        $nextNum = str_pad((string) ($lastNum + 1), 4, '0', STR_PAD_LEFT);

        return "{$prefix}{$nextNum}";
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedMechanic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_mechanic_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(MaintenanceWorkOrderPart::class);
    }

    /**
     * Recalculate cost totals from spare parts and labor.
     */
    public function recalculateTotals(): void
    {
        $this->loadMissing('parts');
        $this->parts_cost = (float) $this->parts->sum('total_price');
        $this->total_cost = round((float) $this->parts_cost + (float) $this->labor_cost + (float) $this->external_cost, 2);
        $this->save();
    }

    /**
     * Complete maintenance work order, restore machine status, deduct inventory parts, and post project expense.
     */
    public function complete(User $user, ?float $finalHours = null, ?float $downtime = null, ?string $workNotes = null): void
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_date = now();
        $this->completed_by = $user->id;

        if ($downtime !== null) {
            $this->downtime_hours = $downtime;
        }

        if ($workNotes !== null) {
            $this->work_performed = $workNotes;
        }

        // 1. Update equipment machine status and operating hours
        $machine = $this->equipment;
        if ($machine) {
            if ($finalHours !== null && $finalHours > (float) $machine->operating_hours) {
                $machine->operating_hours = $finalHours;
                $this->operating_hours = $finalHours;
            }

            $machine->status = 'operational';

            // If preventive service, advance next service interval by 250h
            if ($this->order_type === self::TYPE_PREVENTIVE) {
                $machine->next_service_hours = (float) $machine->operating_hours + 250.00;
            }

            $machine->save();

            // Log event in legacy equipment_logs for audit compatibility
            EquipmentLog::create([
                'equipment_id' => $machine->id,
                'user_id' => $user->id,
                'log_type' => $this->order_type === self::TYPE_PREVENTIVE ? 'service' : 'repair',
                'hours_at_log' => $machine->operating_hours,
                'cost' => $this->total_cost,
                'description' => "Closed Work Order #{$this->work_order_no}: {$this->title}",
                'logged_at' => now()->toDateString(),
            ]);
        }

        // 2. Automatically deduct consumed spare parts & lubricants from inventory
        $this->loadMissing('parts');
        foreach ($this->parts as $part) {
            if ($part->inventory_item_id) {
                $stockItem = InventoryItem::find($part->inventory_item_id);
                if ($stockItem) {
                    $prevQty = (int) $stockItem->quantity;
                    $deductQty = (int) ceil((float) $part->quantity);
                    $newQty = max(0, $prevQty - $deductQty);

                    $stockItem->quantity = $newQty;
                    $stockItem->save();

                    InventoryLog::create([
                        'inventory_item_id' => $stockItem->id,
                        'user_id' => $user->id,
                        'change_amount' => -$deductQty,
                        'previous_quantity' => $prevQty,
                        'new_quantity' => $newQty,
                        'reason' => "Work Order {$this->work_order_no}",
                        'remarks' => "Installed on {$machine?->name} ({$machine?->plate_number})",
                    ]);
                }
            }
        }

        // 3. Automatically post/update project expense if project_id is assigned and total_cost > 0
        if ($this->project_id && (float) $this->total_cost > 0) {
            $expense = $this->expense_id ? Expense::find($this->expense_id) : new Expense();
            if (! $expense) {
                $expense = new Expense();
            }

            $expense->fill([
                'project_id' => $this->project_id,
                'user_id' => $user->id,
                'amount' => $this->total_cost,
                'category' => 'Equipment',
                'description' => "Machinery Maintenance #{$this->work_order_no}: {$machine?->name} ({$this->title})",
                'expense_date' => now()->toDateString(),
                'reference_no' => $this->work_order_no,
                'status' => 'approved',
                'approved_by' => $user->id,
            ]);
            $expense->save();

            $this->expense_id = $expense->id;
        }

        $this->save();
    }
}
