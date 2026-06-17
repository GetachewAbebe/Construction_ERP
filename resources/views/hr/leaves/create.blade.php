<x-layouts.app-shell title="New Leave Request">
    <div class="mx-auto max-w-2xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">New Leave Request</h2>
                <p class="text-sm text-base-content/60">File a leave request for an employee.</p>
            </div>
            <a href="{{ route('hr.leaves.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        <form action="{{ route('hr.leaves.store') }}" method="POST"
              class="space-y-6 rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf

            <div>
                <label class="mb-1.5 block text-sm font-medium">Employee</label>
                <select name="employee_id" id="employeeSelect" required class="select select-bordered w-full {{ $errors->has('employee_id') ? 'select-error' : '' }}">
                    <option value="" disabled @selected(!old('employee_id'))>Select employee…</option>
                    @foreach ($employees as $e)
                        <option value="{{ $e->id }}" @selected(old('employee_id') == $e->id)>{{ $e->first_name }} {{ $e->last_name }}</option>
                    @endforeach
                </select>
                @error('employee_id') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                <p id="availabilityHint" class="mt-2 hidden rounded-lg bg-base-200/60 px-3 py-2 text-xs"></p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Start date</label>
                    <input type="date" name="start_date" id="startDate" value="{{ old('start_date') }}" required class="input input-bordered w-full {{ $errors->has('start_date') ? 'input-error' : '' }}" />
                    @error('start_date') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">End date</label>
                    <input type="date" name="end_date" id="endDate" value="{{ old('end_date') }}" required class="input input-bordered w-full {{ $errors->has('end_date') ? 'input-error' : '' }}" />
                    @error('end_date') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium">Reason</label>
                <textarea name="reason" rows="4" placeholder="Brief explanation…" class="textarea textarea-bordered w-full">{{ old('reason') }}</textarea>
            </div>

            <div class="flex items-start gap-2 rounded-lg border border-info/30 bg-info/5 p-3 text-xs text-base-content/70">
                <x-mary-icon name="o-information-circle" class="h-5 w-5 shrink-0 text-info" />
                <span>Requests need HR/admin approval. Dates overlapping existing approved or pending leaves are rejected on submit.</span>
            </div>

            <div class="flex justify-end gap-2 border-t border-base-200 pt-5">
                <a href="{{ route('hr.leaves.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit request</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sel = document.getElementById('employeeSelect');
            const start = document.getElementById('startDate');
            const end = document.getElementById('endDate');
            const hint = document.getElementById('availabilityHint');
            const today = new Date().toISOString().slice(0, 10);
            start.min = today; end.min = today;
            start.addEventListener('change', () => { end.min = start.value || today; });

            function check(id) {
                if (!id) return;
                fetch(`/hr/employees/${id}/leave-dates`)
                    .then(r => r.json())
                    .then(data => {
                        hint.classList.remove('hidden');
                        hint.textContent = data.length
                            ? `Notice: this employee has ${data.length} active/pending leave(s). Avoid overlapping dates.`
                            : 'This employee is available for new leave requests.';
                    })
                    .catch(() => hint.classList.add('hidden'));
            }
            sel.addEventListener('change', () => check(sel.value));
            if (sel.value) check(sel.value);
        });
    </script>
    @endpush
</x-layouts.app-shell>
