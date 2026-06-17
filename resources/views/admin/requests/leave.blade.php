<x-layouts.app-shell title="Leave Requests">
    <div class="space-y-5">
        <div>
            <h2 class="text-xl font-semibold">Leave Requests</h2>
            <p class="text-sm text-base-content/60">List and process leave requests.</p>
        </div>
        <div class="flex flex-col items-center justify-center gap-3 rounded-xl border border-dashed border-base-300 bg-base-100 py-16 text-base-content/40">
            <x-mary-icon name="o-calendar-days" class="h-10 w-10" />
            <p class="text-sm">This section is coming soon.</p>
            <a href="{{ route('admin.requests.leave-approvals.index') }}" class="btn btn-ghost btn-sm">Go to Leave Approvals</a>
        </div>
    </div>
</x-layouts.app-shell>
