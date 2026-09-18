<?php

declare(strict_types=1);

namespace App\Livewire\HR;

use App\Enums\LeaveStatus;
use App\Models\Employee;
use App\Models\EmployeeOnLeave;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class LeavesTable extends Component
{
    use Toast;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusTab = 'all';

    #[Url]
    public string $view = 'active'; // 'active' or 'logs'

    // Modal state
    public bool $showCreateModal = false;

    public ?int $employee_id = null;

    public ?string $start_date = null;

    public ?string $end_date = null;

    public ?string $reason = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusTab(): void
    {
        $this->resetPage();
    }

    public function updatedView(): void
    {
        $this->resetPage();
    }

    public function setStatus(string $status): void
    {
        $this->statusTab = $status;
        $this->view = 'active';
        $this->resetPage();
    }

    public function setView(string $view): void
    {
        $this->view = $view;
        $this->resetPage();
    }

    public function create(): void
    {
        $this->reset([
            'employee_id',
            'start_date',
            'end_date',
            'reason',
        ]);
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->addDays(1)->format('Y-m-d');
        $this->showCreateModal = true;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        // Check for overlapping leaves
        $hasOverlap = LeaveRequest::where('employee_id', $validated['employee_id'])
            ->whereIn('status', [LeaveStatus::Pending->value, LeaveStatus::Approved->value])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($sub) use ($validated) {
                        $sub->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                    });
            })
            ->exists();

        if ($hasOverlap) {
            $this->warning('Conflict Alert: This employee already has a pending or approved leave overlapping with these dates.');
        }

        $leaveRequest = LeaveRequest::create([
            'employee_id' => $validated['employee_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'status' => LeaveStatus::Pending->value,
        ]);

        // Notify Administrators
        try {
            $admins = User::role('Administrator')->get();
            if ($admins->isEmpty()) {
                $admins = User::where('id', 1)->get();
            }
            if ($admins->isNotEmpty()) {
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\LeaveRequestStatusNotification($leaveRequest, 'request'));
            }
        } catch (\Exception $e) {
            \Log::warning('Leave notification failed: '.$e->getMessage());
        }

        $this->success('Leave application filed and queued for administrative approval.');
        $this->showCreateModal = false;
    }

    public function render(): View
    {
        $employees = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name']);

        if ($this->view === 'logs') {
            $logs = EmployeeOnLeave::with(['employee', 'approver'])
                ->latest('approved_at')
                ->paginate(15);

            return view('livewire.hr.leaves-table', [
                'requests' => null,
                'logs' => $logs,
                'employees' => $employees,
                'totals' => [
                    'pending' => LeaveRequest::where('status', LeaveStatus::Pending->value)->count(),
                    'approved' => LeaveRequest::where('status', LeaveStatus::Approved->value)->count(),
                    'rejected' => LeaveRequest::where('status', LeaveStatus::Rejected->value)->count(),
                ],
            ]);
        }

        $query = LeaveRequest::with('employee');

        if ($this->search !== '') {
            $q = $this->search;
            $query->whereHas('employee', function ($sub) use ($q) {
                $sub->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%");
            });
        }

        if ($this->statusTab !== 'all') {
            $query->where('status', $this->statusTab);
        }

        $requests = $query->latest()->paginate(15);

        return view('livewire.hr.leaves-table', [
            'requests' => $requests,
            'logs' => null,
            'employees' => $employees,
            'totals' => [
                'pending' => LeaveRequest::where('status', LeaveStatus::Pending->value)->count(),
                'approved' => LeaveRequest::where('status', LeaveStatus::Approved->value)->count(),
                'rejected' => LeaveRequest::where('status', LeaveStatus::Rejected->value)->count(),
            ],
        ]);
    }
}
