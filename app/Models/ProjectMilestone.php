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
 * @property int $project_id
 * @property string $title
 * @property string|null $wbs_code
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property int $progress
 * @property float $weight_pct
 * @property float|null $allocated_budget
 * @property string $status
 * @property int $order
 * @property int|null $created_by
 * @property-read Project $project
 * @property-read User|null $creator
 * @property-read bool $is_overdue
 */
class ProjectMilestone extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'wbs_code',
        'description',
        'start_date',
        'due_date',
        'completed_at',
        'progress',
        'weight_pct',
        'allocated_budget',
        'status',
        'order',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'date',
        'progress' => 'integer',
        'weight_pct' => 'decimal:2',
        'allocated_budget' => 'decimal:2',
        'order' => 'integer',
    ];

    protected $appends = [
        'is_overdue',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProjectDocument::class, 'milestone_id');
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'completed' || $this->progress >= 100) {
            return false;
        }

        return $this->due_date !== null && $this->due_date->isPast();
    }

    /**
     * Auto-sync status and completion timestamp based on progress value.
     */
    public function syncProgressState(int $progress): void
    {
        $this->progress = max(0, min(100, $progress));

        if ($this->progress === 100) {
            $this->status = 'completed';
            $this->completed_at = $this->completed_at ?? now()->toDateString();
        } elseif ($this->progress > 0) {
            $this->status = $this->is_overdue ? 'delayed' : 'in_progress';
            $this->completed_at = null;
        } else {
            $this->status = $this->is_overdue ? 'delayed' : 'pending';
            $this->completed_at = null;
        }
    }
}
