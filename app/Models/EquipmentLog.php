<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $equipment_id
 * @property int|null $user_id
 * @property string $log_type
 * @property float|null $hours_at_log
 * @property float|null $cost
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $logged_at
 * @property-read Equipment|null $equipment
 * @property-read User|null $user
 */
class EquipmentLog extends Model
{
    /** @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory<self>> */
    use HasFactory;

    protected $table = 'equipment_logs';

    protected $fillable = [
        'equipment_id',
        'user_id',
        'log_type',
        'hours_at_log',
        'cost',
        'description',
        'logged_at',
    ];

    protected $casts = [
        'hours_at_log' => 'decimal:2',
        'cost' => 'decimal:2',
        'logged_at' => 'date',
    ];

    /**
     * @return BelongsTo<Equipment, $this>
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
