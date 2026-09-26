<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $equipment_type
 * @property int $interval_hours
 * @property int|null $interval_days
 * @property string|null $description
 * @property array|null $checklist_tasks
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class EquipmentMaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'equipment_type',
        'interval_hours',
        'interval_days',
        'description',
        'checklist_tasks',
        'is_active',
    ];

    protected $casts = [
        'interval_hours' => 'integer',
        'interval_days' => 'integer',
        'checklist_tasks' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
