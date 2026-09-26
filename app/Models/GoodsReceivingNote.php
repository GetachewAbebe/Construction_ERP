<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceivingNote extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'grn_no',
        'purchase_order_id',
        'project_id',
        'vendor_id',
        'received_by',
        'received_date',
        'delivery_note_no',
        'status',
        'remarks',
    ];

    protected $casts = [
        'received_date' => 'date',
    ];

    public static function generateGrnNo(): string
    {
        $year = date('Y');
        $prefix = "GRN-{$year}-";
        $last = static::withTrashed()
            ->where('grn_no', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('grn_no');

        if ($last && preg_match('/GRN-\d{4}-(\d+)/', $last, $matches)) {
            $next = ((int) $matches[1]) + 1;
        } else {
            $next = 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceivingNoteItem::class);
    }

    public function getTotalQuantityAcceptedAttribute(): float
    {
        return (float) $this->items()->sum('quantity_accepted');
    }
}
