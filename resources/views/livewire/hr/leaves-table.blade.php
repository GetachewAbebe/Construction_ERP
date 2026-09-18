@php
    $employeeOptions = $employees->map(fn ($e) => ['id' => $e->id, 'name' => $e->first_name . ' ' . $e->last_name])->toArray();
@endphp

<div>
    {{-- Header Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
        <x-mary-stat title="Pending Approvals" :value="$totals['pending']" icon="o-clock" color="text-warning" />
        <x-mary-stat title="Approved Applications" :value="$totals['approved']" icon="o-check-badge" color="text-success" />
        <x-mary-stat title="Declined Applications" :value="$totals['rejected']" icon="o-x-circle" color="text-error" />
    </div>

    <x-mary-card>
        {{-- View Tabs & Actions --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-5">
            {{-- Tabs --}}
            <div class="flex flex-wrap gap-1 p-1 rounded-xl bg-base-200/80 border border-base-300">
                <button type="button" wire:click="setStatus('all')"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $view === 'active' && $statusTab === 'all' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/70 hover:text-base-content' }}">
                    All Requests
                </button>
                <button type="button" wire:click="setStatus('Pending')"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $view === 'active' && $statusTab === 'Pending' ? 'bg-warning text-warning-content shadow-sm' : 'text-base-content/70 hover:text-base-content' }}">
                    Pending ({{ $totals['pending'] }})
                </button>
                <button type="button" wire:click="setStatus('Approved')"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $view === 'active' && $statusTab === 'Approved' ? 'bg-success text-success-content shadow-sm' : 'text-base-content/70 hover:text-base-content' }}">
                    Approved ({{ $totals['approved'] }})
                </button>
                <button type="button" wire:click="setStatus('Rejected')"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $view === 'active' && $statusTab === 'Rejected' ? 'bg-error text-error-content shadow-sm' : 'text-base-content/70 hover:text-base-content' }}">
                    Rejected ({{ $totals['rejected'] }})
                </button>
                <button type="button" wire:click="setView('logs')"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $view === 'logs' ? 'bg-base-100 text-base-content shadow-sm font-bold border border-base-300' : 'text-base-content/70 hover:text-base-content' }}">
                    Historical Vault (Logs)
                </button>
            </div>

            <div class="flex items-center gap-3">
                @if ($view === 'active')
                    <x-mary-input
                        placeholder="Search employee..."
                        wire:model.live.debounce.300ms="search"
                        icon="o-magnifying-glass"
                        clearable
                        class="w-full sm:w-56 input-sm" />
                @endif

                <x-mary-button label="Apply for Leave" wire:click="create" class="btn-primary btn-sm" icon="o-plus" />
            </div>
        </div>

        {{-- Main Table View --}}
        <div class="relative overflow-x-auto">
            <div wire:loading.flex class="absolute inset-0 z-10 items-center justify-center bg-base-100/60 backdrop-blur-[1px]">
                <span class="loading loading-spinner loading-lg text-primary"></span>
            </div>

            @if ($view === 'active' && $requests)
                <table class="table table-zebra w-full">
                    <thead>
                        <tr class="bg-base-200/60 text-xs uppercase text-base-content/70">
                            <th>Employee</th>
                            <th>Duration / Dates</th>
                            <th>Reason & Notes</th>
                            <th>Status</th>
                            <th>Filed On</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-base-200">
                        @forelse ($requests as $leave)
                            <tr class="hover">
                                <td>
                                    <div class="font-semibold text-base-content">{{ $leave->employee->name ?? 'Staff' }}</div>
                                    <div class="text-xs text-base-content/50">{{ $leave->employee->department ?? 'General' }}</div>
                                </td>
                                <td>
                                    <div class="font-mono text-xs font-medium">
                                        {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                    </div>
                                    @php
                                        $days = \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1;
                                    @endphp
                                    <div class="text-xs text-base-content/60">{{ $days }} {{ \Illuminate\Support\Str::plural('day', $days) }}</div>
                                </td>
                                <td>
                                    <div class="text-xs text-base-content/80 max-w-sm italic">
                                        "{{ $leave->reason ?? 'Personal leave application' }}"
                                    </div>
                                </td>
                                <td>
                                    @if ($leave->status === 'Approved')
                                        <span class="badge badge-success badge-sm font-semibold gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                            Approved
                                        </span>
                                    @elseif ($leave->status === 'Pending')
                                        <span class="badge badge-warning badge-sm font-semibold">
                                            Pending Review
                                        </span>
                                    @else
                                        <span class="badge badge-error badge-sm font-semibold">
                                            Rejected
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-xs text-base-content/60">
                                        {{ $leave->created_at ? $leave->created_at->format('M d, Y H:i') : '—' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-base-content/50">
                                    <x-mary-icon name="o-calendar" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                                    <p class="font-medium">No leave applications match this filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $requests->links() }}
                </div>
            @elseif ($view === 'logs' && $logs)
                <table class="table table-zebra w-full">
                    <thead>
                        <tr class="bg-base-200/60 text-xs uppercase text-base-content/70">
                            <th>Employee</th>
                            <th>Approved Leave Span</th>
                            <th>Reason</th>
                            <th>Authorized By</th>
                            <th>Approval Date</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-base-200">
                        @forelse ($logs as $log)
                            <tr class="hover">
                                <td>
                                    <div class="font-semibold text-base-content">{{ $log->employee->name ?? 'Staff' }}</div>
                                </td>
                                <td>
                                    <span class="font-mono text-xs font-medium">
                                        {{ \Carbon\Carbon::parse($log->start_date)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($log->end_date)->format('M d, Y') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-xs text-base-content/70 italic max-w-xs truncate">
                                        {{ $log->reason ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-ghost badge-sm font-medium">
                                        {{ $log->approver->name ?? 'Administrator' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-xs text-base-content/60">
                                        {{ $log->approved_at ? \Carbon\Carbon::parse($log->approved_at)->format('M d, Y H:i') : '—' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-base-content/50">
                                    <x-mary-icon name="o-clock" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                                    <p class="font-medium">No historical leave logs recorded yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </x-mary-card>

    {{-- Leave Application Modal --}}
    <x-mary-modal wire:model="showCreateModal" title="Submit Leave Request" class="backdrop-blur">
        <form wire:submit="save" class="space-y-4">
            <x-mary-select
                label="Select Employee *"
                :options="$employeeOptions"
                wire:model="employee_id"
                placeholder="Choose Staff Member" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-mary-input label="Leave Start Date *" wire:model="start_date" type="date" />
                <x-mary-input label="Leave End Date *" wire:model="end_date" type="date" />
            </div>

            <x-mary-textarea
                label="Reason & Additional Remarks"
                wire:model="reason"
                placeholder="Specify reason (annual leave, medical, emergency, personal)..."
                rows="3" />

            <x-slot:actions>
                <x-mary-button label="Cancel" @click="$wire.showCreateModal = false" class="btn-ghost" />
                <x-mary-button label="Submit Request" type="submit" class="btn-primary" icon="o-check" spinner="save" />
            </x-slot:actions>
        </form>
    </x-mary-modal>
</div>
