<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\LogsActivity;

class InventoryItem extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'inventory_items';

    /**
     * Mass-assignable attributes.
     */
    protected $fillable = [
        'item_no',
        'name',
        'description',
        'unit_of_measurement',
        'quantity',
        'store_location',
        'in_date',
        'status',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'in_date'  => 'date',
        'quantity' => 'integer',
    ];

    /**
     * Relationships
     */

    /**
     * All loans associated with this inventory item.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(InventoryLoan::class, 'inventory_item_id');
    }

    /**
     * Accessors / helpers
     */

    /**
     * Quantity currently out on approved loans.
     */
    public function getOnLoanQuantityAttribute(): int
    {
        return (int) $this->loans()
            ->where('status', 'approved')
            ->sum('quantity');
    }

    /**
     * Available quantity (stock minus approved loans).
     * If you don't want this yet, you can remove this accessor.
     */
    public function getAvailableQuantityAttribute(): int
    {
        $available = (int) $this->quantity - $this->on_loan_quantity;

        return $available > 0 ? $available : 0;
    }
}
