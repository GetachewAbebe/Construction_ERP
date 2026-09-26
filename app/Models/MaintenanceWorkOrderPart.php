<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $maintenance_work_order_id
 * @property int|null $inventory_item_id
 * @property string $part_name
 * @property string|null $part_number
 * @property float $quantity
 * @property string $unit_of_measurement
 * @property float $unit_price
 * @property float $total_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read MaintenanceWorkOrder $workOrder
 * @property-read InventoryItem|null $inventoryItem
 */
class MaintenanceWorkOrderPart extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_work_order_id',
        'inventory_item_id',
        'part_name',
        'part_number',
        'quantity',
        'unit_of_measurement',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(MaintenanceWorkOrder::class, 'maintenance_work_order_id');
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function calculateTotal(): void
    {
        $this->total_price = round(((float) $this->quantity) * ((float) $this->unit_price), 2);
    }
}
