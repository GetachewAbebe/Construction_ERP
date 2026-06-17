<x-layouts.app-shell title="Attendance Settings">
    <div class="mx-auto max-w-2xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Attendance Settings</h2>
                <p class="text-sm text-base-content/60">Shift hours and tardiness tolerance.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Dashboard
            </a>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ session('error') }}</span></div>
        @endif

        <form action="{{ route('admin.attendance-settings.update') }}" method="POST"
              class="space-y-6 rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-clock" class="h-5 w-5 text-primary" /> Shift hours</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Shift start time</label>
                        <input type="time" name="shift_start_time" value="{{ old('shift_start_time', $settings['shift_start_time'] ?? '') }}" required class="input input-bordered w-full {{ $errors->has('shift_start_time') ? 'input-error' : '' }}" />
                        @error('shift_start_time') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Shift end time</label>
                        <input type="time" name="shift_end_time" value="{{ old('shift_end_time', $settings['shift_end_time'] ?? '') }}" required class="input input-bordered w-full {{ $errors->has('shift_end_time') ? 'input-error' : '' }}" />
                        @error('shift_end_time') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-adjustments-horizontal" class="h-5 w-5 text-success" /> Tolerance</h3>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Grace period (minutes)</label>
                    <input type="number" name="grace_period_minutes" min="0" max="120" value="{{ old('grace_period_minutes', $settings['grace_period_minutes'] ?? 0) }}" required class="input input-bordered w-full sm:w-48 {{ $errors->has('grace_period_minutes') ? 'input-error' : '' }}" />
                    <p class="mt-1 text-xs text-base-content/50">Allowed lateness before a check-in is marked late.</p>
                    @error('grace_period_minutes') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-base-200 pt-5">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">Discard</a>
                <button type="submit" class="btn btn-primary">Apply configuration</button>
            </div>
        </form>
    </div>
</x-layouts.app-shell>
