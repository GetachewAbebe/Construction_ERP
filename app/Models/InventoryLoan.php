<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'employee_id',
        'requested_by_user_id',
        'approved_by_user_id',
        'quantity',                // Added by migration
        'status',                  // pending | approved | rejected | returned
        'requested_at',
        'due_date',
        'approved_at',
        'returned_at',
        'remarks',                 // Original column from migration
        'notes',                   // Added by migration
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    // Relationships
    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'rejected_by');
    }
}
