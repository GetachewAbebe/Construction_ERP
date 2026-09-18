<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(function ($model): void {
            $model->logActivity('created');
        });

        static::updated(function ($model): void {
            $changes = [
                'before' => array_intersect_key($model->getOriginal(), $model->getDirty()),
                'after' => $model->getDirty(),
            ];
            $model->logActivity('updated', $changes);
        });

        static::deleted(function ($model): void {
            $model->logActivity('deleted');
        });

        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(static::class), true)) {
            static::restored(function ($model): void {
                $model->logActivity('restored');
            });
        }
    }

    /**
     * @param  array<string, mixed>|null  $changes
     */
    protected function logActivity(string $action, ?array $changes = null): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => static::class,
            'model_id' => $this->getKey(),
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
