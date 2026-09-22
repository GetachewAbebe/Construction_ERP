<?php

declare(strict_types=1);

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMilestone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectMilestoneController extends Controller
{
    /**
     * Store a new milestone for the specified project.
     */
    public function store(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'wbs_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'progress' => 'nullable|integer|min:0|max:100',
            'weight_pct' => 'nullable|numeric|min:0|max:100',
            'allocated_budget' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:pending,in_progress,completed,delayed',
            'order' => 'nullable|integer|min:0',
        ]);

        $nextOrder = $validated['order'] ?? (($project->milestones()->max('order') ?? 0) + 1);

        $milestone = new ProjectMilestone([
            'project_id' => $project->id,
            'title' => $validated['title'],
            'wbs_code' => $validated['wbs_code'] ?? null,
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'weight_pct' => (float) ($validated['weight_pct'] ?? 0),
            'allocated_budget' => isset($validated['allocated_budget']) ? (float) $validated['allocated_budget'] : null,
            'order' => (int) $nextOrder,
            'created_by' => Auth::id(),
        ]);

        $progress = (int) ($validated['progress'] ?? 0);
        $milestone->syncProgressState($progress);

        if (! empty($validated['status'])) {
            $milestone->status = $validated['status'];
            if ($validated['status'] === 'completed' && ! $milestone->completed_at) {
                $milestone->completed_at = now()->toDateString();
                $milestone->progress = 100;
            }
        }

        $milestone->save();

        return back()->with('success', "WBS Milestone '{$milestone->title}' added successfully.");
    }

    /**
     * Update an existing milestone.
     */
    public function update(Request $request, Project $project, ProjectMilestone $milestone): RedirectResponse
    {
        abort_unless($milestone->project_id === $project->id, 404);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'wbs_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'progress' => 'nullable|integer|min:0|max:100',
            'weight_pct' => 'nullable|numeric|min:0|max:100',
            'allocated_budget' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:pending,in_progress,completed,delayed',
            'order' => 'nullable|integer|min:0',
        ]);

        $milestone->fill([
            'title' => $validated['title'],
            'wbs_code' => $validated['wbs_code'] ?? null,
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'weight_pct' => (float) ($validated['weight_pct'] ?? 0),
            'allocated_budget' => isset($validated['allocated_budget']) ? (float) $validated['allocated_budget'] : null,
            'order' => isset($validated['order']) ? (int) $validated['order'] : $milestone->order,
        ]);

        if (isset($validated['progress'])) {
            $milestone->syncProgressState((int) $validated['progress']);
        }

        if (! empty($validated['status'])) {
            $milestone->status = $validated['status'];
            if ($validated['status'] === 'completed') {
                $milestone->completed_at = $milestone->completed_at ?? now()->toDateString();
                $milestone->progress = 100;
            } elseif ($milestone->progress >= 100 && $validated['status'] !== 'completed') {
                $milestone->progress = 90;
                $milestone->completed_at = null;
            }
        }

        $milestone->save();

        return back()->with('success', "WBS Milestone '{$milestone->title}' updated successfully.");
    }

    /**
     * Fast update for milestone progress (e.g. from slider/inline input).
     */
    public function updateProgress(Request $request, Project $project, ProjectMilestone $milestone): RedirectResponse
    {
        abort_unless($milestone->project_id === $project->id, 404);

        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $milestone->syncProgressState((int) $validated['progress']);
        $milestone->save();

        return back()->with('success', "Milestone '{$milestone->title}' progress set to {$milestone->progress}%.");
    }

    /**
     * Soft-delete a milestone.
     */
    public function destroy(Project $project, ProjectMilestone $milestone): RedirectResponse
    {
        abort_unless($milestone->project_id === $project->id, 404);

        $title = $milestone->title;
        $milestone->delete();

        return back()->with('success', "Milestone '{$title}' removed from WBS.");
    }
}
