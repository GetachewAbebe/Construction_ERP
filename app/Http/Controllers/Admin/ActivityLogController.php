<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user')
            ->latest()
            ->paginate(50);

        return \Inertia\Inertia::render('Admin/ActivityLogs/Index', compact('logs'));
    }

    public function show(ActivityLog $activityLog)
    {
        return \Inertia\Inertia::render('Admin/ActivityLogs/Show', ['log' => $activityLog->load('user')]);
    }
}
