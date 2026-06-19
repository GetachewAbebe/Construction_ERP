<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int|null $inventory_item_id
 * @property int|null $employee_id
 * @property int|null $requested_by_user_id
 * @property int|null $approved_by_user_id
 * @property int $quantity
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $requested_at
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property string|null $remarks
 * @property string|null $notes
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property int|null $rejected_by
 * @property \Illuminate\Support\Carbon|null $rejected_at
 * @property \Illuminate\Support\Carbon|null $returned_at
 * @property string|null $rejection_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class InventoryLoan extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'inventory_item_id',
        'employee_id',
        'requested_by_user_id',
        'approved_by_user_id',
        'quantity',
        'status',
        'requested_at',
        'due_date',
        'remarks',
        'notes',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'returned_at',
        'rejection_reason',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'due_date' => 'date',
        'approved_at' => 'datetime',
        'returned_at' => 'datetime',
        'rejected_at' => 'datetime',
        'quantity' => 'integer',
    ];

    // Relationships
    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'rejected_by');
    }
}
