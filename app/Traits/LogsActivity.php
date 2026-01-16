<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('created');
        });

        static::updated(function ($model) {
            $changes = [
                'before' => array_intersect_key($model->getOriginal(), $model->getDirty()),
                'after'  => $model->getDirty(),
            ];
            $model->logActivity('updated', $changes);
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted');
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->logActivity('restored');
            });
        }
    }

    protected function logActivity($action, $changes = null)
    {
        ActivityLog::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'model_type' => get_class($this),
            'model_id'   => $this->id,
            'changes'    => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
