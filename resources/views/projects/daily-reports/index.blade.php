<x-layouts.app-shell title="Daily Progress Reports">
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold tracking-tight">Site Daily Progress Reports (DPR)</h2>
                <p class="text-xs text-base-content/60">Daily construction site diaries, trade manpower logs, materials received, and work progress logs.</p>
            </div>
        </div>

        <livewire:projects.daily-reports-table />
    </div>
</x-layouts.app-shell>
