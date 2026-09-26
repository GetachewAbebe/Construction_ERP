<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequisition extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'requisition_no',
        'project_id',
        'requested_by',
        'required_date',
        'priority',
        'status',
        'purpose',
        'remarks',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'required_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public static function generateRequisitionNo(): string
    {
        $year = date('Y');
        $prefix = "PR-{$year}-";
        $last = static::withTrashed()
            ->where('requisition_no', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('requisition_no');

        if ($last && preg_match('/PR-\d{4}-(\d+)/', $last, $matches)) {
            $next = ((int) $matches[1]) + 1;
        } else {
            $next = 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionItem::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function getTotalEstimatedAmountAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            return (float) $item->quantity_requested * (float) $item->estimated_unit_price;
        });
    }
}
