<x-layouts.app-shell title="Leave Management">
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold tracking-tight">Leave Management</h2>
                <p class="text-xs text-base-content/60">Manage staff leave applications, review histories, and audit absence logs.</p>
            </div>
        </div>

        <livewire:h-r.leaves-table />
    </div>
</x-layouts.app-shell>
