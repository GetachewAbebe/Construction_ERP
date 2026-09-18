<?php

declare(strict_types=1);

namespace App\Livewire\Projects;

use App\Models\DailyProgressReport;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class DailyReportsTable extends Component
{
    use Toast;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public ?int $projectId = null;

    #[Url]
    public ?string $date = null;

    // Modals
    public bool $showCreateModal = false;

    public bool $showDetailModal = false;

    public ?DailyProgressReport $selectedReport = null;

    // Form fields
    public ?int $project_id = null;

    public ?string $report_date = null;

    public string $weather = 'Clear / Sunny';

    public int $manpower_count = 0;

    public string $work_performed = '';

    public ?string $materials_received = null;

    public ?string $machinery_deployed = null;

    public ?string $safety_incidents = 'Zero safety incidents reported today.';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedProjectId(): void
    {
        $this->resetPage();
    }

    public function updatedDate(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'projectId', 'date');
        $this->resetPage();
    }

    public function create(): void
    {
        $this->reset([
            'project_id',
            'work_performed',
            'materials_received',
            'machinery_deployed',
        ]);
        $this->report_date = now()->format('Y-m-d');
        $this->weather = 'Clear / Sunny';
        $this->manpower_count = 0;
        $this->safety_incidents = 'Zero safety incidents reported today.';
        $this->showCreateModal = true;
    }

    public function viewDetails(int $id): void
    {
        $this->selectedReport = DailyProgressReport::with(['project', 'author'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'report_date' => ['required', 'date'],
            'weather' => ['required', 'string', 'max:100'],
            'manpower_count' => ['required', 'integer', 'min:0'],
            'work_performed' => ['required', 'string', 'max:5000'],
            'materials_received' => ['nullable', 'string', 'max:1000'],
            'machinery_deployed' => ['nullable', 'string', 'max:1000'],
            'safety_incidents' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        DailyProgressReport::create([
            'project_id' => $validated['project_id'],
            'user_id' => Auth::id(),
            'report_date' => $validated['report_date'],
            'weather' => $validated['weather'],
            'manpower_count' => $validated['manpower_count'],
            'work_performed' => $validated['work_performed'],
            'materials_received' => $validated['materials_received'],
            'machinery_deployed' => $validated['machinery_deployed'],
            'safety_incidents' => $validated['safety_incidents'],
            'status' => 'submitted',
        ]);

        $this->success('Site Daily Progress Report (DPR) successfully submitted.');
        $this->showCreateModal = false;
    }

    public function render(): View
    {
        $query = DailyProgressReport::with(['project', 'author']);

        if ($this->search !== '') {
            $q = $this->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('work_performed', 'like', "%{$q}%")
                    ->orWhere('materials_received', 'like', "%{$q}%")
                    ->orWhere('machinery_deployed', 'like', "%{$q}%");
            });
        }

        if ($this->projectId) {
            $query->where('project_id', $this->projectId);
        }

        if ($this->date) {
            $query->whereDate('report_date', $this->date);
        }

        $reports = $query->latest('report_date')->paginate(10);

        $projects = Project::orderBy('name')->get(['id', 'name']);

        $totals = [
            'total_reports' => DailyProgressReport::count(),
            'today_reports' => DailyProgressReport::whereDate('report_date', now()->toDateString())->count(),
            'total_manpower_today' => (int) DailyProgressReport::whereDate('report_date', now()->toDateString())->sum('manpower_count'),
        ];

        return view('livewire.projects.daily-reports-table', [
            'reports' => $reports,
            'projects' => $projects,
            'totals' => $totals,
        ]);
    }
}
