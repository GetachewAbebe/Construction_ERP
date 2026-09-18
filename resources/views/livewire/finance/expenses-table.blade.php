@php
    $statusOptions = [
        ['id' => 'pending', 'name' => 'Pending Review'],
        ['id' => 'approved', 'name' => 'Approved'],
        ['id' => 'rejected', 'name' => 'Rejected'],
    ];

    $projectOptions = $projects->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])->toArray();
@endphp

<div>
    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
        <x-mary-stat title="Total Approved Expenses" :value="'ETB ' . number_format($totals['total_amount'], 2)" icon="o-banknotes" color="text-success" />
        <x-mary-stat title="Pending Authorizations" :value="$totals['pending_count']" icon="o-clock" color="text-warning" />
        <x-mary-stat title="Total Vouchers" :value="$totals['total_count']" icon="o-document-currency-dollar" color="text-primary" />
    </div>

    <x-mary-card>
        {{-- Action Toolbar --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between mb-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end flex-1">
                <x-mary-input
                    label="Search Expenses"
                    placeholder="Category, description..."
                    wire:model.live.debounce.300ms="search"
                    icon="o-magnifying-glass"
                    clearable
                    class="w-full sm:w-64" />

                <x-mary-select
                    label="Project"
                    placeholder="All Projects"
                    :options="$projectOptions"
                    wire:model.live="projectId"
                    class="w-full sm:w-56" />

                <x-mary-select
                    label="Status"
                    placeholder="All Statuses"
                    :options="$statusOptions"
                    wire:model.live="status"
                    class="w-full sm:w-44" />

                @if ($search !== '' || $status !== '' || $projectId)
                    <x-mary-button label="Reset" wire:click="clearFilters" class="btn-ghost btn-sm" icon="o-x-mark" />
                @endif
            </div>

            <x-mary-button label="Record Expense" wire:click="create" class="btn-primary" icon="o-plus" />
        </div>

        {{-- Table --}}
        <div class="relative overflow-x-auto">
            <div wire:loading.flex class="absolute inset-0 z-10 items-center justify-center bg-base-100/60 backdrop-blur-[1px]">
                <span class="loading loading-spinner loading-lg text-primary"></span>
            </div>

            <table class="table table-zebra w-full">
                <thead>
                    <tr class="bg-base-200/60 text-xs uppercase text-base-content/70">
                        <th>Date</th>
                        <th>Project</th>
                        <th>Category</th>
                        <th>Requester</th>
                        <th class="text-right">Amount (ETB)</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-base-200">
                    @forelse ($expenses as $expense)
                        <tr class="hover">
                            <td>
                                <span class="font-mono text-xs text-base-content/70">
                                    {{ $expense->expense_date ? $expense->expense_date->format('M d, Y') : '—' }}
                                </span>
                            </td>
                            <td>
                                <div class="font-semibold text-base-content">{{ $expense->project->name ?? '—' }}</div>
                                <div class="text-xs text-base-content/50 truncate max-w-xs">{{ $expense->description ?? 'No notes attached' }}</div>
                            </td>
                            <td>
                                <span class="badge badge-outline font-medium">
                                    {{ $expense->category }}
                                </span>
                            </td>
                            <td>
                                <div class="text-xs font-medium text-base-content/80">
                                    {{ $expense->user->name ?? 'Staff' }}
                                </div>
                            </td>
                            <td class="text-right font-mono font-bold text-base-content">
                                {{ number_format((float) $expense->amount, 2) }}
                            </td>
                            <td>
                                @if ($expense->status === 'approved')
                                    <span class="badge badge-success badge-sm font-semibold gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                        Approved
                                    </span>
                                @elseif ($expense->status === 'pending')
                                    <span class="badge badge-warning badge-sm font-semibold">
                                        Pending Review
                                    </span>
                                @else
                                    <span class="badge badge-error badge-sm font-semibold">
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="text-right">
                                <x-mary-button icon="o-eye" wire:click="viewVoucher({{ $expense->id }})" class="btn-ghost btn-xs text-primary" title="View Voucher" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-base-content/50">
                                <x-mary-icon name="o-banknotes" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                                <p class="font-medium">No expenditure records match your filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $expenses->links() }}
        </div>
    </x-mary-card>

    {{-- Expense Creation Modal --}}
    <x-mary-modal wire:model="showCreateModal" title="Log Field Expenditure" class="backdrop-blur">
        <form wire:submit="save" class="space-y-4">
            <x-mary-select
                label="Target Project *"
                :options="$projectOptions"
                wire:model.live="project_id"
                placeholder="Choose Project" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-mary-input label="Expense Category *" wire:model="category" placeholder="e.g. Fuel, Concrete, Labor" />
                <x-mary-input label="Amount (ETB) *" wire:model="amount" type="number" step="0.01" placeholder="0.00" />
            </div>

            <x-mary-input label="Transaction Date *" wire:model="expense_date" type="date" />
            <x-mary-textarea label="Purpose & Description" wire:model="description" placeholder="Provide context, site location or receipt reference..." rows="3" />

            <x-slot:actions>
                <x-mary-button label="Cancel" @click="$wire.showCreateModal = false" class="btn-ghost" />
                <x-mary-button label="Submit for Authorization" type="submit" class="btn-primary" icon="o-paper-airplane" spinner="save" />
            </x-slot:actions>
        </form>
    </x-mary-modal>

    {{-- Voucher Preview Modal --}}
    <x-mary-modal wire:model="showVoucherModal" title="Expenditure Voucher Details" class="backdrop-blur">
        @if ($selectedExpense)
            <div class="space-y-4 text-sm">
                <div class="p-4 rounded-xl bg-base-200/50 border border-base-300 grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs text-base-content/50 uppercase font-semibold">Voucher ID</div>
                        <div class="font-mono font-bold text-primary">#EXP-{{ str_pad((string) $selectedExpense->id, 5, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-base-content/50 uppercase font-semibold">Status</div>
                        <span class="badge badge-sm font-semibold capitalize {{ $selectedExpense->status === 'approved' ? 'badge-success' : ($selectedExpense->status === 'pending' ? 'badge-warning' : 'badge-error') }}">
                            {{ $selectedExpense->status }}
                        </span>
                    </div>
                    <div>
                        <div class="text-xs text-base-content/50 uppercase font-semibold">Project</div>
                        <div class="font-medium">{{ $selectedExpense->project->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-base-content/50 uppercase font-semibold">Total Amount</div>
                        <div class="font-mono font-bold text-lg text-base-content">ETB {{ number_format((float) $selectedExpense->amount, 2) }}</div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="text-xs text-base-content/50 uppercase font-semibold">Category</div>
                    <div class="badge badge-ghost">{{ $selectedExpense->category }}</div>

                    <div class="text-xs text-base-content/50 uppercase font-semibold mt-3">Description & Justification</div>
                    <p class="text-base-content/80 italic p-3 rounded-lg bg-base-200/30 border border-base-200">
                        {{ $selectedExpense->description ?? 'No narrative recorded.' }}
                    </p>

                    <div class="grid grid-cols-2 gap-2 text-xs text-base-content/60 pt-2 border-t border-base-200">
                        <div>Filed By: <span class="font-semibold text-base-content">{{ $selectedExpense->user->name ?? 'Staff' }}</span></div>
                        <div>Date: <span class="font-semibold text-base-content">{{ $selectedExpense->expense_date ? $selectedExpense->expense_date->format('M d, Y') : '—' }}</span></div>
                    </div>
                </div>
            </div>
        @endif

        <x-slot:actions>
            @if ($selectedExpense)
                <a href="{{ route('finance.expenses.print', $selectedExpense) }}" target="_blank" class="btn btn-sm btn-primary">
                    <x-mary-icon name="o-printer" class="w-4 h-4" /> Print Payment Voucher
                </a>
            @endif
            <x-mary-button label="Close" @click="$wire.showVoucherModal = false" class="btn-ghost btn-sm" />
        </x-slot:actions>
    </x-mary-modal>
</div>
