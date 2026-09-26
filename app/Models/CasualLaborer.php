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
 * @property string $worker_code
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string|null $phone
 * @property string|null $id_card_number
 * @property string $trade
 * @property string $skill_level
 * @property float $daily_wage_rate
 * @property float $overtime_hourly_rate
 * @property string|null $emergency_contact_name
 * @property string|null $emergency_contact_phone
 * @property int|null $project_id
 * @property bool $is_active
 * @property string|null $notes
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $full_name
 * @property-read Project|null $project
 * @property-read User|null $creator
 */
class CasualLaborer extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public const TRADES = [
        'Daily Laborer (Unskilled)',
        'Mason',
        'Carpenter / Formwork',
        'Bar Bender / Steel Fixer',
        'Plasterer',
        'Painter',
        'Welder',
        'Electrician',
        'Plumber',
        'Equipment Operator Helper',
        'Scaffolder',
        'Other Trade',
    ];

    public const SKILL_LEVELS = [
        'unskilled' => 'Unskilled (ጉልበት)',
        'semi_skilled' => 'Semi-Skilled (ረዳት)',
        'skilled' => 'Skilled (ባለሙያ)',
        'master' => 'Master / Lead Artisan (ዋና ባለሙያ)',
    ];

    protected $fillable = [
        'worker_code',
        'first_name',
        'middle_name',
        'last_name',
        'phone',
        'id_card_number',
        'trade',
        'skill_level',
        'daily_wage_rate',
        'overtime_hourly_rate',
        'emergency_contact_name',
        'emergency_contact_phone',
        'project_id',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'daily_wage_rate' => 'decimal:2',
        'overtime_hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'full_name',
    ];

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }

    /**
     * Auto-generate a sequential worker code if not supplied.
     */
    public static function generateWorkerCode(): string
    {
        $lastWorker = self::withTrashed()->orderByDesc('id')->first();
        $nextId = $lastWorker ? ($lastWorker->id + 1) : 1;

        return 'LAB-'.str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function musterRollItems(): HasMany
    {
        return $this->hasMany(MusterRollItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForProject($query, int $projectId)
    {
        return $query->where(function ($q) use ($projectId) {
            $q->where('project_id', $projectId)
              ->orWhereNull('project_id');
        });
    }
}
