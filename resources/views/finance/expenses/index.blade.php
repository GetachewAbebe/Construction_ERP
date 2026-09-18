<x-layouts.app-shell title="Expenses">
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold tracking-tight">Expense Management</h2>
                <p class="text-xs text-base-content/60">Field expenditures, project allocations, and cash requisitions.</p>
            </div>
        </div>

        <livewire:finance.expenses-table />
    </div>
</x-layouts.app-shell>
