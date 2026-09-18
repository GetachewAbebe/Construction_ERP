<?php

declare(strict_types=1);

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\DailyProgressReport;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyProgressReport::with(['project', 'author']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('work_performed', 'like', "%{$q}%")
                    ->orWhere('materials_received', 'like', "%{$q}%")
                    ->orWhere('machinery_deployed', 'like', "%{$q}%");
            });
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('report_date', $request->date);
        }

        $reports = $query->latest('report_date')->paginate(15)->withQueryString();
        $projects = Project::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Projects/DailyReports/Index', [
            'reports' => $reports,
            'projects' => $projects,
            'filters' => $request->only(['q', 'project_id', 'date']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'report_date' => ['required', 'date'],
            'weather' => ['required', 'string', 'max:100'],
            'manpower_count' => ['required', 'integer', 'min:0'],
            'work_performed' => ['required', 'string'],
            'materials_received' => ['nullable', 'string'],
            'machinery_deployed' => ['nullable', 'string'],
            'safety_incidents' => ['nullable', 'string'],
        ]);

        $validated['created_by'] = Auth::id();

        DailyProgressReport::create($validated);

        return redirect()->route('projects.daily-reports.index')
            ->with('success', 'Daily progress diary successfully logged into project historical records.');
    }

    public function show(DailyProgressReport $report)
    {
        $report->load(['project', 'author']);

        return Inertia::render('Projects/DailyReports/Show', compact('report'));
    }
}
