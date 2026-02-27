<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('q');

        $projects = Project::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->withCount('expenses')
            ->latest()
            ->paginate(10);

        return view('finance.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('finance.projects.create');
    }

    public function store(\App\Http\Requests\Projects\StoreProjectRequest $request)
    {
        $data = $request->validated();

        try {
            $project = Project::create($data);

            return redirect()->route('finance.projects.index')
                ->with('success', "Project site '{$project->name}' has been successfully added to registry.");
        } catch (\Exception $e) {
            Log::error('Project creation failed: '.$e->getMessage());

            return back()->withInput()->with('error', 'Error: Failed to initialize project site. Please check input values.');
        }
    }

    public function show(Project $project)
    {
        $project->load(['expenses.user']);

        return view('finance.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('finance.projects.edit', compact('project'));
    }

    public function update(\App\Http\Requests\Projects\UpdateProjectRequest $request, Project $project)
    {
        $data = $request->validated();

        try {
            $project->update($data);

            return redirect()->route('finance.projects.index')
                ->with('success', "Project details for '{$project->name}' have been successfully updated.");
        } catch (\Exception $e) {
            Log::error('Project update failed: '.$e->getMessage());

            return back()->withInput()->with('error', 'Critical Error: Failed to update project configuration.');
        }
    }

    public function destroy(Project $project)
    {
        try {
            $name = $project->name;
            $project->delete();

            return redirect()->route('finance.projects.index')
                ->with('success', "Project '{$name}' has been removed from registry.");
        } catch (\Exception $e) {
            Log::error('Project deletion failed: '.$e->getMessage());

            return back()->with('error', 'Critical Error: Failed to execute project archival sequence.');
        }
    }
}
