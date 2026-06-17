<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
