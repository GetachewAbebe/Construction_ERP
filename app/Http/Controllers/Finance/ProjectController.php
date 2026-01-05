<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $projects = Project::query()
            ->when($status, fn($q) => $q->where('status', $status))
            ->withCount('expenses')
            ->latest()
            ->paginate(10);

        return view('finance.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('finance.projects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
            'budget'      => 'required|numeric|min:0',
            'status'      => 'required|in:active,completed,on_hold,cancelled',
        ]);

        try {
            Project::create($data);
            return redirect()->route('finance.projects.index')->with('status', 'Project created successfully.');
        } catch (\Exception $e) {
            Log::error('Project creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create project.');
        }
    }

    public function show(Project $project)
    {
        $project->load('expenses.user');
        return view('finance.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('finance.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
            'budget'      => 'required|numeric|min:0',
            'status'      => 'required|in:active,completed,on_hold,cancelled',
        ]);

        try {
            $project->update($data);
            return redirect()->route('finance.projects.index')->with('status', 'Project updated successfully.');
        } catch (\Exception $e) {
            Log::error('Project update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update project.');
        }
    }

    public function destroy(Project $project)
    {
        try {
            $project->delete();
            return redirect()->route('finance.projects.index')->with('status', 'Project deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Project deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete project.');
        }
    }
}

