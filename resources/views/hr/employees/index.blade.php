<x-layouts.app-shell title="Employees">
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold tracking-tight">Employee Directory</h2>
                <p class="text-xs text-base-content/60">Manage organizational workforce profiles, designations, and corporate access.</p>
            </div>
        </div>

        <livewire:h-r.employees-table />
    </div>
</x-layouts.app-shell>
